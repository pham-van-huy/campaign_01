<template lang="html">
    <div class="fade show update-event" style="display: block">
        <div class="modal-dialog ui-block window-popup create-event">
            <div class="ui-block-title">
                <h6 class="title">{{ $t('events.update_event') }}</h6>
            </div>
            <div class="ui-block-content">
                <div class="form-group label-floating is-focused" :class="{ 'has-danger': errors.has('name')}">
                    <label class="control-label">{{ $t('form.label.event_name') }}</label>
                    <input
                        name="name"
                        class="form-control"
                        type="text"
                        minlength="20"
                        maxlength="255"
                        v-model="dataUpdate.title"
                        v-validate="'required|max:255'">
                    <span v-show="errors.has('name')" class="material-input text-danger">
                        {{ errors.first('name') }}
                    </span>
                </div>

                <div class="form-group label-floating">
                    <label class="control-label">{{ $t('form.label.event_location') }}</label>
                    <gmap-autocomplete :value="dataUpdate.address" class="form-control" @place_changed="setPlace"></gmap-autocomplete>
                    <span class="material-input"></span>
                </div>

                <div class="form-group label-floating" v-if="showMap">
                    <gmap-map :center="center" :zoom="zoom" style="width: 100%; height: 500px" ref="elMap">
                        <gmap-marker
                            :position="center"
                            :clickable="true"
                            :draggable="true"
                            @click="center"
                            @dragend="updatePosition($event)">
                        </gmap-marker>
                    </gmap-map>
                </div>

                <setting-date v-if="showSettings"
                    :startDay.sync="startDate"
                    :endDay.sync="endDate"
                    :flag.sync="flag"
                    :isUpdate="true">
                </setting-date>

                <quill-editor
                    data-vv-name="description"
                    id="description"
                    :class="{ fullscreen: toggleFullscreen }"
                    :options="editorOption"
                    ref="description"
                    v-model="dataUpdate.description"
                    v-validate="'required'">
                </quill-editor>
                <span v-show="errors.has('description')" class="material-input text-danger">
                    {{ errors.first('description') }}
                </span>

                <div class="form-group label-floating">
                    <div :class="{ 'upload-file': true, 'has-error-upload': hasErrorFiles }">
                        <p>{{ $t('form.title.upload_images') }}</p>
                        <dropzone
                            id="myVueDropzoneId"
                            ref="myVueDropzone"
                            url="/api/file/upload"
                            :dropzone-options="dropzoneOptions"
                            :use-custom-dropzone-options="true"
                            @vdropzone-success="showSuccess"
                            @vdropzone-removed-file="deleteFile"
                            @vdropzone-queue-complete="queueComplete"
                            @vdropzone-files-added="fileAdded">
                        </dropzone>
                    </div>
                </div>
                <div class="wrap-button">
                    <router-link :to="{
                        name: 'event.index',
                        params: {
                            'slugEvent': event.slug,
                            'slug': event.campaign_id
                            }
                        }">
                        <button class="btn btn-breez btn-lg full-width btn-left">
                            {{ $t('form.button.cancel') }}
                        </button>
                    </router-link>
                    <button class="btn btn-breez btn-lg full-width btn-right" @click="createEvent">
                        {{ $t('form.button.save') }}
                    </button>
                </div>
            </div>
            <upload-quill
                :uploadVisible.sync="uploadVisible"
                :imageInsert="imageInsert"
                @insertImage="insertImageToContent">
            </upload-quill>
        </div>
    </div>
</template>

<script type="text/javascript">
    import * as VueGoogleMaps from 'vue2-google-maps'
    import Dropzone from 'vue2-dropzone'
    import Vue from 'vue'
    import SettingDate from '../../libs/SettingDate.vue'
    import { config, editorOption } from '../../../config'
    import { patch, get, del } from '../../../helpers/api'
    import noty from '../../../helpers/noty'
    import uploadedImage from '../../../helpers/mixin/uploadedImage'
    import searchMap from '../../../helpers/mixin/searchMap'

    Vue.use(Dropzone)
    Vue.use(VueGoogleMaps, {
        load: {
            key: config.keyMap,
            libraries: 'places',
        }
    })

    export default {
        data() {
            return {
                showMap: false,
                zoom: config.zoom,
                startDate: '',
                endDate: '',
                flag: true,
                dataUpdate : {
                    title: '',
                    description: '',
                    latitude: null,
                    longitude: null,
                    address: ' ',
                    settings: [],
                    files: [],
                    mediaDels: [],
                },
                hasErrorFiles: false,
                uploadVisible: false,
                imageInsert: '',
                accessToken: `Bearer ${localStorage.getItem('access_token')}`,
                //add when update event
                event: {},
                showSettings: false,
                mockingFile: true,
                dropzoneOptions: {
                    autoProcessQueue: false,
                    maxNumberOfFiles: config.maxFileUpload,
                    maxFileSizeInMB: config.maxSizeFile,
                    acceptedFileTypes: 'image/*',
                    showRemoveLink: true,
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`
                    },
                    language: {
                        dictRemoveFileConfirmation: this.$i18n.t('messages.delete_photo')
                    }
                }
            }
        },
        mixins: [uploadedImage, searchMap],
        methods: {
            addErrors(index, value) {
                this.errorBags[index] = value
            },

            setPlace(place) {
                this.setLocation(this.dataUpdate, place)
            },

            updatePosition(event) {
                const latLng = event.latLng.toJSON()
                this.setGeocoder(this.dataUpdate, latLng)
            },

            addSettings() {
                if (!this.startDate) {
                    this.startDate = moment().format('L');
                }

                this.dataUpdate.settings = [
                    { key: window.Laravel.settings.events.start_day, value: this.startDate },
                    { key: window.Laravel.settings.events.end_day, value: this.endDate }
                ]
            },

            showSuccess(file, response) {
                this.dataUpdate.files.push(response)
            },

            createEvent() {
                this.checkRejectedFiles()
                this.$validator.validateAll().then((result) => {
                    if (!this.hasErrorFiles) {
                        this.mockingFile = false
                        this.$refs.myVueDropzone.processQueue()

                        if (!this.$refs.myVueDropzone.getUploadingFiles().length) {
                            this.queueComplete()
                        }
                    }
                })
            },

            deleteFile(file, error, xhr) {
                this.checkRejectedFiles();

                if (typeof file.id === 'number') {
                    this.dataUpdate.mediaDels.push(file.id)
                    this.$refs.myVueDropzone.dropzone.options.maxFiles++
                }
            },

            queueComplete(status) {
                if (!this.mockingFile && !this.hasErrorFiles) {
                    this.addSettings()
                    patch(`event/update/${this.pageId}`, this.dataUpdate)
                    .then(res => {
                        noty({
                            text: this.$i18n.t('messages.update_success'),
                            force: false,
                            container: false,
                            type: 'success'
                        })
                        this.$router.push({
                            name: 'event.index',
                            params: {
                                slug: this.event.campaign_id,
                                slugEvent: this.pageId
                                }
                            })
                    })
                    .catch(err => {
                        noty({
                            text: this.$i18n.t('messages.update_fail'),
                            type: 'error',
                            force: false,
                            container: false
                        })
                    })
                }
            },

            fileAdded(file) {
                this.checkRejectedFiles()
            },

            //add when update
            getStartDateOfSetting(date) {
                return this.startDate = date.settings.filter(setting => {
                    return setting.key == window.Laravel.settings.events.start_day
                })[0].value
            },

            getEndDateOfSetting(date) {
                return this.endDate = date.settings.filter(setting => {
                    return setting.key == window.Laravel.settings.events.end_day
                })[0].value
            },

            setDataUpdate() {
                this.dataUpdate.title = this.event.title
                this.dataUpdate.description = this.event.description
                this.dataUpdate.latitude = this.center.lat = Number(this.event.latitude)
                this.dataUpdate.longitude = this.center.lng = Number(this.event.longitude)
                this.dataUpdate.address = this.event.address
                this.dataUpdate.settings = [
                    {
                        key: window.Laravel.settings.events.start_day,
                        value: this.getStartDateOfSetting(this.event)
                    },
                    {
                        key: window.Laravel.settings.events.end_day,
                        value: this.getEndDateOfSetting(this.event)
                    }
                ]
            },

            // show images that have already from server
            initImageDropzone() {
                const dropzone = this.$refs.myVueDropzone.dropzone
                const { thumbnailHeight, thumbnailWidth, thumbnailMethod } = dropzone.options

                for (let media of this.event.media) {
                    // mocking file images to emit events so that add file dropzone area
                    let mockFile = { id: media.id, name: media.image_default.substring(media.image_default.lastIndexOf('/') + 1) };
                    // image url that request to server
                    let imageUrl = `${media.image_default}?h=${thumbnailHeight}&w=${thumbnailWidth}&fit=max`

                    // Request to get size image
                    const obj = new XMLHttpRequest();
                    obj.open('HEAD', media.image_default);

                    obj.onreadystatechange = function() {
                        // ReadyState is 4 that means processing finished and response ready
                        if ( obj.readyState == 4 ) {
                            if ( obj.status == 200 ) {
                                mockFile['size'] = obj.getResponseHeader('Content-Length')
                                dropzone.emit("addedfile", mockFile)
                                dropzone.emit("thumbnail", mockFile, imageUrl)
                                dropzone.files.push(mockFile)
                                dropzone.emit("complete", mockFile)
                                const existingFileCount = 1; // The number of files already uploaded
                                dropzone.options.maxFiles = dropzone.options.maxFiles - existingFileCount;
                            }
                        }
                    };

                    obj.send(null);
                }
            },

            checkRejectedFiles() {
                this.hasErrorFiles = this.$refs.myVueDropzone.getRejectedFiles().filter(file => !file.id).length
            }
        },

        components: {
            Dropzone,
            SettingDate,
        },

        created() {
            get(`event/update/${this.pageId}`)
                .then(res => {
                    this.event = res.data.event[0]
                    this.setDataUpdate()
                    this.showSettings = true
                    this.showMap = true
                    this.initImageDropzone()
                })
                .catch(() => this.$router.replace('/not-found'))
        }
    }
</script>

<style lang="scss">
    .update-event {
        .upload-file {
            min-height: 300px;
            form {
                min-height: 300px;
            }
        }
        .has-error-upload {
            border: 1px solid red;
            border-radius: 2px;
        }
        .vue-dropzone {
            .dz-preview {
                .dz-error-mark {
                    i {
                        color: red !important;
                    }
                }
            }
        }
        .dz-error-message {
            top: 5px !important;
            left: 59px !important;
        }
    }
    .list-image {
        margin-top: 20px;
        > div {
            display: inline-block;
            position: relative;
            >img {
                border: 5px solid #fff;
                margin: 2px;
                max-height: 120px;
                max-width: 200px;
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12), 0 1px 3px 0 rgba(0,0,0,.2);
            }
            .remove-img {
                position: absolute;
                right: 12px;
                top: 12px;
            }
            &:hover {
                >img {
                    opacity: 0.8;
                }
            }

            .recovery-img {
                position: absolute;
                display: none;
                right: 12px;
                top: 12px;
                font-size: 45px;
                color: #5c9fe9;
                &:hover {
                    color: #0085ff;
                }
            }
        }
    }
</style>

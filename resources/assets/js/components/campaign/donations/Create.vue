<template>
    <div class="ui-block create-donate">
        <div class="ui-block-title">
            <h6 class="title">{{ $t('campaigns.goal.create') }}</h6>
        </div>
        <div class="ui-block-content">
            <div class="form-group label-floating is-focused" :class="{ 'has-danger': errors.has('title')}">
                <label class="control-label">{{ $t('form.label.title') }}</label>
                <input
                    name="title"
                    class="form-control"
                    type="text"
                    minlength="20"
                    maxlength="255"
                    v-model="newGoals.title"
                    v-validate="'required|max:255'">
                <span v-show="errors.has('title')" class="material-input text-danger">
                    {{ errors.first('title') }}
                </span>
            </div>
            <quill-editor
                id="description"
                :class="{ fullscreen: toggleFullscreen }"
                :options="editorOption"
                ref="description"
                v-model="newGoals.description">
            </quill-editor>
            <div class="wrap-donation">
                <p>{{ $t('form.title.add_donation') }}
                    <i class="fa fa-plus-square icon-donation" aria-hidden="true" id="add-donation" @click="addDonation">
                    </i>
                </p>
                <donations
                    v-for="(donation, index) in donations"
                    :donation="donation"
                    :index="index"
                    :key="index"
                    :visible="visible"
                    @add-instance-validate="addErrors"
                    @update-row-donation="updateGoal"
                    @delete-donation="deleteDonation(index)">
                </donations>
            </div>
            <div class="wrap-button">
                <router-link :to="{ name: 'campaign.timeline', params: { 'slug': this.pageId } }">
                    <button class="btn btn-breez btn-lg full-width btn-left">
                        {{ $t('form.button.cancel') }}
                    </button>
                </router-link>
                <button class="btn btn-breez btn-lg full-width btn-right" @click="createGoals">
                    {{ $t('form.button.create_event') }}
                </button>
            </div>
        </div>
        <upload-quill
            :uploadVisible.sync="uploadVisible"
            :imageInsert="imageInsert"
            @insertImage="insertImageToContent">
        </upload-quill>
    </div>
</template>
<script type="text/javascript">
    import Donations from './Donations.vue'
    import uploadedImage from '../../../helpers/mixin/uploadedImage'
    import { post } from '../../../helpers/api'
    import noty from '../../../helpers/noty'

    export default {
        data: () => ({
            visible: true,
            donations: [
                { type : '', goal: '', quality: ''}
            ],
            errorBags: {},
            newGoals: {
                title: '',
                description: ''
            },
        }),
        mixins: [uploadedImage],
        methods: {
            addDonation() {
                let donation = { type : '', goal: '', quality: ''}
                this.donations.push(donation)
                this.visible = false
            },
            getGoals() {
                this.newGoals.goals = []

                for (let donation of this.donations) {
                    let flag = true

                    for (let key in donation) {
                        if (!donation[key]) {
                            flag = false
                            break
                        }
                    }

                    if (flag) {
                        this.newGoals.goals.push(donation)
                    }
                }
            },
            deleteDonation(index) {
                this.donations.length > 1 && this.donations.splice(index, 1)

                this.errorBags.length > 1 && this.errorBags.splice(index, 1)

                if (this.donations.length === 1) {
                    this.visible = true
                }
            },
            updateGoal(newValue) {
                // mix <=> key: type, goal, quality
                // key <=> key of newValue that children emitted
                const [key, mix, instanse] = Object.keys(newValue)
                const index = newValue[key]

                this.errorBags[index] = newValue[instanse]
                this.donations[index][mix] = newValue[mix]
            },
            hasErrorDonation() {
                let errorDonations = []

                for(let index in this.errorBags) {
                    // Triger validation children donation
                    this.errorBags[index].validateAll().catch(() => {})
                    errorDonations.push(this.errorBags[index].getErrors())
                }

                return !errorDonations.every(item => !item.count())
            },
            addErrors(index, value) {
                this.errorBags[index] = value
            },
            createGoals() {
                this.getGoals()
                this.newGoals.campaign_id = this.pageId
                this.$validator.validateAll().then((result) => {
                    if (Object.keys(this.errorBags).length >= 1 && this.hasErrorDonation()) {
                        return
                    }

                    this.$Progress.start()
                    post('campaign/goal', this.newGoals).then(res => {
                        this.$Progress.finish()
                        noty({
                            text: this.$i18n.t('messages.create_success'),
                            force: false,
                            container: false,
                            type: 'success'
                        })
                    }).catch(err => {
                        this.$Progress.fail()
                        noty({
                            text: this.$i18n.t('messages.create_fail'),
                            type: 'error',
                            force: false,
                            container: false
                        })
                    })
                }).catch(() => {})
            },
        },
        components: {
            Donations
        }
    }
</script>
<style lang="scss">
    .create-donate {
        width: 100% !important;
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
        .wrap-button {
            height: 50px;
            .btn-breez {
                width: 45%;
            }
            a {
                color: white;
                .btn-left {
                    float: left;
                    background-color: #ff5e3a;
                }
            }
            .btn-right {
                float: right;
            }
        }
    }
    .wrap-donation {
        #add-donation {
            &:hover {
                color: #08ddc1;
                cursor: pointer;
            }
        }
        .store-icon {
            padding-left: 5px;
            margin-bottom: 25px;
        }
        .icon-donation {
            font-size: 1.5em !important;
            padding-left: 5px;
        }
        #delete-donation {
            margin-top: 1em;
            &:hover {
                color: #ff5e3a;
                cursor: pointer;
            }
        }
        .visible:hover {
            cursor: not-allowed !important;
        }
    }
</style>

<template>
    <div class="modal fade show wrap-action" id="open-photo-popup-v1" style="display: block" @click.self="close">
        <div
            class="modal-dialog ui-block window-popup open-photo-popup open-photo-popup-v1">
            <a href="javascript:void(0)" @click="close" class="close icon-close" data-dismiss="modal" aria-label="Close">
                <svg class="olymp-close-icon">
                    <use xlink:href="/frontend/icons/icons.svg#olymp-close-icon"></use>
                </svg>
            </a>
            <div class="open-photo-content">
                <article class="hentry post" v-if="events.data[indexOfGoal].user">
                    <div class="post__author author vcard inline-items">
                        <img :src="events.data[indexOfGoal].user.image_thumbnail" alt="author">

                        <div class="author-date">
                            <router-link :to="{ name: 'user.timeline', params: { slug: events.data[indexOfGoal].user_id } }">
                                <a class="h6 post__author-name fn" href="javascript:void(0)">
                                    {{ events.data[indexOfGoal].user.name }}
                                </a>
                            </router-link>
                            <div class="post__date">
                                <time class="published" datetime="2017-03-24T18:18">
                                    <timeago :since="events.data[indexOfGoal].created_at"/>
                                </time>
                            </div>
                        </div>
                    </div>

                    <p>
                        <span class="ui-block-title about-action">
                            <p v-if="!events.data[indexOfGoal].expense_id" class='h3 caption-of-action'>{{ events.data[indexOfGoal].title }}</p>
                            <p v-else class='h3 caption-of-action'>{{ showTextExpense(events.data[indexOfGoal].caption) }}</p>
                            <table class="mt-3 table-bordered table table-sm" v-if="events.data[indexOfGoal].goals.length">
                                <thead class="thead-default">
                                    <tr>
                                        <th><i class="fa fa-hashtag" aria-hidden="true"></i></th>
                                        <th>{{ $t('campaigns.goal_donate.type') }}</th>
                                        <th>{{ $t('campaigns.goal_donate.goal') }}</th>
                                        <th>{{ $t('campaigns.goal_donate.received') }}</th>
                                        <th>{{ $t('campaigns.goal_donate.waitting') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in events.data[indexOfGoal].goals">
                                        <th scope="row">{{ i + 1 }}</th>
                                        <td>{{ `${item.donation_type.name}(${item.donation_type.quality.name})` }}</td>
                                        <td>{{ item.goal }}</td>
                                        <td>{{ totalReceived(item.donations) }}</td>
                                        <td>{{ totalWaitting(item.donations) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <show-text
                                :text="events.data[indexOfGoal].description"
                                :show_char=500
                                :show="$t('events.show_more')"
                                :hide="$t('events.show_less')">
                            </show-text>
                        </span>
                    </p>

                    <master-like
                        :likes="events.data[indexOfGoal].likes"
                        :checkLiked="checkLikeGoal"
                        :flag="model"
                        :type="'like'"
                        :modelId="events.data[indexOfGoal].id"
                        :numberOfComments="events.data[indexOfGoal].number_of_comments"
                        :numberOfLikes="events.data[indexOfGoal].number_of_likes"
                        :showMore="true"
                        :deleteDate="events.data[indexOfGoal].deleted_at"
                        :roomLike="roomLike">
                    </master-like>

                    <div class="control-block-button post-control-button">
                        <master-like
                            :likes="events.data[indexOfGoal].likes"
                            :checkLiked="checkLikeGoal"
                            :flag="model"
                            :type="'like-infor'"
                            :modelId="events.data[indexOfGoal].id"
                            :numberOfComments="events.data[indexOfGoal].number_of_comments"
                            :numberOfLikes="events.data[indexOfGoal].number_of_likes"
                            :deleteDate="events.data[indexOfGoal].deleted_at"
                            :roomLike="roomLike">
                        </master-like>
                    </div>
                </article>
            </div>
            <div class="row m-t-sm">
                <div class="col-lg-12">
                    <div class="panel blank-panel">
                        <div class="panel-heading">
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab-1" data-toggle="tab" aria-expanded="false">{{ $t('campaigns.donations.comments') }}</a>
                                    </li>
                                    <li class="">
                                        <a href="#tab-2" data-toggle="tab" aria-expanded="true">{{ $t('campaigns.donations.donates') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-1">
                                    <comment
                                        v-if="events.data[indexOfGoal].comments"
                                        :comments="events.data[indexOfGoal].comments"
                                        :numberOfComments="events.data[indexOfGoal].number_of_comments"
                                        :model-id ="events.data[indexOfGoal].id"
                                        :flag="'campaignGoal'"
                                        :classListComment="''"
                                        :classFormComment="''"
                                        :deleteDate="events.data[indexOfGoal].deleted_at"
                                        :canComment="canComment"
                                        :roomLike="roomLike">
                                    </comment>
                                </div>
                                <div class="tab-pane" id="tab-2">
                                    <donate
                                        :listGoals="events.data[indexOfGoal].goals"
                                        :campaignGoalId="events.data[indexOfGoal].id"
                                        :indexOfGoal="indexOfGoal"
                                        v-if="events.data[indexOfGoal].goals">
                                    </donate>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    import { mapState, mapActions } from 'vuex'
    import ShowText from '../../libs/ShowText.vue'
    import Comment from '../../comment/Comment.vue'
    import MasterLike from '../../like/MasterLike.vue'
    import Donate from './Donate.vue'

    export default {
        props: {
            showGoal: {},
            checkLikeGoal: {},
            canComment: true,
            roomLike: '',
            indexOfGoal: null
        },

        data() {
            return {
                model: 'campaignGoal'
            }
        },
        computed: {
            ...mapState('auth', {
                user: state => state.user
            }),
            ...mapState('campaign', ['events'])
        },

        updated() {
            console.log(1, this.events.data[this.indexOfGoal].user)
            this.setScrollBar()
            this.eventPostComment()
        },
        methods: {
            ...mapActions('like', [
                'appendLike',
            ]),

            close() {
                this.$emit('update:showGoal', false)
                this.$emit('update:indexOfGoal', null)
            },

            setScrollBar() {
                if ($(".list-comment-action")[0]) {
                    $(".list-comment-action")[0].scrollTop = $(".list-comment-action")[0].scrollHeight;
                }
            },

            eventPostComment() {
                $(".input-comment-action textarea").on('keyup', function(e) {
                    if(e.keyCode == 13) {
                        $(".list-comment-action")[0].scrollTop = $(".list-comment-action")[0].scrollHeight;
                    }
                })
            },

            showTextExpense(data) {
                var caption = JSON.parse(data);

                return `${caption.cost} ${caption.nameQuality}
                    ${caption.typeName} ${this.$i18n.t('actions.is_used')}
                    ${moment(caption.expenseTime, 'YYYY-MM-DD').format('DD/MM/YYYY')}`
            },
            totalReceived(donations) {
                return _.sumBy(donations, function(donation) {
                    return donation.status == window.Laravel.settings.donations.accept ? donation.value : 0
                })
            },
            totalWaitting(donations) {
                return _.sumBy(donations, function(donation) {
                    return donation.status == window.Laravel.settings.donations.not_accept ? donation.value : 0
                })
            },
        },

        components: {
            ShowText,
            Comment,
            MasterLike,
            Donate
        },
        sockets: {
            newLike: function (data) {
                if (this.user.id != data.user.id) {
                    this.appendLike(data)
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .wrap-action {
        background: rgba(0, 0, 0, 0.53);
        overflow-y: scroll;
        &::-webkit-scrollbar {
            display: none;
        }
        .open-photo-thumb {
            padding: 0px;
            .swiper-container {
                padding-bottom: 0px;
            }
        }
        .modal-dialog {
            margin-bottom: 100px;
        }
        .list-comment-action {
            max-height: 400px;
            overflow-y: scroll;
            &::-webkit-scrollbar {
                display: none;
            }
        }

        .about-action {
            .caption-of-action {
                display: block;
                font-size: 25px;
                margin-bottom: 17px;
            }
            span {
                display: block;
            }
        }

        .ui-block-title {
            padding: 0px 25px;
        }

        .btn-next-without {
            right: 0px;
            text-align: right;
        }

        .btn-prev-without {
            left: 0px;
            text-align: left;
        }

        .btn-next-without, .btn-prev-without {
            width: 30%;
            height: 100%;
            padding-top: 56%;
        }
        .post {
            border-bottom: 0px solid;
        }
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        color: #555;
        cursor: default;
        background-color: #fff;
        border: 1px solid #ddd;
        border-bottom-color: transparent;
    }

    .nav-tabs>li>a {
        margin-right: 2px;
        line-height: 1.42857143;
        border: 1px solid transparent;
        border-radius: 4px 4px 0 0;
    }

    .nav>li>a {
        position: relative;
        display: block;
        padding: 10px 15px;
    }
    .nav-tabs {
        border-bottom: 1px solid #ddd;
    }
    .nav {
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }
    .nav-tabs>li {
        float: left;
        margin-bottom: -1px;
    }
    .nav>li {
        position: relative;
        display: block;
    }
</style>

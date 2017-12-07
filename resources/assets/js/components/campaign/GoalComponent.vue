<template>
    <article :class="{
        'event-close': event.deleted_at,
        'hentry post has-post-thumbnail thumb-full-width': true}">
        <div class="post__author author vcard inline-items" v-if="event.user">
            <router-link :to="{ name: 'user.timeline', params: { slug: event.user.slug }}">
                <img :src="event.user.image_thumbnail" :alt="event.user.name">
            </router-link>
            <div class="author-date">
                <router-link :to="{ name: 'user.timeline', params: { slug: event.user.slug }}"
                    class="h6 post__author-name fn">
                    {{ event.user.name }}
                </router-link>
                <div class="post__date">
                    <timeago :since="event.created_at"/>
                </div>
            </div>
            <div class="more">
                <i aria-hidden="true" class="fa fa-calendar-check-o"></i>
            </div>
        </div>
        <list-image v-if="event.media.length" :listImage="event.media" ></list-image>
        <router-link :to="{ name: 'event.index', params: { slugEvent: event.slug }}"
            class="h2 post-title">
            {{ event.title }}
        </router-link>

        <show-text
            :type="false"
            :text="event.description"
            :show_char=300
            :show="$t('events.show_more')"
            :hide="$t('events.show_less')"
            :number_char_show=200>
        </show-text>

        <master-like
            :likes="event.likes"
            :checkLiked="checkLiked"
            :flag="model"
            :type="'like'"
            :modelId="event.id"
            :numberOfComments="event.number_of_comments"
            :numberOfLikes="event.number_of_likes"
            :showMore="true"
            :deleteDate="event.deleted_at"
            :roomLike="`campaign${pageId}`">
        </master-like>

        <div class="control-block-button post-control-button">
            <master-like
                :likes="event.likes"
                :checkLiked="checkLiked"
                :flag="model"
                :type="'like-infor'"
                :modelId="event.id"
                :numberOfComments="event.number_of_comments"
                :numberOfLikes="event.number_of_likes"
                :deleteDate="event.deleted_at"
                :roomLike="`campaign${pageId}`">
            </master-like>
            <plugin-sidebar>
                <template scope="props" slot="sharing-social">
                    <share-social-network
                        :url="{
                            name: 'event.index',
                            params: {
                                slug: event.campaign_id,
                                slugEvent: event.slug
                            }
                        }"
                        :title="event.title"
                        :description="event.description"
                        :isSocialSharing="props.isPopupShare">
                    </share-social-network>
                </template>
            </plugin-sidebar>
        </div>
    </article>
    <comment
        :comments="event.comments"
        :numberOfComments="event.number_of_comments"
        :model-id ="event.id"
        :flag="model"
        :classListComment="''"
        :classFormComment="''"
        :deleteDate="event.deleted_at"
        :canComment="checkComemnt()"
        :roomLike="`campaign${pageId}`">
    </comment>
</template>

<script type="text/javascript">
    import Comment from '../comment/Comment.vue'
    import MasterLike from '../like/MasterLike.vue'
    import ListImage from '../home/ListImage.vue'
    import ShowText from '../libs/ShowText.vue'
    import ShareSocialNetwork from '../libs/ShareSocialNetwork.vue'
    import PluginSidebar from '../libs/PluginSidebar.vue'

    export default {
        props: ['goal'],
    }
</script>

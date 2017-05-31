<template lang = 'html'>
    <div>
    {{ console.log(VueGoogleMaps) }}
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="ui-block">
                        <div class="top-header top-header-favorit">
                            <div class="top-header-thumb">
                                <img src="http://hdwallpaperbackgrounds.net/wp-content/uploads/2016/07/natural-image.jpg" alt="nature">
                                <div class="top-header-author">
                                    <div class="author-thumb">
                                        <img src="img/author-main2.jpg" alt="author">
                                    </div>
                                    <div class="author-content">
                                        <a href="#" class="h3 author-name">Ho Chi Minh</a>
                                        <div class="country">{{ info.description }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="profile-section">
                                <div class="row">
                                    <div class="col-xl-8 offset-xl-2 col-lg-8 offset-lg-2 col-md-12 offset-md-0">
                                        <ul class="profile-menu">
                                            <li>
                                                <a href="#" class="active">Timeline</a>
                                            </li>
                                            <li>
                                                <a href="#">Members</a>
                                            </li>
                                            <li>
                                                <a href="#">Photos</a>
                                            </li>
                                            <li>
                                                <a href="#">Videos</a>
                                            </li>
                                            <li>
                                                <a href="#">Events</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="control-block-button">
                                    <a href="#" class="btn btn-control bg-primary">
                                        <svg class="olymp-star-icon"><use xlink:href="icons/icons.svg#olymp-star-icon"></use></svg>
                                    </a>

                                    <a href="#" class="btn btn-control bg-purple">
                                        <svg class="olymp-chat---messages-icon"><use xlink:href="icons/icons.svg#olymp-chat---messages-icon"></use></svg>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "container">
            <div class = "row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="ui-block">
                        <div class="ui-block-title">
                            <h6 class="title">Page Intro</h6>
                            <a href="#" class="more"><svg class="olymp-three-dots-icon"><use xlink:href="icons/icons.svg#olymp-three-dots-icon"></use></svg></a>
                        </div>
                        <div class="ui-block-content">
                            <ul class="widget w-personal-info item-block">
                                <li>
                                    <span class="text">We are Rock Band from Los Angeles, now based in San Francisco, come and listen to us play!</span>
                                </li>
                                <li>
                                    <span class="title">Created:</span>
                                    <span class="text">{{ formatDate(info.created_at).format('ll') }}</span>
                                </li>
                                <li>
                                    <span class="title">Based in:</span>
                                    <span class="text">San Francisco, California</span>
                                </li>
                                <li>
                                    <span class="title">Contact:</span>
                                    <a href="#" class="text">greengoo_gigs@youmail.com</a>
                                </li>
                                <li>
                                    <span class="title">Website:</span>
                                    <a href="#" class="text">www.ggrock.com</a>
                                </li>
                                <li>
                                    <span class="title">Favourites:</span>
                                    <a href="#" class="text">5630 </a>
                                </li>
                            </ul>

                            <div class="widget w-socials">
                                <h6 class="title">Other Social Networks:</h6>
                                <a href="#" class="social-item bg-facebook">
                                    <i class="fa fa-facebook" aria-hidden="true"></i>
                                    Facebook
                                </a>
                                <a href="#" class="social-item bg-twitter">
                                    <i class="fa fa-twitter" aria-hidden="true"></i>
                                    Twitter
                                </a>
                                <a href="#" class="social-item bg-green">
                                    <i class="fa fa-dribbble" aria-hidden="true"></i>
                                    Spotify
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="ui-block">
                        <div class="ui-block-title">
                            <h6 class="title">Location</h6>
                            <a href="#" class="more"><svg class="olymp-three-dots-icon"><use xlink:href="icons/icons.svg#olymp-three-dots-icon"></use></svg></a>
                        </div>

                        <div class="widget w-contacts">
                            <div class="section">
                                <div id="map">
                                    <gmap-map
                                        :center="center"
                                        :zoom="7"
                                        style="width: 100%; height: 100%"
                                    >
                                        <gmap-marker
                                            :position="center"
                                            @click="center"
                                        ></gmap-marker>
                                </gmap-map>
                                </div>
                            </div>

                            <ul>
                                <li>
                                    <span class="title">Address:</span>
                                    <span class="text">Fake Street 320, San Francisco California, USA.
                                    </span>
                                </li>
                                <li>
                                    <span class="title">Working Hours:</span>
                                    <span class="text">Mon-Fri 9:00am to 6:00pm
                                        Weekends 10:00am to 8:00pm
                                    </span>
                                </li>
                            </ul>
                        </div>

                    </div>

                    <div class="ui-block">
                        <div class="ui-block-title">
                            <h6 class="title">Members</h6>
                            <a href="#" class="more"><svg class="olymp-three-dots-icon"><use xlink:href="icons/icons.svg#olymp-three-dots-icon"></use></svg></a>
                        </div>
                        <div class="ui-block-content">
                            <ul class="widget w-faved-page">
                                <li>
                                    <a href="#">
                                        <img src="img/faved-page1.jpg" alt="user">
                                    </a>
                                </li>

                                <li class="all-users">
                                    <a href="#">+5k</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import moment from 'moment'
    import { get } from '../../helpers/api'
    import * as VueGoogleMaps from 'vue2-google-maps'
    console.log(VueGoogleMaps);
    import Vue from 'vue'
    Vue.use(VueGoogleMaps, {
        load: {
          key: 'AIzaSyBESxStZOWN9aMvTdR3Nov66v6TXxpRZMM'
        }
    });

    export default {
        data()
        {
            return {
                info : '',
                center: { lat: 0, lng: 0 }
            }
        },

        created() {
            this.getData()
        },

        methods : {
            formatDate(date)
            {
                return moment(date);
            },

            getData() {
                get('/campaign/3')
                .then(response => {
                    this.info = response.data
                    this.center = { lat: parseFloat(response.data.latitude), lng: parseFloat(response.data.longitude) }
                })
                .catch(response => alert('error'));
            }
        }
    }
</script>

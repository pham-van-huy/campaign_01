<template>
    <div class="modal fade show wraper-expense" id="edit-widget-pool" style="display: block" v-if="showExpense">
        <div class="modal-dialog ui-block window-popup edit-widget edit-widget-pool">
            <a href="javascript:void(0)" class="close icon-close" data-dismiss="modal" aria-label="Close" @click="close()">
                <svg class="olymp-close-icon"><use xlink:href="/frontend/icons/icons.svg#olymp-close-icon"></use></svg>
            </a>

            <div class="ui-block-title">
                <h6 class="title">Edit My Poll</h6>
            </div>
            <div class="news-feed-form">
                
            </div>
            <div class="tab-content">
                
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    import CreateExpense from './CreateExpense.vue'
    import CreateExpenseBuy from './CreateExpenseBuy.vue'
    import { mapState } from 'vuex'

    export default {
        data: () => ({
            visible: true,
            errorBags: {},
            type: {
                0: [],
                1: []
            }
        }),

        props: [
            'showExpense',
            'goals'
        ],

        computed: {
            ...mapState('auth', [
                'user',
            ])
        },

        updated() {
            this.showTab()
        },

        created() {
            let expense = {
                user_id: this.user.id,
                event_id: this.$route.params.event_id,
                goal_id: null,
                cost: null,
                time: '',
                reason: '',
                type: 0
            }
            let expenseBuy = {
                    expense: {
                        user_id: this.user.id,
                        event_id: this.$route.params.event_id,
                        goal_id: null,
                        cost: null,
                        time: '',
                        reason: '',
                        type: 0
                    },
                    quantity: null,
                    name: '',
                    quality: ''
                }
            this.type[0].push(expense)
            this.type[1].push(expenseBuy)
        },

        methods: {
            close() {
                this.$emit('update:showExpense', false)
            },

            showTab() {
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    var target = $(e.target).attr("href")
                    $('a[data-toggle="tab"]').removeClass('active')
                    $(e.target).addClass('active')
                })
            },

            addExpense() {
                let expense = {
                    user_id: this.user.id,
                    event_id: this.$route.params.event_id,
                    goal_id: null,
                    cost: null,
                    time: '',
                    reason: '',
                    type: 0
                }
                this.type[0].push(expense)
                this.visible = false
            },

            addExpenseBuy() {
                let expenseBuy = {
                    expense: {
                        user_id: this.user.id,
                        event_id: this.$route.params.event_id,
                        goal_id: null,
                        cost: null,
                        time: '',
                        reason: '',
                        type: 0
                    },
                    quantity: null,
                    name: '',
                    quality: ''
                }
                this.type[1].push(expense)
                this.visible = false
            },

            deleteExpense(index) {
                this.type[0].length > 1 && this.type[0].splice(index, 1)

                if (this.type[0].length === 1) {
                    this.visible = true
                }
            },

            updateExpense(newValue) {
                // mix <=> key: type, goal, quality
                // key <=> key of newValue that children emitted
                const [key, mix, instanse] = Object.keys(newValue)
                const index = newValue[key]

                this.errorBags[index] = newValue[instanse]
                this.type[0][index][mix] = newValue[mix]
            },

            updateExpense1(newValue) {
                // mix <=> key: type, goal, quality
                // key <=> key of newValue that children emitted
                const [key, mix, instanse] = Object.keys(newValue)
                const index = newValue[key]

                this.errorBags[index] = newValue[instanse]
                this.type[1][index]['expense'][mix] = newValue[mix]
            },

            updateExpense1Outside(newValue) {
                // mix <=> key: type, goal, quality
                // key <=> key of newValue that children emitted
                const [key, mix, instanse] = Object.keys(newValue)
                const index = newValue[key]

                this.errorBags[index] = newValue[instanse]
                this.type[1][index][mix] = newValue[mix]
            }
        },

        components: {
            CreateExpense,
            CreateExpenseBuy,
        }
    }
</script>

<style lang="scss">
    .wraper-expense {
        background: rgba(0, 0, 0, 0.55);
        .edit-widget-pool {
            min-height: 85%;
            width: 80%;
            margin-top: 90px;
            .ui-block {
                min-height: 380px;
                position: relative;
                .news-feed-form {
                    overflow: visible;
                }
                .add-expense {
                    position: absolute;
                    bottom: 0px;
                    margin-bottom: 5px;
                    width: 100%;
                    text-align: center;
                    i {
                        &:hover {
                            color: #08ddc1;
                            cursor: pointer;
                        }
                    }
                }
            }
            .news-feed-form {
                height: 50px;
                .active {
                    a {
                        background-color: #eceeef !important;
                    }
                }
            }
            .tab-content {
                min-height: 675px;
                .tab-item-1 {
                    min-height: 300px;
                    background-color: #eceeef;
                }
                .tab-item-2 {
                    min-height: 300px;
                    background-color: #eaf8ff;
                }
                .row-add {
                    height: 20px;
                    width: 100%;
                    .expense-1 {
                        background-color: #eceeef;
                    }
                    .expense-2 {
                        background-color: #eaf8ff;
                    }
                }
            }
        }
        .wraper-btn {
            position: absolute;
            bottom: 0px;
            width: 100%;
            margin-left: 0px;
            .btn {
                position: relative;
                width: 50%;
                border-radius: 0px;
                margin-bottom: 0px;
            }
        }
        .multiselect__tags {
            padding: 21px 40px 0 8px;
        }
    }
</style>

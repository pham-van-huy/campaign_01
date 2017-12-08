<template>
    <div class="modal-dialog ui-block window-popup choose-from-my-photo">
        <div class="ui-block-title">
            <h6 class="title">
                {{ $t('events.donation.title') }}
            </h6>
        </div>
        <div class="tab-content">
            <div class="ui-block-content">
                <div v-for="(goal, i) in listGoals">
                    <span>
                        {{ $t('events.donation.receive') }}
                        {{ inforReceived[i] }}/{{ goal.goal }} {{ goal.donation_type.quality.name }}
                        {{ goal.donation_type.name }}
                    </span>
                    <div class="progress">
                        <div
                            class="progress-bar progress-bar-striped active bg-primary"
                            role="progressbar"
                            :style="{ width: (inforReceived[i]/goal.goal > 1 ?
                                goal.goal/inforReceived[i] : inforReceived[i]/goal.goal) * 100 + '%'}"
                            :aria-valuenow="inforReceived[i]/goal.goal*100"
                            aria-valuemin="0"
                            :aria-valuemax="inforReceived[i]/goal.goal*100 > 100 ?
                                Math.round(inforReceived[i]/goal.goal*100) : 100">
                            {{  inforReceived[i]/goal.goal > 1 ? 100 : Math.round(inforReceived[i]/goal.goal*100) }}%
                        </div>
                        <div
                            class="progress-bar progress-bar-striped bg-success active"
                            role="progressbar"
                            :style="{ width: 100 * (1 - goal.goal/inforReceived[i]) + '%' }"
                            v-show="inforReceived[i]/goal.goal > 1">
                            {{ Math.round(inforReceived[i]/goal.goal*100)- 100 }}% over
                        </div>
                    </div>
                </div>
                <div class="form-group label-floating is-select">
                    <label class="control-label">{{ $t('events.donation.select_type') }}</label>
                    <select class="selectpicker form-control dropup" multiple v-model="goal_id" ref="select" data-dropup-auto="false">
                        <option v-for="goal in listGoals" :value="goal.id">{{ goal.donation_type.name }}</option>
                    </select>
                </div>
                <div class="form-group label-floating" :class="{ 'has-danger': errors.has('donate-' + index) }" v-for="(goal, index) in goal_id">
                    <label class="control-label">
                        {{ $t('events.donation.how_much') }} ({{ idNameGoal[goal] }})
                    </label>
                    <input
                        class="form-control"
                        type="text"
                        :name="'donate-' + index"
                        v-model="donate[index]"
                        v-validate="'required|numeric|min_value:1'"
                        data-vv-delay="100"
                        value="">
                    <span v-show="errors.has('donate-' + index)" class="material-input text-danger clearfix">
                        {{ errors.first('donate-' + index) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-secondary btn-lg btn--half-width"
                @click="reset()">
                {{ $t('form.button.reset') }}
            </button>
            <button
                type="button"
                class="btn btn-primary btn-lg half-width"
                @click.prevent="handleDonate"
                :disabled="!!!goal_id.length" >
                {{ $t('events.donation.donate') }}
            </button>
        </div>
        <message :show.sync="showMessage">
            <h2 class="exclamation-header" slot="header">
                <span class="fa fa-check-circle"></span>&nbsp;{{ $t('events.donation.thank_title') }}
            </h2>
            <div class="body-modal" slot="main">
                <p>{{ $t('events.donation.thank_message') }}</p>
                <ul>
                    <li v-for="(goal, index) in goal_id">
                        <p>{{ donate[index] }} {{ idNameGoal[goal] }}</p>
                    </li>
                </ul>
            </div>
        </message>
    </div>
</template>
<script type="text/javascript">
    import { mapState, mapActions } from 'vuex'
    import Message from '../../libs/Modal.vue'
    import noty from '../../../helpers/noty'

    export default {
        props: [
            'listGoals',
            'indexOfGoal',
            'campaignGoalId',
        ],
        data: () => ({
            inforReceived: {},
            goal_id: [],
            idNameGoal: {},
            donate: [],
            showMessage: false
        }),
        methods: {
            ...mapActions('campaign', [
                'update_donate',
            ]),
            totalReceived(goals) {
                goals.forEach((goal, index) => {
                    let received = _.sumBy(goal.donations, function(donation) {
                        return donation.status == window.Laravel.settings.donations.accept ? donation.value : 0
                    })
                    this.inforReceived[index] = received
                    this.idNameGoal[goal.id] = goal.donation_type.quality.name + ' ' + goal.donation_type.name

                })
            },
            handleDonate() {
                this.$validator.validateAll().then((result) => {
                    this.update_donate({
                        dataPost: {
                            campaign_id: this.pageId,
                            goal_id: this.goal_id,
                            value: this.donate,
                            campaignGoalId: this.campaignGoalId
                        },
                        index: this.indexOfGoal
                    }).then(res => {
                        this.showMessage = true
                        $('.selectpicker').val('').selectpicker('refresh')
                        this.goal_id = []
                        this.donate = []
                    }).catch(er => {
                        noty({
                            text: this.$i18n.t('messages.create_fail'),
                            type: 'error',
                            force: false,
                            container: false
                        })
                    })
                })
            },

            reset() {
                this.goal_id = []
                this.donate = []
                $('.selectpicker').val('').selectpicker('refresh')
            }
        },
        created() {
            this.totalReceived(this.listGoals)
        },
        mounted() {
            $('.selectpicker').selectpicker()
        },
        components: {
            Message
        }
    }
</script>

<style lang="scss" scoped>
    .choose-from-my-photo {
        width: 100%;
        border: 0px solid white;
    }
</style>

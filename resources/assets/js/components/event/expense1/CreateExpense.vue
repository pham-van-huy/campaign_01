<template>
    <div>
        <div class="row expense">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4" :class="{ unable : !maxOfExpense }">
                <div class="form-group label-floating" :class="{ 'has-danger': this.error1.has('type') }">
                    <multiselect
                        @select="(value, id) => { type = value }"
                        @remove="(value, id) => { type = ''}"
                        :value="getNameGoalSelected(expense.goal_id)"
                        data-vv-name="type"
                        :class="{ 'has-danger': this.error1.has('type') }"
                        :placeholder="$i18n.t('form.placeholder.choose_type')"
                        :searchable="false"
                        :options="types">
                    </multiselect>
                    <span v-show="error1.has('type')" class="material-input text-danger">
                        {{ error1.first('type') }}
                    </span>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                <div class="form-group label-floating" :class="{ 'has-danger': this.error1.has('cost') }">
                    <label class="control-label">{{ $t('form.label.goal') }}</label>
                    <input
                        type="text"
                        data-vv-name="cost"
                        class="form-control"
                        :value="expense.cost"
                        @input="cost = $event.target.value">
                    <span v-show="error1.has('cost')" class="material-input text-danger">
                        {{ error1.first('cost') }}
                    </span>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                <div class="form-group date-time-picker label-floating">
                    <label class="control-label">{{ $t('form.label.time') }}</label>
                    <date-picker :date.sync="time = expense.time"></date-picker>
                    <span class="input-group-addon">
                        <svg class="olymp-calendar-icon">
                            <use xlink:href="/frontend/icons/icons.svg#olymp-calendar-icon"></use>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-11">
                <div class="form-group label-floating" :class="{ 'has-danger': this.error1.has('reason') }">
                    <label class="control-label">{{ $t('form.label.reason') }}</label>
                    <input
                        type="text"
                        name="reason"
                        class="form-control"
                        :value="expense.reason"
                        @input="reason = $event.target.value">
                    <span v-show="error1.has('reason')" class="material-input text-danger">
                        {{ error1.first('reason') }}
                    </span>
                </div>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 store-icon">
                <i class="fa fa-trash icon-donation"
                    aria-hidden="true"
                    :class="{ visible: visible }"
                    @click="deleteExpense">
                </i>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    import Multiselect from 'vue-multiselect'
    import DatePicker from '../../libs/DatePicker.vue'
    import { mapState } from 'vuex'
    import { Validator } from 'vee-validate';

    export default {
        validator: null,
        data: () => ({
            types: [],
            type: '',
            goalDetail: {},
            goalId: null,
            cost: 0,
            reason: '',
            time: '',
            error1: null,
            dataAll: {}
        }),

        props: {
            visible: {
                type: Boolean,
                required: true
            },
            index: {
                type: Number,
                required: true
            },
            expense: {
                type:Object,
                required: true
            },
            dataObject: {
                type:Object,
                required: true
            }
        },

        computed: {
            ...mapState('event', [
                'event',
                'dataGoals'
            ]),

            maxOfExpense() {
                if (this.goalDetail) {
                    if (this.goalDetail.donations) {
                        this.goalId = this.goalDetail.id
                        let receives = this.goalDetail.donations.filter(donation => donation.status == 1)
                        let totalReceive = receives.reduce((sum, value) => sum + value.value, 0)
                        let totalExpense = this.goalDetail.expenses.reduce((sum, expense) => sum + expense.cost, 0)
                        let result = totalReceive - totalExpense - this.getTotallExpensed(this.type)
                        return result <= 0 ? 0 : result
                    } else {
                        this.goalDetail = ''
                    }
                }

                return 0
            }
        },

        watch: {
            cost: {
                handler: function() {
                    this.$emit('update-row-donation', { index: this.index, cost: this.cost, instanseValidate: this.$validator })
                    this.validator.attach('cost', `required|min:0|max_value:${this.maxOfExpense}`)
                    this.validator.validate('cost', this.cost)
                },
                deep: true
            },

            time: {
                handler: function() {
                    this.$emit('update-row-donation', { index: this.index, time: this.time, instanseValidate: this.$validator })
                },
                deep: true
            },

            reason: {
                handler: function() {
                    this.$emit('update-row-donation', { index: this.index, reason: this.reason, instanseValidate: this.$validator })
                },
                deep: true
            },

            goalDetail: {
                handler: function(goalDetail) {
                    this.$emit('update-row-donation', {
                        index: this.index,
                        goal_id: this.goalDetail? this.goalDetail.id : '',
                        instanseValidate: this.$validator
                    })
                },
                deep: true
            },

            quality: {
                handler: function(newQuality) {
                    this.$emit('update-row-donation', { index: this.index, quality: newQuality, instanseValidate: this.$validator})
                },
                deep: true
            },

            type() {
                this.goalDetail = this.dataGoals.filter(goal => goal.donation_type.name == this.type)[0]
                this.validator.attach('cost', `required|min:0|max_value:${this.maxOfExpense}`)
                this.validator.validate('cost', this.cost)
            },

            dataAll: {
                handler: function() {
                    this.validator.attach('cost', `required|min:0|max_value:${this.maxOfExpense}`)
                    this.validator.validate('cost', this.cost)
                },
                deep: true
            }
        },

        created() {
            this.types = this.dataGoals.map(goal => goal.donation_type.name)
            this.validator = new Validator({
                type: 'required',
                cost: `required|min:0|max_value:${this.maxOfExpense}`,
                reason: 'required'
            })

            this.$set(this, 'error1', this.validator.errorBag)
            this.dataAll = this.dataObject
        },

        methods: {
            deleteExpense() {
                this.$emit('delete-expense')
            },

            getNameGoalSelected(id) {
                let goal = this.dataGoals.filter(goal => goal.id == id)

                if (goal.length) {
                    return goal[0].donation_type.name
                }

                return ''
            },

            getTotallExpensed(type) {
                if (type) {
                    let expenses0 = [...this.dataObject[0].filter(
                        (expense, index) => this.getNameGoalSelected(expense.goal_id) == type && this.index != index
                    )]
                    let result0 =  expenses0.reduce((sum, expense) => sum + Number(expense.cost), 0)
                    let expenses1 = [...this.dataObject[1].filter(expense => this.getNameGoalSelected(expense.expense.goal_id) == type)]
                    let result1 = expenses1.reduce((sum, expense) => sum + Number(expense.expense.cost), 0)

                    return result0 + result1
                }

                return 0
            }
        },

        components: {
            Multiselect,
            DatePicker
        }
    }
</script>

<style lang="scss">
    .expense {
        .quanlity {
            input {
                padding: 10px 5px 16px 5px;
            }
        }
        .store-icon {
            padding-left: 5px;
            margin-top: 18px;
            .icon-donation {
                &:hover {
                    color: #ff5e3a;
                    cursor: pointer;
                }
                font-size: 1.5em !important;
                padding-left: 5px;
            }
            .visible:hover {
                cursor: not-allowed !important;
            }
        }
    }
</style>

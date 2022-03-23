<template>
    <div class="pad">
        <button
            class="btn btn-primary"
            type="button"
            :data-toggle="[modalEnable ? 'modal' : '']"
            data-target=".payment-modal"
        >
            {{ selectedPaymentDetails || "Select Payment" }}
        </button>

        <div class="modal fade payment-modal"  data-backdrop="static" ref="paymentModal">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <form @submit.prevent="submitSelectPaymentForm">
                        <div class="modal-header">
                            <h5 class="mb-0">Payment</h5>
                            <!--<button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                            >
                                <span>&#10005;</span>
                            </button>-->
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Select Payment Method</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" v-model="paymentType" id="payment-cash" value="cash">
                                        <label class="form-check-label" for="payment-cash">Cash</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" v-model="paymentType" id="payment-bank" value="bank">
                                        <label class="form-check-label" for="payment-bank">Bank</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Cash -->
                            <component v-bind:is="currentPaymentMethod" @paymentUpdated="selectedPayment"/>
                            <!-- End of the cash -->
                        </div>
                        <div class="modal-footer">
                            <div class="btn-wrapper">
                                <!--<button
                                    type="button"
                                    class="btn btn-danger mr-2"
                                    data-dismiss="modal"
                                >
                                    Cancel
                                </button>-->
                                <button type="submit" class="btn btn-success">
                                    Select
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CashPaymentComponent from "./CashPaymentComponent";
    import BankPaymentComponent from "./BankPaymentComponent";
    export default {
        name: "SelectPaymentComponent",
        components: {BankPaymentComponent, CashPaymentComponent},
        computed: {
            currentPaymentMethod(){
                if (this.paymentType === 'cash'){
                    return CashPaymentComponent;
                }else if(this.paymentType === 'bank'){
                    return BankPaymentComponent;
                }
            },
            selectedPaymentDetails(){
                if (this.paymentType === 'cash' && this.paymentInfo && this.paymentInfo.title){
                    return this.paymentInfo.title;
                }else if(this.paymentType === 'bank' && this.paymentInfo && this.paymentInfo.bank && this.paymentInfo.bankAccount){
                    return `${this.paymentInfo.bank.name} (${this.paymentInfo.bankAccount.account_name})`
                }else {
                    return null;
                }
            }
        },
        watch: {
            paymentType: {
                handler: function() {
                    this.paymentInfo = null;
                }
            }
        },
        data: () => ({
            modalEnable: true,
            paymentType: "cash",
            paymentInfo: null,
            paymentModal: {},
        }),
        methods: {
            showModal() {
                this.paymentModal.modal("show");
            },
            hideModal() {
                this.paymentModal.modal("hide");
            },
            submitSelectPaymentForm(){

                if (this.paymentType === null){
                    alert('Select Payment Method');
                    return;
                }

                this.hideModal();
            },
            selectedPayment(data){
                this.paymentInfo = data;
            }
        },
        mounted() {
            this.paymentModal = $(this.$refs.paymentModal);
        }
    }
</script>

<style scoped>

</style>

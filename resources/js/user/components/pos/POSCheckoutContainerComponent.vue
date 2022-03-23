    <template>
    <div class="row">
        <div class="col-lg-7">
            <!-- total -->
            <payment-details-component
                ref="payDetails"
            ></payment-details-component>
            <!-- End of the main -->
        </div>
        <div class="col-lg-5">
            <!-- Pads -->
            <div class="pads">
                <div class="card-body p-0 custom-body">
                    <div class="pad flex-fill">
                        <button v-if="isDelivered" class="bg-success" type="button" @click.prevent="isDelivered = false">Delivered</button>
                        <button v-else class="bg-danger" type="button" @click.prevent="isDelivered = true">Pending</button>
                    </div>
                    <salesman-component ref="salesmanComponent"/>
                </div>
                <div class="card-body p-0">
                    <div class="pad">
                        <button type="button" @click.prevent="processPayment">
                            <i class="fa fa-chevron-right"></i
                            ><span>Payment</span>
                        </button>
                    </div>
                    <!--<cash-component ref="cash"></cash-component>-->

                    <!--Select Payment Component-->
                    <select-payment-component ref="payment"/>

                    <customer-component ref="customer"></customer-component>
                    <div class="pad">
                        <button type="button">1</button>
                    </div>
                    <div class="pad">
                        <button type="button">2</button>
                    </div>
                    <div class="pad">
                        <button type="button">3</button>
                    </div>
                    <div class="pad">
                        <button type="button">Discount</button>
                    </div>
                    <div class="pad">
                        <button type="button">4</button>
                    </div>
                    <div class="pad">
                        <button type="button">5</button>
                    </div>
                    <div class="pad">
                        <button type="button">6</button>
                    </div>
                    <div class="pad">
                        <button type="button">Tendered</button>
                    </div>
                    <div class="pad">
                        <button type="button">7</button>
                    </div>
                    <div class="pad">
                        <button type="button">8</button>
                    </div>
                    <div class="pad">
                        <button type="button">9</button>
                    </div>

                    <div class="pad">
                        <button type="button">
                            0
                        </button>
                    </div>
                    <div class="pad">
                        <button type="button">.</button>
                    </div>

                    <div class="pad">
                        <button type="button">C</button>
                    </div>
                    <div class="pad">
                        <button type="button">+/-</button>
                    </div>
                </div>
            </div>
            <!-- End of the pads -->
        </div>
    </div>
</template>

<script>
import PaymentDetailsComponent from "./components/PaymentDetailsComponent";

import CashComponent from "./components/CashComponent";
import CustomerComponent from "./components/CustomerComponent";
import SalesmanComponent from "./components/SalesmanComponent";
import SelectPaymentComponent from "./components/SelectPaymentComponent";
export default {
    name: "POSCheckoutContainerComponent",
    components: {SelectPaymentComponent, SalesmanComponent, CustomerComponent, CashComponent, PaymentDetailsComponent },
    computed: {},
    data(){
      return {
          isDelivered: true
      }
    },
    methods: {
        processPayment() {
            let payment = this.$refs.payment;
            let tendered = this.$refs.payDetails.tendered;
            let purchaseTotalPrice = this.$refs.payDetails.cartPurchaseTotal;
            console.log(this.$refs.payDetails.cartPurchaseTotal)
            let customerId = this.$refs.customer.customer.id;
            let adjustToCustomerBalance = this.$refs.payDetails
                .adjustToCustomerBalance;
            let salesManId = this.$refs.salesmanComponent.salesmanId;

            if (payment.paymentType === null){
                alert("Select payment type")
                return;
            }

            let paymentInfo = null;



            switch (payment.paymentType) {
                case 'cash':
                    if (payment.paymentInfo != null){
                        paymentInfo = {cash_id: payment.paymentInfo.id};
                    }else{
                        alert("Select payment type")
                        return;
                    }
                    break;
                case 'bank':
                    const _paymentInfo = { ...payment.paymentInfo }

                    // bank account id
                    _paymentInfo['bank_account_id'] = _paymentInfo.bankAccount.id;
                    _paymentInfo['cheque_number'] = _paymentInfo.chequeNumber;
                    _paymentInfo['issue_date'] = _paymentInfo.issueDate;

                    // delete unnecessary key
                    delete _paymentInfo['bank'];
                    delete _paymentInfo['bankAccount'];
                    delete _paymentInfo['chequeNumber'];
                    delete _paymentInfo['issueDate'];

                    paymentInfo = _paymentInfo;
                    break;
                default:
                    alert('Select Payment')
                    return;
            }

            if (tendered === null || tendered.length === 0) {
                alert("Enter tendered amount");
                return;
            }

            if (salesManId == null){
                alert('Select salesman')
                return;
            }

            if (!customerId) {
                alert("Select customer");
                return;
            }

            this.$awn.asyncBlock(
                axios.post(baseURL + "user/pos-proceed-payment", {
                    payment_type: payment.paymentType,
                    payment_info: paymentInfo,
                    tendered: tendered,
                    purchaseTotalPrice: purchaseTotalPrice,
                    customer_id: customerId,
                    salesman_id: salesManId,
                    delivered: this.isDelivered,
                    adjust_to_customer_balance: adjustToCustomerBalance
                }),
                response => {
                    window.location.href =
                        baseURL +
                        "user/invoice-generate/" +
                        response.data.invoice_no;
                    console.log(response.data)
                },
                reason => {
                    console.log(reason);
                }
            );
        }
    },
    mounted() {
        // console.log(this.$refs.payment)
    }
};
</script>

<style scoped>
.custom-body{
 display: flex !important;
}

</style>

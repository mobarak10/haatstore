<template>
    <div class="total">
        <ul class="list-group">
            <li class="list-group-item row mx-0">
                <div class="col-6 px-0">Subtotal</div>
                <div class="col-6 px-0 text-right">
                    {{
                        cartSubTotal
                            ? Number.parseFloat(cartSubTotal).toFixed(2)
                            : "0.00"
                    }}
                </div>
            </li>

            <li class="list-group-item row mx-0">
                <div class="col-6 px-0">Purchase Price</div>
                <div class="col-6 px-0 text-right">
<!--                    <input-->
<!--                        type="hidden"-->
<!--                        v-model="purchaseTotalPrice"-->
<!--                        :value="{{cartPurchaseTotal}}"-->
<!--                    >-->
                    {{
                        cartPurchaseTotal
                            ? Number.parseFloat(cartPurchaseTotal).toFixed(2)
                            : Number.parseFloat(0).toFixed(2)
                    }}
                </div>
            </li>
            <li class="list-group-item row mx-0">
                <div class="col-6 px-0">{{ regularVat.name }}</div>
                <div class="col-6 px-0 text-right">
                    {{ Number.parseFloat(regularVat.amount).toFixed(2) }}
                </div>
            </li>
            <li class="list-group-item row mx-0">
                <div class="col-6 px-0">Total</div>
                <div class="col-6 px-0 text-right">
                    {{
                        Number.parseFloat(
                            cartSubTotal + regularVat.amount
                        ).toFixed(2)
                    }}
                </div>
            </li>
            <li class="list-group-item row mx-0">
                <div class="col-7 px-0">
                    Discount
                    <div class="row mx-0 mt-1" style="font-size: 14px;">
                        <div class="custom-control custom-radio mr-3">
                            <input
                                type="radio"
                                id="taka"
                                v-model="discount.type"
                                value="flat"
                                @change="applyDiscount"
                                class="custom-control-input"
                            />
                            <label class="custom-control-label" for="taka"
                                >Flat</label
                            >
                        </div>
                        <div class="custom-control custom-radio">
                            <input
                                type="radio"
                                id="customRadio2"
                                v-model="discount.type"
                                value="percentage"
                                @change="applyDiscount"
                                class="custom-control-input"
                            />
                            <label
                                class="custom-control-label"
                                for="customRadio2"
                                >Percentage</label
                            >
                        </div>
                    </div>
                </div>
                <div class="col-5 px-0 align-self-center">
                    <input
                        type="number"
                        class="form-control text-right"
                        v-model="discount.amount"
                        @blur="applyDiscount"
                        :disabled="!discount.type"
                        step="any"
                        placeholder="Enter discount amount (0.00)"
                    />
                </div>
            </li>
            <li class="list-group-item row mx-0">
                <div class="col-6 px-0">Grand total</div>
                <div class="col-6 px-0 text-right">
                    {{
                        cartTotal
                            ? Number.parseFloat(cartTotal).toFixed(2)
                            : "0.00"
                    }}
                </div>
            </li>
            <li class="list-group-item row mx-0">
                <div class="col-6 px-0">Previous Balance</div>
                <div class="col-6 px-0 text-right">
                    <button
                        type="button"
                        class="btn btn-info btn-sm"
                        @click="openSelectCustomerModal"
                        v-if="customer == null || customer.balance == null"
                    >
                        Select Customer First
                    </button>
                    <span
                        v-else
                        :class="[customer.balance < 0 ? 'text-danger' : '']"
                        >{{ customer.balance }}</span
                    >
                </div>
            </li>
            <li class="list-group-item row mx-0 align-items-center">
                <div class="col-7 px-0">
                    Tendered

                    <div class="row mx-0 mt-1" style="font-size: 14px;">
                        <div class="form-group form-check mb-0">
                            <input
                                v-model="adjustToCustomerBalance"
                                type="checkbox"
                                class="form-check-input"
                                id="adjust-to-customer-balance"
                            />
                            <label
                                class="form-check-label"
                                for="adjust-to-customer-balance"
                                >Adjust to customer balance</label
                            >
                        </div>
                    </div>
                </div>
                <div class="col-5 px-0">
                    <input
                        type="number"
                        class="form-control text-right"
                        step="any"
                        v-model="tendered"
                        @blur="updateDueChange"
                        @change="updateDueChange"
                        placeholder="0.00"
                    />
                </div>
            </li>
            <li
                class="list-group-item row mx-0"
                v-if="adjustToCustomerBalance || cartTotal > tendered"
            >
                <div class="col-6 px-0">Current Balance</div>
                <div class="col-6 px-0 text-right">
                    <button
                        type="button"
                        class="btn btn-info btn-sm"
                        @click="openSelectCustomerModal"
                        v-if="currentBalance == null"
                    >
                        Select Customer First
                    </button>
                    <span
                        v-else
                        :class="[currentBalance < 0 ? 'text-danger' : '']"
                        >{{
                            Number.parseFloat(currentBalance).toFixed(2)
                        }}</span
                    >
                </div>
            </li>
            <li
                class="list-group-item row mx-0"
                v-if="
                    (change || cartTotal == tendered) &&
                        adjustToCustomerBalance == false
                "
            >
                <div class="col-6 px-0">Change</div>
                <div class="col-6 px-0 text-right">
                    {{ Number.parseFloat(change).toFixed(2) }}
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
    name: "PaymentDetailsComponent",
    computed: {
        ...mapState(["cartSubTotal", "cartPurchaseTotal"]),
        currentBalance() {
            if (this.customer != null) {
                let balance = this.customer.balance;

                if (this.customer.discount != null){
                    this.discount.type = 'percentage'
                    this.discount.amount = this.customer.discount
                    this.applyDiscount()
                }

                if (
                    this.adjustToCustomerBalance ||
                    this.cartTotal > this.tendered
                ) {
                    balance -= this.cartTotal;
                    balance += Number.parseFloat(this.tendered || 0);
                }

                return balance;
            }

            return null;
        }
    },
    data() {
        return {
            cartTotal: null,
            purchaseTotalPrice: this.purchaseTotal(),
            regularVat: {},
            discount: {
                type: "flat",
                amount: 0.0
            },
            tendered: null,
            due: 0,
            change: 0,
            customer: null,
            adjustToCustomerBalance: false
        };
    },
    methods: {
        ...mapActions(["actionRefreshCartSubTotal", "actionRefreshCartPurchaseTotal"]),
        purchaseTotal(){
            this.actionRefreshCartPurchaseTotal()
        },
        refreshCartTotal() {
            this.getAppliedDiscount().then(() => {
                axios
                    .post(baseURL + "user/pos/get-total")
                    .then(response => {
                        this.actionRefreshCartSubTotal() // update cart sub total
                            .then(() => {
                                this.getAmountOfVatInSubtotal(); //update the vat amount in subtotal
                                this.cartTotal = response.data;
                                this.updateDueChange();
                            })
                            .catch(reason => console.log(reason));
                    })
                    .catch(reason => console.log(reason.data));
            });
        },
        getAmountOfVatInSubtotal() {
            axios
                .post(
                    baseURL +
                        "user/pos/get-calculated-amount-of-subtotal-for-regular-vat"
                )
                .then(response => (this.regularVat = response.data))
                .catch(reason => console.log(reason));
        },
        getAppliedDiscount() {
            return new Promise((resolve, reject) => {
                axios
                    .post(baseURL + "user/pos/get-applied-discount")
                    .then(response => {
                        this.discount.type = response.data.type;
                        this.discount.amount = response.data.amount;
                        resolve();
                    });
            });
        },
        applyDiscount() {
            this.$awn.asyncBlock(
                axios.post(baseURL + "user/pos/apply-discount", {
                    discount: {
                        type: this.discount.type,
                        amount: this.discount.amount || 0
                    }
                }),
                response => {
                    this.cartTotal = response.data;
                    this.updateDueChange();
                },
                reason => console.log(reason)
            );
        },
        updateDueChange() {
            let value = this.tendered - this.cartTotal;

            if (value > 0) {
                this.change = value;
                this.due = null;
            } else {
                this.change = 0;
                this.due = Math.abs(value);
            }
        },
        openSelectCustomerModal() {
            eventBus.$emit("openSelectCustomerModal");
        }
    },
    mounted() {
        this.refreshCartTotal();
        this.purchaseTotal()
        eventBus.$on(
            "customerSelectedFromModal",
            customer => (this.customer = customer)
        );

        // eventBus.$on(
        //     "customerSelectedFromModal",
        // )
    }
};
</script>

<style scoped></style>

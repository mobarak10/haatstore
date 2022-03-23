<template>
    <div class="pos">
        <div class="row">
            <div class="col-12">
                <div class="container-fluid">
                    <form action="" @submit.prevent="sale">
                        <!-- Top Form Start-->
                        <div class="row">
                            <div class="ml-3 col-md-2">
                                <div class="row">
                                    <strong>Sale Type: </strong>
                                    <div class="form-check ml-2">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            v-model="saleType"
                                            id="retail"
                                            value="retail">
                                        <label class="form-check-label" for="retail">
                                            Retail
                                        </label>
                                    </div>

                                    <div class="form-check ml-2">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            v-model="saleType"
                                            id="wholesale"
                                            value="wholesale">
                                        <label class="form-check-label" for="wholesale">
                                            Wholesale
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input
                                    type="text"
                                    autofocus
                                    @keydown.enter.prevent="getProductByBarcode(barcode)"
                                    placeholder="Enter barcode number"
                                    class="form-control form-control-sm"
                                    v-model="barcode"
                                />
                            </div>

                            <!-- Total Amount Start -->
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <label
                                        class="text-right col-4 col-form-label"
                                        for="total-amount">
                                        Subtotal
                                    </label>
                                    <input
                                        type="number"
                                        style="color: white !important; font-size: 20px !important;"
                                        class="form-control bg-success"
                                        id="total-amount"
                                        autocomplete="off"
                                        :value="Number.parseFloat(subtotal).toFixed(2)"
                                        disabled
                                    />
                                </div>
                            </div>
                            <!-- Total Amount End -->

                            <!-- Total Amount Start -->
                            <div class="col-md-2">
                                <div class="input-group input-group-sm">
                                    <label
                                        class="text-right col-4 col-form-label"
                                        for="total-amount">
                                        Total Item
                                    </label>
                                    <input
                                        type="number"
                                        style="color: white !important; font-size: 20px !important;"
                                        class="form-control bg-info"
                                        id="total-amount"
                                        autocomplete="off"
                                        :value="selectedProducts.reduce((total, product) => {
                                            return (product.total_quantity_for_show) + total
                                        }, 0)"
                                        disabled
                                    />
                                </div>
                            </div>
                            <!-- Total Amount End -->
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <input
                                    type="date"
                                    disabled
                                    class="form-control form-control-sm"
                                    v-model="form.date"
                                />
                            </div>

                            <div class="col-md-2">
                                <div class="row">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            v-model="customerType"
                                            value="new"
                                            @change="getRetailDetails"
                                            id="new">
                                        <label class="form-check-label" for="new">
                                            New Customer
                                        </label>
                                    </div>

                                    <div class="form-check ml-2">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            v-model="customerType"
                                            @change="getCreditDetails"
                                            value="old"
                                            id="old">
                                        <label class="form-check-label" for="old">
                                            Old Customer
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select
                                    class="form-control form-control-sm"
                                    v-model="warehouse_id"
                                >
                                    <option :value="null" disabled
                                    >Select warehouse</option
                                    >
                                    <option
                                        v-for="(warehouse,
                                        warehouseIndex) in warehouses"
                                        :value="warehouse.id"
                                        :key="warehouseIndex"
                                        v-text="warehouse.title"
                                    />
                                </select>
                            </div>
                            <div class="col-md-4">
                                <v-select
                                    :options="products"
                                    label="name"
                                    v-model="selectedProduct"
                                    placeholder="Select product"
                                    @input="onProductSelected"
                                    class="bg-white"
                                ></v-select>
                            </div>
                        </div>
                        <!-- Top Form End -->

                        <!-- Table Form Start -->
                        <div class="row">
                            <div class="my-2 col-12 border-top border-bottom">
                                <table
                                    class="table my-2 table-striped table-bordered table-sm"
                                >
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th style="min-width: 180px">Product Name</th>
                                        <th>Barcode</th>
                                        <th class="text-right">Stock</th>
                                        <th style="width: 350px">Quantity</th>
                                        <th>Sale Price</th>
                                        <th class="text-center">P Price</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center print-none">
                                            Action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-if="selectedProducts.length === 0">
                                        <td
                                            colspan="20"
                                            class="text-center"
                                        >
                                            No product selected
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="(product,
                                            productIndex) in selectedProducts"
                                        :key="productIndex"
                                    >
                                        <td>{{ productIndex + 1 }}.</td>
                                        <td>{{ product.name }}</td>
                                        <td>{{ product.barcode }}</td>

                                        <td class="text-right">
                                            {{ product.stock.quantity }}
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    aria-describedby="quantityError"
                                                    v-for="(label,
                                                    labelIndex) in product
                                                    .product_unit_labels"
                                                    :placeholder="label"
                                                    :key="labelIndex"
                                                    :value="product.quantity[labelIndex]"
                                                    @blur="
                                                        addQuantity(
                                                            $event,
                                                            product.id,
                                                            labelIndex
                                                        )
                                                    "
                                                    @change="
                                                        addQuantity(
                                                            $event,
                                                            product.id,
                                                            labelIndex
                                                        )
                                                    "
                                                    @keyup="
                                                        addQuantity(
                                                            $event,
                                                            product.id,
                                                            labelIndex
                                                        )
                                                    "
                                                    min="0"
                                                    class="form-control form-control-sm"
                                                />
                                            </div>
                                            <div v-if="product.error" id="quantityError" class="form-text text-danger">
                                                {{ product.error }}
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Sale Price -->
                                            <input
                                                v-if="saleType === 'retail'"
                                                type="number"
                                                step="any"
                                                class="form-control form-control-sm"
                                                v-model.trim="
                                                        product.retail_price
                                                    "
                                            />

                                            <input
                                                v-else
                                                type="number"
                                                step="any"
                                                class="form-control form-control-sm"
                                                v-model.trim="
                                                        product.wholesale_price
                                                    "
                                            />
                                        </td>

                                        <td class="text-center">
                                            <span v-if="product.showPurchasePrice">{{ product.stock.avarage_purchase_price, }}</span>
                                            <a
                                                href="#"
                                                style="font-size: 20px"
                                                @click.prevent="showPurchasePrice(product.id)">
                                                <i v-if="product.showPurchasePrice" class="fa fa-eye-slash" aria-hidden="true"></i>
                                                <i v-else class="fa fa-eye"></i>
                                            </a>
                                        </td>

                                        <td class="text-right">
                                            <div v-if="saleType === 'retail'">
                                                {{
                                                    Number.parseFloat(
                                                        (product.total_price =
                                                            parseFloat(product.total_quantity)
                                                            * parseFloat(product.retail_price))).toFixed(2)
                                                }}
                                            </div>
                                            <div v-else>
                                                {{
                                                    Number.parseFloat(
                                                        (product.total_price =
                                                            parseFloat(product.total_quantity)
                                                            * parseFloat(product.wholesale_price))).toFixed(2)
                                                }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <button
                                                class="btn btn-sm btn-danger"
                                                type="button"
                                                @click.prevent="
                                                        selectedProducts.splice(
                                                            productIndex,
                                                            1
                                                        )
                                                    "
                                            >
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Table Form End -->

                        <!-- Bottom Section Start -->
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <!-- Bottom Left Section Start -->
                                    <div class="col-md-6">
                                        <!-- Client Select Start -->
                                        <div v-if="customerType === 'new'">
                                            <div class="form-group row">
                                                <label
                                                    class="col-md-3 col-form-label"
                                                    for="client-name"
                                                >Customer Name</label
                                                >
                                                <div class="col-md-9">
                                                    <input
                                                        type="text"
                                                        ref="inputCustomer"
                                                        class="form-control form-control-sm"
                                                        id="client-name"
                                                        v-model="form.customer.name"
                                                    />
                                                </div>
                                            </div>
                                            <!-- Cient Select End -->
                                            <!-- Mobile Number Start -->
                                            <div class="form-group row required">
                                                <label
                                                    class="col-md-3 col-form-label"
                                                    for="client-phone"
                                                >Phone</label
                                                >
                                                <div class="col-md-9">
                                                    <input
                                                        type="text"
                                                        required
                                                        class="form-control form-control-sm"
                                                        id="client-phone"
                                                        v-model="
                                                        form.customer.phone"/>
                                                    <small
                                                        class="d-block text-danger"
                                                        v-if="errors"
                                                    >
                                                        <strong>Error:</strong>
                                                        {{ errors }}
                                                    </small>
                                                </div>
                                            </div>
                                            <!-- Mobile Number End -->
                                            <!-- Address Start -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-md-3 col-form-label"
                                                    for="client-address"
                                                >Address</label
                                                >
                                                <div class="col-md-9">
                                                <textarea
                                                    class="form-control form-control-sm"
                                                    id="client-address"
                                                    v-model="
                                                        form.customer.address
                                                    "
                                                ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Address End -->

                                        <div v-else>
                                            <div class="form-group row required">
                                                <label
                                                    class="col-md-3 col-form-label"
                                                    for="client-name"
                                                >Customer Name</label
                                                >
                                                <div class="col-md-9">
                                                    <v-select
                                                        :options="customers"
                                                        v-model="customerId"
                                                        ref="selectCustomer"
                                                        :reduce="customer => customer.id"
                                                        placeholder="Select Client"
                                                        @input="getCustomerDetails(customerId)"
                                                        label="custom_name">
                                                        <template slot="option" slot-scope="option">
                                                            <span class="fa" :class="option.icon"></span>
                                                            {{ option.name +' '+ (option.phone || '') }}
                                                        </template>
                                                    </v-select>
                                                </div>
                                            </div>
                                            <!-- Client Select End -->
                                            <!-- Mobile Number Start -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-md-3 col-form-label"
                                                    for="client-phone"
                                                >Phone</label
                                                >
                                                <div class="col-md-9">
                                                    <input
                                                        type="text"
                                                        disabled
                                                        class="form-control"
                                                        v-model="customerMobile"
                                                        id="model">
                                                </div>
                                            </div>

                                            <!-- Mobile Number End -->
                                            <!-- Address Start -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-md-3 col-form-label"
                                                    for="client-address"
                                                >Address</label
                                                >
                                                <div class="col-md-9">
                                                    <textarea
                                                        id="address"
                                                        disabled
                                                        v-model="customerAddress"
                                                        rows="3"
                                                        class="form-control">
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Remark Start -->
                                        <div class="form-group row">
                                            <label
                                                class="col-md-3 col-form-label"
                                                for="remark"
                                            >Remark</label
                                            >
                                            <div class="col-md-9">
                                                <textarea
                                                    class="form-control form-control-sm"
                                                    id="remark"
                                                    v-model="form.comment"
                                                    rows="8"
                                                    cols="8"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <!-- Remark End -->
                                    </div>
                                    <!-- Bottom Left Section End -->

                                    <!-- Bottom Right Section Start -->
                                    <div class="col-md-6">
                                        <!-- Total Amount Start -->
                                        <div class="form-group row">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="total-amount"
                                            >Total Amount</label
                                            >
                                            <div class="col-9">
                                                <div
                                                    class="input-group input-group-sm"
                                                >
                                                    <input
                                                        type="number"
                                                        style="color: white !important; font-size: 20px !important;"
                                                        class="form-control bg-success"
                                                        id="total-amount"
                                                        autocomplete="off"
                                                        :value="Number.parseFloat(subtotal).toFixed(2)"
                                                        disabled
                                                    />
                                                    <div
                                                        class="input-group-append"
                                                    >
                                                        <span
                                                            class="input-group-text"
                                                        >৳</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Total Amount End -->

                                        <div v-if="customerType === 'old'" class="form-group row">
                                            <label for="previous_balance" class="text-right col-3 col-form-label">Previous Balance</label>
                                            <div class="col-5">
                                                <input type="number" disabled class="form-control form-control-sm" :value="Math.abs(customerBalance)" id="previous_balance">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" disabled :value="(customerBalance <= 0) ? 'Receivable' : 'Payable'" class="form-control form-control-sm">
                                            </div>
                                        </div>

                                        <!-- Total Discount Start -->
                                        <div class="form-group row">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="total-discount"
                                            >Total Discount</label
                                            >
                                            <div class="col-9">
                                                <div
                                                    class="input-group input-group-sm"
                                                >
                                                    <input
                                                        type="number"
                                                        ref="discount"
                                                        step="any"
                                                        class="form-control form-control-sm"
                                                        id="total-discount"
                                                        v-model="
                                                            form.payment
                                                                .discount
                                                        "
                                                        autocomplete="off"
                                                    />
                                                    <div
                                                        class="input-group-append"
                                                    >
                                                        <span
                                                            class="input-group-text"
                                                        >৳</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Total Discount End -->

                                        <!-- Grand Total Start -->
                                        <div class="form-group row">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="grand-total"
                                            >Grand Total</label
                                            >
                                            <div class="col-9">
                                                <div
                                                    class="input-group input-group-sm"
                                                >
                                                    <input
                                                        type="number"
                                                        style="color: white !important; font-size: 20px !important;"
                                                        class="form-control bg-info"
                                                        id="grand-total"
                                                        autocomplete="off"
                                                        disabled
                                                        :value="
                                                            Number.parseFloat(
                                                                (parseFloat(
                                                                subtotal)
                                                            + parseFloat((customerBalance < 0) ? Math.abs(customerBalance) : (-1 * customerBalance)))
                                                            -
                                                                parseFloat(
                                                                    form.payment
                                                                        .discount ||
                                                                        0
                                                                )
                                                            ).toFixed(2)
                                                        "
                                                    />
                                                    <div
                                                        class="input-group-append"
                                                    >
                                                        <span
                                                            class="input-group-text"
                                                        >৳</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Grand Total End -->

                                        <!-- Paid Start -->
                                        <div class="form-group row required">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="paid"
                                            >Paid</label
                                            >
                                            <div class="col-6">
                                                <div
                                                    class="input-group input-group-sm"
                                                >
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="paid"
                                                        min="0"
                                                        required
                                                        autocomplete="off"
                                                        v-model="
                                                            form.payment.paid
                                                        "
                                                    />
                                                    <div
                                                        class="input-group-append"
                                                    >
                                                        <span
                                                            class="input-group-text"
                                                        >৳</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="input-group-append">
                                                    <select
                                                        class="form-control form-control-sm"
                                                        v-model="
                                                            form.payment.method
                                                        "
                                                    >
                                                        <option value="cash"
                                                        >Cash</option
                                                        >
                                                        <option value="bank"
                                                        >Bank</option
                                                        >
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Paid End -->

                                        <!-- Cash Extra Field Start -->
                                        <div
                                            v-if="form.payment.method == 'cash'"
                                        >
                                            <div class="form-group row">
                                                <label
                                                    class="text-right col-3 col-form-label"
                                                    for="bank-name"
                                                >Cash Name</label
                                                >
                                                <div class="col-9">
                                                    <select
                                                        class="form-control form-control-sm"
                                                        v-model="
                                                            paymentInfo.cash_id
                                                        "
                                                    >
                                                        <option
                                                            v-for="(cash,
                                                            cashIndex) in cashes"
                                                            :key="cashIndex"
                                                            :value="cash.id"
                                                        >{{
                                                                cash.title
                                                            }}</option
                                                        >
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Cash Extra Field End -->

                                        <!-- Bank Extra Field Start -->
                                        <div
                                            v-if="form.payment.method == 'bank'"
                                        >
                                            <div class="form-group row">
                                                <label
                                                    class="text-right col-3 col-form-label"
                                                    for="bank-name"
                                                >Bank Name</label
                                                >
                                                <div class="col-9">
                                                    <select
                                                        class="form-control form-control-sm"
                                                        v-model="
                                                            paymentInfo.bank_account_id
                                                        "
                                                    >
                                                        <option
                                                            v-for="(bankAccount,
                                                            bankIndex) in bankAccounts"
                                                            :value="
                                                                bankAccount.id
                                                            "
                                                            :key="bankIndex"
                                                        >{{
                                                                `${bankAccount.bank.name} (${bankAccount.account_name})`
                                                            }}</option
                                                        >
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <label
                                                    class="text-right col-3 col-form-label"
                                                    for="cheque-number"
                                                >Check No</label
                                                >
                                                <div class="col-9">
                                                    <input
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        id="cheque-number"
                                                        v-model="
                                                            paymentInfo.cheque_number
                                                        "
                                                        placeholder="Enter check no"
                                                        autocomplete="off"
                                                    />
                                                </div>
                                            </div>

                                            <div class="row">
                                                <label
                                                    class="text-right col-3 col-form-label"
                                                    for="issue-date"
                                                >Issue Date</label
                                                >
                                                <div class="col-9">
                                                    <input
                                                        type="date"
                                                        class="form-control form-control-sm"
                                                        id="issue-date"
                                                        v-model="
                                                            paymentInfo.issue_date
                                                        "
                                                        autocomplete="off"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Bank Extra Field End -->

                                        <!-- Amount Start -->
                                        <div class="form-group row">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="due-or-change"
                                            >{{
                                                    dueOrChange >= 0
                                                        ? "Due"
                                                        : "Return"
                                                }}</label
                                            >
                                            <div class="col-9">
                                                <div
                                                    class="input-group input-group-sm"
                                                >
                                                    <input
                                                        type="number"
                                                        style="color: white !important; font-size: 20px !important;"
                                                        class="form-control text-bold bg-danger"
                                                        id="due-or-change"
                                                        autocomplete="off"
                                                        disabled
                                                        :value="
                                                            Number.parseFloat(
                                                                Math.abs(
                                                                dueOrChange
                                                            )
                                                            ).toFixed(2)
                                                        "
                                                    />
                                                    <div
                                                        class="input-group-append"
                                                    >
                                                        <span
                                                            class="input-group-text"
                                                        >৳</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Amount End -->
                                    </div>
                                    <!-- Bottom Right Section End -->
                                </div>

                                <div class="pt-2 row border-top">
                                    <div class="col-md-6">
                                        <!-- Send SMS Start -->
                                        <div class="form-group row d-none">
                                            <div class="col-10 offset-2">
                                                <div
                                                    class="custom-control custom-checkbox"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="client-send-sms"
                                                    />
                                                    <label
                                                        class="custom-control-label"
                                                        for="client-send-sms"
                                                    >Send SMS</label
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Send SMS End -->
                                    </div>
                                    <div class="text-right col-md-6">
                                        <button class="btn btn-sm btn-primary">
                                            Sale
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Bottom Section End -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "RetailSaleComponent",
    props: {
        warehouses: Array,
        cashes: Array,
        customers: Array,
        bankAccounts: Array,
    },
    watch: {
        selectedProducts: {
            deep: true,
            handler: function(value) {
                this.subtotal = value.reduce((total, item) => {
                    return parseFloat(item.total_price) + parseFloat(total);
                }, 0);
            }
        }
    },
    computed: {
        products: function() {
            try {
                let allProducts = this.warehouses.find(
                    warehouse => warehouse.id === this.warehouse_id
                ).products || []
                return (
                    allProducts.sort(function(a, b){
                        if(a.name < b.name) { return -1; }
                        if(a.name > b.name) { return 1; }
                        return 0;
                    })
                );
            } catch (error) {
                return [];
            }
        },
        dueOrChange() {
            return (
                (parseFloat(this.subtotal) + parseFloat((this.customerBalance <=0) ? Math.abs(this.customerBalance) : (-1 * this.customerBalance)))
                -
                (parseFloat(this.form.payment.discount) || 0)
                -
                (parseFloat(this.form.payment.paid) || 0)
            );
        }
    },
    data() {
        return {
            form: {
                delivered: 1,
                date: new Date().toISOString().slice(0, 10),
                customer: {
                    name: null,
                    phone: null,
                    address: null
                },
                payment: {
                    discount: 0,
                    paid: null,
                    method: "cash"
                },
                comment: null,
            },
            allProducts: [],
            errors: null,
            barcode: null,
            customPrice: 0,
            customerType: 'old',
            totalPurchasePrice: 0,
            saleType: 'retail',
            warehouse_id: null,
            selectedProducts: [],
            selectedProduct: null,
            customerId: null,
            customerMobile: null,
            customerAddress: null,
            subtotal: 0,
            paymentInfo: {
                cash_id: null,
                phone_number: null,
                bank_account_id: null,
                cheque_number: null,
                issue_date: null
            },
            customerBalance: 0,
        };
    },

    mounted() {
        this.customers.map(function (customer) {
            return customer.custom_name = customer.name + ' ' + (customer.phone || '')
        })
        this.warehouse_id = this.warehouses[0].id
        this.customerId = this.customers[0].id
        this.customerBalance = this.customers[0].balance
        this.allProducts = this.warehouses[0].products
        this.allProducts.map(function (product) {
            return product.custom_name = product.name + ' ' + (product.barcode || '')
        })

        this.init();
    },

    methods: {
        onProductSelected(value) {

            const index = this.selectedProducts.findIndex(
                product => product.id === value.id
            );

            if (index === -1) {
                // default quantity
                this.selectedProducts.push(value);
                // let _product = this.allProducts.find(pro => warehouse.id === this.warehouse_id).products
                let totalQuantity = 0;
                if (value.unit.unit_length > 1){
                    let unitRelation = value.unit.relation.split('/')
                    let unitValue = unitRelation.reduce( (a, b) => a * b )
                    totalQuantity = (1 * unitValue)
                }else{
                    totalQuantity = 1;
                }

                const newProduct = {
                    ...value,
                    quantity: {
                        0: 1
                    },
                    error: '',
                    total_quantity: totalQuantity,
                    firstValue: totalQuantity,
                    secondValue: 0,
                    total_quantity_for_show: (value.unit.unit_length > 1 ? 1 : totalQuantity),
                    total_price: 0,
                    total_vat: 0,
                    created_at: Date.now(),
                    showPurchasePrice: false,
                    purchasePrice: value.stock.avarage_purchase_price,
                    total_discount: 0,
                };

                this.selectedProducts.splice(
                    this.selectedProducts.length - 1,
                    1,
                    newProduct
                );

                this.selectedProduct = null;
            }

            this.selectedProducts = this.selectedProducts.sort((a, b) => parseFloat(b.created_at) - parseFloat(a.created_at));
        },

        addQuantity(event, productId, order) {
            let product = this.selectedProducts.find(product => product.id === productId)
            this.$set(product.quantity, order, parseFloat(event.target.value));

            if (product.unit.unit_length > 1){
                let unitRelation = product.unit.relation.split('/')
                let value = unitRelation.reduce( (a, b) => a * b )
                let totalValue = 0
                if (order === 0) {
                    product.firstValue = (event.target.value * value)
                }else{
                    product.secondValue = event.target.value
                }
                totalValue = (parseFloat((product.firstValue ? product.firstValue : 0)) + parseFloat((product.secondValue ? product.secondValue : 0)))
                product.total_quantity = totalValue
                product.total_quantity_for_show = 1
            }else{
                product.total_quantity = parseFloat(event.target.value);
                product.total_quantity_for_show = parseFloat(event.target.value);
            }

            product.purchasePrice = (product.stock.avarage_purchase_price)
        },

        showPurchasePrice(productId) {
            const index = this.selectedProducts.findIndex(
                product => product.id === productId
            );

            const product = this.selectedProducts.find(
                product => product.id === productId
            );

            (product.showPurchasePrice ? product.showPurchasePrice = false : product.showPurchasePrice = true)

            this.selectedProducts.splice(index, 1, product)
        },

         getProductByBarcode(_barcode) {
            if(_barcode === null){
                this.$refs.discount.focus()
            }else{
                this.warehouse_id = this.warehouses[0].id
                const barcode = _barcode.toString()
                const productBarcodeDetails = {
                    productBarCode: null,
                    quantity: {},
                };

                if (barcode.length === 17) {
                    productBarcodeDetails.productBarCode = barcode.slice(1, 6)

                    const kgs = barcode.slice(12, 14)
                    const grams = barcode.slice(14, 17)
                    productBarcodeDetails.quantity = {
                        0: parseFloat(kgs),
                        1: parseFloat(grams)
                    }
                } else if (barcode.length === 13) {
                    let product = this.allProducts.find(product => product.barcode === barcode)
                    if (product){
                        productBarcodeDetails.productBarCode = barcode
                        productBarcodeDetails.quantity = {
                            0: 1,
                        }
                    }else {
                        productBarcodeDetails.productBarCode = barcode.slice(2, 7)
                        const pcs = barcode.slice(7, 12)
                        productBarcodeDetails.quantity = {
                            0: pcs,
                        }
                    }
                } else if (barcode.length === 12 || barcode.length === 6 || barcode.length === 8) {
                    productBarcodeDetails.productBarCode = barcode
                    productBarcodeDetails.quantity = {
                        0: 1,
                    }
                } else {
                    productBarcodeDetails.productBarCode = _barcode
                    productBarcodeDetails.quantity = {
                        0: 1,
                    }
                }

                let _product = this.allProducts.find(product => product.barcode === productBarcodeDetails.productBarCode)

                if(_product) {
                    const index = this.selectedProducts.findIndex(
                        product => product.id === _product.id
                    );
                    if (index === -1) {
                        // default quantity
                        this.selectedProducts.push(_product);

                        let unitRelation = _product.unit.relation.split('/')
                        let value = unitRelation.reduce( (a, b) => a * b )
                        let totalValue = 0

                        _product.firstValue = parseFloat(productBarcodeDetails.quantity[0]) * value

                        _product.secondValue = parseFloat(productBarcodeDetails.quantity[1])

                        totalValue = (parseFloat((_product.firstValue ? _product.firstValue : 0)) + parseFloat((_product.secondValue ? _product.secondValue : 0)))
                        _product.total_quantity = parseFloat(totalValue)

                        _product.purchasePrice = _product.stock.avarage_purchase_price

                        const newProduct = {
                            ..._product,
                            quantity: productBarcodeDetails.quantity,
                            total_vat: 0,
                            total_price: 0,
                            total_quantity_for_show: (_product.unit.unit_length > 1 ? 1 : totalValue),
                            created_at: Date.now(),
                            error: '',
                            showPurchasePrice: false,
                            total_discount: 0,
                        };

                        this.selectedProducts.splice(
                            this.selectedProducts.length - 1,
                            1,
                            newProduct
                        );

                        this.selectedProducts = this.selectedProducts.sort((a, b) => parseFloat(b.created_at) - parseFloat(a.created_at));
                        this.barcode = null;
                    }else{
                        const previousProduct = {...this.selectedProducts[index]};
                        previousProduct.total_quantity_for_show
                        previousProduct.total_quantity = (previousProduct.total_quantity + 1)
                        previousProduct.quantity = {
                            0: previousProduct.quantity[0] + 1
                        }
                        previousProduct.total_quantity_for_show = (previousProduct.unit.unit_length > 1 ? 1 : previousProduct.total_quantity),
                        this.selectedProducts.splice(index, 1, previousProduct)
                        this.barcode = null;
                    }

                    _product.purchasePrice = (_product.stock.avarage_purchase_price)
                }else{
                    this.$awn.alert("Opps! Products not found!");
                    this.barcode = null;
                }

            }

        },


        getCreditDetails() {
            this.form.customer.name = null;
            this.form.customer.address = null;
            this.form.customer.phone = null;
        },

        getRetailDetails() {
            this.customerId = null;
            this.customerMobile = null;
            this.customerAddress = null;
            this.customerBalance = 0;
        },

        getCustomerDetails(id) {
            let customer = this.customers.find(customer => customer.id === id)
            this.customerBalance = customer.balance
            this.customerMobile = customer.phone
            this.customerAddress = customer.address
        },

        sale() {
            if (!this.customerId && !this.form.customer.phone){
                alert('Please enter customer phone number or select customer')
                return
            }
            const form = {
                ...this.form,
                products: []
            };

            // for products
            let quantityError = false;
            this.selectedProducts.forEach(product => {
                if(product.stock.quantity < product.total_quantity) {
                    quantityError = true
                    product.error = "Insufficient quantity"
                }
                form.products.push({
                    id: product.id,
                    quantities: product.quantity,
                    total_quantity: product.total_quantity,
                    discount: product.total_discount || 0,
                    vat: product.total_vat || 0,
                    price: (this.saleType === 'retail') ? product.retail_price : product.wholesale_price,
                    warehouse_id: this.warehouse_id,
                    purchase_price: product.purchasePrice,
                    line_total: product.total_price
                });
            });

            if(quantityError) {
                form.products = []
                return
            }

            // for payments details
            switch (form.payment.method) {
                case "cash":
                    form.payment.sale_payments = {
                        cash_id: this.paymentInfo.cash_id
                    };
                    break;
                case "bank":
                    form.payment.sale_payments = {
                        bank_account_id: this.paymentInfo.bank_account_id,
                        cheque_number: this.paymentInfo.cheque_number,
                        issue_date: this.paymentInfo.issue_date
                    };
                    break;
                case "bkash":
                    form.payment.sale_payments = {
                        phone_number: this.paymentInfo.phone_number
                    };
                    break;

                default:
                    break;
            }
            // add subtotal
            form.payment.subtotal = this.subtotal;

            // add sale type
            form.sale_type = this.saleType;
            form.customer_type = this.customerType;
            form.party_id = this.customerId;

            // due or change
            if (this.dueOrChange > 0) {
                // due
                form.payment.due = Math.abs(this.dueOrChange);
            } else {
                // change
                form.payment.change = Math.abs(this.dueOrChange);
                form.payment.due = 0;
            }

            this.$awn.asyncBlock(
                axios.post(baseURL + "user/retail-sale", form),
                response => {
                    // redirec to invoice
                    window.location.href =
                        baseURL +
                        "user/invoice-generate/" +
                        response.data.invoice_no;
                    console.log(response.data)
                },
                reason => {
                    console.log(reason.response.data)
                    this.errors = reason.response.data;
                }
            );
        },
        init() {
            try {
                // select first cash id
                this.paymentInfo.cash_id = (this.cashes.length > 0) ? this.cashes[0].id : null;
                // select first bank account
                this.paymentInfo.bank_account_id = (this.bankAccounts.length > 0) ? this.bankAccounts[0].id : null;
            } catch (error) {
                console.log(error);
            }
        }
    },
}
</script>

<style scoped>

</style>

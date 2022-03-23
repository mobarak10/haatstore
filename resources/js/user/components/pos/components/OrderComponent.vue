<template>
    <div class="order">
        <!-- Product header -->
        <search-by-barcode-component />

        <!-- Search by Product Code -->
        <!-- <search-by-product-code-component /> -->

        <!-- cart start -->
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Details</th>
                    <th class="text-right">Price (BDT)</th>
                    <th class="text-right"><i class="fa fa-trash"></i></th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="cartProducts.length === 0">
                    <th colspan="4" class="text-center">No item available in cart.</th>
                </tr>

                <tr v-for="(product, index) in cartProducts" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>
                        <a
                            href="#"
                            @click.prevent="editCartProduct(product.id)"
                            :title="
                                product.name +
                                    ', ' +
                                    product.attributes.meta.units.display +
                                    ', BDT' +
                                    product.price
                            "
                        >
                            <span>{{ product.name }}</span>

                            <small class="d-block">
                                {{ product.attributes.meta.units.display }}, BDT
                                {{ product.price }}
                            </small>
                        </a>
                    </td>

                    <td class="text-right">
                        {{
                            Number.parseFloat(
                                product.attributes.price.priceSumWithConditions
                            ).toFixed(2)
                        }}
                    </td>

                    <td class="text-right">
                        <a
                            href="#"
                            title="Remove product"
                            @click.prevent="removeProduct(product.id)"
                        >
                            <i class="fa fa-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
            </tbody>

            <!-- subtotal component start -->
            <total-amount-component></total-amount-component>
            <!-- subtotal component end -->
        </table>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import SearchByProductCodeComponent from "./SearchByProductCodeComponent";
import SearchByBarcodeComponent from "./SearchByBarcodeComponent";
import TotalAmountComponent from "./TotalAmountComponent";

export default {
    name: "OrderComponent",
    components: {
        SearchByBarcodeComponent,
        TotalAmountComponent,
        SearchByProductCodeComponent
    },
    computed: {
        ...mapGetters(["getterSortedCartProducts"]),
        cartProducts(){
            return this.getterSortedCartProducts
        }
    },
    methods: {
        ...mapActions(["actionRefreshCartProducts", "actionRemoveCartProduct"]),
        removeProduct(productId) {
            if (confirm("Are you sure want to remove this item?")) {
                this.actionRemoveCartProduct({
                    id: productId
                });
            }
        },
        editCartProduct(productId) {
            eventBus.$emit("editCartProduct", productId);
        }
    },
    mounted() {
        this.actionRefreshCartProducts();
        eventBus.$on("refreshCartProducts", () =>
            this.actionRefreshCartProducts()
        );
    },
    beforeDestroy() {
        eventBus.$off("refreshCartProducts", () =>
            this.actionRefreshCartProducts()
        );
    }
};
</script>

<style scoped></style>

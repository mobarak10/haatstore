<template>
    <div class="pos-products">
        <strong>PRODUCTS</strong>

        <div class="row mx-n2">
            <div
                class="col-12 px-2 text-center"
                v-if="
                    productFilter.categoryId === undefined &&
                        !productFilter.productName
                "
            >
                <!-- Single -->
                Select catgory or search by name
                <!-- End of the single -->
            </div>
            <div class="col-12 px-2 text-center" v-else-if="productsIsLoading">
                <!-- Single -->
                Product is loading...
                <!-- End of the single -->
            </div>
            <div class="col-12 px-2 text-center" v-else-if="!products.length">
                <!-- Single -->
                No product available
                <!-- End of the single -->
            </div>

            <div
                v-else
                class="col-6 col-lg-2 col-sm-3 px-2"
                v-for="(product, index) in products"
                :key="index"
            >
                <!-- Single -->
                <div class="single border mb-3">
                    <a
                        href="#"
                        @click="showItemInformation(product.barcode)"
                        class="stretched-link"
                        data-toggle="modal"
                    >
                        <div class="content">
                            <span class="name">{{ product.name }}</span>
                            <div class="price">
                                <span>{{ product.barcode }}</span>
                            </div>
                            <div class="price">
                                <span>RP: BDT {{ product.retail_price }}</span>
                            </div>
                            <div class="price">
                                <span
                                    >WP: BDT {{ product.wholesale_price }}</span
                                >
                            </div>
                        </div>
                    </a>
                </div>
                <!-- End of the single -->
            </div>

            <product-modal-component></product-modal-component>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions, mapGetters } from "vuex";
import ProductModalComponent from "./ProductModalComponent";
export default {
    name: "ProductComponent",
    components: { ProductModalComponent },
    computed: {
        ...mapState(["activeProducts", "productFilter", "productsIsLoading"]),
        ...mapGetters(["getterFilterWiseProducts"])
    },
    watch: {
        activeProducts: {
            deep: true,
            immediate: true,
            handler: function(val, oldVal) {
                this.products = val;
            }
        },
        productFilter: {
            deep: true,
            handler: "filterProducts"
        }
    },
    data() {
        return {
            products: []
        };
    },
    methods: {
        ...mapActions(["actionFilterWiseProducts"]),
        showItemInformation(barcode) {
            eventBus.$emit("modalShowProductInfo", barcode);
        },
        filterProducts(payload) {
            //this.products = this.getterFilterWiseProducts(payload);

            this.$awn.asyncBlock(
                this.actionFilterWiseProducts(payload),
                null,
                null,
                null,
                {
                    minDurations: {
                        "async-block": null
                    }
                }
            );
        }
    },
    mounted() {},
    beforeDestroy() {}
};
</script>

<style scoped></style>

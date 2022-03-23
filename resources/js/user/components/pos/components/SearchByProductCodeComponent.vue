<template>
    <div class="order-header">
        <form action="" @submit.prevent="searchByProductCode">
            <div class="input-group">
                <input
                    type="text"
                    v-model="productCode"
                    class="form-control rounded-0"
                    placeholder="Product Code"
                    required
                />

                <div class="input-group-append">
                    <button
                        class="btn btn-info rounded-0"
                        type="submit"
                        title="search"
                    >
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "SearchByProductCodeComponent",
    computed: {
        ...mapGetters(["getterSingleActiveProductByProductCode"])
    },
    data() {
        return {
            productCode: null
        };
    },
    methods: {
        searchByProductCode() {
            if (this.getterSingleActiveProductByProductCode(this.productCode)) {
                eventBus.$emit(
                    "modalShowProductInfoByProductCode",
                    this.productCode
                );
            } else {
                this.$awn.alert("Product not found");
            }

            this.productCode = null;
        }
    }
};
</script>

<style scoped></style>

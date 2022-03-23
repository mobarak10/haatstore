<template>
    <div class="order-header">
        <form @submit.prevent="searchByBarcodeFromServer">
            <div class="input-group">
                <input
                    type="text"
                    v-model="barcode"
                    class="form-control rounded-0"
                    placeholder="Barcode"
                    autofocus
                    required />

                <div class="input-group-append">
                    <button
                        class="btn btn-info rounded-0"
                        type="submit"
                        title="search">
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
    name: "SearchByBarcodeComponent",
    computed: {
        ...mapGetters(["getterSingleActiveProduct"])
    },
    data() {
        return {
            barcode: null,
            autofocus: true
        };
    },
    methods: {
        searchByBarcode() {
            // this.barcode.toString
            // let split_barcode = this.barcode.slice(2, 7)
            if (this.getterSingleActiveProduct(this.barcode)) {
                eventBus.$emit("modalShowProductInfo", this.barcode
                );
            } else {
                this.$awn.alert("Product not found");
            }
            // this.split_barcode = null;
        },
        searchByBarcodeFromServer() {
            eventBus.$emit("modalShowProductInfo", this.barcode);
            this.barcode = null;
        }
    }
};
</script>

<style scoped></style>

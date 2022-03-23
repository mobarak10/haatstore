<template>
    <div class="order-header">
        <form action="" @submit.prevent="updateSearch">
            <div class="input-group">
                <input
                    type="text"
                    class="form-control rounded-0"
                    placeholder="Type product name"
                    v-model="productName"
                />

                <div class="input-group-append">
                    <button :class="iconBackgroundColor" type="submit">
                        <i :class="serchCrossIcon"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { mapMutations, mapState } from "vuex";
export default {
    name: "SearchByNameComponent",
    watch: {
        productName: {
            handler: function (name) {
                this.mutationTempProductSearchCurrentValue(name)
            },
            immediate: true
        }
    },
    computed: {
        ...mapState(["productFilter"]),
        iconBackgroundColor() {
            let productNameLenght = this.productName.length;

            return {
                btn: true,
                "rounded-0": true,
                //"btn-danger": productNameLenght > 0,
                //"btn-info": productNameLenght == 0
                "btn-info": true
            };
        },
        serchCrossIcon() {
            let productNameLenght = this.productName.length;
            return {
                fa: true,
                // "fa-times": productNameLenght > 0,
                //"fa-search": productNameLenght == 0
                "fa-search": true
            };
        }
    },
    data() {
        return {
            productName: ""
        };
    },
    methods: {
        ...mapMutations(["mutationProductFilters", "mutationTempProductSearchCurrentValue"]),

        updateSearch(productName) {
            const filters = { ...this.productFilter };

            filters.productName = this.productName;

            this.mutationProductFilters(filters);
        },
        resetForm() {
            this.productName = "";
        }
    }
};
</script>

<style></style>

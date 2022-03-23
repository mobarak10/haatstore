<template>
    <div class="form-group">
        <label for="select-cash">Select Cash</label>
        <select v-model="selectedCash" id="select-cash" class="form-control" required>
            <option :value="null" disabled>Select Cash</option>
            <option v-for="(_cash, index) in cashes"
                    :key="index" :value="_cash">{{ _cash.title }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "CashPaymentComponent",
        watch: {
            selectedCash: {
                handler: 'selectedCashUpdated'
            }
        },
        data() {
            return {
                cashes: [],
                cashIndex: null,
                selectedCash: null
            };
        },
        methods: {
            loadCashes() {
                // load cashes
                axios
                    .post(baseURL + "user/get-all-cashes")
                    .then(response => {
                        this.cashes = response.data;
                        if (this.cashes.length === 1){
                            this.selectedCash = this.cashes[0]
                        }
                    })
                    .catch(reason => console.log(reason));
            },
            selectedCashUpdated(updatedData){
                this.$emit("paymentUpdated", updatedData)
            }
        },
        mounted() {
            this.loadCashes();
        }
    }
</script>

<style scoped>

</style>

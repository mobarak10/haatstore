<template>
    <div class="form-row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="select-bank">Bank</label>
                <select class="form-control form-control-sm" id="select-bank" v-model="form.bank" @change="loadBankAccounts" required>
                    <option :value="null" disabled>Select Bank</option>
                    <option v-for="(_bank, bankIndex) in banks" :key="bankIndex" :value="_bank">{{ _bank.name }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="select-bank-account">Bank Account</label>
                <select class="form-control form-control-sm" id="select-bank-account" v-model="form.bankAccount" required>
                    <option :value="null" disabled>Select Bank Account</option>
                    <option v-for="(_bankAccount, bankAccountIndex) in bankAccounts" :key="bankAccountIndex" :value="_bankAccount">{{ `${_bankAccount.account_name} (${_bankAccount.account_number})` }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="cheque-number">Cheque Number</label>
                <input type="text" class="form-control form-control-sm" v-model="form.chequeNumber" id="cheque-number" placeholder="Enter cheque number" autocomplete="off">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="issue-date">Issue Date</label>
                <input type="date" class="form-control form-control-sm" v-model="form.issueDate" id="issue-date">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "BankPaymentComponent",
        watch: {
            form: {
                handler: 'bankPaymentUpdated',
                deep: true
            }
        },
        data: () => ({
            banks: [],
            bankAccounts: [],
            form: {
                bank: null,
                bankAccount: null,
                chequeNumber: null,
                issueDate: null
            }
        }),
        methods: {
            loadBanks(){
                axios.post(baseURL + 'user/get-all-banks')
                .then(response => {
                    this.banks = response.data
                })
                .catch(reason => console.log(reason))
            },
            loadBankAccounts(){
                this.form.bankAccount = null;

                if (! this.form.bank){
                    return ;
                }

                this.$awn.asyncBlock(
                    axios.post(baseURL + 'user/get-accounts-from-bank', {
                        id: this.form.bank.id
                    }),
                    response => {
                        this.bankAccounts = response.data // bank accounts
                    },
                    reason => console.log(reason)
                )
            },
            bankPaymentUpdated(updatedData){
                this.$emit("paymentUpdated", updatedData)
            },
            initData(){
                this.loadBanks();
            }
        },
        mounted() {
            this.initData();
        }
    }
</script>

<style scoped>

</style>

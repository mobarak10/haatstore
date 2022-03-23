/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

require('./bootstrap');

window.Vue = require('vue');
Vue.component("v-select", vSelect);

/*
* add base url into vue
* */

//console.log(document.getElementsByTagName("meta").find(meta => meta.name == ''))
Vue.prototype.baseURL = document.head.querySelector('meta[name="base-url"]').content + '/';
window.baseURL = document.head.querySelector('meta[name="base-url"]').content + '/';

window.Chart = require('chart.js');
require("chart.js/dist/Chart.min.css");

/**
 * Vuex initialization
 */

import store from './user/store/index'
Vue.use(store)

import 'select2/dist/css/select2.css';
import 'select2/dist/js/select2'

// Vue awesome notification
import VueAWN from "vue-awesome-notifications";
require("vue-awesome-notifications/dist/styles/style.css");
Vue.use(VueAWN);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key)))

// for passport components
Vue.component('passport-clients', require('./components/passport/Clients.vue'));
Vue.component('passport-authorized-clients', require('./components/passport/AuthorizedClients.vue'));
Vue.component('passport-personal-access-tokens', require('./components/passport/PersonalAccessTokens.vue'));

// User Component
Vue.component('pos-component', require('./user/components/pos/POSContainerComponent.vue'));
Vue.component('pos-checkout-component', require('./user/components/pos/POSCheckoutContainerComponent.vue'));
Vue.component('sale-return-component', require('./user/components/pos/SaleReturnContainerComponent.vue'));
Vue.component('product-purchase-component', require('./user/components/purchase/PurchaseProductComponent.vue'))
Vue.component('product-purchase-return-container-component', require('./user/components/purchase/PurchaseReturnContainerComponent.vue'))
Vue.component('create-product-component', require('./user/components/product/CreateProductComponent.vue'));
Vue.component('product-search-company-brand-category', require('./user/components/product/search-form/CompanyBrandCategoryComponent.vue'));
Vue.component('update-product-component', require('./user/components/product/UpdateProductComponent.vue'));
Vue.component('bank-transaction-component', require('./user/components/banking/TransactionComponent.vue'));

// retail sale
Vue.component("retail-sale", require("./user/components/pos/RetailSaleComponent.vue"));

// gl account head component
Vue.component('create-gl-account-head-component', require('./user/components/glAccountHead/CreateGLAccountHeadComponent.vue'));
Vue.component('update-gl-account-head-component', require('./user/components/glAccountHead/UpdateGLAccountHeadComponent.vue'));

// expense components
Vue.component('expense-entry-component', require('./user/components/expense/ExpenseCreateComponent.vue'));

// supplier-due-management components
Vue.component('supplier-due-manage-component', require('./user/components/supplierDueManagement/SupplierDueManagementComponent.vue'));

// Manage Due
Vue.component('manage-due-create', require('./user/components/due-management/ManageDueCreateComponent.vue'));

// product-transfer components
Vue.component('product-transfer-creat-component', require('./user/components/product-transfer/ProductTransferCreatComponent.vue'));
// product-transfer components
Vue.component('order-confirm-component', require('./user/components/order-manage/OrderConfirmComponent.vue'));


// Vue.component('example-component', require('./components/ExampleComponent.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 //global event bus for vue component
window.eventBus = new Vue()

const app = new Vue({
    el: '#app',
    store
});

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// frontend
Route::get('/', 'WelcomeController@welcome');

Route::get('/storage_link', function () {
    Artisan::call('storage:link');
});

// localization
Route::get('/locale/{lang}', 'LocaleController@changeLocale');

// Auth
Auth::routes();
Route::get('/subscriber/activate/{email}/{token}', 'Auth\RegisterController@activate')->name('subscriber.activate');

// User controller
Route::group(['namespace' => 'User'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('user/home/dailySale', 'HomeController@getDailySale');
    Route::get('/api-token', 'HomeController@generateToken');
    // Warehouse Route
    Route::get('user/warehouse/{id}/status', 'WarehouseController@changeStatus')->name('warehouse.status');

    // cash ledger details route
    Route::get('user/cash-ledger-details/{id}', 'CashController@ledgerDetails')->name('cash.ledger-details');

    // bank Route
    Route::get('user/bank/{id}/status', 'BankController@changeStatus')->name('bank.status');

    // bank account Route
    Route::get('user/bank/transaction/{id}', 'BankAccountController@showTransaction')->name('bankAccount.transaction');

    // accounting Route
    Route::get('user/glAccount/{id}/status', 'GLAccountController@changeStatus')->name('glAccount.status');

    Route::get('user/glAccountHead/{id}/status', 'GLAccountHeadController@changeStatus')->name('glAccountHead.status');

    //point of sale (pos) list
    Route::get('user/pos/', 'POSController@index')->name('pos.index');

    Route::post('user/pos/delivered/{sale}', 'POSController@deliver')->name('pos.deliver');


    //point of sale (pos) new
    Route::get('user/pos/create', 'POSController@create')->name('pos.create');
    //point of sale (pos) show
    Route::get('user/pos/show/{sale}', 'POSController@show')->name('pos.show');
    //pos user payment proceed
    Route::get('user/pos/checkout', 'POSController@checkout')->name('pos.checkout');
    //pos return
    Route::get('user/pos/return/{invoice_no}', 'POSController@return')->name('pos.return');
    Route::post('user/pos/return', 'POSController@returnProceed');

    // add discount
    Route::patch('user/discount/{id}', 'POSController@addDiscount')->name('pos.discount');

    //invoice generate
    Route::get('user/invoice-generate/{invoice_no}', 'InvoiceController@index')->name('invoice.generate');

    // Category Active Toggle Route
    Route::get('user/category/{category}/toggleActive', 'CategoryController@toggleActive')->name('category.toggleActive');

    // change supplier status
    Route::get('user/supplier/{id}/status', 'SupplierController@changeSuppliersStatus')->name('supplier.changeSuppliersStatus');

    // change customer status
    Route::get('user/customer/{id}/status', 'CustomerController@changeCustomerStatus')->name('customer.changeCustomerStatus');

    // Brand Active Toggle Route
    Route::get('user/brand/{brand}/toggleActive', 'BrandController@toggleActive')->name('brand.toggleActive');

    // daily report route
    // Route::get('user/Reports/cashBook', 'CashBookController@index')->name('cashBook.index');

    //product search route
    Route::get('user/search-product', 'ProductController@search')->name('product.search');

    // barcode routes
    Route::get('user/barcode', 'BarcodeGeneratorController@index')->name('barcode.index');

    //damage-stock edit route
    Route::get('user/damage-stock/{id}/edit', 'DamageStockController@editDamage')->name('damage.edit');

    //damage-stock update route
    Route::post('user/damage-stock/{id}/update', 'DamageStockController@updateDamage')->name('damage.update');

    // Due management route
    Route::get('user/dueManagement/supplier/index', 'DueManagementController@supplierIndex');
    Route::get('user/dueManagement/customer/index', 'DueManagementController@customerIndex');
    Route::get('user/dueManagement/{type}', 'DueManagementController@create')->name('dueManagement.create');
    Route::post('user/dueManagement', 'DueManagementController@store')->name('dueManagement.store');
    Route::get('user/dueManagement/{id}/show', 'DueManagementController@show')->name('dueManagement.show');

    //purchase return
    Route::get('user/purchase/{purchase}/return', 'PurchaseController@returnPurchase')->name('purchase.return');
    Route::post('user/purchase/{id}/return', 'PurchaseController@returnPurchaseProceed');

    // PDF route
    Route::get('user/pdf/stock-pdf', 'PDFController@stockPDF')->name('stockPDF');

    Route::get('user/stock/product-export', 'StockController@productExport')->name('productExport');

    Route::group(['namespace' => 'Report'], function () {
        Route::get('user/Reports/cashBook', 'CashBookController@index')->name('cashBook.index'); // daily report route
        Route::post('user/Reports/storeBalance', 'CashBookController@storeBalance')->name('cashBook.storeBalance');
        Route::get('user/Reports/profitLoss', 'ProfitLossController@index')->name('profitLoss.index'); // profit-loss report route
        Route::get('user/Reports/productReport', 'ProductReportController@index')->name('productReport.index'); // purchase report route

        // sale reports
        Route::get('user/reports/salesreport', 'SalesReportController@index')->name('report.sales');
        Route::get('user/reports/salesreport/{period}', 'SalesReportController@daily')->name('report.sales.daily');
        Route::get('user/reports/salesreport/details/{period}', 'SalesReportController@details')->name('report.sales.details');

        // supplier ledger report
        Route::get('user/reports/supplierLedger', 'LedgerReportController@supplierLedger')->name('report.supplierLedger');
        // customer ledger report
        Route::get('user/reports/customerLedger', 'LedgerReportController@customerLedger')->name('report.customerLedger');

        // purchase reports
        Route::get('user/reports/purchasesreport', 'PurchaseReportController@index')->name('report.purchases');
        // date wise stock report
        Route::get('user/reports/stockReport', 'StockReportController@index')->name('report.stockReport');
        Route::get('user/reports/purchasesreport/{period}', 'PurchaseReportController@daily')->name('report.purchases.daily');
        Route::get('user/reports/purchasesreport/details/{period}', 'PurchaseReportController@details')->name('report.purchases.details');

        // purchase reports
        Route::get('user/reports/expenditurereport', 'ExpenditureReportController@index')->name('report.expenditure');
        Route::get('user/reports/expenditurereport/{period}', 'ExpenditureReportController@daily')->name('report.expenditure.daily');
        Route::get('user/reports/expenditurereport/details/{period}', 'ExpenditureReportController@details')->name('report.expenditure.details');
    });

    Route::resources([
        'user/stock'            => 'StockController', // stock resource route
        'user/damageStock'      => 'DamageStockController', // damage-stock
        'user/category'         => 'CategoryController', // category resource route
        'user/cash'             => 'CashController', // cash resource route
        'user/capital'          => 'CapitalController', //Capital resourse route
        'user/media'            => 'MediaController', // media resource route
        'user/account'          => 'AccountController', // account resource route
        'user/unit'             => 'UnitController', // unit resource route
        'user/warehouse'        => 'WarehouseController', // Warehouse Route
        'user/supplier'         => 'SupplierController', // supplier resource route
        'user/brand'            => 'BrandController', // brand resource route
        'user/customer'         => 'CustomerController', // customer resource route
        'user/product'          => 'ProductController', // product resource route
        'user/bank'             => 'BankController', // bank resource route
        'user/bankAccount'      => 'BankAccountController', // bank-account resource route
        'user/balanceTransfer'  => 'BalanceTransferController', // balance-transfer resource route
        // accounting
        'user/glAccount'        => 'GLAccountController', // general ledger account resource route
        'user/glAccountHead'    => 'GLAccountHeadController', // genaral ledger account head resource route
        'user/expenditure'      => 'ExpenditureController', // expenditure resource route
        'user/purchase'         => 'PurchaseController', // purchase resource route
        'user/productTransfer'  => 'ProductTransferController', // product-transfer resource route
        'user/orderManagement'  => 'OrderManagementController', // order-manage resource route
    ]);

    /*------------------AJAX Route Start------------------*/

    //get all salesman
    Route::post('get-salesmen', 'POSController@salesmen');

    //get all active warehouses
    Route::post('user/get-all-active-warehouses', 'WarehouseController@allActiveWarehouses');

    //calculate sale return line total
    Route::post('user/calculate-sale-return-line-total', 'POSController@calculateLineTotal');

    //get all active categories
    Route::post('user/get-all-active-categories', 'POSController@allActiveCategories');

    //get product details
    Route::post('user/get-details-from-product', 'POSController@productDetails');

    //get all active products
    Route::post('user/get-all-active-products', 'POSController@allActiveProducts');

    // get all filtered products
    Route::post('user/pos/filter-wise-products', 'POSController@filterWiseProducts');

    Route::post('user/get-all-products', 'POSController@allProducts');

    //get warehouse-wise product
    Route::post('user/get-product-from-warehouse', 'StockController@getProductsFromWarehouse');

    //get product unit
    Route::post('user/get-product-unit', 'StockController@getProductUnit');

    // get details from cashes
    Route::post('user/get-details-from-cash', 'CashController@cashDetails');

    //get all cashes
    Route::post('user/get-all-cashes', 'CashController@allCashes');

    // get accounts from bank
    Route::post('user/get-accounts-from-bank', 'BankController@accounts');

    // get banks
    Route::post('user/get-all-banks', 'BankController@allBankAccounts');

    // get details from account
    Route::post('user/get-details-from-account', 'BankAccountController@accountDetails');

    //pos user - add to cart
    Route::post('user/pos/add-to-cart', 'CartController@addToCart');

    //pos user - update cart item
    Route::post('user/pos/update-cart-item', 'CartController@updateCartItem');

    //pos user get current cart content
    Route::post('user/pos/get-current-cart-items', 'CartController@cartItems');

    //pos user get sub total
    Route::post('user/pos/get-sub-total', 'CartController@subTotal');
    Route::post('user/pos/get-purchase-total', 'CartController@purchaseTotal');

    //pos user get total
    Route::post('user/pos/get-total', 'CartController@total');

    //pos user get calculated vat amount of subtotal
    Route::post('user/pos/get-calculated-amount-of-subtotal-for-regular-vat', 'CartController@calculateValueOfSubtotalForRegularVat');

    //pos user apply discount
    Route::post('user/pos/apply-discount', 'CartController@applyDiscount');

    //pos user apply discount
    Route::post('user/pos/get-applied-discount', 'CartController@appliedDiscount');

    //pos user remove specific item form cart
    Route::post('user/pos/remove-cart-item', 'CartController@removeItem');

    //pos user clear cart
    Route::post('user/pos/clear-cart-items', 'CartController@clearCartItems');

    //pos user payment proceed
    Route::post('user/pos-proceed-payment', 'CartController@proceedPayment');

    // get brands from suppliers
    Route::post('user/get-brands-from-supplier', 'SupplierController@brands');

    // get categories from brand
    Route::post('user/get-categories-from-brand', 'BrandController@categories');

    //get all supplier
    Route::post('user/get-all-active-suppliers', 'SupplierController@allActiveSuppliers');

    //get all customers
    Route::post('user/get-all-active-customers', 'CustomerController@allActiveCustomers');

    //create new customer for pos
    Route::post('user/create-new-customer', 'CustomerController@createNewCustomer');

    //get supplier details
    Route::post('user/get-details-from-party', 'SupplierController@partyDetails');

    // get gl head
    Route::post('user/get-gl-account-heads', 'GLAccountController@getGLAccountHeads');

    //purchase user add to cart
    Route::post('user/purchase/add-to-cart', 'PurchaseController@addToCart');
    //purchase user get cart contents
    Route::post('user/purchase/get-cart-contents', 'PurchaseController@getCartContents');
    //purchase user remove item
    Route::post('user/purchase/remove-cart-item', 'PurchaseController@removeCartItem');
    //purchase user clear cart contents
    Route::post('user/purchase/clear-cart-contents', 'PurchaseController@clearCartContents');

    Route::post('user/calculate-purchase-return-line-total', 'PurchaseController@calculatePurchaseReturnLineTotal');
    Route::post('user/purchase-return', 'PurchaseController@returnProceed');

    //get all active product from supplier
    Route::post('get-all-active-products-from-supplier', 'SupplierController@allActiveProducts');

    // Retail sale
    Route::post('user/retail-sale', 'RetailSaleController@sale');
    /*-------------------AJAX Route End------------------*/
});

// Admin controller
Route::name('admin.')->group(function () {
    Route::group(['namespace' => 'Admin'], function () {
        // admin dashboard
        Route::get('admin/home', 'HomeController@index')->name('home');
        Route::get('admin/home/dailySale', 'HomeController@getDailySale');

        //point of sale (pos) list
        Route::get('admin/pos/', 'POSController@index')->name('pos.index');
        //point of sale (pos) new
        Route::get('admin/pos/create', 'POSController@create')->name('pos.create');
        //point of sale (pos) show
        Route::get('admin/pos/show/{sale}', 'POSController@show')->name('pos.show');
        //pos admin payment proceed
        Route::get('admin/pos/checkout', 'POSController@checkout')->name('pos.checkout');
        //pos return
        Route::get('admin/pos/return/{invoice_no}', 'POSController@return')->name('pos.return');
        Route::post('admin/pos/return', 'POSController@returnProceed');

        //invoice generate
        Route::get('admin/invoice-generate/{invoice_no}', 'InvoiceController@index')->name('invoice.generate');

        //purchase return
        Route::get('admin/purchase/{purchase}/return', 'PurchaseController@returnPurchase')->name('purchase.return');
        Route::post('admin/purchase/{id}/return', 'PurchaseController@returnPurchaseProceed');

        // cash ledger details route
        Route::get('admin/cash-ledger-details/{id}', 'CashController@ledgerDetails')->name('cash.ledger-details');

        // Warehouse Route
        Route::get('admin/warehouse/{id}/status', 'WarehouseController@changeStatus')->name('warehouse.status');

        // Category Active Toggle Route
        Route::get('admin/category/{category}/toggleActive', 'CategoryController@toggleActive')->name('category.toggleActive');

        // Brand Active Toggle Route
        Route::get('admin/brand/{brand}/toggleActive', 'BrandController@toggleActive')->name('brand.toggleActive');

        // bank Route
        Route::get('admin/bank/{id}/status', 'BankController@changeStatus')->name('bank.status');

        // bank account Route
        Route::get('admin/bank/transaction/{id}', 'BankAccountController@showTransaction')->name('bankAccount.transaction');

        //product search route
        Route::get('admin/search-product', 'ProductController@search')->name('product.search');

        //damage-stock edit route
        Route::get('admin/damage-stock/{id}/edit', 'DamageStockController@editDamage')->name('damage.edit');

        //damage-stock update route
        Route::post('admin/damage-stock/{id}/update', 'DamageStockController@updateDamage')->name('damage.update');

        // accounting Route
        Route::get('admin/glAccount/{id}/status', 'GLAccountController@changeStatus')->name('glAccount.status');
        Route::get('admin/glAccountHead/{id}/status', 'GLAccountHeadController@changeStatus')->name('glAccountHead.status');
        // change supplier status
        Route::get('admin/supplier/{id}/status', 'SupplierController@changeSuppliersStatus')->name('supplier.changeSuppliersStatus');

        // change Employee status
        Route::get('admin/employee/{id}/status', 'EmployeeController@changeEmployeeStatus')->name('employee.changeEmployeeStatus');

        // change customer status
        Route::get('admin/customer/{id}/status', 'CustomerController@changeCustomerStatus')->name('customer.changeCustomerStatus');

        Route::group(['namespace' => 'Reports'], function () {
            // current-stock report route
            Route::get('admin/Reports/currentStock', 'StockReportController@currentStock')->name('currentStockReport.currentStock');
            // damage-stock report route
            Route::get('admin/Reports/damageStock', 'StockReportController@damageStock')->name('damageStockReport.damageStock');
            // expenditure report route
            Route::get('admin/Reports/expenditure', 'ExpenditureReportController@index')->name('expenditureReport.index');
            // daily report route
            Route::get('admin/Reports/dailyReport', 'DailyReportController@index')->name('dailyReport.index');
            // sale report route
            Route::get('admin/Reports/saleReport', 'SaleReportController@saleReport')->name('saleReport');
            // purchase report route
            Route::get('admin/Reports/purchaseReport', 'PurchaseReportController@purchaseReport')->name('purchaseReport');
            // sale return report route
            Route::get('admin/Reports/saleReturnReport', 'SaleReturnReportController@saleReturn')->name('saleReturnReport');
            // profit loss report route
            Route::get('admin/Reports/profitLossReport', 'ProfitLossReportController@index')->name('profitLossReport');
        });

        Route::resources([
            'admin/account'                     => 'AccountController', // account resource routes
            'admin/role'                        => 'RoleController', // role resource routes
            'admin/permission'                  => 'PermissionController', // permission resource routes
            'admin/cash'                        => 'CashController', // cash resource route
            'admin/capital'                     => 'CapitalController', // capital resource route
            'admin/supplier'                    => 'SupplierController', // supplier resource route
            'admin/brand'                       => 'BrandController', // brand resource route
            'admin/product'                     => 'ProductController', // product resource route
            'admin/category'                    => 'CategoryController', // category resource route
            'admin/customer'                    => 'CustomerController', // customer resource route
            'admin/warehouse'                   => 'WarehouseController', // Warehouse Route
            'admin/media'                       => 'MediaController', // media resource route
            'admin/bank'                        => 'BankController', // bank resource route
            'admin/bankAccount'                 => 'BankAccountController', // bank-account resource route
            'admin/balanceTransfer'             => 'BalanceTransferController', // balance-transfer resource route
            'admin/unit'                        => 'UnitController', // unit resource route
            'admin/expenditure'                 => 'ExpenditureController', // expenditure resource route
            'admin/stock'                       => 'StockController', // stock resource route
            'admin/damageStock'                 => 'DamageStockController', // damage-stock resource route
            'admin/employee'                    => 'EmployeeController', // employee resource route
            'admin/purchase'                    => 'PurchaseController', // purchase resource route
            // accounting
            'admin/glAccount'                   => 'GLAccountController', // general ledger account resource route
            'admin/glAccountHead'               => 'GLAccountHeadController', // genaral ledger account head resource route
            'admin/business'                    => 'BusinessController', // businesses resource route
            'admin/settings'                    => 'SettingsController', // businesses resource route
        ]);

        Route::group(['namespace' => 'PayrollManagement'], function () {
            Route::get('admin/salaryPay/{id}', 'SalaryController@salaryPay')->name('salaryPay');
            Route::get('admin/salaryView/{id}', 'SalaryController@salaryView')->name('salaryView');
            Route::resources([
                'admin/advancedSalary'      => 'AdvancedSalaryController', // advanced salary management resource route
                'admin/salary'              => 'SalaryController', // salary management resource route
            ]);
        });


        /*------------------AJAX Route Start------------------*/

        //calculate sale return line total
        Route::post('calculate-sale-return-line-total', 'POSController@calculateLineTotal');

        //pos admin - add to cart
        Route::post('admin/pos/add-to-cart', 'CartController@addToCart');

        //pos admin - update cart item
        Route::post('admin/pos/update-cart-item', 'CartController@updateCartItem');

        //pos admin get current cart content
        Route::post('admin/pos/get-current-cart-items', 'CartController@cartItems');

        //pos admin get sub total
        Route::post('admin/pos/get-sub-total', 'CartController@subTotal');

        //pos admin get total
        Route::post('admin/pos/get-total', 'CartController@total');

        //pos admin get calculated vat amount of subtotal
        Route::post('admin/pos/get-calculated-amount-of-subtotal-for-regular-vat', 'CartController@calculateValueOfSubtotalForRegularVat');

        //pos admin apply discount
        Route::post('admin/pos/apply-discount', 'CartController@applyDiscount');

        //pos admin apply discount
        Route::post('admin/pos/get-applied-discount', 'CartController@appliedDiscount');

        //pos admin remove specific item form cart
        Route::post('admin/pos/remove-cart-item', 'CartController@removeItem');

        //pos admin clear cart
        Route::post('admin/pos/clear-cart-items', 'CartController@clearCartItems');

        //pos admin payment proceed
        Route::post('admin/pos-proceed-payment', 'CartController@proceedPayment');

        //purchase admin add to cart
        Route::post('admin/purchase/add-to-cart', 'PurchaseController@addToCart');
        //purchase admin get cart contents
        Route::post('admin/purchase/get-cart-contents', 'PurchaseController@getCartContents');
        //purchase admin remove item
        Route::post('admin/purchase/remove-cart-item', 'PurchaseController@removeCartItem');
        //purchase admin clear cart contents
        Route::post('admin/purchase/clear-cart-contents', 'PurchaseController@clearCartContents');

        Route::post('calculate-purchase-return-line-total', 'PurchaseController@calculatePurchaseReturnLineTotal');
        Route::post('admin/purchase-return', 'PurchaseController@returnProceed');

        //get all active products
        Route::post('get-all-active-products', 'POSController@allActiveProducts');
        //get all products
        Route::post('get-all-products', 'POSController@allProducts');
        //get all active warehouses
        Route::post('get-all-active-warehouses', 'WarehouseController@allActiveWarehouses');

        // get business wise warehouse
        Route::post('get-business-warehouse', 'BusinessController@warehouse');

        // get business wise expenditure
        Route::post('get-business-expenditure', 'BusinessController@expenditure');

        //get all active categories
        Route::post('get-all-active-categories', 'POSController@allActiveCategories');

        //get product details
        Route::post('admin/get-details-from-product', 'POSController@productDetails');

        // get brands from suppliers
        Route::post('get-brands-from-supplier', 'SupplierController@brands');

        // get categories from brand
        Route::post('get-categories-from-brand', 'BrandController@categories');

        //get all supplier
        Route::post('get-all-active-suppliers', 'SupplierController@allActiveSuppliers');
        //get supplier details
        Route::post('get-details-from-party', 'SupplierController@partyDetails');

        //get all active product from supplier
        Route::post('get-all-active-products-from-supplier', 'SupplierController@allActiveProducts');

        //get all customers
        Route::post('get-all-active-customers', 'CustomerController@allActiveCustomers');

        //create new customer for pos
        Route::post('create-new-customer', 'CustomerController@createNewCustomer');

        //get all cashes
        Route::post('get-all-cashes', 'CashController@allCashes');

        // get details from cashes
        Route::post('get-details-from-cash', 'CashController@cashDetails');

        // get accounts from bank
        Route::post('get-accounts-from-bank', 'BankController@accounts');

        // get banks
        Route::post('get-all-banks', 'BankController@allBankAccounts');

        // get details from account
        Route::post('get-details-from-account', 'BankAccountController@accountDetails');

        // get general ledger name from GlAccount
        // Route::post('get-glName-from-gl-account', 'GenaralLedgerHeadController@getGLName');
        Route::post('get-gl-account-heads', 'GLAccountController@getGLAccountHeads');

        // salaryDetails route
        Route::post('get-salary-details', 'PayrollManagement\SalaryController@salaryDetails');

        /*-------------------AJAX Route End------------------*/

        // Due management route
        Route::get('dueManagement/{type}/index', 'DueManagementController@index')->name('dueManagement.index');
        Route::get('dueManagement/{type}', 'DueManagementController@create')->name('dueManagement.create');
        Route::post('dueManagement', 'DueManagementController@store')->name('dueManagement.store');

        // barcode routes
        Route::get('barcode', 'BarcodeGeneratorController@index')->name('barcode.index');

        // admin auth
        Route::get('admin-login', 'Auth\LoginController@showLoginForm')->name('login');
        Route::post('admin-login', 'Auth\LoginController@login');
        Route::post('admin-logout', 'Auth\LoginController@logout')->name('logout');
    });
});

Route::prefix('artisan')->group(function(){
    Route::get('storage_link', function(){
        Artisan::call('storage:link');
    });

    Route::get('clear', function(){
        $exitCode = Artisan::call('optimize:clear');
        echo $exitCode;
    });

    Route::get('passport', function(){
        $exitCode = Artisan::call('passport:install');
        echo $exitCode;
    });
});

// routes for 2nd database connection
Route::get('get-meta', 'SystemController@getMeta');
Route::get('set-meta', 'SystemController@setMeta');
Route::get('set-meta2', 'SystemController@setMeta2');

<!-- aside content start -->
<aside class="column aside">
    <div class="brand">
        <span title="Company name">{{ config('app.name') }}</span>
        <a href="#" id="aside-close">
            <i class="fa fa-times" aria-hidden="true"></i>
        </a>
    </div>

    <!-- aside nav start -->
    <nav class="aside-nav overlay-scrollbar">
        <h6>@lang('contents.basic')</h6>
        <ul>
            <!-- dashboard -->
            <li id="dashboard">
                <a href="{{ route('admin.home') }}">
                    <i class="fa fa-tachometer" aria-hidden="true"></i>
                    <span title="Dashboard">@lang('contents.dashboard')</span>
                </a>
            </li>

            <!-- email -->
            <li id="email">
                <a href="#">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <span title="Email">@lang('contents.email')</span>
                    <span class="badge float-right">9</span>
                </a>
            </li>

            <!-- media -->
            <li id="media" class="dropdown">
                <a href="#">
                    <i class="fa fa-camera" aria-hidden="true"></i>
                    <span title="Media">@lang('contents.media')</span>
                    <span class="float-right">
                        <i class="fa fa-angle-right right" aria-hidden="true"></i>
                        <i class="fa fa-angle-down down" aria-hidden="true"></i>
                    </span>
                </a>

                <ul>
                    <li id="media-list"><a href="{{ route('admin.media.index') }}" title="Images">@lang('contents.images')</a></li>
                    {{-- <li id="media-others"><a href="{{ route('admin.media.index') }}" title="Others">Others</a></li> --}}
                    <li id="media-add"><a href="{{ route('admin.media.create') }}" title="New upload">@lang('contents.new_upload')</a></li>
                </ul>
            </li>
        </ul>

        <h6>@lang('contents.app_modules')</h6>
        <ul>
            {{-- pos --}}
{{--            <li id="pos" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-shopping-cart"></i>--}}
{{--                    <span title="Point of Sale">@lang('contents.pos')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}
{{--                <ul>--}}
{{--                    <li id="pos-list">--}}
{{--                        <a href="{{ route('admin.pos.index') }}">--}}
{{--                            <span title="Sale list">@lang('contents.sale_list')</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li id="pos-add">--}}
{{--                        <a href="{{ route('admin.pos.create') }}">--}}
{{--                            <span title="New Sale">@lang('contents.new_sale')</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            {{-- cash --}}
{{--            <li id="cash">--}}
{{--                <a href="{{ route('admin.cash.index') }}" title="Cash setup">--}}
{{--                    <i class="fa fa-money" aria-hidden="true"></i>--}}
{{--                    <span title="Cash">@lang('contents.cash')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            {{-- category --}}
{{--            <li id="category">--}}
{{--                <a href="{{ route('admin.category.index') }}">--}}
{{--                    <i class="fa fa-list-ul" aria-hidden="true"></i>--}}
{{--                    <span title="All Categories">@lang('contents.category')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            {{-- unit --}}
{{--            <li id="unit">--}}
{{--                <a href="{{ route('admin.unit.index') }}">--}}
{{--                    <i class="fa fa-cc-diners-club" aria-hidden="true"></i>--}}
{{--                    <span title="Unit">@lang('contents.unit')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            <!-- supplier -->
{{--            <li id="supplier" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-building"></i>--}}
{{--                    <span title="Company">@lang('contents.supplier')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                <ul>--}}
{{--                    <li id="supplier-list"><a href="{{ route('admin.supplier.index') }}" title="All Companies">@lang('contents.supplier_list')</a></li>--}}
{{--                    <li id="supplier-brand-list"><a href="{{ route('admin.brand.index') }}" title="All Brands">@lang('contents.brand')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            {{-- product --}}
{{--            <li id="product">--}}
{{--                <a href="{{ route('admin.product.index') }}">--}}
{{--                    <i class="fa fa-product-hunt" aria-hidden="true"></i>--}}
{{--                    <span title="Product">@lang('contents.product')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            <!-- purchase -->
{{--            <li id="purchase" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-building"></i>--}}
{{--                    <span title="Company">@lang('contents.purchase')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                <ul>--}}
{{--                    <li id="purchase-list"><a href="{{ route('admin.purchase.index') }}" title="All Purchases">@lang('contents.all_purchases')</a></li>--}}
{{--                    <li id="purchase-add"><a href="{{ route('admin.purchase.create') }}" title="Add new Purchase">@lang('contents.new_purchase')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            {{-- barcode --}}
{{--            <li id="barcode" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-barcode" aria-hidden="true"></i>--}}
{{--                    <span title="Barcode">@lang('contents.barcode')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                <ul>--}}
{{--                    <li id="barcode-generator">--}}
{{--                        <a href="{{ route('admin.barcode.index') }}" title="Barcode generator">@lang('contents.generator') </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            <!-- werehouse -->
{{--            <li id="warehouse" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-industry" aria-hidden="true"></i>--}}
{{--                    <span title="Warehouse">@lang('contents.warehouse')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}
{{--                <ul>--}}
{{--                    <li id="warehouse-list"><a href="{{ route('admin.warehouse.index') }}" title="Records">@lang('contents.records')</a></li>--}}
{{--                    <li id="warehouse-add"><a href="{{ route('admin.warehouse.create') }}" title="Create new">@lang('contents.create_new')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            <!-- due management -->
{{--            <li id="manage-due" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-line-chart" aria-hidden="true"></i>--}}
{{--                    <span title="Company">@lang('contents.manage_due')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                <ul>--}}
{{--                    <li id="manage-due-supplier"><a href="{{ route('admin.dueManagement.create', 'supplier') }}">@lang('contents.supplier')</a></li>--}}
{{--                    <li id="manage-due-customer"><a href="{{ route('admin.dueManagement.create', 'customer') }}">@lang('contents.customer')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            <!-- accounting -->
{{--            <li id="accounting" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-book" aria-hidden="true"></i>--}}
{{--                    <span title="Accounting">@lang('contents.accounting')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                <ul>--}}
{{--                    <li id="gl-account"><a href="{{ route('admin.glAccount.index') }}" title="GL Accounts">@lang('contents.gl_accounts')</a></li>--}}
{{--                    <li id="gl-account-head"><a href="{{ route('admin.glAccountHead.index') }}" title="GL Accounts Head">@lang('contents.gl_accounts_head')</a></li>--}}
{{--                    --}}{{-- <li id="journam"><a href="#" title="journal">@lang('contents.journal')</a></li>--}}
{{--                    <li id="daily-entry"><a href="#" title="daily-account">@lang('contents.daily_accounts')</a></li> --}}
{{--                </ul>--}}
{{--            </li>--}}

            <!-- bank -->
{{--            <li id="bank" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-university" aria-hidden="true"></i>--}}
{{--                    <span title="Banking">@lang('contents.banking') </span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                <ul>--}}
{{--                    <li id="bank-list"><a href="{{ route('admin.bank.index') }}" title="Bank list">@lang('contents.bank_list')</a></li>--}}
{{--                    <li id="bank-account-list"><a href="{{ route('admin.bankAccount.index') }}" title="Back account list">@lang('contents.bank_account_list')</a></li>--}}
{{--                    <li id="balance-transfer"><a href="{{ route('admin.balanceTransfer.index') }}" title="Balance transfer">@lang('contents.balance_transfer')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            {{-- Expenditure --}}
{{--            <li id="expenditure" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-cc-mastercard" aria-hidden="true"></i>--}}
{{--                    <span title="Expenditure">@lang('contents.expenditure')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                 <ul>--}}
{{--                    <li id="expenditure-index"><a href="{{ route('admin.expenditure.index') }}" title="Expenditure list">@lang('contents.todays_list')</a></li>--}}
{{--                    <li id="expenditure-create"><a href="{{ route('admin.expenditure.create') }}" title="Add new expenditure">@lang('contents.new_expense')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            <!-- customer -->
{{--            <li id="customer" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-users"></i>--}}
{{--                    <span title="Company">@lang('contents.customer')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                <ul>--}}
{{--                    <li id="customer-list"><a href="{{ route('admin.customer.index') }}" title="Records">@lang('contents.records')</a></li>--}}
{{--                    <li id="customer-add"><a href="{{ route('admin.customer.create') }}" title="Create new">@lang('contents.create_new')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            {{-- stock --}}
{{--            <li id="stock" class="dropdown">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-archive" aria-hidden="true"></i>--}}
{{--                    <span title="Stock">@lang('contents.stock')</span>--}}
{{--                    <span class="float-right">--}}
{{--                        <i class="fa fa-angle-right right" aria-hidden="true"></i>--}}
{{--                        <i class="fa fa-angle-down down" aria-hidden="true"></i>--}}
{{--                    </span>--}}
{{--                </a>--}}

{{--                 <ul>--}}
{{--                    <li id="current-stock"><a href="{{ route('admin.stock.index') }}" title="avaible stock">@lang('contents.current_stock')</a></li>--}}
{{--                    <li id="damage-stock"><a href="{{ route('admin.damageStock.index') }}" title="Damage Stock">@lang('contents.damage_stock')</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}

            {{-- report --}}
            <li id="report" class="dropdown">
                <a href="#">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    <span title="Reports">@lang('contents.reports')</span>
                    <span class="float-right">
                        <i class="fa fa-angle-right right" aria-hidden="true"></i>
                        <i class="fa fa-angle-down down" aria-hidden="true"></i>
                    </span>
                </a>

                 <ul>
                    {{-- total stock report --}}
                    <li id="current-stock"><a href="{{ route('admin.currentStockReport.currentStock') }}" title="avaible stock">@lang('contents.current_stock')</a></li>
                    {{-- damage report --}}
                    <li id="damage-stock"><a href="{{ route('admin.damageStockReport.damageStock') }}" title="Damage Stock">@lang('contents.damage_stock')</a></li>
                    {{-- sale report --}}
                    <li id="sale"><a href="{{ route('admin.saleReport') }}" title="sale">@lang('contents.sale')</a></li>
                     {{-- sale return report --}}
                     <li id="sale-return"><a href="{{ route('admin.saleReturnReport') }}" title="sale">@lang('contents.sale_return')</a></li>
                     {{-- purchase report --}}
                     <li id="purchase"><a href="{{ route('admin.purchaseReport') }}" title="sale">@lang('contents.purchase')</a></li>
                    {{-- expenditure report --}}
                    <li id="expenditure"><a href="{{ route('admin.expenditureReport.index') }}" title="Expenditure">@lang('contents.expenditure')</a></li>
                    {{-- daily report --}}
                    <li id="daily-report"><a href="{{ route('admin.dailyReport.index') }}" title="DailyReport">@lang('contents.daily_report')</a></li>
                     {{-- loss profit report --}}
                     <li id="loss-profit-report"><a href="{{ route('admin.profitLossReport') }}" title="Loss-Profit">Profit & Loss</a></li>
                </ul>
            </li>

            {{-- add business --}}
            <li id="business">
                <a href="{{ route('admin.business.index') }}">
                    <i class="fa fa-building-o" aria-hidden="true"></i>
                    <span title="Business Add">@lang('contents.add_business')</span>
                </a>
            </li>

            {{-- payroll --}}
            <li id="payroll" class="dropdown">
                <a href="#">
                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                    <span title="Payroll">Payroll</span>
                    <span class="float-right">
                    <i class="fa fa-angle-right right" aria-hidden="true"></i>
                    <i class="fa fa-angle-down down" aria-hidden="true"></i>
                </span>
                </a>
                <ul>
                    <li id="advanced-salary"><a href="{{ route('admin.advancedSalary.index') }}" title="Advanced">Advanced Salary</a></li>
                    <li id="salary"><a href="{{ route('admin.salary.index') }}" title="Salary">Salary</a></li>
                </ul>
            </li>

            <!-- pages -->
            {{--
            <li id="pages" class="dropdown">
                <a href="#">
                    <i class="icon ion-ios-clipboard"></i>
                    <span title="Pages">@lang('contents.pages')</span>
                    <span class="float-right">
                        <i class="icon ion-ios-arrow-forward right"></i>
                        <i class="icon ion-ios-arrow-down down"></i>
                    </span>
                </a>

                <ul>
                    <li id="about-ejobsbd"><a href="#" title="About EJobsBD">About EJobsBD</a></li>
                    <li id="faq"><a href="#" title="Faq">Faq</a></li>
                    <li id="privacy-policy"><a href="#" title="Privacy policy">Privacy policy</a></li>
                    <li id="disclaimer"><a href="#" title="Disclaimer - Terms and conditions">Disclaimer - Terms and conditions</a></li>
                </ul>
            </li>
            --}}
        </ul>

{{--        <h6>@lang('contents.user_stuff')</h6>--}}
{{--        <ul>--}}
{{--            <!-- permission -->--}}
{{--            <li id="permission">--}}
{{--                <a href="{{ route('admin.permission.index') }}">--}}
{{--                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>--}}
{{--                    <span title="User permission">@lang('contents.permission')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <!-- role -->--}}
{{--            <li id="role">--}}
{{--                <a href="{{ route('admin.role.index') }}">--}}
{{--                    <i class="fa fa-users" aria-hidden="true"></i>--}}
{{--                    <span title="User roles">@lang('contents.roles')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <!-- analysis tools -->--}}
{{--            <li id="analysis-tools">--}}
{{--                <a href="analysis-tools.html">--}}
{{--                    <i class="fa fa-area-chart" aria-hidden="true"></i>--}}
{{--                    <span title="Analysis tools">@lang('contents.analysis_tools')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <!-- sitemap -->--}}
{{--            <li id="sitemap">--}}
{{--                <a href="index.html">--}}
{{--                    <i class="fa fa-sitemap" aria-hidden="true"></i>--}}
{{--                    <span title="Site map">@lang('contents.site_map')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <!-- contact -->--}}
{{--            <li id="contact">--}}
{{--                <a href="contact.html">--}}
{{--                    <i class="fa fa-address-book" aria-hidden="true"></i>--}}
{{--                    <span title="Contact">@lang('contents.contact')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <!-- profile -->--}}
{{--            <li id="profile">--}}
{{--                <a href="{{ route('admin.account.index') }}">--}}
{{--                    <i class="fa fa-user" aria-hidden="true"></i>--}}
{{--                    <span title="Profile">@lang('contents.profile')</span>--}}
{{--                    <span class="badge float-right">30%</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <!-- settings -->--}}
{{--            <li id="settings">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-cog" aria-hidden="true"></i>--}}
{{--                    <span title="Settings">@lang('contents.settings')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <!-- document -->--}}
{{--            <li id="documents">--}}
{{--                <a href="#">--}}
{{--                    <i class="fa fa-book" aria-hidden="true"></i>--}}
{{--                    <span title="Documents">@lang('contents.documents')</span>--}}
{{--                    <span class="badge float-right">10</span>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--        </ul>--}}
    </nav>
</aside>
<!-- aside content end -->

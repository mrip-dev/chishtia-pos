@extends('admin.layouts.app')

@section('panel')


<style>
    .app-card {

        display: block;
        text-decoration: none;
        color: inherit;
        background-color: white !important;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .icon-wrap {

        transition: transform 0.3s ease;
    }

    .app-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        border-left: 5px solid #a0522d !important;
    }

    .app-card:hover .icon-wrap {
        transform: scale(1.08);
    }

    .app-card:hover h2.text-primary {
        color: var(--bs-primary-dark) !important;
    }
</style>


<div class="row gy-4">
    <div class="col-xxl-3 col-sm-4">
        <a href="{{ route('admin.product.index') }}" class="app-card border-0 rounded-3 shadow-sm d-block">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted fw-semibold mb-1 text-uppercase">Total Products</div>
                        <h2 class="display-5 fw-bold mb-0 text-primary">{{ $widget['total_product'] }}</h2>
                    </div>
                    <div class="icon-wrap bg-primary-subtle text-primary rounded-circle p-3">
                        <i class="las la-box fs-3"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-4">
        <a href="{{ route('admin.customer.index') }}" class="app-card border-0 rounded-3 shadow-sm d-block">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted fw-semibold mb-1 text-uppercase">Total Customers</div>
                        <h2 class="display-5 fw-bold mb-0 text-success">{{ $widget['total_customer'] }}</h2>
                    </div>
                    <div class="icon-wrap bg-success-subtle text-success rounded-circle p-3">
                        <i class="las la-users fs-3"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-4">
        <a href="{{ route('admin.manage_sale') }}" class="app-card border-0 rounded-3 shadow-sm d-block">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted fw-semibold mb-1 text-uppercase">Total Orders</div>
                        <h2 class="display-5 fw-bold mb-0 text-info">{{ $widget['total_orders'] }}</h2>
                    </div>
                    <div class="icon-wrap bg-info-subtle text-info rounded-circle p-3">
                        <i class="las la-user-friends fs-3"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-4">
        <a href="{{ route('admin.product.category.index') }}" class="app-card border-0 rounded-3 shadow-sm d-block">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted fw-semibold mb-1 text-uppercase">Total Categories</div>
                        <h2 class="display-5 fw-bold mb-0 text-warning">{{ $widget['total_category'] }}</h2>
                    </div>
                    <div class="icon-wrap bg-warning-subtle text-warning rounded-circle p-3">
                        <i class="lab la-buffer fs-3"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xxl-3 col-sm-4">
        <a href="{{ route('admin.manage_sale') }}" class="app-card border-0 rounded-3 shadow-sm d-block">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted fw-semibold mb-1 text-uppercase">Paid Orders</div>
                        <h2 class="display-5 fw-bold mb-0 text-success">{{ $widget['paid_orders'] }}</h2>
                    </div>
                    <div class="icon-wrap bg-success-subtle text-success rounded-circle p-3">
                        <i class="las la-check-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-4">
        <a href="{{ route('admin.manage_sale') }}" class="app-card border-0 rounded-3 shadow-sm d-block">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted fw-semibold mb-1 text-uppercase">Unpaid Orders</div>
                        <h2 class="display-5 fw-bold mb-0 text-danger">{{ $widget['unpaid_orders'] }}</h2>
                    </div>
                    <div class="icon-wrap bg-danger-subtle text-danger rounded-circle p-3">
                        <i class="las la-times-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>




@endsection

@push('script-lib')
<script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
<script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
<link type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('script')
<script>
    "use strict";

    const start = moment().subtract(14, 'days');
    const end = moment();

    const dateRangeOptions = {
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 15 Days': [moment().subtract(14, 'days'), moment()],
            'Last 30 Days': [moment().subtract(30, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
        },
        maxDate: moment()
    }

    const changeDatePickerText = (element, startDate, endDate) => {
        $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
    }

    let dwChart = barChart(
        document.querySelector("#psChartArea"),
        @json(__(gs('cur_text'))),
        [{
                name: 'Purchases',
                data: []
            },
            {
                name: 'Sales',
                data: []
            }
        ],
        [],
    );

    let sSrChart = lineChart(
        document.querySelector("#sSrChartArea"),
        [{
                name: "Sales Chart",
                data: []
            },
            {
                name: "Sales Return Chart",
                data: []
            }
        ],
        []
    );
    let pPrChart = lineChart(
        document.querySelector("#pPrChartArea"),
        [{
                name: "Purchases Chart",
                data: []
            },
            {
                name: "Purchases Return Chart",
                data: []
            }
        ],
        []
    );


    const purchaseSaleChart = (startDate, endDate) => {

        const data = {
            start_date: startDate.format('YYYY-MM-DD'),
            end_date: endDate.format('YYYY-MM-DD')
        }

        const url = @json(route('admin.chart.purchase.sale'));

        $.get(url, data,
            function(data, status) {
                if (status == 'success') {
                    dwChart.updateSeries(data.data);
                    dwChart.updateOptions({
                        xaxis: {
                            categories: data.created_on,
                        }
                    });
                }
            }
        );
    }

    const saleSaleReturnChart = (startDate, endDate) => {

        const data = {
            start_date: startDate.format('YYYY-MM-DD'),
            end_date: endDate.format('YYYY-MM-DD')
        }

        const url = @json(route('admin.chart.sales.return'));


        $.get(url, data,
            function(data, status) {
                if (status == 'success') {


                    sSrChart.updateSeries(data.data);
                    sSrChart.updateOptions({
                        xaxis: {
                            categories: data.created_on,
                        }
                    });
                }
            }
        );
    }

    const purchasePurchaseReturnChart = (startDate, endDate) => {

        const data = {
            start_date: startDate.format('YYYY-MM-DD'),
            end_date: endDate.format('YYYY-MM-DD')
        }

        const url = @json(route('admin.chart.purchases.return'));


        $.get(url, data,
            function(data, status) {
                if (status == 'success') {


                    pPrChart.updateSeries(data.data);
                    pPrChart.updateOptions({
                        xaxis: {
                            categories: data.created_on,
                        }
                    });
                }
            }
        );
    }



    $('#psDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#psDatePicker span', start, end));
    $('#saleSaleReturnDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#saleSaleReturnDatePicker span', start,
        end));
    $('#purchasePurchaseReturnDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#saleSaleReturnDatePicker span',
        start, end));

    changeDatePickerText('#psDatePicker span', start, end);
    changeDatePickerText('#saleSaleReturnDatePicker span', start, end);
    changeDatePickerText('#purchasePurchaseReturnDatePicker span', start, end);

    purchaseSaleChart(start, end);
    saleSaleReturnChart(start, end);
    purchasePurchaseReturnChart(start, end);

    $('#psDatePicker').on('apply.daterangepicker', (event, picker) => purchaseSaleChart(picker.startDate, picker.endDate));
    $('#saleSaleReturnDatePicker').on('apply.daterangepicker', (event, picker) => saleSaleReturnChart(picker.startDate, picker.endDate));
    $('#purchasePurchaseReturnDatePicker').on('apply.daterangepicker', (event, picker) => purchasePurchaseReturnChart(picker.startDate, picker
        .endDate));
</script>
@endpush
@push('style')
<style>
    .apexcharts-menu {
        min-width: 120px !important;
    }
</style>
@endpush
@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['total_product'] }}" title="Total Products" style="6" link="{{ route('admin.product.index') }}" icon="las la-box" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['total_customer'] }}" title="Total Customers" style="6" link="{{ route('admin.customer.index') }}" icon="las la-users" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['total_supplier'] }}" title="Total Suppliers" style="6" link="{{ route('admin.supplier.index') }}" icon="las la-user-friends" bg="purple" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['total_category'] }}" title="Total Categories" style="6" link="{{ route('admin.product.category.index') }}" icon="lab la-buffer" bg="warning" />
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

@extends('admin.layouts.app')
@section('panel')
    <x-permission_check permission="view all transactions">
        <x-admin.ui.widget.group.dashboard.trx :widget="$widget" />
    </x-permission_check>
    <div class="row  responsive-row">
        <div class="col-lg-6">
            <x-admin.ui.card class="h-100">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('User Statistics')</h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body class="text-center">
                    <div id="chart"></div>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
        <div class="col-lg-6">
            <x-admin.ui.card class="h-100">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('Revenue Statistics')</h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body>
                    <div id="revenueCharge"></div>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-permission_check :permission="['view user add money', 'view merchant withdraw', 'view agent add money', 'view agent withdraw']">
        <x-admin.ui.widget.group.dashboard.financial_overview :widget="$widget" />
    </x-permission_check>

    <x-permission_check permission="view agents">
        <x-admin.ui.widget.group.dashboard.agent :widget="$widget" />
    </x-permission_check>

    <x-permission_check permission="view merchants">
        <x-admin.ui.widget.group.dashboard.merchant :widget="$widget" />
    </x-permission_check>


    <div class="row gy-4 mb-4">
        <x-permission_check permission="view all transactions">
            <x-admin.other.dashboard_trx_chart />
        </x-permission_check>
        <div class="col-xl-4">
            <x-permission_check permission="view login history">
                <x-admin.other.dashboard_login_chart :userLogin=$userLogin />
            </x-permission_check>
        </div>
    </div>

    <x-permission_check permission="manage cron job">
        <x-admin.other.cron_modal />
    </x-permission_check>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush


@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush



@push('script')
    <script>
        "use strict";
        (function($) {

            //userStatistic chart 
            (function() {

                const labels = @json(array_keys($userStatistic));
                const data = @json(array_values($userStatistic));
                const total = parseInt("{{ array_sum(array_values($userStatistic)) }}");

                const legendLabels = labels.map((label, index) => {
                    const percent = parseFloat(((data[index] / total) * 100)).toFixed(0);
                    return `<div class=" d-flex  flex-column gap-1 text-dark  align-items-start mb-3 me-1"><span>${isNaN(percent) ? 0  : percent}%</span> <span>${toTitleCase(label)}</span> </div>`;
                });

                const options = {
                    series: data,
                    chart: {
                        type: 'donut',
                        height: 300,
                        width: '100%'
                    },
                    labels: labels,
                    dataLabels: {
                        enabled: false,
                    },
                    colors: ['#33c758', '#ff382e', '#ff9500', '#6338ff', '#00849b'],
                    legend: {
                        position: 'bottom',
                        markers: {
                            show: false // Hide the default markers
                        },
                        formatter: function(seriesName, opts) {
                            return legendLabels[opts.seriesIndex];
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            })();


            ////revenueStatistic chart  
            (function() {

                const labels = @json(array_keys($revenueStatistic));
                const data = @json(array_values($revenueStatistic));

                var options = {
                    series: [{
                        name: "Revenue Statistic",
                        data: data
                    }],
                    chart: {
                        type: 'bar',
                        height: 250
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            borderRadiusApplication: 'end',
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: labels.map(label => toTitleCase(label)),
                    },
                    tooltip: {
                        y: {
                            formatter: function(value, {
                                seriesIndex,
                                dataPointIndex,
                                w
                            }) {
                                let categoryLabel = w.config.xaxis.categories[dataPointIndex];
                                return `${categoryLabel}: ${`{{ gs('cur_sym') }}${parseFloat(value).toFixed(2)}`}`;
                            }
                        }
                    }

                };
                var chart = new ApexCharts(document.querySelector("#revenueCharge"), options);
                chart.render();
            })();


            $(".date-picker").flatpickr({
                mode: 'range',
                maxDate: new Date(),
            });

            function toTitleCase(str) {
                return str
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()) // Capitalize each word
                    .join(' ');
            }
        })(jQuery);
    </script>
@endpush

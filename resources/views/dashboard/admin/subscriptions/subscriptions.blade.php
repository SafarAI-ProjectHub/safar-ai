@extends('layouts_dashboard.main')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/dateRange.css') }}" rel="stylesheet" />
    <link href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Active Subscriptions List</h5>
            <div class="d-flex justify-content-end align-items-center mb-3">
                <label for="daterange" class="mr-2">Filter by Date:</label>
                <input type="text" name="daterange" id="daterange" class="form-control">
            </div>

            <div class="table-responsive">
                <table id="subscriptions-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Subscription ID</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Next Billing Time</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
        $(document).ready(function() {
            var start = moment().startOf('day');
            var end = moment().endOf('day');
            var label = 'All Dates';

            function cb(start, end, label) {
                if (label === 'All Dates') {
                    $('#daterange span').html('All Dates');
                    $('#daterange').val('');
                } else {
                    $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#daterange').val(start.format('MM/DD/YYYY h:mm A') + ' - ' + end.format(
                        'MM/DD/YYYY h:mm A'));
                }
                table.ajax.reload();
            }

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 30,
                ranges: {
                    'Today': [moment().startOf('day'), moment().endOf('day')],
                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days')
                        .endOf('day')
                    ],
                    'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                    'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'All Dates': [moment().subtract(10, 'years'), moment()]
                },
                locale: {
                    format: 'MM/DD/YYYY h:mm A',
                    cancelLabel: 'Clear'
                }
            }, cb);

            var table = $('#subscriptions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.subscriptions') }}',
                    data: function(d) {
                        d.daterange = $('#daterange').val();
                    }
                },
                columns: [{
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'subscription_id',
                        name: 'subscription_id'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'next_billing_time',
                        name: 'next_billing_time'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    }
                ]
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                cb(picker.startDate, picker.endDate, picker.chosenLabel);
            });

            // Initialize with the default date range
            cb(start, end, label);
        });
    </script>
@endsection

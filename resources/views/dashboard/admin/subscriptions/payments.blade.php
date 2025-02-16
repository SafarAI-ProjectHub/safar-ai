@extends('layouts_dashboard.main')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/dateRange.css') }}" rel="stylesheet" />
    <link href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Payments List</h5>
            <div class="d-flex justify-content-end align-items-center mb-3">
                <label for="daterange" class="mr-2">Filter by Date:</label>
                <input type="text" name="daterange" id="daterange" class="form-control">
            </div>

            <div class="table-responsive">
                <table id="payments-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Subscription</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Payment Type</th>
                            <th>Transaction Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
        $(document).ready(function() {
            var start = moment().subtract(29, 'days').startOf('day');
            var end = moment().endOf('day');
            var label = 'All Dates';

            function cb(start, end, label) {
                if (label === 'All Dates') {
                    $('#daterange span').html('All Dates');
                    $('#daterange').val('');
                } else {
                    $('#daterange span').html(
                        start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY')
                    );
                    $('#daterange').val(
                        start.format('MM/DD/YYYY h:mm A') + ' - ' + end.format('MM/DD/YYYY h:mm A')
                    );
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
                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                    'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                    'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ],
                    'All Dates': [moment().subtract(10, 'years'), moment()]
                },
                locale: {
                    format: 'MM/DD/YYYY h:mm A',
                    cancelLabel: 'Clear'
                }
            }, cb);

            var table = $('#payments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.payments') }}',
                    data: function(d) {
                        d.daterange = $('#daterange').val();
                    }
                },
                columns: [
                    { data: 'user_name', name: 'user_name' },
                    { data: 'subscription_name', name: 'subscription_name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'payment_type', name: 'payment_type' },
                    { data: 'transaction_date', name: 'transaction_date' }
                ],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy',  className: 'btn btn-outline-secondary' },
                    { extend: 'excel', className: 'btn btn-outline-secondary' },
                    { extend: 'pdf',   className: 'btn btn-outline-secondary' },
                    { extend: 'print', className: 'btn btn-outline-secondary' }
                ],
                lengthChange: false
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                cb(picker.startDate, picker.endDate, picker.chosenLabel);
            });

            cb(start, end, label);
        });
    </script>
@endsection

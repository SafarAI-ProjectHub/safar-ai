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
                            <th>Details</th> <!-- زر التفاصيل -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal لعرض تفاصيل الاشتراك -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detailsModalLabel">Subscription Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Subscription Type:</strong>
                    <p id="modal_subscription_type"></p>
                </div>
                <div class="col-md-6">
                    <strong>Product Name:</strong>
                    <p id="modal_product_name"></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <strong>Description:</strong>
                    <p id="modal_description"></p>
                </div>
                <div class="col-md-6">
                    <strong>Price:</strong>
                    <p id="modal_price"></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <strong>Features:</strong>
                    <ul id="modal_features" style="list-style: disc; margin-left: 20px;"></ul>
                </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables + Bootstrap4 JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Moment + Daterangepicker -->
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

            var table = $('#subscriptions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.subscriptions') }}',
                    data: function(d) {
                        d.daterange = $('#daterange').val();
                    }
                },
                columns: [
                    { data: 'user_name',          name: 'user_name' },
                    { data: 'subscription_id',    name: 'subscription_id' },
                    { data: 'status',             name: 'status' },
                    { data: 'start_date',         name: 'start_date' },
                    { data: 'next_billing_time',  name: 'next_billing_time' },
                    { data: 'payment_status',     name: 'payment_status' },
                    {
                        data: null,
                        name: 'details',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-info btn-sm view-details" data-id="${row.id}">View</button>`;
                        }
                    }
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-outline-secondary buttons-copy buttons-html5'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-secondary buttons-excel buttons-html5'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-secondary buttons-pdf buttons-html5'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary buttons-print'
                    }
                ],
                lengthChange: false
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                cb(picker.startDate, picker.endDate, picker.chosenLabel);
            });

            cb(start, end, label);

            $(document).on('click', '.view-details', function() {
                var rowData = table.row($(this).closest('tr')).data();
                if (!rowData) return;

                $('#modal_subscription_type').text(rowData.subscription_type ?? 'N/A');
                $('#modal_product_name').text(rowData.product_name ?? 'N/A');
                $('#modal_description').text(rowData.description ?? 'N/A');
                $('#modal_price').text(rowData.price ?? 'N/A');

                // فكّ مصفوفة features
                var features = rowData.features;
                $('#modal_features').empty();
                if (Array.isArray(features) && features.length > 0) {
                    features.forEach(function(item) {
                        $('#modal_features').append('<li>' + item + '</li>');
                    });
                } else {
                    $('#modal_features').append('<li>No features found</li>');
                }

                $('#detailsModal').modal('show');
            });
        });
    </script>
@endsection

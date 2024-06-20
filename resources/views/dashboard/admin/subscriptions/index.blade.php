@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Manage Subscriptions</h5>
            <div class="d-flex justify-content-end mb-3">
                <button id="addSubscriptionBtn" class="btn btn-primary">Add New Subscription</button>
            </div>
            <div class="table-responsive">
                <table id="subscriptions-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Duration (months)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Add/Edit Subscription -->
    <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subscriptionModalLabel">Add Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="subscriptionForm">
                        @csrf
                        <input type="hidden" id="subscription_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (months)</label>
                            <input type="number" class="form-control" id="duration" name="duration" required>
                        </div>
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-control" id="is_active" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="features" class="form-label">Features</label>
                            <textarea class="form-control" id="features" name="features"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#subscriptions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.subscriptions.index') }}',
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'duration',
                        name: 'duration'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary'
                    }
                ],
                columnDefs: [{
                    targets: 4,
                    width: '30%'
                }],
                lengthChange: false
            });

            // Handle Add Subscription button click
            $('#addSubscriptionBtn').on('click', function() {
                $('#subscriptionForm').trigger('reset');
                $('#subscription_id').val('');
                $('#subscriptionModalLabel').text('Add Subscription');
                $('#subscriptionModal').modal('show');
            });

            // Handle Edit button click
            $(document).on('click', '.edit-subscription', function() {
                var subscriptionId = $(this).data('id');
                $.get('/admin/subscriptions/' + subscriptionId + '/edit', function(data) {
                    $('#subscriptionModalLabel').text('Edit Subscription');
                    $('#subscription_id').val(data.id);
                    $('#name').val(data.name);
                    $('#description').val(data.description);
                    $('#price').val(data.price);
                    $('#duration').val(data.duration);
                    $('#is_active').val(data.is_active ? 1 : 0);
                    $('#features').val(data.features ? JSON.stringify(data.features) : '');
                    $('#subscriptionModal').modal('show');
                });
            });

            // Handle form submission
            $('#subscriptionForm').on('submit', function(e) {
                e.preventDefault();
                var subscriptionId = $('#subscription_id').val();
                var url = subscriptionId ? '/admin/subscriptions/' + subscriptionId :
                '/admin/subscriptions';
                var method = subscriptionId ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#subscriptionModal').modal('hide');
                        Swal.fire('Success!', response.success, 'success');
                        table.ajax.reload();
                    },
                    error: function(response) {
                        Swal.fire('Error!', 'There was an error saving the subscription.',
                            'error');
                    }
                });
            });

            // Handle Delete button click
            $(document).on('click', '.delete-subscription', function() {
                var subscriptionId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/subscriptions/' + subscriptionId,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.success, 'success');
                                table.ajax.reload();
                            },
                            error: function(response) {
                                Swal.fire('Error!',
                                    'There was an error deleting the subscription.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Handle Activate/Deactivate switch
            $(document).on('change', '.activate-subscription', function() {
                var subscriptionId = $(this).data('id');
                var isActive = $(this).is(':checked');
                $.ajax({
                    url: '/admin/subscriptions/toggle-active/' + subscriptionId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        is_active: isActive
                    },
                    success: function(response) {
                        table.ajax.reload(null,
                        false); // Reload the table without resetting pagination
                        Swal.fire('Success!', 'The subscription has been ' + (isActive ?
                            'activated' : 'deactivated') + '.', 'success');
                    },
                    error: function(response) {
                        Swal.fire('Error!',
                            'There was an error updating the subscription status.', 'error');
                    }
                });
            });
        });
    </script>
@endsection

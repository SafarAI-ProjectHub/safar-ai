@extends('layouts_dashboard.main')
<style>
    .form-switch input[type="checkbox"] {
        width: 40px;
        height: 20px;
    }
</style>
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
                            <th>Name (Admin)</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Add Subscription -->
    <div class="modal fade" id="addSubscriptionModal" tabindex="-1" aria-labelledby="addSubscriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubscriptionModalLabel">Add Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSubscriptionForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
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
                            <label for="features" class="form-label">Features</label>
                            <textarea class="form-control" id="features" name="features"></textarea>
                            <small class="form-text text-muted">Please enter each feature on a new line.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Subscription -->
    <div class="modal fade" id="editSubscriptionModal" tabindex="-1" aria-labelledby="editSubscriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubscriptionModalLabel">Edit Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSubscriptionForm">
                        @csrf
                        <input type="hidden" id="edit_subscription_id" name="subscription_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" disabled required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="edit_price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_features" class="form-label">Features</label>
                            <textarea class="form-control" id="edit_features" name="features"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Subscription</button>
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
                        data: 'adminName',
                        name: 'adminName'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
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
                        data: 'is_active',
                        name: 'is_active'
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
                    width: '10%'

                }],
                lengthChange: false
            });

            // Handle Add Subscription button click
            $('#addSubscriptionBtn').on('click', function() {
                $('#addSubscriptionForm').trigger('reset');
                $('#addSubscriptionModal').modal('show');
            });

            // Handle Edit button click
            $(document).on('click', '.edit-subscription', function() {
                var subscriptionId = $(this).data('id');
                $.get('/admin/subscriptions/' + subscriptionId + '/edit', function(data) {
                    $('#editSubscriptionModalLabel').text('Edit Subscription');
                    $('#edit_name').val(data.product_name);
                    $('#edit_subscription_id').val(data.id);
                    $('#edit_description').val(data.description);
                    $('#edit_price').val(data.price);
                    $('#edit_features').val(data.features ? data.features.join('\n') : '');
                    $('#editSubscriptionModal').modal('show');
                });
            });

            // Handle Add form submission
            $('#addSubscriptionForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/admin/subscriptions',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addSubscriptionModal').modal('hide');
                        Swal.fire('Success!', response.success, 'success');
                        table.ajax.reload();
                    },
                    error: function(response) {
                        Swal.fire('Error!', 'There was an error saving the subscription.',
                            'error');
                    }
                });
            });

            // Handle Edit form submission
            $('#editSubscriptionForm').on('submit', function(e) {
                e.preventDefault();
                var subscriptionId = $('#edit_subscription_id').val();
                $.ajax({
                    url: '/admin/subscriptions/' + subscriptionId,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editSubscriptionModal').modal('hide');
                        Swal.fire('Success!', response.success, 'success');
                        table.ajax.reload();
                    },
                    error: function(response) {
                        Swal.fire('Error!', 'There was an error updating the subscription.',
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
                            false);

                        Swal.fire(
                            'Success!',
                            'The test has been ' + (isActive ? 'activated' :
                                'deactivated') + '.',
                            'success'
                        );

                    },
                    error: function(response) {
                        table.ajax.reload(null,
                            false);
                        console.log(response.responseJSON);
                        Swal.fire(
                            'Error!',
                            response.responseJSON.message,
                            'error'
                        );
                    }
                });
            });
        });
    </script>
@endsection

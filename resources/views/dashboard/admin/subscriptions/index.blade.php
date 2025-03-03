@extends('layouts_dashboard.main')

<style>
    /* يمكنك تعديل الألوان والأحجام بما يناسبك */
    .form-switch .form-check-input {
        width: 40px;
        height: 20px;
        cursor: pointer;
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
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Subscription Type</th>
                        <th>Status</th>
                        <th>Actions</th> <!-- عمود للـ Edit/Delete -->
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
                        <label for="subscription_type" class="form-label">Subscription Type</label>
                        <select class="form-control" id="subscription_type" name="subscription_type" required>
                            <option value="yolo">YOLO</option>
                            <option value="solo">SOLO</option>
                            <option value="tolo">TOLO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="features" class="form-label">Features</label>
                        <textarea class="form-control" id="features" name="features"></textarea>
                        <small class="form-text text-muted">Enter each feature on a new line.</small>
                    </div>

                    <!-- اختيار الحالة -->
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
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
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="edit_id" name="id">

                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
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
                        <label for="edit_subscription_type" class="form-label">Subscription Type</label>
                        <select class="form-control" id="edit_subscription_type" name="subscription_type" required>
                            <option value="yolo">YOLO</option>
                            <option value="solo">SOLO</option>
                            <option value="tolo">TOLO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_features" class="form-label">Features</label>
                        <textarea class="form-control" id="edit_features" name="features"></textarea>
                        <small class="form-text text-muted">Enter each feature on a new line.</small>
                    </div>

                    <!-- اختيار الحالة عند التعديل -->
                    <div class="mb-3">
                        <label for="edit_is_active" class="form-label">Status</label>
                        <select class="form-control" id="edit_is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Subscription</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- تأكد من تحميل SweetAlert2 & DataTables في الـlayout -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#subscriptions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.subscriptions.index") }}',
            columns: [
                { data: 'product_name',       name: 'product_name' },
                { data: 'description',        name: 'description' },
                { data: 'price',              name: 'price' },
                { data: 'subscription_type',  name: 'subscription_type' },
                {
                    data: 'is_active',
                    name: 'is_active',
                },
                {
                    data: null,
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-warning editBtn" data-id="${row.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.id}">Delete</button>
                        `;
                    }
                }
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy',  className: 'btn btn-outline-secondary' },
                { extend: 'excel', className: 'btn btn-outline-secondary' },
                { extend: 'pdf',   className: 'btn btn-outline-secondary' },
                { extend: 'print', className: 'btn btn-outline-secondary' }
            ],
            order: [[0, 'asc']],
            lengthChange: false
        });

        // Show modal for adding a subscription
        $('#addSubscriptionBtn').on('click', function() {
            $('#addSubscriptionForm').trigger('reset');
            $('#addSubscriptionModal').modal('show');
        });

        // Save (Add) subscription
        $('#addSubscriptionForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("admin.subscriptions.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#addSubscriptionModal').modal('hide');
                    Swal.fire('Success!', response.success, 'success');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    console.log(xhr);
                    Swal.fire('Error!', 'There was an error saving the subscription.', 'error');
                }
            });
        });

        // Click "Edit" => تعبئة النموذج وعرضه
        $(document).on('click', '.editBtn', function() {
            var subscriptionId = $(this).data('id');
            $.ajax({
                url: '/admin/subscriptions/' + subscriptionId,
                method: 'GET',
                success: function(response) {
                    const sub = response.subscription;
                    $('#edit_id').val(sub.id);
                    $('#edit_name').val(sub.product_name);
                    $('#edit_description').val(sub.description);
                    $('#edit_price').val(sub.price);
                    $('#edit_subscription_type').val(sub.subscription_type);

                    if (Array.isArray(sub.features)) {
                        $('#edit_features').val(sub.features.join("\n"));
                    } else {
                        $('#edit_features').val('');
                    }

                    // تعيين قيمة الـ is_active في السيلكت
                    $('#edit_is_active').val(sub.is_active ? '1' : '0');

                    $('#editSubscriptionModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr);
                    Swal.fire('Error!', 'Failed to fetch subscription data.', 'error');
                }
            });
        });

        // Submit edit form => update
        $('#editSubscriptionForm').on('submit', function(e) {
            e.preventDefault();
            var subscriptionId = $('#edit_id').val();
            $.ajax({
                url: '/admin/subscriptions/' + subscriptionId,
                method: 'POST', // نظرًا لاستخدامنا hidden input: _method=PUT
                data: $(this).serialize(),
                success: function(response) {
                    $('#editSubscriptionModal').modal('hide');
                    Swal.fire('Success!', response.success, 'success');
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.log(xhr);
                    Swal.fire('Error!', 'There was an error updating the subscription.', 'error');
                }
            });
        });

        // Click "Delete"
        $(document).on('click', '.deleteBtn', function() {
            var subscriptionId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/subscriptions/' + subscriptionId,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.success, 'success');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.log(xhr);
                            Swal.fire('Error!', 'Failed to delete the subscription.', 'error');
                        }
                    });
                }
            });
        });

        // عند الضغط على الـCheckbox (Toggle)
        $(document).on('change', '.activate-subscription', function() {
            var subscriptionId = $(this).data('id');
            $.ajax({
                url: '/admin/subscriptions/toggle-active/' + subscriptionId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload(null, false);
                    if (response.success) {
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Warning', response.message, 'warning');
                    }
                },
                error: function(xhr) {
                    table.ajax.reload(null, false);
                    Swal.fire('Error!', 'Failed to toggle status.', 'error');
                }
            });
        });
    });
</script>
@endsection

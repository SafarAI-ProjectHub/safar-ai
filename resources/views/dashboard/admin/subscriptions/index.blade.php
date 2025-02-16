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
                        <th>Subscription Type</th>
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
                    <button type="submit" class="btn btn-primary">Save Subscription</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- تأكد من تحميل مكتبة SweetAlert2 وDatatables في layout الرئيسي -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var table = $('#subscriptions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.subscriptions.index") }}', 
            columns: [
                {
                    data: 'adminName',
                    name: 'adminName',
                    defaultContent: 'N/A',
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
                    data: 'subscription_type',
                    name: 'subscription_type'
                },
                {
                    data: 'is_active',
                    name: 'is_active'
                }
                
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy',  className: 'btn btn-outline-secondary' },
                { extend: 'excel', className: 'btn btn-outline-secondary' },
                { extend: 'pdf',   className: 'btn btn-outline-secondary' },
                { extend: 'print', className: 'btn btn-outline-secondary' }
            ],
            columnDefs: [
                {
                    targets: 5,
                    width: '10%'
                }
            ],
            lengthChange: false
        });

        // إظهار المودال الخاص بالإضافة
        $('#addSubscriptionBtn').on('click', function() {
            $('#addSubscriptionForm').trigger('reset');
            $('#addSubscriptionModal').modal('show');
        });

        // حفظ الاشتراك (إضافة جديد)
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

        // Toggle Active
        $(document).on('change', '.activate-subscription', function() {
            var subscriptionId = $(this).data('id');
            var isActive = $(this).is(':checked');
            $.ajax({
                url: '/admin/subscriptions/toggle-active/' + subscriptionId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_active: isActive
                },
                success: function(response) {
                    table.ajax.reload(null, false);
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            response.message,
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Warning',
                            response.message,
                            'warning'
                        );
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

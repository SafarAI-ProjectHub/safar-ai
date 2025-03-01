@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    {{-- إضافة رابط مكتبة SweetAlert2 CSS إن لم تكن مضافة مسبقًا --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Offers List</h5>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addOfferModal">Add New
                    Offer</button>
            </div>
            <div class="table-responsive">
                <table id="offers-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Offer Name</th>
                            <th>Offer Title</th>
                            <th>Offer Description</th>
                            <th>Call to Action Type</th>
                            <th>Call to Action Value</th>
                            <th>Active Status</th>
                            <th>Offer Start Date</th>
                            <th>Offer End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Offer Modal -->
    <div class="modal fade" id="addOfferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <form id="addOfferForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addOfferModalLabel">Add New Offer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Offer Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Offer Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Offer Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="action_type" class="form-label">Call to Action Type</label>
                            <select class="form-select" id="action_type" name="action_type" required>
                                <option value="" disabled selected>Select Action Type</option>
                                <option value="link">Navigate to URL</option>
                                <option value="email">Send Email</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="action_value" class="form-label">Call to Action Value</label>
                            <input type="text" class="form-control" id="action_value" name="action_value" required>
                        </div>
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Active Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" checked>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Offer Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Offer End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label for="background_image" class="form-label">Background Image</label>
                            <input type="file" class="filepond" id="background_image" name="background_image">
                        </div>
                        <div class="mb-3">
                            <label for="alignment" class="form-label">Alignment</label>
                            <select class="form-select" id="alignment" name="alignment" required>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                                <option value="center">Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Offer Modal -->
    <div class="modal fade" id="editOfferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <form id="editOfferForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOfferModalLabel">Edit Offer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_offer_id" name="offer_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Offer Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Offer Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Offer Description</label>
                            <textarea class="form-control" id="edit_description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_action_type" class="form-label">Call to Action Type</label>
                            <select class="form-select" id="edit_action_type" name="action_type" required>
                                <option value="" disabled selected>Select Action Type</option>
                                <option value="link">Navigate to URL</option>
                                <option value="email">Send Email</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_action_value" class="form-label">Call to Action Value</label>
                            <input type="text" class="form-control" id="edit_action_value" name="action_value"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_is_active" class="form-label">Active Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                    value="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_start_date" class="form-label">Offer Start Date</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="edit_end_date" class="form-label">Offer End Date</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label for="edit_background_image" class="form-label">Background Image</label>
                            <input type="file" class="filepond" id="edit_background_image" name="background_image">
                        </div>
                        <div class="mb-3">
                            <label for="edit_alignment" class="form-label">Alignment</label>
                            <select class="form-select" id="edit_alignment" name="alignment" required>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                                <option value="center">Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- مكتبة SweetAlert2 JS إن لم تكن مضافة مسبقًا --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#offers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('offers.index') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'title', name: 'title' },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            return data.length > 50 ? data.substr(0, 50) + '...' : data;
                        }
                    },
                    { data: 'action_type', name: 'action_type' },
                    { data: 'action_value', name: 'action_value' },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        render: function(data, type, row) {
                            return `
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-complete" type="checkbox" data-id="${row.id}" ${data ? 'checked' : ''}>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        render: function(data) {
                            if (data === null) {
                                return 'No start date';
                            }
                            return moment(data).format('YYYY-MM-DD');
                        }
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        render: function(data) {
                            if (data === null) {
                                return 'No end date';
                            }
                            return moment(data).format('YYYY-MM-DD');
                        }
                    },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ]
            });

            // Initialize FilePond
            FilePond.registerPlugin(FilePondPluginFileValidateSize, FilePondPluginFileValidateType);

            var createpond = FilePond.create(document.querySelector('input#background_image'), {
                allowFileTypeValidation: true,
                acceptedFileTypes: ['image/*'],
                fileValidateTypeLabelExpectedTypes: 'Expected file type: Image'
            });

            var editpond = FilePond.create(document.querySelector('input#edit_background_image'), {
                allowFileTypeValidation: true,
                acceptedFileTypes: ['image/*'],
                fileValidateTypeLabelExpectedTypes: 'Expected file type: Image'
            });

            // دالة عرض SweetAlert (نجاح أو خطأ) بشكل مبسط
            function showAlertS(type, message) {
                let icon = (type === 'success') ? 'success' : 'error';
                Swal.fire({
                    icon: icon,
                    title: (type.charAt(0).toUpperCase() + type.slice(1)),
                    text: message,
                    showConfirmButton: false,
                    timer: 3000
                });
            }

            // Validate dates (لا يزال تنبيه عادي، بالإمكان تحويله لسويت أليرت لو أردت)
            function validateDates(startDateInput, endDateInput) {
                var startDate = moment(startDateInput.val());
                var endDate = moment(endDateInput.val());
                var today = moment().startOf('day');

                if (startDate.isBefore(today)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date',
                        text: 'Start date must be at least today.',
                        confirmButtonText: 'OK'
                    });
                    startDateInput.val('');
                    return false;
                }

                if (endDate.isValid() && endDate.isSameOrBefore(startDate)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date',
                        text: 'End date must be at least one day after the start date.',
                        confirmButtonText: 'OK'
                    });
                    endDateInput.val(startDate.add(1, 'days').format('YYYY-MM-DD'));
                    return false;
                }

                return true;
            }

            // Attach date validation
            $('#start_date, #edit_start_date').on('change', function() {
                var startDateInput = $(this);
                var endDateInput = (startDateInput.attr('id') === 'start_date')
                    ? $('#end_date')
                    : $('#edit_end_date');
                validateDates(startDateInput, endDateInput);
            });

            $('#end_date, #edit_end_date').on('change', function() {
                var endDateInput = $(this);
                var startDateInput = (endDateInput.attr('id') === 'end_date')
                    ? $('#start_date')
                    : $('#edit_start_date');
                validateDates(startDateInput, endDateInput);
            });

            // Handle add offer form submission
            $('#addOfferForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var newform = new FormData();

                formData.forEach((value, key) => {
                    if (key !== 'background_image') {
                        newform.append(key, value);
                    }
                });

                // التحقق من رفع صورة
                if (createpond.getFiles().length > 0) {
                    var file = createpond.getFile().file;
                    newform.append('background_image', file);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Image Selected',
                        text: 'Please select a background image.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('offers.store') }}',
                    method: 'POST',
                    data: newform,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#addOfferModal').modal('hide');
                        table.ajax.reload();
                        showAlertS('success', 'Offer added successfully!');
                    },
                    error: function(xhr) {
                        $('#addOfferModal').modal('hide');
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            showAlertS('danger', 'Error: ' + xhr.responseJSON.message);
                        } else {
                            showAlertS('danger', 'Error adding offer');
                        }
                    }
                });
            });

            // Handle edit offer button click
            $('#offers-table').on('click', '.edit-offer', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '/admin/offers/' + id,
                    method: 'GET',
                    success: function(response) {
                        $('#edit_offer_id').val(response.id);
                        $('#edit_name').val(response.name);
                        $('#edit_title').val(response.title);
                        $('#edit_description').val(response.description);
                        $('#edit_action_type').val(response.action_type);
                        $('#edit_action_value').val(response.action_value);
                        $('#edit_is_active').prop('checked', response.is_active);
                        $('#edit_start_date').val(response.start_date ? moment(response.start_date).format('YYYY-MM-DD') : '');
                        $('#edit_end_date').val(response.end_date ? moment(response.end_date).format('YYYY-MM-DD') : '');
                        $('#edit_alignment').val(response.alignment);
                        $('#editOfferModal').modal('show');
                    }
                });
            });

            // Handle edit offer form submission
            $('#editOfferForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#edit_offer_id').val();
                var formData = new FormData(this);
                var newform = new FormData();

                formData.forEach((value, key) => {
                    if (key !== 'background_image') {
                        newform.append(key, value);
                    }
                });

                // في حال تم رفع صورة جديدة
                if (editpond.getFiles().length > 0) {
                    var file = editpond.getFile().file;
                    newform.append('background_image', file);
                }

                $.ajax({
                    url: '/admin/offers/' + id + '/update',
                    method: 'POST',
                    data: newform,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#editOfferModal').modal('hide');
                        table.ajax.reload();
                        showAlertS('success', 'Offer updated successfully!');
                    },
                    error: function(xhr) {
                        $('#editOfferModal').modal('hide');
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            showAlertS('danger', 'Error: ' + xhr.responseJSON.message);
                        } else {
                            showAlertS('danger', 'Error updating offer');
                        }
                    }
                });
            });

            // Handle delete offer button click
            $('#offers-table').on('click', '.delete-offer', function() {
                var id = $(this).data('id');

                // استخدام SweetAlert2 للتأكيد
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this offer!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/offers/' + id,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                table.ajax.reload();
                                showAlertS('success', 'Offer deleted successfully!');
                            },
                            error: function() {
                                showAlertS('danger', 'Error deleting offer');
                            }
                        });
                    }
                });
            });

            // Handle toggle offer status button click
            $('#offers-table').on('click', '.toggle-complete', function() {
                var id = $(this).data('id');
                var isActive = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/admin/offers/' + id + '/toggle',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_active: isActive
                    },
                    success: function() {
                        table.ajax.reload();
                        showAlertS('success', 'Offer status updated successfully!');
                    },
                    error: function() {
                        showAlertS('danger', 'Error updating offer status');
                    }
                });
            });

            // Change placeholder based on action type
            $('#action_type, #edit_action_type').change(function() {
                var selectedType = $(this).val();
                var actionValueField = $(this).attr('id') === 'action_type'
                    ? '#action_value'
                    : '#edit_action_value';

                if (selectedType === 'email') {
                    $(actionValueField).attr('placeholder', 'Enter email address');
                } else if (selectedType === 'link') {
                    $(actionValueField).attr('placeholder', 'Enter URL (e.g., https://example.com)');
                } else {
                    $(actionValueField).attr('placeholder', '');
                }
            });
        });
    </script>
@endsection

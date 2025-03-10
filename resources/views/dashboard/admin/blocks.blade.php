@extends('layouts_dashboard.main')

@section('styles')
    <!-- يمكنك إضافة أي ستايلات إضافية هنا مثل DataTables أو غيره -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Blocks for Course: {{ $course->title }}</h5>
        <div class="d-flex justify-content-end mb-3 gap-2">
            <!-- زر الإضافة -->
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBlockModal">
                Add New Block
            </button>
        </div>

        <div class="table-responsive">
            <table id="blocks-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Position</th>
                        <th>Moodle Section ID</th>
                        <th>Visibility</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add Block Modal -->
<div class="modal fade" id="addBlockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="addBlockForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Block</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- نخفي حقل course_id تلقائيًا كي لا يعدله المستخدم -->
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label">Block Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>

                    <!-- Position -->
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="number" class="form-control" name="position" value="1" required>
                    </div>

                    <!-- Moodle Section ID -->
                    <div class="mb-3">
                        <label class="form-label">Moodle Section ID</label>
                        <input type="number" class="form-control" name="moodle_section_id">
                    </div>

                    <!-- Visibility -->
                    <div class="mb-3">
                        <label class="form-label">Visibility</label>
                        <select class="form-select" name="visibility" required>
                            <option value="1" selected>Visible</option>
                            <option value="0">Hidden</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Block</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Block Modal -->
<div class="modal fade" id="editBlockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editBlockForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Block</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- حقل مخفي يضم الـ block_id -->
                    <input type="hidden" id="editBlockId" name="block_id">

                    <div class="mb-3">
                        <label class="form-label">Block Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="number" class="form-control" id="editPosition" name="position" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Moodle Section ID</label>
                        <input type="number" class="form-control" id="editMoodleSectionId" name="moodle_section_id">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Visibility</label>
                        <select class="form-select" id="editVisibility" name="visibility" required>
                            <option value="1">Visible</option>
                            <option value="0">Hidden</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Block</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- حقل مخفي لحذف البلوكات -->
<input type="hidden" id="delete_source" value="{{ route('admin.blocks.delete', ['id' => 0]) }}">

@endsection

@section('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function() {
    let courseId = "{{ $course->id }}";

    // إعداد DataTable
    let table = $('#blocks-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.blocks.getBlocks") }}',
            data: {
                course_id: courseId
            }
        },
        columns: [
            { data: 'id',                name: 'id' },
            { data: 'name',              name: 'name' },
            { data: 'description',       name: 'description' },
            { data: 'position',          name: 'position' },
            { data: 'moodle_section_id', name: 'moodle_section_id' },
            { data: 'visibility',        name: 'visibility',
              render: function(data) {
                  return data == 1 ? 'Visible' : 'Hidden';
              }
            },
            { data: 'actions',           name: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            { visible: false, targets: 0 }
        ]
    });

    // إضافة بلوك جديد
    $('#addBlockForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize(); // لعدم وجود رفع صور هنا

        $.ajax({
            url: '{{ route("admin.blocks.store") }}',
            method: 'POST',
            data: formData,
            success: function(resp) {
                $('#addBlockModal').modal('hide');
                table.ajax.reload();
                showAlert('success', resp.success || 'Block created!', 'bxs-check-circle');
                $('#addBlockForm')[0].reset();
            },
            error: function() {
                showAlert('danger', 'Error adding block', 'bxs-message-square-x');
            }
        });
    });

    // فتح مودال التعديل
    $(document).on('click', '.edit-btn', function() {
        let blockId = $(this).data('id');
        $.ajax({
            url: '/admin/blocks/edit/' + blockId,
            method: 'GET',
            success: function(block) {
                $('#editBlockId').val(block.id);
                $('#editName').val(block.name);
                $('#editDescription').val(block.description);
                $('#editPosition').val(block.position);
                $('#editMoodleSectionId').val(block.moodle_section_id);
                $('#editVisibility').val(block.visibility);

                $('#editBlockModal').modal('show');
            },
            error: function() {
                showAlert('danger', 'Error fetching block info', 'bxs-message-square-x');
            }
        });
    });

    // حفظ التعديلات
    $('#editBlockForm').on('submit', function(e) {
        e.preventDefault();
        let blockId  = $('#editBlockId').val();
        let formData = $(this).serialize();

        $.ajax({
            url: '/admin/blocks/update/' + blockId,
            method: 'POST',
            data: formData,
            success: function(resp) {
                $('#editBlockModal').modal('hide');
                table.ajax.reload();
                showAlert('success', resp.success || 'Block updated!', 'bxs-check-circle');
            },
            error: function() {
                showAlert('danger', 'Error updating block', 'bxs-message-square-x');
            }
        });
    });

    // حذف بلوك
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let baseUrl = $('#delete_source').val(); // .../delete/0
        let deleteUrl = baseUrl.replace('/0', '/' + id);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                $.ajax({
                    url: deleteUrl,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success"
                            }).then(() => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: response.message,
                                icon: "error"
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error",
                            text: xhr.responseJSON?.message || "Unknown error",
                            icon: "error"
                        });
                    }
                });
            }
        });
    });

    // دالة تنبيه
    function showAlert(type, message, icon) {
        let alertHtml = `
          <div class="alert alert-${type} border-0 bg-${type} alert-dismissible fade show py-2 position-fixed top-0 end-0 m-3" role="alert">
            <div class="d-flex align-items-center">
              <div class="font-35 text-white">
                <i class="bx ${icon}"></i>
              </div>
              <div class="ms-3">
                <h6 class="mb-0 text-white">${type.charAt(0).toUpperCase() + type.slice(1)}</h6>
                <div class="text-white">${message}</div>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => {
            $('.alert').alert('close');
        }, 4000);
    }
});
</script>
@endsection

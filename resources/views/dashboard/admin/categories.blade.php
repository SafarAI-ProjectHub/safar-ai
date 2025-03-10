@extends('layouts_dashboard.main')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h4>Categories List</h4>
        <div class="d-flex justify-content-end mb-3 gap-2">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
            <a href="{{ route('admin.course_categories.sync') }}" class="btn btn-sm btn-success">Sync from Moodle</a>
        </div>

        <div class="table-responsive">
            <table id="categories-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Age Group</th>
                        <th>General Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="addCategoryForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- name -->
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <!-- parent_id -->
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_id">
                            <option value="">No Parent</option>
                            @foreach(\App\Models\CourseCategory::all() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- age_group -->
                    <div class="mb-3">
                        <label class="form-label">Age Group</label>
                        <select class="form-select" name="age_group">
                            <option value="">- None -</option>
                            <option value="6-10">6-10</option>
                            <option value="10-14">10-14</option>
                            <option value="14-18">14-18</option>
                            <option value="18+">18+</option>
                        </select>
                    </div>
                    <!-- general_category -->
                    <div class="mb-3">
                        <label class="form-label">General Category</label>
                        <select class="form-select" name="general_category">
                            <option value="">- None -</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Science">Science</option>
                            <option value="Programming">Programming</option>
                            <option value="Arts">Arts</option>
                            <option value="Languages">Languages</option>
                            <option value="Business">Business</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editCategoryForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="editCategoryId">
                    <!-- name -->
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <!-- parent_id -->
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_id" id="editParentId">
                            <option value="">No Parent</option>
                            @foreach(\App\Models\CourseCategory::all() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- age_group -->
                    <div class="mb-3">
                        <label class="form-label">Age Group</label>
                        <select class="form-select" name="age_group" id="editAgeGroup">
                            <option value="">- None -</option>
                            <option value="6-10">6-10</option>
                            <option value="10-14">10-14</option>
                            <option value="14-18">14-18</option>
                            <option value="18+">18+</option>
                        </select>
                    </div>
                    <!-- general_category -->
                    <div class="mb-3">
                        <label class="form-label">General Category</label>
                        <select class="form-select" name="general_category" id="editGeneralCategory">
                            <option value="">- None -</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Science">Science</option>
                            <option value="Programming">Programming</option>
                            <option value="Arts">Arts</option>
                            <option value="Languages">Languages</option>
                            <option value="Business">Business</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- حقل مخفي لمساعدتنا في حذف التصنيف -->
<input type="hidden" id="delete_category_url" value="{{ route('admin.course_categories.destroy', ['category'=>'0']) }}">

@endsection

@section('scripts')
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){

    // DataTable للتصنيفات
    let table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.course_categories.getCategories") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'parent_name', name: 'parent_id' },
            { data: 'age_group', name: 'age_group', defaultContent: '-' },
            { data: 'general_category', name: 'general_category', defaultContent: '-' },
            { data: 'actions', name: 'actions', orderable:false, searchable:false },
        ],
        dom: 'Bfrtip',
        buttons: ['copy','excel','pdf','print'],
        lengthChange: false
    });

    // إضافة تصنيف
    $('#addCategoryForm').on('submit', function(e){
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: '{{ route("admin.course_categories.store") }}',
            method: 'POST',
            data: formData,
            success: function(resp){
                if(resp.success){
                    $('#addCategoryModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', resp.message);
                    $('#addCategoryForm')[0].reset();
                } else {
                    showAlert('danger','Error adding category');
                }
            },
            error: function(){
                showAlert('danger','Error adding category');
            }
        });
    });

    $('#addCategoryModal').on('hidden.bs.modal', function(){
        $('#addCategoryForm')[0].reset();
    });

    // فتح مودال التعديل وجلب البيانات
    $(document).on('click','.editBtn', function(){
        let catId = $(this).data('id');
        $.ajax({
            url: '/admin/course_categories/edit/'+catId,
            method: 'GET',
            success: function(category){
                $('#editCategoryId').val(category.id);
                $('#editName').val(category.name);
                $('#editParentId').val(category.parent_id || '');
                $('#editAgeGroup').val(category.age_group || '');
                $('#editGeneralCategory').val(category.general_category || '');

                $('#editCategoryModal').modal('show');
            },
            error: function(){
                showAlert('danger','Error fetching category data');
            }
        });
    });

    // حفظ التعديل
    $('#editCategoryForm').on('submit', function(e){
        e.preventDefault();
        let catId = $('#editCategoryId').val();
        let formData = $(this).serialize();
        $.ajax({
            url: '/admin/course_categories/update/'+catId,
            method: 'POST',
            data: formData,
            success: function(resp){
                if(resp.success){
                    $('#editCategoryModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', resp.message);
                } else {
                    showAlert('danger','Error updating category');
                }
            },
            error: function(){
                showAlert('danger','Error updating category');
            }
        });
    });

    $('#editCategoryModal').on('hidden.bs.modal', function(){
        $('#editCategoryForm')[0].reset();
    });

    // حذف التصنيف
    $(document).on('click','.deleteBtn', function(){
        let catId   = $(this).data('id');
        let baseUrl = $('#delete_category_url').val(); // .../delete/0
        let delUrl  = baseUrl.replace('/0','/'+catId);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url: delUrl,
                    method: 'DELETE',
                    success: function(resp){
                        if(resp.success){
                            Swal.fire('Deleted!', resp.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error','Something went wrong','error');
                        }
                    },
                    error: function(){
                        Swal.fire('Error','Error deleting category','error');
                    }
                });
            }
        });
    });

    // دالة التنبيه
    function showAlert(type, message){
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" 
                 style="z-index:9999" role="alert">
                <strong>${type.toUpperCase()}!</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(()=>{
            $('.alert').alert('close');
        },4000);
    }

});
</script>
@endsection

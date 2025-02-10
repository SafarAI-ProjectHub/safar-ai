@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endsection

@section('content')
    <div class="container mt-5">
        <div class="card p-1">
            <div class="card-header text-white">
                <div class="d-flex justify-content-between mb-3">
                    <h2>YouTube Videos List</h2>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Add
                        Video</button>
                </div>
            </div>
            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select id="age-group-filter" class="form-control">
                            <option value="">Select Age Group</option>
                            @foreach ($ageGroups as $ageGroup)
                                <option value="{{ $ageGroup }}">{{ $ageGroup }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <table id="youtube-videos-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>URL</th>
                            <th>Age Group</th>
                            <th>View Count</th>
                            <th>Like Count</th>
                            <th>Comment Count</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  text-white">
                    <h5 class="modal-title" id="addModalLabel">Add YouTube Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addVideoForm">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">YouTube URL</label>
                            <input type="url" class="form-control" id="url" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="age_group" class="form-label">Age Group</label>
                            <select id="age_group" name="age_group" class="form-control" required>
                                @foreach ($ageGroups as $ageGroup)
                                    <option value="{{ $ageGroup }}">{{ $ageGroup }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Video</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  text-white">
                    <h5 class="modal-title" id="editModalLabel">Edit YouTube Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editVideoForm">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_url" class="form-label">YouTube URL</label>
                            <input type="url" class="form-control" id="edit_url" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_age_group" class="form-label">Age Group</label>
                            <select id="edit_age_group" name="age_group" class="form-control" required>
                                @foreach ($ageGroups as $ageGroup)
                                    <option value="{{ $ageGroup }}">{{ $ageGroup }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#age-group-filter').select2({
                placeholder: 'Select Age Group',
                allowClear: true
            });

            // Initialize DataTable
            var table = $('#youtube-videos-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('youtube_videos.index') }}',
                    data: function(d) {
                        d.age_group = $('#age-group-filter').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'

                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'url',
                        name: 'url'
                    },
                    {
                        data: 'age_group',
                        name: 'age_group'
                    },
                    {
                        data: 'view_count',
                        name: 'view_count'
                    },
                    {
                        data: 'like_count',
                        name: 'like_count'
                    },
                    {
                        data: 'comment_count',
                        name: 'comment_count'
                    },
                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<div class="d-flex justify-content-around gap-2">
                                <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button></div>
                            `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: 6,
                    width: '10%'
                }],
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
                lengthChange: false
            });

            // Handle age group filter change
            $('#age-group-filter').on('change', function() {
                table.ajax.reload();
            });

            // Handle add form submit
            $('#addVideoForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('youtube_videos.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addModal').modal('hide');
                        $('#addModal').find('input').val('');

                        // Use Bootstrap's modal event to ensure proper cleanup
                        $('#addModal').on('hidden.bs.modal', function() {
                            // Remove modal backdrop
                            $('.modal-backdrop').remove();
                            // Allow body to scroll again
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');
                        });

                        $('#youtube-videos-table').DataTable().ajax.reload();
                        Swal.fire('Success', response.success, 'success').then(() => {
                            location.reload();
                        });

                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseText, 'error');
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.get('/admin/youtube_videos/' + id, function(data) {
                    $('#edit_title').val(data.title);
                    $('#edit_url').val(data.url);
                    $('#edit_age_group').val(data.age_group).trigger('change');
                    $('#editVideoForm').attr('action', '/admin/youtube_videos/' + id);
                    $('#editModal').modal('show');
                });
            });

            // Handle edit form submit
            $('#editVideoForm').on('submit', function(e) {
                e.preventDefault();
                var id = $(this).attr('action').split('/').pop();
                $.ajax({
                    url: '/admin/youtube_videos/' + id,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editModal').modal('hide');
                        $('#youtube-videos-table').DataTable().ajax.reload();
                        Swal.fire('Success', response.success, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseText, 'error');
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
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
                            url: '/admin/youtube_videos/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.success, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseText.error, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

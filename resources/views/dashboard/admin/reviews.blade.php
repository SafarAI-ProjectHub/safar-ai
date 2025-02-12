@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        span.comment-clickable {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Reviews List</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="course-filter" class="form-control">
                        <option value="">Select Unit</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="student-filter" class="form-control">
                        <option value="">Select Student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="rate-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>unit Title</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            $('#course-filter').select2({
                placeholder: 'Select Course',
                allowClear: true
            });
            $('#student-filter').select2({
                placeholder: 'Select Student',
                allowClear: true
            });

            $.fn.dataTable.ext.order['rating'] = function(settings, col) {
                return this.api().column(col, {
                    order: 'index'
                }).nodes().map(function(td, i) {
                    return $(td).data('rating');
                });
            }

            var table = $('#rate-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reviews.index') }}",
                    data: function(d) {
                        d.course_id = $('#course-filter').val();
                        d.student_id = $('#student-filter').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'course_title',
                        name: 'course_title'
                    },
                    {
                        data: 'rate',
                        name: 'rate',
                        orderDataType: 'rating',
                        render: function(data, type, row) {
                            var stars = '';
                            for (var i = 1; i <= 5; i++) {
                                stars += i <= data ? '★' : '☆';
                            }
                            return '<span data-rating="' + data + '">' + stars + '</span>';
                        }
                    },
                    {
                        data: 'comment',
                        name: 'comment',
                        render: function(data, type, row) {
                            if (!data) {
                                return 'Empty comment';
                            }
                            if (data.length > 50) {
                                return '<span class="comment-clickable" data-comment="' + data +
                                    '">' + data.substr(0, 50) + '...</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('YYYY-MM-DD HH:mm:ss');
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<a href="#" class="btn btn-sm btn-danger delete" data-id="' +
                                row.id + '"><i class="bx bx-trash"></i> Delete</a>';
                        }
                    }
                ],
                order: [
                    [0, 'desc']
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
                    targets: 3,
                    width: '30%'
                }],
                lengthChange: false
            });

            $('#course-filter, #student-filter').on('change', function() {
                table.draw();
            });

            // Event delegation to handle click on comments
            $('#rate-table tbody').on('click', 'span.comment-clickable', function() {
                var comment = $(this).data('comment');
                Swal.fire({
                    title: 'Comment',
                    text: comment,
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            });

            // Event delegation to handle delete button
            $('#rate-table tbody').on('click', 'a.delete', function(e) {
                e.preventDefault();
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
                            url: "{{ route('rates.destroy', '') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload();
                                    Swal.fire(
                                        'Deleted!',
                                        'Review has been deleted.',
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'An error occurred while deleting the review.',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

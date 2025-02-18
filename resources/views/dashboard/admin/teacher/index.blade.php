@extends('layouts_dashboard.main')

@section('styles')
    <!-- CSS الرئيسي للـ DataTables -->
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- CSS الخاص بأزرار التصدير (Buttons) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Teachers' Activity for today</h5>
            <div class="table-responsive">
                <table id="teacher-activity-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Teacher</th>
                            <th>Email</th>
                            <th>Active Time (Today)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- سكريبت الـ DataTables الرئيسي -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <!-- مكاتب أزرار التصدير (Buttons) -->
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

    <script>
        // تأكد من وجود الـ CSRF Token في الميتا الرئيسية
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#teacher-activity-table').DataTable({
                processing: true,
                serverSide: true,
                // يمكن استخدام صيغة الكائن لضبط النوع وتنسيق البيانات
                ajax: {
                    url: '{{ route('teachers.logs.index') }}',
                    type: 'GET', // أو POST حسب إعداداتك
                    dataType: 'json'
                },
                columns: [
                    { data: 'teacher_id', name: 'teacher_id' },
                    { data: 'teacher',    name: 'teacher' },
                    { data: 'email',      name: 'email' },
                    { data: 'active_time',name: 'active_time' },
                    {
                        data: 'id',
                        name: 'actions',
                        render: function(data) {
                            return `<a href="/admin/teacher/logs/${data}" class="btn btn-primary btn-sm">View Logs</a>`;
                        }
                    }
                ],
                columnDefs: [
                    {
                        targets: 2,
                        width: '10%'
                    }
                ],
                // تفعيل أزرار التصدير
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
                lengthChange: false,
                order: [[0, 'desc']]
            });
        });
    </script>
@endsection

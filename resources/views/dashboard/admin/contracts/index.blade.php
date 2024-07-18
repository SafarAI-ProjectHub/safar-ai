@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Contracts</h5>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary mb-3">Create New Contract</a>
            <table class="table table-striped" id="contracts-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
                        <th>Company Name</th>
                        <th>Other Party Name</th>
                        <th>Contract Date</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#contracts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('contracts.index') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'teacher_name',
                        name: 'teacher_name'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'other_party_name',
                        name: 'other_party_name'
                    },
                    {
                        data: 'contract_date',
                        name: 'contract_date'
                    },
                    {
                        data: 'salary',
                        name: 'salary'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        function viewContract(id) {
            $.ajax({
                url: '/contracts/' + id,
                method: 'GET',
                success: function(response) {
                    // Show contract details in a modal or a new page
                    console.log(response);
                },
                error: function(response) {
                    console.error('Error:', response);
                }
            });
        }

        function editContract(id) {
            window.location.href = '/contracts/' + id + '/edit';
        }

        function deleteContract(id) {
            if (confirm('Are you sure you want to delete this contract?')) {
                $.ajax({
                    url: '/contracts/' + id,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.status);
                        $('#contracts-table').DataTable().ajax.reload();
                    },
                    error: function(response) {
                        console.error('Error:', response);
                    }
                });
            }
        }
    </script>
@endsection

@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <style>
        .alert {
            z-index: 0 !important
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Approval Pending</h4>
                <p>Your account is currently pending approval. Please wait for the admin to approve your account. If you
                    have any questions, feel free to contact support.{{ env('Email_Adrees') ?? 'safar-ai@example.com' }}</p>
                <hr>
                <p class="mb-0">Thank you for your patience.</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        function showPendingApprovalAlert() {
            Swal.fire({
                icon: 'info',
                title: 'Approval Pending',
                text: 'Your account is still pending approval. Please wait for the admin to approve your account.',
                confirmButtonText: 'OK'
            });
        }
    </script>
@endsection

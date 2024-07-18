@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Sign Contract</h5>
            <p><strong>Company Name:</strong> {{ $contract->company_name }}</p>
            <p><strong>Other Party Name:</strong> {{ $contract->other_party_name }}</p>
            <p><strong>Contract Date:</strong> {{ $contract->contract_date }}</p>
            <p><strong>Salary:</strong> {{ $contract->salary }}</p>
            <p><strong>Logo:</strong></p>
            @if ($contract->company_logo)
                <img src="{{ asset('storage/' . $contract->company_logo) }}" alt="Company Logo" width="100">
            @else
                <p>No logo uploaded</p>
            @endif
            <h5>Rules</h5>
            <ul>
                @foreach ($contract->rules as $rule)
                    <li>{{ $rule->rule }}</li>
                @endforeach
            </ul>
            <form id="sign-contract-form">
                @csrf
                <div class="mb-3">
                    <label for="signature" class="form-label">Signature</label>
                    <input type="text" name="signature" id="signature" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Sign Contract</button>
            </form>
        </div>
    </div>

    <script>
        $('#sign-contract-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: '{{ route('contracts.storeSignature', $contract->id) }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert(response.status);
                    window.location.href = '{{ route('contracts.index') }}';
                },
                error: function(response) {
                    console.error('Error:', response);
                }
            });
        });
    </script>
@endsection

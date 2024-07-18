@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Edit Contract</h5>
            <form id="edit-contract-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="form-control" disabled>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}"
                                {{ $contract->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }} -
                                {{ $teacher->email }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="teacher_id" value="{{ $contract->teacher_id }}">
                </div>
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" name="company_name" id="company_name" class="form-control"
                        value="Your Company Name" readonly>
                </div>
                <div class="mb-3">
                    <label for="other_party_name" class="form-label">Other Party Name</label>
                    <input type="text" name="other_party_name" id="other_party_name" class="form-control"
                        value="{{ $contract->other_party_name }}" required>
                </div>
                <div class="mb-3">
                    <label for="contract_date" class="form-label">Contract Date</label>
                    <input type="date" name="contract_date" id="contract_date" class="form-control"
                        value="{{ $contract->contract_date }}" required>
                </div>
                <div class="mb-3">
                    <label for="company_logo" class="form-label">Company Logo</label>
                    <input type="text" name="company_logo" id="company_logo" class="form-control"
                        value="path/to/your/company/logo.png" readonly>
                </div>
                <div class="mb-3">
                    <label for="salary" class="form-label">Salary</label>
                    <input type="number" name="salary" id="salary" class="form-control" value="{{ $contract->salary }}"
                        required>
                </div>
                <div id="rules-container" class="mb-3">
                    <label for="rules" class="form-label">Rules</label>
                    @foreach ($contract->rules as $rule)
                        <div class="input-group mb-3">
                            <input type="text" name="rules[]" class="form-control" value="{{ $rule->rule }}" required>
                            <button type="button" class="btn btn-danger" onclick="removeRule(this)">Remove Rule</button>
                        </div>
                    @endforeach
                    <div class="input-group mb-3">
                        <input type="text" name="rules[]" class="form-control" required>
                        <button type="button" class="btn btn-success" onclick="addRule()">Add Rule</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Contract</button>
            </form>
        </div>
    </div>

    <script>
        function addRule() {
            const container = document.getElementById('rules-container');
            const newRule = document.createElement('div');
            newRule.className = 'input-group mb-3';
            newRule.innerHTML =
                `<input type="text" name="rules[]" class="form-control" required>
                             <button type="button" class="btn btn-danger" onclick="removeRule(this)">Remove Rule</button>`;
            container.appendChild(newRule);
        }

        function removeRule(button) {
            button.parentElement.remove();
        }

        $('#edit-contract-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: '{{ route('contracts.update', $contract->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
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

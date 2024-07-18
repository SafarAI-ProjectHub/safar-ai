@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Create New Contract</h5>
            <form id="create-contract-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Select Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="form-control"
                        @if (isset($selectedTeacher)) disabled @endif required>
                        <option value="">Select Teacher</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @if (isset($selectedTeacher) && $selectedTeacher->id == $teacher->id) selected @endif>
                                {{ $teacher->name }} - {{ $teacher->email }}</option>
                        @endforeach
                    </select>
                    @if (isset($selectedTeacher))
                        <input type="hidden" name="teacher_id" value="{{ $selectedTeacher->id }}">
                    @endif
                </div>
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" name="company_name" id="company_name" class="form-control"
                        value="Your Company Name" readonly>
                </div>
                <div class="mb-3">
                    <label for="other_party_name" class="form-label">Other Party Name</label>
                    <input type="text" name="other_party_name" id="other_party_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contract_date" class="form-label">Contract Date</label>
                    <input type="date" name="contract_date" id="contract_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="company_logo" class="form-label">Company Logo</label>
                    <input type="text" name="company_logo" id="company_logo" class="form-control"
                        value="path/to/your/company/logo.png" readonly>
                </div>
                <div class="mb-3">
                    <label for="salary" class="form-label">Salary</label>
                    <input type="number" name="salary" id="salary" class="form-control" required>
                </div>
                <div id="rules-container" class="mb-3">
                    <label for="rules" class="form-label">Rules</label>
                    <div class="input-group mb-3">
                        <input type="text" name="rules[]" class="form-control" required>
                        <button type="button" class="btn btn-success" onclick="addRule()">Add Rule</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Contract</button>
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

        $('#create-contract-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: '{{ route('contracts.store') }}',
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

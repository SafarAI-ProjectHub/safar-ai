@extends('layouts_dashboard.main')

@section('content')
    <div class="container">
        <h1>Create Contract</h1>
        <form id="create-contract-form">
            @csrf

            <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
            <div class="mb-3">
                <label for="other_party_name" class="form-label">Teacher Name</label>
                <input type="text" class="form-control" name="other_party_name" required>
            </div>
            <div class="mb-3">
                <label for="salary" class="form-label">Salary</label>
                <input type="number" class="form-control" name="salary" required>
            </div>
            <div class="mb-3">
                <label for="salary_period" class="form-label">Salary Period</label>
                <select name="salary_period" class="form-control" required>
                    <option value="hour">Hour</option>
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="contract_agreement" class="form-label">Contract Agreement</label>
                <div id="contract_agreement_editor" class="editor"></div>
                <textarea name="contract_agreement" class="form-control d-none"></textarea>
            </div>
            <div class="mb-3">
                <label for="employee_duties" class="form-label">Employee Duties</label>
                <div id="employee_duties_editor" class="editor"></div>
                <textarea name="employee_duties" class="form-control d-none"></textarea>
            </div>
            <div class="mb-3">
                <label for="responsibilities" class="form-label">Responsibilities</label>
                <div id="responsibilities_editor" class="editor"></div>
                <textarea name="responsibilities" class="form-control d-none"></textarea>
            </div>
            <div class="mb-3">
                <label for="employment_period" class="form-label">Employment Period</label>
                <div id="employment_period_editor" class="editor"></div>
                <textarea name="employment_period" class="form-control d-none"></textarea>
            </div>
            <div class="mb-3">
                <label for="compensation" class="form-label">Compensation</label>
                <div id="compensation_editor" class="editor"></div>
                <textarea name="compensation" class="form-control d-none"></textarea>
            </div>
            <div class="mb-3">
                <label for="legal_terms" class="form-label">Legal Terms</label>
                <div id="legal_terms_editor" class="editor"></div>
                <textarea name="legal_terms" class="form-control d-none"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Contract</button>
        </form>
    </div>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            var editors = {};
            $('.editor').each(function() {
                var editorId = $(this).attr('id');
                editors[editorId] = new Quill('#' + editorId, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{
                                'font': []
                            }],
                            [{
                                'size': ['small', false, 'large', 'huge']
                            }],
                            [{
                                'header': [1, 2, 3, 4, 5, 6, false]
                            }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{
                                'align': []
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            [{
                                'indent': '-1'
                            }, {
                                'indent': '+1'
                            }],
                            [{
                                'color': []
                            }, {
                                'background': []
                            }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });
            });

            $('#create-contract-form').on('submit', function(e) {
                e.preventDefault();
                $('.editor').each(function() {
                    var editorId = $(this).attr('id');
                    var htmlContent = editors[editorId].root.innerHTML;
                    $('textarea[name="' + editorId.replace('_editor', '') + '"]').val(htmlContent);
                });

                $.ajax({
                    url: '{{ route('contracts.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        swal('Success', 'Contract created successfully.', 'success');
                        window.location.href = '{{ route('contracts.index') }}';
                    },
                    error: function(response) {
                        alert('Error creating contract.');
                    }
                });
            });
        });
    </script>
@endsection

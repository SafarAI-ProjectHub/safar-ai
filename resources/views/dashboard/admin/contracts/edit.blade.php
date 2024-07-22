@extends('layouts_dashboard.main')

@section('styles')
    <style>
        .chat-container {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
            height: 500px;
            /* Adjust height as needed */
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .chat-body {
            height: calc(100% - 60px);
            /* Adjust according to header/footer height */
            overflow-y: auto;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .wrapper {

            z-index: 15;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3><strong>Status of the Contract: </strong>
                @if ($contract->signature)
                    <span class="badge bg-success">Signed</span>
                @else
                    <span class="badge bg-warning">Not Signed</span>
                @endif
            </h3>
            <hr class="border border-primary border-3 opacity-75">

            <h3>Notes</h3> <!-- Notes for the admins -->
            <p>1. You have <a href="#chat">a chat</a> option below to discuss the contract with the teacher. You can edit
                the contract
                based on these discussions.</p>
            <p>2. The teacher will be able to download the contract after signing it. Please ensure not to edit the
                contract unless you have discussed the changes with the teacher to avoid any confusion.</p>
        </div>
    </div>
    <div class="card p-3">
        <div class="container">
            <h1>Edit Contract</h1>

            <form id="edit-contract-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                <div class="mb-3">
                    <label for="other_party_name" class="form-label">Teacher Name</label>
                    <input type="text" class="form-control" name="other_party_name"
                        value="{{ $contract->other_party_name }}" required>
                </div>
                <div class="mb-3">
                    <label for="salary" class="form-label">Salary</label>
                    <input type="number" class="form-control" name="salary" value="{{ $contract->salary }}" required>
                </div>
                <div class="mb-3">
                    <label for="salary_period" class="form-label">Salary Period</label>
                    <select name="salary_period" class="form-control" required>
                        <option value="hour" {{ $contract->salary_period == 'hour' ? 'selected' : '' }}>Hour</option>
                        <option value="week" {{ $contract->salary_period == 'week' ? 'selected' : '' }}>Week</option>
                        <option value="month" {{ $contract->salary_period == 'month' ? 'selected' : '' }}>Month</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="contract_agreement" class="form-label">Contract Agreement</label>
                    <div id="contract_agreement_editor" class="editor">{!! $contract->contract_agreement !!}</div>
                    <textarea name="contract_agreement" class="form-control d-none"></textarea>
                </div>
                <div class="mb-3">
                    <label for="employee_duties" class="form-label">Employee Duties</label>
                    <div id="employee_duties_editor" class="editor">{!! $contract->employee_duties !!}</div>
                    <textarea name="employee_duties" class="form-control d-none"></textarea>
                </div>
                <div class="mb-3">
                    <label for="responsibilities" class="form-label">Responsibilities</label>
                    <div id="responsibilities_editor" class="editor">{!! $contract->responsibilities !!}</div>
                    <textarea name="responsibilities" class="form-control d-none"></textarea>
                </div>
                <div class="mb-3">
                    <label for="employment_period" class="form-label">Employment Period</label>
                    <div id="employment_period_editor" class="editor">{!! $contract->employment_period !!}</div>
                    <textarea name="employment_period" class="form-control d-none"></textarea>
                </div>
                <div class="mb-3">
                    <label for="compensation" class="form-label">Compensation</label>
                    <div id="compensation_editor" class="editor">{!! $contract->compensation !!}</div>
                    <textarea name="compensation" class="form-control d-none"></textarea>
                </div>
                <div class="mb-3">
                    <label for="legal_terms" class="form-label">Legal Terms</label>
                    <div id="legal_terms_editor" class="editor">{!! $contract->legal_terms !!}</div>
                    <textarea name="legal_terms" class="form-control d-none"></textarea>
                </div>
                <button type="submit" class="btn btn-primary mb-3">Update Contract</button>
            </form>
        </div>
    </div>
    <input type="hidden" id="contract_id" value="{{ $contract->id }}">

    <div class="chat-container" id="chat">
        @include('Chatify::pages.app')
    </div>
@endsection

@section('scripts')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        $(document).ready(function() {
            username = getMessengerId();
            @php
                $username = $contract->teacher->full_name;
                $userImage = asset($contract->teacher->profile_image ? $contract->teacher->profile_image : asset('assets/images/avatars/profile-Img.png'));

            @endphp
            $('.user-name').text('{{ $username }}');
            // class="avatar av-s header-avatar"
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
            $('#edit-contract-form').on('submit', function(e) {
                e.preventDefault();
                $('.editor').each(function() {
                    var editorId = $(this).attr('id');
                    var htmlContent = editors[editorId].root.innerHTML;
                    $('textarea[name="' + editorId.replace('_editor', '') + '"]').val(htmlContent);
                });

                var contractId = $('input[name="contract_id"]').val();
                $.ajax({
                    url: '/admin/contracts/' + contractId,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert('Contract updated successfully.');
                        window.location.href = '{{ route('contracts.index') }}';
                    },
                    error: function(response) {
                        alert('Error updating contract.');
                    }
                });
            });

            setMessengerId({{ $contract->teacher->id }});
            console.log('Teacher ID: ' + getMessengerId());

            // Fetch messages on page load
            fetchMessages(getMessengerId(), true);

            // Send message form submission
            $("#message-form").on("submit", (e) => {
                e.preventDefault();
                sendMessage();
            });
        });
        // set interval and fetch messages
        setInterval(() => {
            fetchMessages();
        }, 5000);
    </script>
@endsection

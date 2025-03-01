@extends('layouts_dashboard.main')

@section('styles')
    {{-- الروابط الافتراضية لـChatify: تحوي ملفات الـCSS الخاصة بعرض المحادثة --}}
    @include('Chatify::layouts.headLinks')

    <style>
        .chat-container {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
            height: 500px; /* يمكنك ضبط الارتفاع حسب الحاجة */
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
            overflow-y: auto;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .wrapper {
            z-index: 15;
        }

        /* إصلاح عرض الصور في منطقة الشات */
        .chat-container img {
            max-width: 100%;
            height: auto;
            display: block;
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
            <p>1. You have <a href="#chat">a chat</a> option below to discuss the contract with the teacher.
               You can edit the contract based on these discussions.</p>
            <p>2. The teacher will be able to download the contract after signing it. Please ensure not to edit
               the contract unless you have discussed the changes with the teacher to avoid any confusion.</p>
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
                    <input type="number" class="form-control" name="salary"
                           value="{{ $contract->salary }}" required>
                </div>
                <div class="mb-3">
                    <label for="salary_period" class="form-label">Salary Period</label>
                    <select name="salary_period" class="form-control" required>
                        <option value="hour"  {{ $contract->salary_period == 'hour'  ? 'selected' : '' }}>Hour</option>
                        <option value="week"  {{ $contract->salary_period == 'week'  ? 'selected' : '' }}>Week</option>
                        <option value="month" {{ $contract->salary_period == 'month' ? 'selected' : '' }}>Month</option>
                    </select>
                </div>

                {{-- مثال على الحقول القابلة للتحرير باستخدام محرر Quill --}}
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
        {{-- تضمين صفحة المحادثة من حزمة Chatify --}}
        @include('Chatify::pages.app')
    </div>
@endsection

@section('scripts')
    {{-- روابط الجافاسكربت الخاصة بـChatify --}}
    @include('Chatify::layouts.footerLinks')

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function() {
            // عرض اسم المعلّم أو المستخدم في واجهة الشات
            @php
                $username = $contract->teacher->full_name;
                $userImage = asset($contract->teacher->profile_image
                            ? $contract->teacher->profile_image
                            : 'assets/images/avatars/profile-Img.png');
            @endphp

            $('.name-user').text('{{ $username }}');

            var editors = {};
            $('.editor').each(function() {
                var editorId = $(this).attr('id');
                editors[editorId] = new Quill('#' + editorId, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{'font': []}],
                            [{'size': ['small', false, 'large', 'huge']}],
                            [{'header': [1, 2, 3, 4, 5, 6, false]}],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{'align': []}],
                            [{'list': 'ordered'}, {'list': 'bullet'}],
                            [{'indent': '-1'}, {'indent': '+1'}],
                            [{'color': []}, {'background': []}],
                            ['link'],
                            ['clean']
                        ]
                    }
                });
            });

            $('#edit-contract-form').on('submit', function(e) {
                e.preventDefault();

                // ننسخ محتوى كل Quill Editor في الـ<textarea> المرتبطة به
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
                        Swal.fire({
                            title: 'Success!',
                            text: 'Contract updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route('contracts.index') }}';
                        });
                    },
                    error: function(response) {
                        if (response.responseJSON.errors) {
                            var errors = response.responseJSON.errors;
                            var errorMessage = '';
                            for (var key in errors) {
                                errorMessage += errors[key][0] + '\n';
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error updating contract.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });

            // تعيين هوية المرسل/المستلم للدردشة
            setMessengerId({{ $contract->teacher->id }});
            console.log('Teacher ID: ' + getMessengerId());

            // جلب الرسائل عند تحميل الصفحة
            fetchMessages(getMessengerId(), true);

            // إرسال رسالة عند الضغط على زر الإرسال
            $("#message-form").on("submit", (e) => {
                e.preventDefault();
                sendMessage();
            });
        });

        // تحديث الرسائل كل 5 ثوانٍ
        setInterval(() => {
            fetchMessages();
        }, 5000);
    </script>
@endsection

@extends('layouts_dashboard.main')

@section('styles')
    {{-- الروابط الافتراضية لـChatify: تحوي ملفات الـCSS الخاصة بعرض المحادثة --}}
    @include('Chatify::layouts.headLinks')

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .contract-container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .company-logo {
            width: 100px;
            height: auto;
        }

        .company-info h1 {
            margin: 0;
            font-size: 28px;
        }

        .company-info p {
            margin: 5px 0 0 0;
            font-size: 16px;
        }

        .contract-section {
            margin-bottom: 20px;
        }

        .contract-section h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #444;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }

        .contract-section p {
            margin: 10px 0;
            line-height: 1.6;
        }

        .inline-logo {
            width: 30px;
            height: auto;
            vertical-align: middle;
        }

        footer {
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 20px;
        }

        footer p {
            margin: 0;
            font-size: 14px;
            color: #777;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-line {
            display: block;
            border-top: 1px solid #333;
            margin-top: 50px;
            text-align: center;
            font-size: 16px;
            color: #444;
        }

        .contract-template {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
        }

        .contract-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .contract-logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .contract-company-name {
            font-size: 24px;
            font-weight: bold;
        }

        .contract-title {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .contract-template p {
            font-size: 16px;
            margin: 10px 0;
        }

        .contract-template p strong {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .date-div {
            margin: 0;
            padding: 0 20px;
        }

        .signature-line {
            display: block;
            border-bottom: 1px solid #333;
            margin-bottom: 50px;
            text-align: center;
            font-size: 16px;
            color: #444;
        }

        .signature {
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
            color: #444;
        }

        p#contract-date {
            border-bottom: 1px solid black;
            width: fit-content;
            text-align: left;
        }

        p.date-p {
            text-align: left;
        }

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

        /* إصلاح عرض الصور في منطقة الشات */
        .chat-container img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .wrapper {
            z-index: 15;
        }
    </style>
@endsection

@section('content')
    @if ($contract && $contract->teacher_id == auth()->user()->id)
        <div class="card">
            <div class="card-header">
                <h3>Notes</h3>
                <p>1. If you have any questions or need any changes, please contact the admin using
                    <a href="#chat">the chat</a> below.</p>

                @if ($contract && !$contract->signature && $contract->teacher_id == auth()->user()->id)
                    <p>2. Please read the contract carefully before signing.</p>
                @endif
            </div>
        </div>
    @endif

    @if ($contract && $contract->teacher_id == auth()->user()->id)
        @if ($contract->signature)
            <div class="card">
                <div class="card-header">
                    <h3>Download Contract</h3>
                </div>
                <a href="{{ route('contracts.downloadPDF', $contract->id) }}" class="btn btn-success">
                    Download Contract as PDF
                </a>
            </div>
        @endif

        <input type="hidden" id="contract_id" value="{{ $contract->id }}">

        <div class="contract-container">
            <div class="contract-header">
                <img src="{{ asset('assets/img/logo2.png') }}" id="company-logo" alt="Company Logo" class="contract-logo">
                <div class="company-info">
                    <h1 class="contract-company-name">{{ env('Company_Name') }}</h1>
                    <p><strong>Date:</strong> <span id="contract-date">{{ $contract->contract_date }}</span></p>
                </div>
            </div>
            <section class="contract-section">
                <h2>Contract Agreement</h2>
                <p>
                    This Contract is made and entered into on
                    <span id="contract-date-span">{{ $contract->contract_date }}</span>, by and between
                    {{ env('Company_Name') }} ("Company") and
                    <span id="teacher-name">{{ $contract->teacher->full_name }}</span> ("Teacher").
                </p>
            </section>
            <section class="contract-section">
                <h2>Employee Duties</h2>
                <p id="employee-duties">{!! $contract->employee_duties !!}</p>
            </section>
            <section class="contract-section">
                <h2>Responsibilities</h2>
                <p id="responsibilities">{!! $contract->responsibilities !!}</p>
            </section>
            <section class="contract-section">
                <h2>Employment Period</h2>
                <p id="employment-period">{!! $contract->employment_period !!}</p>
            </section>
            <section class="contract-section">
                <h2>Compensation</h2>
                <p id="compensation">{!! $contract->compensation !!}</p>
                <p>
                    <strong>Salary:</strong>
                    <span id="salary">{{ $contract->salary }}</span> per
                    <span id="salary-period">{{ $contract->salary_period }}</span>
                </p>
            </section>
            <section class="contract-section">
                <h2>Legal Terms</h2>
                <p id="legal-terms">{!! $contract->legal_terms !!}</p>
            </section>
            <div class="signature-section">
                <div>
                    <p class="signature-line">
                        <strong> Employee Signature :</strong><br>
                        <span class="signature signature-value">
                            {{ $contract->signature ? $contract->signature : 'NOT SIGNED' }}
                        </span>
                    </p>
                </div>
                <div class="date-div">
                    <p class="signature-line date">
                        <strong>Date:</strong><br>
                        <span class="signature date-value">{{ $contract->contract_date }}</span>
                    </p>
                </div>
            </div>
            <footer>
                <p>
                    {{ env('Company_Name') }} | Contact: {{ env('Email_Adrees') }} |
                    Phone: {{ env('phone_number') }}
                </p>
            </footer>
            @if (!$contract->signature)
                <div class="form-group">
                    <label for="signature">Sign Contract</label>
                    <input type="text" id="signature" name="signature" class="form-control">
                </div>
                <button id="sign-contract-button" class="btn btn-primary">Sign Contract</button>
            @endif
        </div>
    @else
        <div class="contract-container">
            <p>Your contract has not been created yet. Please wait for the admin to create the contract.</p>
        </div>
    @endif

    @if ($contract && $contract->teacher_id == auth()->user()->id)
        <div class="chat-container" id="chat">
            {{-- تضمين صفحة المحادثة من حزمة Chatify --}}
            @include('Chatify::pages.app')
        </div>
    @endif
@endsection

@section('scripts')
    {{-- روابط الجافاسكربت الخاصة بـChatify --}}
    @include('Chatify::layouts.footerLinks')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function() {
            @if ($contract && $contract->teacher_id == auth()->user()->id)
                setMessengerId({{ $admin->id }});  // اجعل الـadmin هو المستلم
                fetchMessages(getMessengerId(), true);
            @endif

            @if ($contract && !$contract->signature)
                $('#sign-contract-button').on('click', function() {
                    var signature = $('#signature').val();
                    if (!signature) {
                        alert('Please enter your signature.');
                        return;
                    }

                    $.ajax({
                        url: '{{ route('contracts.signContract') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            contract_id: '{{ $contract->id }}',
                            signature: signature
                        },
                        success: function(response) {
                            alert(response.message);
                            location.reload();
                        },
                        error: function(response) {
                            alert('Error signing contract.');
                        }
                    });
                });
            @endif

            // إرسال رسالة في الشات
            @if ($contract && $contract->teacher_id == auth()->user()->id)
                $("#message-form").on("submit", (e) => {
                    e.preventDefault();
                    sendMessage();
                });
            @endif
        });
    </script>
@endsection

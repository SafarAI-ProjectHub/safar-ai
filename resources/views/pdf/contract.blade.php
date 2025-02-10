<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contract PDF</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            background-color: #fff;
            margin: 40px;
        }



        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 100px;
            /* Fixed height for the logo */
        }

        .header h1 {
            font-size: 24px;
            margin-top: 10px;
        }

        h2 {
            color: #444;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            margin-top: 10px;
        }

        .signature-section {

            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-block {
            text-align: center;
        }

        .signature-block p {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 10px;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="contract-container">
        <div class="header">
            <img src="{!! asset('assets/img/logo2.png') !!}" alt="Company Logo">
            <h1>Contract Agreement</h1>
            <p class="date-p"><strong>Date:</strong><span id="contract-date">{!! $contract->contract_date !!}</span></p>
        </div>
        <section class="contract-section">
            <h2>Contract Agreement</h2>
            <p>This Contract is made and entered into on <span id="contract-date-span">{!! $contract->contract_date !!}</span>, by
                and
                between Safar AI ("Company") and <span id="teacher-name">{!! $contract->teacher->full_name !!}</span>
                ("Teacher").</p>
        </section>
        <section class="contract-section">
            <h2>Employee Duties</h2>
            <dev id="employee-duties">{!! $contract->employee_duties !!}</dev>
        </section>
        <section class="contract-section">
            <h2>Responsibilities</h2>
            <dev id="responsibilities">{!! $contract->responsibilities !!}</dev>
        </section>
        <section class="contract-section">
            <h2>Employment Period</h2>
            <dev id="employment-period">{!! $contract->employment_period !!}</dev>
        </section>
        <section class="contract-section">
            <h2>Compensation</h2>
            <dev id="compensation">{!! $contract->compensation !!}</dev>
            <p><strong>Salary:</strong> <span id="salary">{!! $contract->salary !!}</span> per <span
                    id="salary-period">{!! $contract->salary_period !!}</span></p>
        </section>
        <section class="contract-section">
            <h2>Legal Terms</h2>
            <dev id="legal-terms">{!! $contract->legal_terms !!}</dev>
        </section>
        <div class="signature-section">
            <div>
                <p class="signature-line"><strong> Employee Signature :</strong><br>
                    <span class="signature signature-value">{!! $contract->signature !!}</span>
                </p>
            </div>
            <div class="date-div">
                <p class="signature-line date"><strong>Date:</strong><br> <span
                        class="signature date-value">{!! $contract->contract_date !!}</span></p>
            </div>
        </div>
        <footer>
            <p>Safar AI | Contact:{!! env('Email_Adrees') !!} | Phone: {!! env('phone_number') !!}</p>
        </footer>
    </div>
</body>

</html>

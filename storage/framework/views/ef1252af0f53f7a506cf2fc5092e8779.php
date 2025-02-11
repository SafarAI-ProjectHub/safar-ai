<?php $__env->startSection('styles'); ?>
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

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            /* margin: 0 40px; */
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

        footer {
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 20px;
        }

        p#contract-date {
            border-bottom: 1px solid black;
            width: fit-content;
            text-align: left;
        }

        footer p {
            margin: 0;
            font-size: 14px;
            color: #777;
        }

        p.date-p {
            text-align: left;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="contracts-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Teacher Name</th>
                            <th>Salary</th>
                            <th>Salary Period</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- View Contract Modal -->
    <div class="modal fade" id="viewContractModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="contract-details" class="contract-template">
                        <div class="contract-header">
                            <img src="<?php echo e(asset('assets/img/logo2.png')); ?>" id="company-logo" alt="Company Logo"
                                class="contract-logo">
                            <p class="date-p"><strong>Date:</strong><span id="contract-date"></span> </p>
                        </div>
                        <section class="contract-section">
                            <h2>Contract Agreement</h2>
                            <p>This Contract is made and entered into on <span id="contract-date-span"></span>, by and
                                between Safar AI ("Company") and <span id="teacher-name"></span> ("Teacher").</p>
                        </section>
                        <section class="contract-section">
                            <h2>Employee Duties</h2>
                            <p id="employee-duties"></p>
                        </section>
                        <section class="contract-section">
                            <h2>Responsibilities</h2>
                            <p id="responsibilities"></p>
                        </section>
                        <section class="contract-section">
                            <h2>Employment Period</h2>
                            <p id="employment-period"></p>
                        </section>
                        <section class="contract-section">
                            <h2>Compensation</h2>
                            <p id="compensation"></p>
                            <p><strong>Salary:</strong> <span id="salary"></span> per <span id="salary-period"></span>
                            </p>
                        </section>
                        <section class="contract-section">
                            <h2>Legal Terms</h2>
                            <p id="legal-terms"></p>
                        </section>
                        <div class="signature-section">
                            <div>
                                <p class="signature-line"><strong> Employee Signature :</strong><br>
                                    <span class="signature  signature-value"></span>
                                </p>

                            </div>
                            <div class="date-div">
                                <p class="signature-line date"><strong>Date:</strong><br> <span
                                        class="signature date-value">07/18/2024</span></p>
                            </div>
                        </div>
                        <footer>
                            <p>Safar AI | Contact:<?php echo e(env('Email_Adrees')); ?> | Phone: <?php echo e(env('phone_number')); ?></p>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            var table = $('#contracts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('contracts.index')); ?>',
                columns: [{
                        data: 'teacher_name',
                        name: 'teacher_name'
                    },
                    {
                        data: 'salary',
                        name: 'salary'
                    },
                    {
                        data: 'salary_period',
                        name: 'salary_period'
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
                ],
                columnDefs: [{
                    targets: 4,
                    width: '20%'
                }]
            });

            $('#contracts-table').on('click', '.view-contract', function() {
                var contractId = $(this).data('id');
                $.get('/admin/contracts/' + contractId, function(data) {
                    $('#teacher-name').text(data.teacher_name);
                    $('#company-name').text(<?php echo e(env('Company_Name')); ?>);
                    $('#other-party-name').text(data.other_party_name);
                    $('#salary').text(data.salary);
                    $('#salary-period').text(data.salary_period);
                    $('#status').text(data.status);
                    $('.signature.signature-value').text(data.signature ? data.signature :
                        'NOT SIGNED');
                    $('#contract-date').text(data.contract_date ? data.contract_date :
                        'The date of the contract is not set yet');
                    $('.signature.date-value').text(data.contract_date ? data.contract_date :
                        'The date of the contract is not set yet');
                    $('#salary').text(data.salary);
                    $('#salary-period').text(data.salary_period);
                    $('#contract-agreement').html(data.contract_agreement);
                    $('#employee-duties').html(data.employee_duties);
                    $('#responsibilities').html(data.responsibilities);
                    $('#employment-period').html(data.employment_period);
                    $('#compensation').html(data.compensation);
                    $('#legal-terms').html(data.legal_terms);
                    $('#viewContractModal').modal('show');
                });
            });

            $('#contracts-table').on('click', '.edit-contract', function() {
                var contractId = $(this).data('id');
                window.location.href = '/admin/contracts/' + contractId + '/edit';
            });

            function showAlert(type, message, icon) {
                var alertHtml = `
                <div class="alert alert-${type} border-0 bg-${type} alert-dismissible fade show py-2 position-fixed top-0 end-0 m-3" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="font-35 text-white">
                            <i class="bx ${icon}"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-white">${type.charAt(0).toUpperCase() + type.slice(1)}</h6>
                            <div class="text-white">${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                $('body').append(alertHtml);
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/admin/contracts/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('styles'); ?>
    <link href="<?php echo e(asset('assets/plugins/bs-stepper/css/bs-stepper.css')); ?>" rel="stylesheet" />
    <style>
        .bs-stepper-content {
            padding: 20px;
        }

        input.form-check-input.choice-correct {
            width: 40px;
        }

        .question,
        .choice {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .question h6 {
            margin-bottom: 10px;
        }

        .remove-question-button {
            display: inline-block;
            vertical-align: middle;
            margin-top: 10px;
        }

        .remove-choice-button {
            display: inline-block;
            vertical-align: middle;
        }

        .add-choice-button {
            display: block;
            margin-top: 10px;
        }

        .form-check-input[type=checkbox] {
            width: 30px;
            height: 20px;
            margin-left: 10px;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <h5>Add New Teacher Level Test</h5>
            <form id="addTestForm">
                <?php echo csrf_field(); ?>
                <div id="stepper3" class="bs-stepper">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between"
                                role="tablist">
                                <div class="step" data-target="#step1">
                                    <div class="step-trigger" role="tab" id="stepper3trigger1" aria-controls="step1">
                                        <div class="bs-stepper-circle"><i class='bx bx-edit-alt fs-4'></i></div>
                                        <div class="">
                                            <h5 class="mb-0 steper-title">Test Title</h5>
                                            <p class="mb-0 steper-sub-title">Enter the test title</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bs-stepper-line"></div>
                                <div class="step" data-target="#step2">
                                    <div class="step-trigger" role="tab" id="stepper3trigger2" aria-controls="step2"
                                        disabled>
                                        <div class="bs-stepper-circle"><i class='bx bx-question-mark fs-4'></i></div>
                                        <div class="">
                                            <h5 class="mb-0 steper-title">Add Questions</h5>
                                            <p class="mb-0 steper-sub-title">Add test questions</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="bs-stepper-content">
                                <div id="step1" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper3trigger1">
                                    <div class="mb-3">
                                        <label for="test-title" class="form-label">Test Title</label>
                                        <input type="text" class="form-control" id="test-title" name="title" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="test-description" class="form-label">Description</label>
                                        <textarea class="form-control" id="test-description" name="description"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="next-to-step2">Next</button>
                                </div>
                                <div id="step2" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper3trigger2">
                                    <div id="questions-container">
                                        <h5>Questions</h5>
                                        <!-- Questions will be added dynamically here -->
                                    </div>
                                    <button type="button" class="btn btn-secondary" id="add-question-button">Add
                                        Question</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary" style="display:none;">Add Test</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Question Template -->
    <div id="question-template" style="display:none;">
        <div class="question mb-3">
            <h6>Question - <span class="question-number"></span></h6>
            <label>Question Text</label>
            <input type="text" class="form-control question-text" name="questions[][text]" required>
            <div class="invalid-feedback"></div>
            <label>Notes</label>
            <input type="text" class="form-control question-sub-text" name="questions[][sub_text]">
            <div class="invalid-feedback"></div>
            <label>Response Type</label>
            <select class="form-select question-type" name="questions[][type]" required>
                <option value="" disabled selected>Select Type</option>
                <option value="choice">Multiple Choice</option>
                <option value="text">Writing</option>
                <option value="voice">Voice Recorded</option>
            </select>
            
            <div class="multiple-choice-options" style="display:none;">
                <label>Choices</label>
                <div class="choices-container">
                    <!-- Choices will be added dynamically here -->
                </div>
                <button type="button" class="btn btn-secondary add-choice-button"><i class='bx bx-plus'></i> Add
                    Choice</button>
                <div class="invalid-feedback"></div>
            </div>
            <button type="button" class="btn btn-danger remove-question-button"><i class='bx bx-trash'></i></button>
        </div>
    </div>

    <!-- Choice Template -->
    <div id="choice-template" style="display:none;">
        <div class="choice mb-2">
            <div class="input-group">
                <input type="text" class="form-control choice-text" name="questions[][choices][][text]" required
                    placeholder="Choice Text">
                <button type="button" class="btn btn-danger remove-choice-button"><i class='bx bx-trash'></i></button>
            </div>
            <label>Correct</label>
            <div class="form-check form-switch form-check-success">
                <input class="form-check-input choice-correct" type="checkbox" role="switch">
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/plugins/bs-stepper/js/bs-stepper.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            var stepper3 = new Stepper(document.querySelector('#stepper3'));

            $('#next-to-step2').on('click', function() {
                if ($('#test-title').val()) {
                    stepper3.next();
                    $('button[type="submit"]').show(); // Show submit button after opening the last step
                    addInitialQuestion(); // Add initial question when opening the last step
                } else {
                    if (!$('#test-title').val()) {
                        showFieldError($('#test-title'), 'Please enter a test title.');
                    }
                    if (!$('#test-description').val()) {
                        showFieldError($('#test-description'), 'Please enter a test description.');
                    }

                }
            });

            function addInitialQuestion() {
                if ($('#questions-container .question').length === 0) {
                    var questionTemplate = $('#question-template').html();
                    var questionElement = $(questionTemplate).clone();
                    questionElement.find('.question-number').text(1);
                    questionElement.find('.remove-question-button')
                        .remove(); // Remove the remove button for the first question
                    $('#questions-container').append(questionElement);
                    addDefaultChoices(questionElement);
                }
            }

            $('#add-question-button').on('click', function() {
                var questionCount = $('#questions-container .question').length;
                addNewQuestion();
                // if (questionCount < 10) {
                //     addNewQuestion();
                //     if (questionCount + 1 === 10) {
                //         $(this).hide(); // Hide add question button if question limit is reached
                //     }
                // } else {
                //     showAlert('danger', 'You cannot add more than 10 questions.',
                //         'bx bxs-message-square-x');
                // }
            });

            function addNewQuestion() {
                var questionTemplate = $('#question-template').html();
                var questionCount = $('#questions-container .question').length + 1;
                var questionElement = $(questionTemplate).clone();
                questionElement.find('.question-number').text(questionCount);
                $('#questions-container').append(questionElement);
                addDefaultChoices(questionElement);
            }

            $(document).on('click', '.remove-question-button', function() {
                $(this).closest('.question').remove();
                $('#add-question-button').show(); // Show add question button if question is removed
                updateQuestionNumbers();
            });

            function updateQuestionNumbers() {
                $('#questions-container .question').each(function(index) {
                    $(this).find('.question-number').text(index + 1);
                });
            }

            function addDefaultChoices(questionElement) {
                for (let i = 0; i < 2; i++) {
                    addNewChoice(questionElement, false); // Add default choices without remove buttons
                }
            }

            $(document).on('click', '.add-choice-button', function() {
                var choicesContainer = $(this).siblings('.choices-container');
                if (choicesContainer.children('.choice').length < 4) {
                    addNewChoice($(this).closest('.question'), true); // Add new choice with remove button
                    if (choicesContainer.children('.choice').length === 4) {
                        $(this).hide(); // Hide add choice button if choice limit is reached
                    }
                } else {
                    showAlert('danger', 'You cannot add more than 4 choices.', 'bx bxs-message-square-x');
                }
            });

            function addNewChoice(questionElement, withRemoveButton) {
                var choiceTemplate = $('#choice-template').html();
                var choiceElement = $(choiceTemplate).clone();
                if (!withRemoveButton) {
                    choiceElement.find('.remove-choice-button')
                        .remove(); // Remove the remove button for default choices
                }
                questionElement.find('.choices-container').append(choiceElement);
            }

            $(document).on('click', '.remove-choice-button', function() {
                var questionElement = $(this).closest('.question');
                $(this).closest('.choice').remove();
                if (questionElement.find('.choice').length < 4) {
                    questionElement.find('.add-choice-button').show();
                }
            });

            $(document).on('change', '.question-type', function() {
                var selectedType = $(this).val();
                var questionElement = $(this).closest('.question');
                if (selectedType === 'choice') {
                    questionElement.find('.multiple-choice-options').show();
                    questionElement.find('.choices-container').empty();
                    addDefaultChoices(
                        questionElement); // Add default choices when selecting multiple choice
                } else {
                    questionElement.find('.multiple-choice-options').hide();
                    questionElement.find('.choices-container').empty();
                }
            });

            $(document).on('change', '.choice-correct', function() {
                var currentQuestion = $(this).closest('.question');
                currentQuestion.find('.choice-correct').not(this).prop('checked', false);
            });

            $('#addTestForm').on('submit', function(e) {
                e.preventDefault();

                clearFieldErrors();

                var formData = {
                    _token: $('input[name="_token"]').val(),
                    title: $('#test-title').val(),
                    description: $('#test-description').val(),
                    questions: []
                };

                var valid = true;
                $('#questions-container .question').each(function(index) {
                    var questionData = {
                        text: $(this).find('.question-text').val(),
                        sub_text: $(this).find('.question-sub-text').val(),
                        question_type: $(this).find('.question-type').val(),
                        // media_url: $(this).find('.question-media-url').val(),
                        // media_type: $(this).find('.question-media-type').val(),
                        choices: []
                    };

                    if (questionData.question_type === 'choice') {
                        var hasCorrectChoice = false;
                        $(this).find('.choices-container .choice').each(function() {
                            var choiceData = {
                                text: $(this).find('.choice-text').val(),
                                is_correct: $(this).find('.choice-correct').is(
                                    ':checked') ? 1 : 0
                            };
                            questionData.choices.push(choiceData);
                            if (choiceData.is_correct) {
                                hasCorrectChoice = true;
                            }
                        });

                        if (!hasCorrectChoice) {
                            valid = false;
                        }
                    }

                    formData.questions.push(questionData);
                });

                if (!valid) {
                    showAlert('danger', 'Each multiple-choice question must have one correct choice.',
                        'bx bxs-message-square-x');
                    return;
                }

                $.ajax({
                    url: '<?php echo e(route('teacherTest.store')); ?>',
                    method: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        showAlert('success', 'Test and questions added successfully',
                            'bxs-check-circle');
                        window.location.href =
                            "<?php echo e(route('teacherTests.index')); ?>"; // Redirect to the teacher tests page
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.errors) {
                            let errors = response.responseJSON.errors;
                            let errorMessages = 'Please fix the following errors:<br><ul>';
                            for (let key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    let fieldKey = key.replace(/\.\d+/g, '[]').replace(
                                        /questions\[\]/g, 'questions');
                                    let field = $(`[name="${fieldKey}"]`);
                                    let questionNumber = parseInt(key.match(/\d+/)[0]) + 1;
                                    let specificField = key.split('.').slice(2).join('.')
                                        .replace('_', ' ');

                                    errorMessages +=
                                        `<li>Question ${questionNumber}: The ${specificField} field is required.</li>`;
                                }
                            }
                            errorMessages += '</ul>';
                            showAlert('danger', errorMessages, 'bxs-message-square-x');
                        } else {
                            showAlert('danger', 'Error adding test', 'bxs-message-square-x');
                        }
                    }
                });
            });

            function clearFieldErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            }

            function showFieldError(field, message) {
                field.addClass('is-invalid');
                if (field.next('.invalid-feedback').length === 0) {
                    field.after(`<div class="invalid-feedback">${message}</div>`);
                }

            }

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
                    </div>
                `;
                $('body').append(alertHtml);
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/level_test/add_teacher_test.blade.php ENDPATH**/ ?>
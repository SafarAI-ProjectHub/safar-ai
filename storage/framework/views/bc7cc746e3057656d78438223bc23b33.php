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
            <h5>Add New Activity</h5>
            <form id="addQuizForm">
                <?php echo csrf_field(); ?>
                <div id="stepper3" class="bs-stepper">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between"
                                role="tablist">
                                <div class="step" data-target="#step1">
                                    <div class="step-trigger" role="tab" id="stepper3trigger1" aria-controls="step1">
                                        <div class="bs-stepper-circle"><i class='bx bx-book fs-4'></i></div>
                                        <div class="">
                                            <h5 class="mb-0 steper-title">Select Unit and Lesson</h5>
                                            <p class="mb-0 steper-sub-title">Choose the unit and lesson</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bs-stepper-line"></div>
                                <div class="step" data-target="#step2">
                                    <div class="step-trigger" role="tab" id="stepper3trigger2" aria-controls="step2"
                                        disabled>
                                        <div class="bs-stepper-circle"><i class='bx bx-edit-alt fs-4'></i></div>
                                        <div class="">
                                            <h5 class="mb-0 steper-title">Activity Title</h5>
                                            <p class="mb-0 steper-sub-title">Enter the activity title</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bs-stepper-line"></div>
                                <div class="step" data-target="#step3">
                                    <div class="step-trigger" role="tab" id="stepper3trigger3" aria-controls="step3"
                                        disabled>
                                        <div class="bs-stepper-circle"><i class='bx bx-question-mark fs-4'></i></div>
                                        <div class="">
                                            <h5 class="mb-0 steper-title">Add Questions</h5>
                                            <p class="mb-0 steper-sub-title">Add activity questions</p>
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
                                        <label for="course-select" class="form-label">Select Unit</label>
                                        <select class="form-select" id="course-select">
                                            <?php if($courses->isEmpty()): ?>
                                                <option value="" disabled>No units available</option>
                                            <?php else: ?>
                                                <option value="" disabled selected>Select a unit</option>

                                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($course->id); ?>"><?php echo e($course->title); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div id="no-units-message" class="p-3" style="display: none;">
                                        All Lessons in this unit have been assigned activities or there are no Lessons available.
                                    </div>
                                    <div class="mb-3" id="units-container" style="display:none;">
                                        <label for="unit-select" class="form-label">Select Lesson</label>
                                        <select class="form-select" id="unit-select">
                                            <option value="" disabled selected>Select a lesson</option>
                                            <!-- Units will be populated via AJAX -->
                                        </select>
                                        <div id="no-units-message" class="mt-2 text-danger" style="display:none;">No Lessons
                                            available or all Lessons have been assigned activities.</div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="next-to-step2"
                                        style="display:none;">Next</button>
                                </div>
                                <div id="step2" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper3trigger2">
                                    <div class="mb-3">
                                        <label for="quiz-title" class="form-label">Activity Title</label>
                                        <input type="text" class="form-control" id="quiz-title" name="title" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quiz-pass-mark" class="form-label">Pass Mark</label>
                                        <input type="number" class="form-control" id="quiz-pass-mark" name="pass_mark"
                                            required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="next-to-step3">Next</button>
                                </div>
                                <div id="step3" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper3trigger3">
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
                    <button type="submit" class="btn btn-primary" id="saveBtn" style="display:none;">Add Activity</button>
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
            </select>
            <div class="invalid-feedback"></div>
            <div class="multiple-choice-options" style="display:none;">
                <label>Choices</label>
                <div class="choices-container">
                    <!-- Choices will be added dynamically here -->
                </div>
                <button type="button" class="btn btn-secondary add-choice-button"><i class='bx bx-plus'></i> Add
                    Choice</button>
                <div class="invalid-feedback"></div>
            </div>
            <label>Mark</label>
            <input type="number" class="form-control question-mark" name="questions[][mark]" required>
            <div class="invalid-feedback"></div>
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

            $('#course-select').on('change', function() {
                var courseId = $(this).val();
                $.ajax({
                    url: '/courses/getUnits/' + courseId,
                    method: 'GET',
                    success: function(units) {
                        $('#unit-select').empty().append(
                            '<option value="" disabled selected>Select a lesson</option>');
                        units.forEach(function(unit) {
                            $('#unit-select').append(
                                `<option value="${unit.id}">${unit.title} - ${unit.content_type}</option>`
                            );
                        });
                        $('#units-container').show();
                        $('#no-units-message').hide();
                        $('#next-to-step2').hide();
                    },
                    error: function(response) {
                        $('#units-container').hide();
                        $('#no-units-message').show();
                        $('#next-to-step2').hide();
                    }
                });
            });

            $('#unit-select').on('change', function() {
                var unitContentType = $('#unit-select option:selected').text().split(' - ')[1];
                $('#next-to-step2').show();
                populateQuestionTypes(unitContentType);
            });

            function populateQuestionTypes(contentType) {
                var questionTypeSelect = $('.question-type');
                questionTypeSelect.empty().append('<option value="" disabled selected>Select Type</option>');
                questionTypeSelect.append('<option value="choice">Multiple Choice</option>');
                if (contentType === 'video') {
                    questionTypeSelect.append('<option value="voice">Voice Recorded</option>');
                    questionTypeSelect.append('<option value="text">Writing</option>');
                } else if (contentType === 'text') {
                    questionTypeSelect.append('<option value="voice">Voice Recorded</option>');
                    questionTypeSelect.append('<option value="text">Writing</option>');
                }
            }

            $('#next-to-step2').on('click', function() {
                stepper3.next();
            });

            $('#next-to-step3').on('click', function() {
                if ($('#quiz-title').val() && $('#quiz-pass-mark').val()) {
                    stepper3.next();
                    $('button[type="submit"]').show();
                    addInitialQuestion();
                } else {
                    if (!$('#quiz-title').val()) {
                        showFieldError($('#quiz-title'), 'Please enter a quiz title.');
                    }
                    if (!$('#quiz-pass-mark').val()) {
                        showFieldError($('#quiz-pass-mark'), 'Please enter a pass mark.');
                    }


                }
            });

            function addInitialQuestion() {
                if ($('#questions-container .question').length === 0) {
                    var questionTemplate = $('#question-template').html();
                    var questionElement = $(questionTemplate).clone();
                    questionElement.find('.question-number').text(1);
                    questionElement.find('.remove-question-button')
                        .remove();
                    $('#questions-container').append(questionElement);
                    addDefaultChoices(questionElement);
                }
            }

            $('#add-question-button').on('click', function() {
                var questionCount = $('#questions-container .question').length;
                if (questionCount < 10) {
                    addNewQuestion();
                    if (questionCount + 1 === 10) {
                        $(this).hide();
                    }
                } else {
                    showAlert('danger', 'You cannot add more than 10 questions.',
                        'bx bxs-message-square-x');
                }
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
                $('#add-question-button').show();
                updateQuestionNumbers();
            });

            function updateQuestionNumbers() {
                $('#questions-container .question').each(function(index) {
                    $(this).find('.question-number').text(index + 1);
                });
            }

            function addDefaultChoices(questionElement) {
                for (let i = 0; i < 2; i++) {
                    addNewChoice(questionElement, false);
                }
            }

            $(document).on('click', '.add-choice-button', function() {
                var choicesContainer = $(this).siblings('.choices-container');
                if (choicesContainer.children('.choice').length < 4) {
                    addNewChoice($(this).closest('.question'), true);
                    if (choicesContainer.children('.choice').length === 4) {
                        $(this).hide();
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
                        .remove();
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
                        questionElement); 
                } else {
                    questionElement.find('.multiple-choice-options').hide();
                    questionElement.find('.choices-container').empty();
                }
            });

            $(document).on('change', '.choice-correct', function() {
                var currentQuestion = $(this).closest('.question');
                currentQuestion.find('.choice-correct').not(this).prop('checked', false);
            });

            $('#addQuizForm').on('submit', function(e) {
                e.preventDefault();

                const submitButton = $('#saveBtn');
                submitButton.prop('disabled', true);


                clearFieldErrors();

                var formData = {
                    _token: $('input[name="_token"]').val(),
                    unit_id: $('#unit-select').val(),
                    title: $('#quiz-title').val(),
                    pass_mark: $('#quiz-pass-mark').val(),
                    questions: []
                };

                var valid = true;
                $('#questions-container .question').each(function(index) {
                    var questionData = {
                        text: $(this).find('.question-text').val(),
                        sub_text: $(this).find('.question-sub-text').val(),
                        question_type: $(this).find('.question-type').val(),
                        mark: $(this).find('.question-mark').val(),
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
                    showAlert('danger',
                        'Each multiple-choice question must have one correct choice.',
                        'bx bxs-message-square-x');
                    return;
                }

                $.ajax({
                    url: '<?php echo e(route('quiz.storeQuiz')); ?>',
                    method: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        showAlert('success', 'Activity and questions added successfully',
                            'bxs-check-circle');
                        window.location.href =
                            "<?php echo e(route('quizzes.index')); ?>";
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
                                    let questionNumber = parseInt(key.match(/\d+/)[0]) +
                                        1;
                                    let specificField = key.split('.').slice(2).join('.')
                                        .replace('_',
                                            ' ');

                                    errorMessages +=
                                        `<li>Question ${questionNumber}: The ${specificField} field is required.</li>`;
                                }
                            }
                            errorMessages += '</ul>';
                            showAlert('danger', errorMessages, 'bxs-message-square-x');
                            submitButton.prop('disabled', false);
                        } else {
                            showAlert('danger', 'Error adding activity', 'bxs-message-square-x');
                            submitButton.prop('disabled', false);
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

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/quiz/add-quiz.blade.php ENDPATH**/ ?>
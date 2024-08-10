@extends('layouts_dashboard.main')

@section('styles')
    <link href="{{ asset('assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet" />
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

        /* Loader Styles */
        .loader {
            display: none;
            position: fixed;
            z-index: 999;
            top: 50%;
            left: 50%;
            width: 50px;
            height: 50px;
            margin: -25px 0 0 -25px;
            border: 8px solid #f3f3f3;
            border-radius: 50%;
            border-top: 8px solid #3498db;
            border-right: 8px solid transparent;
            width: 60px;
            height: 60px;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .overlay {
            display: none;
            position: fixed;
            z-index: 998;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Add New Student Level Test</h5>
            <form id="addTestForm">
                @csrf
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
                                        <div class="invalid-feedback">Please enter a test title.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="test-description" class="form-label">Description</label>
                                        <textarea class="form-control" id="test-description" name="description"></textarea>
                                        <div class="invalid-feedback">Please enter a test description.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="age-group" class="form-label">Age Group</label>
                                        <select class="form-select" id="age-group" name="age_group_id" required>
                                            <option value="" disabled selected>Select Age Group</option>
                                            @foreach ($ageGroups as $ageGroup)
                                                <option value="{{ $ageGroup->id }}">{{ $ageGroup->age_group }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select an age group.</div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="next-to-step2">Next</button>
                                </div>
                                <div id="step2" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper3trigger2">
                                    <div class="card p-2">
                                        <h3 class="mt-2">Note</h3>
                                        <p><strong> - Audio files should be less than 5MB and no longer than 5 minutes.
                                            </strong></p>
                                    </div>

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

    <!-- Loader -->
    <div class="loader"></div>
    <div class="overlay"></div>

    <!-- Question Template -->
    <div id="question-template" style="display:none;">
        <div class="question mb-3">
            <h6>Question - <span class="question-number"></span></h6>
            <div class="mb-3">
                <label for="question-type-switch" class="form-label">Question Type</label>
                <div class="form-check form-switch">
                    <input class="form-check-input question-type-switch" type="checkbox" id="question-type-switch">
                    <label class="form-check-label" for="question-type-switch">Text</label>
                </div>
            </div>
            <div class="question-input">
                <label>Question Text</label>
                <input type="text" class="form-control question-text" name="questions[][text]" required>
                <input type="file" class="form-control question-audio" name="questions[][audio]" accept="audio/*"
                    style="display:none;">
            </div>
            <div class="invalid-feedback"></div>
            <label>Notes</label>
            <input type="text" class="form-control question-sub-text" name="questions[][sub_text]">
            <div class="invalid-feedback"></div>
            <label>Question Type</label>
            <select class="form-select question-type" name="questions[][type]" required>
                <option value="" disabled selected>Select Type</option>
                <option value="choice">Multiple Choice</option>
                <option value="text">Writing</option>
                <option value="voice">Voice Recorded</option>
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
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var stepper3 = new Stepper(document.querySelector('#stepper3'));

            $('#next-to-step2').on('click', function() {
                if ($('#test-title').val() && $('#age-group').val()) {
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
                    if (!$('#age-group').val()) {
                        showFieldError($('#age-group'), 'Please select an age group.');
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
                addNewQuestion();
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

            $(document).on('change', '.question-type-switch', function() {
                var isChecked = $(this).is(':checked');
                var questionElement = $(this).closest('.question');
                var textInput = questionElement.find('.question-text');
                var audioInput = questionElement.find('.question-audio');
                var label = $(this).siblings('.form-check-label');

                if (isChecked) {
                    label.text('Audio');
                    textInput.hide().prop('required', false);
                    audioInput.show().prop('required', true);
                } else {
                    label.text('Text');
                    textInput.show().prop('required', true);
                    audioInput.hide().prop('required', false);
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

                var formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('title', $('#test-title').val());
                formData.append('description', $('#test-description').val());
                formData.append('age_group_id', $('#age-group').val());

                var valid = true;
                $('#questions-container .question').each(function(index) {
                    var questionElement = $(this);
                    var questionData = {
                        text: questionElement.find('.question-text').val(),
                        sub_text: questionElement.find('.question-sub-text').val(),
                        type: questionElement.find('.question-type').val(),
                        question_type_switch: questionElement.find('.question-type-switch').is(
                            ':checked') ? 'audio' : 'text',
                        choices: []
                    };

                    if (questionData.type === 'choice') {
                        var hasCorrectChoice = false;
                        questionElement.find('.choices-container .choice').each(function() {
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
                            showAlert('danger',
                                'Each multiple-choice question must have at least one correct answer.',
                                'bx bxs-message-square-x');
                        }
                    }

                    if (questionData.question_type_switch === 'audio' && !questionElement.find(
                            '.question-audio').val()) {
                        valid = false;
                        showAlert('danger', 'Please upload an audio file for the audio question.',
                            'bx bxs-message-square-x');
                    }

                    if (questionData.question_type_switch === 'text' && !questionData.text) {
                        valid = false;
                        showAlert('danger', 'Please enter text for the text question.',
                            'bx bxs-message-square-x');
                    }

                    formData.append('questions[' + index + '][text]', questionData.text);
                    formData.append('questions[' + index + '][sub_text]', questionData.sub_text);
                    formData.append('questions[' + index + '][type]', questionData.type);
                    formData.append('questions[' + index + '][question_type_switch]', questionData
                        .question_type_switch);

                    if (questionData.question_type_switch === 'audio') {
                        var audioFile = questionElement.find('.question-audio').prop('files')[0];
                        formData.append('questions[' + index + '][audio]', audioFile);
                    }

                    $.each(questionData.choices, function(choiceIndex, choiceData) {
                        formData.append('questions[' + index + '][choices][' + choiceIndex +
                            '][text]', choiceData.text);
                        formData.append('questions[' + index + '][choices][' + choiceIndex +
                            '][is_correct]', choiceData.is_correct);
                    });
                });

                if (!valid) {
                    return;
                }

                $('.loader').show();
                $('.overlay').show();

                $.ajax({
                    url: '{{ route('studentTest.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('.loader').hide();
                        $('.overlay').hide();
                        showAlert('success', 'Test and questions added successfully',
                            'bxs-check-circle');
                        window.location.href =
                            "{{ route('studentTests.index') }}"; // Redirect to the student tests page
                    },
                    error: function(response) {
                        // Hide loader on error
                        $('.loader').hide();
                        $('.overlay').hide();
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
@endsection

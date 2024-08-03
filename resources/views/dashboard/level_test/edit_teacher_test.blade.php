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
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Edit Teacher Level Test : {{ $levelTest->title }}</h5>
            <form id="editTestForm">
                @csrf
                @method('PUT')
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
                                            <h5 class="mb-0 steper-title">Edit Questions</h5>
                                            <p class="mb-0 steper-sub-title">Edit test questions</p>
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
                                        <input type="text" class="form-control" id="test-title" name="title"
                                            value="{{ $levelTest->title }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="test-description" class="form-label">Description</label>
                                        <textarea class="form-control" id="test-description" name="description">{{ $levelTest->description }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="next-to-step2">Next</button>
                                </div>
                                <div id="step2" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper3trigger2">
                                    <div id="questions-container">
                                        <h5>Questions</h5>
                                        <!-- Existing questions will be populated dynamically here -->
                                    </div>
                                    <button type="button" class="btn btn-secondary" id="add-question-button">Add
                                        Question</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">Update Test</button>
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
            <label>Question Type</label>
            <select class="form-select question-type" name="questions[][type]" required>
                <option value="" disabled>Select Type</option>
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
            {{-- <label>Mark</label>
            <input type="number" class="form-control question-mark" name="questions[][mark]" required>
            <div class="invalid-feedback"></div>
            <label>Media URL</label>
            <input type="text" class="form-control question-media-url" name="questions[][media_url]">
            <div class="invalid-feedback"></div>
            <label>Media Type</label>
            <select class="form-select question-media-type" name="questions[][media_type]">
                <option value="" disabled selected>Select Media Type</option>
                <option value="image">Image</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
                <option value="document">Document</option>
            </select>
            <div class="invalid-feedback"></div> --}}
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
                if ($('#test-title').val()) {
                    stepper3.next();
                } else {
                    showFieldError($('#test-title'), 'Please enter a test title.');
                }
            });

            $('#add-question-button').on('click', function() {
                var questionCount = $('#questions-container .question').length;
                addNewQuestion();
                // if (questionCount < 10) {
                //     addNewQuestion();
                //     if (questionCount + 1 === 10) {
                //         $(this).hide();
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
                questionElement.find('input, select').each(function() {
                    var nameAttr = $(this).attr('name');
                    if (nameAttr) {
                        $(this).attr('name', nameAttr.replace('[]', '[' + questionCount + ']'));
                    }
                });
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
                var questionIndex = $(this).closest('.question').find('.question-number').text();
                if (choicesContainer.children('.choice').length < 4) {
                    addNewChoice($(this).closest('.question'), true, questionIndex);
                    if (choicesContainer.children('.choice').length === 4) {
                        $(this).hide();
                    }
                } else {
                    showAlert('danger', 'You cannot add more than 4 choices.', 'bx bxs-message-square-x');
                }
            });

            function addNewChoice(questionElement, withRemoveButton, questionIndex) {
                var choiceTemplate = $('#choice-template').html();
                var choiceElement = $(choiceTemplate).clone();
                choiceElement.find('input').each(function() {
                    var nameAttr = $(this).attr('name');
                    if (nameAttr) {
                        $(this).attr('name', nameAttr.replace('[]', '[' + questionIndex + ']'));
                    }
                });
                if (!withRemoveButton) {
                    choiceElement.find('.remove-choice-button').remove();
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
                    addDefaultChoices(questionElement);
                } else {
                    questionElement.find('.multiple-choice-options').hide();
                    questionElement.find('.choices-container').empty();
                }
            });

            $(document).on('change', '.choice-correct', function() {
                var currentQuestion = $(this).closest('.question');
                currentQuestion.find('.choice-correct').not(this).prop('checked', false);
            });

            $('#editTestForm').on('submit', function(e) {
                e.preventDefault();

                clearFieldErrors();

                var formData = {
                    _token: $('input[name="_token"]').val(),
                    _method: 'PUT',
                    title: $('#test-title').val(),
                    description: $('#test-description').val(),
                    questions: []
                };

                var valid = true;
                $('#questions-container .question').each(function(index) {
                    var questionIndex = $(this).find('.question-number').text();
                    var questionData = {
                        text: $(this).find('.question-text').val(),
                        sub_text: $(this).find('.question-sub-text').val(),
                        question_type: $(this).find('.question-type').val(),
                        // mark: $(this).find('.question-mark').val(),
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
                    url: '{{ route('teacherTest.update', $levelTest->id) }}',
                    method: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        showAlert('success', 'Test and questions updated successfully',
                            'bxs-check-circle');
                        window.location.href = "{{ route('teacherTests.index') }}";
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
                            showAlert('danger', 'Error updating test', 'bxs-message-square-x');
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

            // Populate existing questions
            @foreach ($levelTest->questions as $index => $question)
                addExistingQuestion(@json($question), {{ $index + 1 }});
            @endforeach

            function addExistingQuestion(question, index) {
                var questionTemplate = $('#question-template').html();
                var questionElement = $(questionTemplate).clone();
                questionElement.find('.question-number').text(index);
                questionElement.find('.question-text').val(question.question_text);
                questionElement.find('.question-sub-text').val(question.sub_text);
                questionElement.find('.question-type').val(question.question_type);
                // questionElement.find('.question-mark').val(question.mark);
                // questionElement.find('.question-media-url').val(question.media_url);
                // questionElement.find('.question-media-type').val(question.media_type);

                questionElement.find('input').each(function() {
                    var nameAttr = $(this).attr('name');
                    if (nameAttr) {
                        $(this).attr('name', nameAttr.replace('[]', '[' + index + ']'));
                    }
                });

                questionElement.find('select').each(function() {
                    var nameAttr = $(this).attr('name');
                    if (nameAttr) {
                        $(this).attr('name', nameAttr.replace('[]', '[' + index + ']'));
                    }
                });

                if (question.question_type === 'choice') {
                    questionElement.find('.multiple-choice-options').show();
                    questionElement.find('.choices-container').empty();
                    question.choices.forEach(function(choice, choiceIndex) {
                        addExistingChoice(questionElement, choice, choiceIndex < 2, index);
                    });
                } else {
                    questionElement.find('.multiple-choice-options').hide();
                    questionElement.find('.choices-container').empty();
                }

                if (index === 1) {
                    questionElement.find('.remove-question-button').remove();
                }

                $('#questions-container').append(questionElement);
            }

            function addExistingChoice(questionElement, choice, isDefault, questionIndex) {
                var choiceTemplate = $('#choice-template').html();
                var choiceElement = $(choiceTemplate).clone();
                choiceElement.find('.choice-text').val(choice.choice_text);
                choiceElement.find('input').each(function() {
                    var nameAttr = $(this).attr('name');
                    if (nameAttr) {
                        $(this).attr('name', nameAttr.replace('[]', '[' + questionIndex + ']'));
                    }
                });
                if (choice.is_correct) {
                    choiceElement.find('.choice-correct').prop('checked', true);
                }
                if (isDefault) {
                    choiceElement.find('.remove-choice-button').remove();
                }
                questionElement.find('.choices-container').append(choiceElement);
            }
        });
    </script>
@endsection

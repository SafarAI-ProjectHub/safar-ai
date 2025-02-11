<?php $__env->startSection('styles'); ?>
    <style>
        .question-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            padding: 15px;
        }

        .question-header {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .question-body {
            padding: 15px;
        }

        .question-text {
            font-weight: bold;
        }

        .sub-text {
            font-style: italic;
            margin-bottom: 10px;
        }

        .instruction-text {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-check-input[type=checkbox] {
            width: 40px;
            height: 20px;
            margin-left: 10px;
        }

        .form-check-primary .form-check-input {
            width: 20px !important;
            height: 20px;
        }

        .form-check-label {
            margin-left: 5px;
        }

        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loader .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .recording {
            background-color: red !important;
        }

        .animated-arrow {
            display: inline-flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .animated-arrow:hover {
            transform: translateX(-5px);
        }

        /* Light Theme */
        .light-theme .question-card {
            background-color: #f8f9fa;
        }

        .light-theme .question-header {
            background-color: #e9ecef;
        }

        .light-theme .sub-text {
            color: #6c757d;
        }

        .light-theme .correct-answer {
            color: green;
        }

        .light-theme .incorrect-answer {
            color: red;
        }

        .light-theme .note-text {
            color: blue;
        }

        /* Dark Theme */
        .dark-theme .question-card {
            background-color: #2c2c2c;
            border-color: #444;
        }

        .dark-theme .question-header {
            background-color: #3c3c3c;
            border-bottom-color: #444;
        }

        .dark-theme .question-body {
            color: #ddd;
        }

        .dark-theme .question-text {
            color: #fff;
        }

        .dark-theme .sub-text {
            color: #999;
        }

        .dark-theme .correct-answer {
            color: #28a745;
        }

        .dark-theme .incorrect-answer {
            color: #dc3545;
        }

        .dark-theme .note-text {
            color: #17a2b8;
        }

        .dark-theme .assessment-info th {
            background-color: #444;
        }

        .dark-theme .assessment-info td,
        .dark-theme .assessment-info th {
            border-color: #555;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="loader" style="display: none;">
        <div class="spinner-border text-primary" role="status">
        </div>
    </div>

    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <?php if($quiz): ?>
                    <h2>Title: <?php echo e($quiz->title); ?></h2>
                    <p><?php echo e($quiz->unit->title); ?> - <?php echo e($quiz->unit->course->title); ?></p>
                <?php else: ?>
                    <h2>No Activity Available</h2>
                <?php endif; ?>
            </div>
        </div>
        <?php if($quiz): ?>
            <form id="quiz-form" method="POST" action="<?php echo e(route('student.quiz.submit', $quiz->id)); ?>"
                enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="question-card mb-4">
                        <div class="question-header">
                            <h5 class="card-title">Question <?php echo e($loop->iteration); ?> : <?php echo e($question->question_text); ?></h5>
                        </div>
                        <div class="question-body">
                            <?php if($question->sub_text): ?>
                                <p class="sub-text"><strong>Note:</strong><?php echo e($question->sub_text); ?></p>
                            <?php endif; ?>

                            <?php if($question->question_type === 'text'): ?>
                                <div class="form-group">
                                    <label class="instruction-text">Please write your answer below:</label>
                                    <textarea class="form-control" name="question_<?php echo e($question->id); ?>" required></textarea>
                                </div>
                            <?php elseif($question->question_type === 'voice'): ?>
                                <div class="form-group">
                                    <label class="instruction-text">Please record your answer:</label>
                                    <button type="button" class="btn btn-primary record-btn mb-3"
                                        data-target="audio-playback_<?php echo e($loop->iteration); ?>">
                                        Record <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                                <input type="file" id="audio-upload_<?php echo e($loop->iteration); ?>"
                                    name="question_<?php echo e($question->id); ?>" style="display:none;" accept="audio/*">
                                <audio id="audio-playback_<?php echo e($loop->iteration); ?>" controls
                                    style="display:none; width: 100%;"></audio>
                            <?php elseif($question->question_type === 'choice'): ?>
                                <label class="instruction-text">Please select one of the following options:</label>
                                <?php $__currentLoopData = $question->choices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $choice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check form-check-primary">
                                        <input class="form-check-input" type="radio" name="question_<?php echo e($question->id); ?>"
                                            id="option_<?php echo e($loop->index); ?>" value="<?php echo e($choice->id); ?>" required>
                                        <label class="form-check-label"
                                            for="option_<?php echo e($loop->index); ?>"><?php echo e($choice->choice_text); ?></label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Submit Activity</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script>
        $(document).ready(function() {

            $('.sidebar-wrapper').block({
                message: '<div style="color: #000; font-size: 16px;">The sidebar will be available after the exam.</div>',
                overlayCSS: {
                    backgroundColor: '#000',
                    opacity: 0.6,
                    cursor: 'not-allowed'
                }
            });

            $('.navbar ').block({
                message: null,
                overlayCSS: {
                    backgroundColor: '#000',
                    opacity: 0.6,
                    cursor: 'not-allowed'
                }
            });

            const recordBtns = $('.record-btn');
            const audioUploads = $('input[type="file"]');
            let mediaRecorders = [];
            let audioChunks = [];
            let isRecordingCompleted = Array(recordBtns.length).fill(false);
            let activeRecorderIndex = null;

            recordBtns.each(function(index) {
                $(this).on('click', function() {
                    const recordBtn = $(this);
                    const audioPlayback = $('#' + recordBtn.data('target'))[0];
                    if (activeRecorderIndex !== null && activeRecorderIndex !== index) {
                        mediaRecorders[activeRecorderIndex].stop();
                        $(recordBtns[activeRecorderIndex]).text('Record').removeClass('recording');
                    }

                    if (!mediaRecorders[index] || mediaRecorders[index].state === 'inactive') {
                        navigator.mediaDevices.getUserMedia({
                                audio: true
                            })
                            .then(stream => {
                                mediaRecorders[index] = new MediaRecorder(stream);
                                mediaRecorders[index].start();
                                activeRecorderIndex = index;
                                isRecordingCompleted[index] = false;
                                recordBtn.addClass('recording').text('Stop Recording');
                                mediaRecorders[index].addEventListener('dataavailable',
                                    event => {
                                        audioChunks[index] = event.data;
                                    });
                                mediaRecorders[index].addEventListener('stop', () => {
                                    const audioBlob = new Blob([audioChunks[index]], {
                                        type: 'audio/wav'
                                    });
                                    audioChunks[index] = [];
                                    const audioUrl = URL.createObjectURL(audioBlob);
                                    audioPlayback.src = audioUrl;
                                    $(audioPlayback).show();
                                    const file = new File([audioBlob],
                                        `recording_${index + 1}.wav`, {
                                            type: 'audio/wav'
                                        });
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    audioUploads[index].files = dataTransfer.files;
                                    isRecordingCompleted[index] = true;
                                    recordBtn.removeClass('recording').text('Record');
                                    activeRecorderIndex = null;
                                });
                            });
                    } else if (mediaRecorders[index].state === 'recording') {
                        mediaRecorders[index].stop();
                        recordBtn.text('Record');
                    }
                });
            });

            $('#quiz-form').on('submit', function(event) {
                event.preventDefault();

                let allAudioRecorded = true;
                audioUploads.each(function(index, input) {
                    if ($(input).attr('required') && !isRecordingCompleted[index]) {
                        allAudioRecorded = false;
                    }
                });

                if (!allAudioRecorded) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete Recordings',
                        text: 'Please complete all required audio recordings before submitting the activity.',
                    });
                    return;
                }

                audioUploads.each(function() {
                    $(this).removeAttr('required');
                });

                $('#loader').css('display', 'flex');
                const formData = new FormData(this);

                $.ajax({
                    url: '<?php echo e(route('student.quiz.submit', $quiz->id)); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(data) {
                        $('#loader').css('display', 'none');
                        if (data.success) {
                            window.location.href = '<?php echo e(route('student.quizzes.list')); ?>';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Error',
                                text: 'An error occurred while submitting your activity. Please try again.',
                            });
                        }
                    },
                    error: function(error) {
                        $('#loader').css('display', 'none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Error',
                            text: 'An error occurred while submitting your activity. Please try again.',
                        });
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/student/quiz.blade.php ENDPATH**/ ?>
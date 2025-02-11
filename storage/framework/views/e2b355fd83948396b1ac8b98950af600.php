<?php $__env->startSection('styles'); ?>
    <style>
        .question-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 15px;
        }

        .question-header {
            background-color: #e9ecef;
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
            color: #6c757d;
            margin-bottom: 10px;
        }

        .correct-answer {
            color: green;
            font-weight: bold;
        }

        .incorrect-answer {
            color: red;
            font-weight: bold;
        }

        .note-text {
            color: blue;
            font-style: italic;
        }

        .assessment-info {
            margin-bottom: 20px;
        }

        .assessment-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .assessment-info th,
        .assessment-info td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .assessment-info th {
            background-color: #f8f9fa;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2>Activity Result: <?php echo e($assessment->quiz->title); ?></h2>
                <p><?php echo e($assessment->quiz->unit->title); ?> - <?php echo e($assessment->quiz->unit->course->title); ?></p>
            </div>
        </div>

        <?php if($assessment->ai_assessment || $assessment->teacher_review): ?>
            <div class="assessment-info">
                <table>
                    <thead>
                        <tr>
                            <th>Assessment Type</th>
                            <th>Mark</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($assessment->ai_assessment): ?>
                            <tr>
                                <td>AI Assessment</td>
                                <td><?php echo e($assessment->ai_mark); ?> / 100</td>
                                <td><?php echo e($assessment->ai_notes); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if($assessment->teacher_review): ?>
                            <tr>
                                <td>Teacher Review</td>
                                <td><?php echo e($assessment->teacher_mark); ?> / 100</td>
                                <td><?php echo e($assessment->teacher_notes); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php $__currentLoopData = $assessment->quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="question-card mb-4">
                <div class="question-header">
                    <h5 class="card-title">Question <?php echo e($loop->iteration); ?>: <?php echo e($question->question_text); ?></h5>
                    <p><strong>Type:</strong>
                        <?php if($question->question_type === 'text'): ?>
                            Writing Question
                        <?php elseif($question->question_type === 'voice'): ?>
                            Speaking Question
                        <?php elseif($question->question_type === 'choice'): ?>
                            Multiple Choice Question
                        <?php endif; ?>
                    </p>
                </div>
                <div class="question-body">
                    <?php if($question->sub_text): ?>
                        <p class="sub-text"><strong>Note:</strong> <?php echo e($question->sub_text); ?></p>
                    <?php endif; ?>

                    <?php
                        $response = $assessment->userResponses->where('question_id', $question->id)->first();
                    ?>

                    <?php if($response): ?>
                        <?php if($question->question_type === 'voice'): ?>
                            <p class="instruction-text">Your Answer:</p>
                            <audio controls>
                                <source src="<?php echo e(asset($response->response)); ?>" type="audio/wav">
                                Your browser does not support the audio element.
                            </audio>
                        <?php else: ?>
                            <p class="instruction-text">Your Answer: <?php echo e($response->response); ?></p>
                        <?php endif; ?>

                        <?php if($assessment->ai_assessment || $assessment->teacher_review): ?>
                            <?php if($question->question_type === 'choice'): ?>
                                <p class="instruction-text"><strong>Choices:</strong></p>
                                <?php
                                    $choices = ['a', 'b', 'c', 'd'];
                                ?>
                                <?php $__currentLoopData = $question->choices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $choice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <p
                                        class="<?php if($choice->choice_text === $response->response): ?> <?php if($response->correct): ?> correct-answer <?php else: ?> incorrect-answer <?php endif; ?>
<?php elseif($choice->is_correct): ?>
correct-answer <?php endif; ?>">
                                        <strong><?php echo e($choices[$index]); ?>.</strong> <?php echo e($choice->choice_text); ?>

                                        <?php if($choice->choice_text === $response->response): ?>
                                            (Your answer)
                                        <?php elseif($choice->is_correct): ?>
                                            (Correct answer)
                                        <?php endif; ?>
                                    </p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                            <p class="note-text">Notes: <?php echo e($response->ai_review ?? 'Pending Review'); ?></p>
                            <?php if($response->teacher_review): ?>
                                <p class="note-text">Teacher Review: <?php echo e($response->teacher_review); ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="note-text">Awaiting review.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="incorrect-answer">No response</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(player => {
                player.addEventListener('play', () => {
                    audioPlayers.forEach(p => {
                        if (p !== player) {
                            p.pause();
                        }
                    });
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/student/quiz_result.blade.php ENDPATH**/ ?>
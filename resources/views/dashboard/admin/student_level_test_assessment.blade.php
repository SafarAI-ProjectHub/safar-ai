@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <h4 class="card-title">Student Level Test Assessment</h4>
                <table id="students-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Proficiency Level</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Assessment Modal -->
    <div class="modal fade" id="assessmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Student Assessment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dev class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h2>Instructions</h2>
                        </div>
                        <dev class="card-body mb-3">

                            <p>1. Mark the correct answers and provide admin review for each question.</p>
                            <p>2. Make sure Voice questions are reviewed by listening to the audio.</p>
                            <p>3. You can update the English Proficiency Level of the student based on the assessment.</p>
                            <p>4. Click on the "Save Changes" button to save the updates.</p>
                        </dev>
                    </dev>
                    <div id="assessmentDetails">
                        <!-- Assessment details will be dynamically loaded here -->
                    </div>
                    <div class="mb-3">
                        <label for="english_proficiency_level" class="form-label">English Proficiency Level</label>
                        <select class="form-select" id="english_proficiency_level">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#students-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.getStudentsWithAssessments') }}',
                columns: [{
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'level',
                        name: 'level'
                    },
                    {
                        data: 'age',
                        name: 'age'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#students-table').on('click', '.view-assessment', function() {
                var studentId = $(this).data('id');
                $.get('/admin/students/' + studentId + '/assessments', function(data) {
                    var assessmentsHtml = '';
                    data.assessments.forEach(function(assessment) {
                        var responseContent = '';
                        // Display the user's response differently based on the type
                        if (assessment.question && assessment.question.question_type ===
                            'voice') {
                            responseContent =
                                `<audio controls><source src="/${assessment.response}" type="audio/wav">Your browser does not support the audio element.</audio>`;
                        } else {
                            responseContent = assessment.response;
                        }

                        var mediaContent = '';
                        // Check if the question has an associated audio file based on media_type
                        if (assessment.question && assessment.question.media_type ===
                            'audio') {
                            mediaContent =
                                `<audio controls style="margin-top:10px;"><source src="{{ asset('${assessment.question.media_url}') }}" type="audio/mpeg">Your browser does not support the audio element.</audio>`;
                        }

                        assessmentsHtml += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Question: ${assessment.question ?assessment.question.question_text :'N/A'}</h5>
                        ${mediaContent}
                    </div>
                    <div class="card-body">
                        <p><strong>Answer:</strong> ${responseContent}</p>
                        <p><strong>Correct:</strong> ${assessment.correct ? 'Yes' : 'No'}</p>
                        <p><strong>AI Review:</strong> ${assessment.ai_review}</p>
                        <p><strong>Admin Review:</strong> ${assessment.admin_review || ''}</p>
                        <div class="mb-3">
                            <label for="correct_${assessment.id}" class="form-label">Mark as Correct</label>
                            <input type="checkbox" class="form-check-input" id="correct_${assessment.id}" ${assessment.correct ? 'checked' : ''} data-id="${assessment.id}" data-student="${studentId}">
                        </div>
                        <div class="mb-3">
                            <label for="admin_review_${assessment.id}" class="form-label">Admin Review</label>
                            <textarea class="form-control" id="admin_review_${assessment.id}" rows="3">${assessment.admin_review || ''}</textarea>
                        </div>
                    </div>
                </div>
            `;
                    });

                    // Set the current English proficiency level
                    $('#english_proficiency_level').val(data.student.english_proficiency_level);

                    // Populate the modal with the assessments
                    $('#assessmentDetails').html(assessmentsHtml);
                    $('#assessmentModal').modal('show');
                });
            });



            $('#saveChanges').on('click', function() {
                var studentId = $('#assessmentDetails').find('.form-check-input').data('student');
                var englishProficiencyLevel = $('#english_proficiency_level').val();

                $('#assessmentModal .card').each(function() {
                    var assessmentId = $(this).find('input[type="checkbox"]').data('id');
                    var isCorrect = $(this).find('input[type="checkbox"]').is(':checked');
                    var adminReview = $(this).find('textarea').val();

                    $.ajax({
                        url: '/admin/students/' + studentId + '/assessments/' +
                            assessmentId,
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            correct: isCorrect,
                            admin_review: adminReview,
                            english_proficiency_level: englishProficiencyLevel
                        },
                        success: function(response) {
                            $('#assessmentModal').modal('hide');
                            table.ajax.reload();
                            showAlert('success', 'Assessment updated successfully!',
                                'bxs-check-circle');
                        },
                        error: function(response) {
                            showAlert('danger', 'Error updating assessment',
                                'bxs-message-square-x');
                        }
                    });
                });
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

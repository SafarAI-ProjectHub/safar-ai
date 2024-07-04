@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <h4 class="card-title">Teacher Level Test Assessment</h4>
                <table id="teachers-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Years of Experience</th>
                            <th>Country Location</th>
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
                    <h5 class="modal-title">Teacher Assessment Details</h5>
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
                            <p>3. Click on the "Save Changes" button to save the updates.</p>
                        </dev>
                    </dev>
                    <div id="assessmentDetails">
                        <!-- Assessment details will be dynamically loaded here -->
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
            var table = $('#teachers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.getTeachersWithAssessments') }}',
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
                        data: 'years_of_experience',
                        name: 'years_of_experience'
                    },
                    {
                        data: 'country_location',
                        name: 'country_location'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#teachers-table').on('click', '.view-assessment', function() {
                var teacherId = $(this).data('id');
                $.get('/admin/teachers/' + teacherId + '/assessments', function(data) {
                    var assessmentsHtml = '';
                    data.assessments.forEach(function(assessment) {
                        var responseContent = '';
                        if (assessment.question.question_type === 'voice') {
                            responseContent =
                                `<audio controls><source src="/storage/${assessment.response}" type="audio/wav">Your browser does not support the audio element.</audio>`;
                        } else {
                            responseContent = assessment.response;
                        }

                        assessmentsHtml += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Question: ${assessment.question.question_text}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Answer:</strong> ${responseContent}</p>
                            <p><strong>Correct:</strong> ${assessment.correct ? 'Yes' : 'No'}</p>
                            <p><strong>AI Review:</strong> ${assessment.ai_review}</p>
                            <p><strong>Admin Review:</strong> ${assessment.admin_review || ''}</p>
                            <div class="mb-3">
                                <label for="correct_${assessment.id}" class="form-label">Mark as Correct</label>
                                <input type="checkbox" class="form-check-input" id="correct_${assessment.id}" ${assessment.correct ? 'checked' : ''} data-id="${assessment.id}" data-teacher="${teacherId}">
                            </div>
                            <div class="mb-3">
                                <label for="admin_review_${assessment.id}" class="form-label">Admin Review</label>
                                <textarea class="form-control" id="admin_review_${assessment.id}" rows="3">${assessment.Admin_review ? assessment.Admin_review : ''}</textarea>
                            </div>
                        </div>
                    </div>
                `;
                    });
                    $('#assessmentDetails').html(assessmentsHtml);
                    $('#assessmentModal').modal('show');
                });
            });

            $('#saveChanges').on('click', function() {
                $('#assessmentModal .card').each(function() {
                    var assessmentId = $(this).find('input[type="checkbox"]').data('id');
                    var teacherId = $(this).find('input[type="checkbox"]').data('teacher');
                    var isCorrect = $(this).find('input[type="checkbox"]').is(':checked');
                    var adminReview = $(this).find('textarea').val();

                    $.ajax({
                        url: '/admin/teachers/' + teacherId + '/assessments/' +
                            assessmentId,
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            correct: isCorrect,
                            admin_review: adminReview
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

@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('zoom-meetings.create') }}" class="btn btn-primary">Add New Zoom Meeting</a>
            </div>
            <div class="table-responsive">
                <table id="zoom-meetings-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Topic</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Join URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Zoom Meeting Details Modal -->
    <div class="modal fade" id="zoomMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Zoom Meeting Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Topic:</strong> <span id="meetingTopic"></span></p>
                    <p><strong>Agenda:</strong> <span id="meetingAgenda"></span></p>
                    <p><strong>Start Time:</strong> <span id="meetingStartTime"></span></p>
                    <p><strong>Duration:</strong> <span id="meetingDuration"></span> minutes</p>
                    <p><strong>Join URL:</strong> <a href="#" id="meetingJoinUrl" target="_blank"></a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#zoom-meetings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('zoom-meetings.datatable') }}",
                columns: [{
                        data: 'topic',
                        name: 'topic'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'duration',
                        name: 'duration'
                    },
                    {
                        data: 'join_url',
                        name: 'join_url'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#zoom-meetings-table').on('click', '.view-meeting', function() {
                var meetingId = $(this).data('id');
                $.get('/zoom-meetings/' + meetingId, function(data) {
                    $('#meetingTopic').text(data.topic);
                    $('#meetingAgenda').text(data.agenda);
                    $('#meetingStartTime').text(data.start_time);
                    $('#meetingDuration').text(data.duration);
                    $('#meetingJoinUrl').text(data.join_url).attr('href', data.join_url);
                    $('#zoomMeetingModal').modal('show');
                });
            });
        });
    </script>
@endsection

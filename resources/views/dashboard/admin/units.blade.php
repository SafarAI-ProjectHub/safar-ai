@extends('layouts_dashboard.main')

@section('styles')
    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .modal-content {

            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Units for Course: <a href="{{ route('admin.courses') }}">{{ $course->title }}</a></h5>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">Add New Unit</button>
            </div>
            <div class="table-responsive">
                <table id="units-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Content Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Unit Modal -->
    <div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <form id="addUnitForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUnitModalLabel">Add New Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Unit Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle">
                        </div>
                        <div class="mb-3">
                            <label for="content_type" class="form-label">Content Type</label>
                            <select class="form-select" id="content_type" name="content_type" required>
                                <option value="" disabled selected>Select Content Type</option>
                                <option value="video">Video</option>
                                <option value="text">Text</option>
                            </select>
                        </div>
                        <div class="mb-3" id="text-content" style="display:none;">
                            <label for="editor" class="form-label">Content</label>
                            <div id="editor"></div>
                            <textarea name="content" id="content" style="display:none;"></textarea>
                        </div>
                        <div class="mb-3" id="video-content" style="display:none;">
                            <label for="video" class="form-label">Upload Video</label>
                            <input type="file" class="filepond" name="video" data-allow-reorder="true"
                                data-max-file-size="100MB" data-max-files="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Unit Modal -->
    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <form id="editUnitForm">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="unit_id" id="edit-unit-id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUnitModalLabel">Edit Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit-title" class="form-label">Unit Title</label>
                            <input type="text" class="form-control" id="edit-title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-subtitle" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="edit-subtitle" name="subtitle">
                        </div>
                        <div class="mb-3">
                            <label for="edit-content_type" class="form-label">Content Type</label>
                            <select class="form-select" id="edit-content_type" name="content_type" required>
                                <option value="" disabled selected>Select Content Type</option>
                                <option value="video">Video</option>
                                <option value="text">Text</option>
                            </select>
                        </div>
                        <div class="mb-3" id="edit-text-content" style="display:none;">
                            <label for="edit-editor" class="form-label">Content</label>
                            <div id="edit-editor"></div>
                            <textarea name="content" id="edit-content" style="display:none;"></textarea>
                        </div>
                        <div class="mb-3" id="edit-video-content" style="display:none;">
                            <label for="edit-video" class="form-label">Upload Video</label>
                            <input id="edit-video" type="file" class="filepond" name="video" multiple
                                data-allow-reorder="true" data-max-file-size="100MB" data-max-files="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <!-- FilePond JS and Plugins -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
    </script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-rename/dist/filepond-plugin-file-rename.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <!-- Image Resize Module JS -->
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#units-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.getUnits', $course->id) }}',
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'subtitle',
                        name: 'subtitle'
                    },
                    {
                        data: 'content_type',
                        name: 'content_type'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '20%',
                        render: function(data, type, row) {
                            return `
                        <div class="d-flex justify-content-around">
                            <button class="btn btn-primary btn-sm edit-unit" data-id="${row.id}" data-status="${row.approval_status}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-unit" data-id="${row.id}">Delete</button>
                        </div>
                    `;
                        }
                    },
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-outline-secondary buttons-copy buttons-html5'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-secondary buttons-excel buttons-html5'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-secondary buttons-pdf buttons-html5'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary buttons-print'
                    }
                ],
                columnDefs: [{
                    targets: 3,
                    width: '10%'
                }],
                lengthChange: false
            });

            $('#units-table').on('click', '.delete-unit', function() {
                var unitId = $(this).data('id');
                if (confirm('Are you sure you want to delete this unit?')) {
                    $.ajax({
                        url: '/admin/units/delete/' + unitId,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                'content')
                        },
                        success: function(response) {
                            table.ajax.reload(null,
                                false); // Reload DataTable without resetting paging
                            alert('Unit deleted successfully');
                        },
                        error: function(xhr) {
                            alert('Error deleting unit: ' + xhr.responseText);
                        }
                    });
                }
            });

            table.buttons().container().appendTo('#units-table_wrapper .col-md-6:eq(0)');

            var quillAdd = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'font': []
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'align': []
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        ['link', 'image'], // Added 'image' button
                        ['clean']
                    ],
                    imageResize: {
                        displayStyles: {
                            backgroundColor: 'black',
                            border: 'none',
                            color: 'white'
                        },
                        modules: ['Resize', 'DisplaySize', 'Toolbar']
                    }
                }
            });

            // Initialize Quill Editor for Edit Unit
            var quillEdit = new Quill('#edit-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'font': []
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'align': []
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        ['link', 'image'], // Added 'image' button
                        ['clean']
                    ],
                    imageResize: {
                        displayStyles: {
                            backgroundColor: 'black',
                            border: 'none',
                            color: 'white'
                        },
                        modules: ['Resize', 'DisplaySize', 'Toolbar']
                    }
                }
            });

            function imageHandler() {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function() {
                    var file = input.files[0];
                    if (/^image\//.test(file.type)) {
                        saveToServer(file);
                    } else {
                        console.warn('You could only upload images.');
                    }
                };
            }
            // Handle content type selection for Add Unit
            $('#content_type').on('change', function() {
                var selectedType = $(this).val();
                if (selectedType === 'text') {
                    $('#text-content').show();
                    $('#video-content').hide();
                } else if (selectedType === 'video') {
                    $('#video-content').show();
                    $('#text-content').hide();
                } else {
                    $('#text-content').hide();
                    $('#video-content').hide();
                }
            });

            // Handle content type selection for Edit Unit
            $('#edit-content_type').on('change', function() {
                var selectedType = $(this).val();
                if (selectedType === 'text') {
                    $('#edit-text-content').show();
                    $('#edit-video-content').hide();
                } else if (selectedType === 'video') {
                    $('#edit-video-content').show();
                    $('#edit-text-content').hide();
                } else {
                    $('#edit-text-content').hide();
                    $('#edit-video-content').hide();
                }
            });
            const contentTypeSelect = document.getElementById('content_type');
            contentTypeSelect.addEventListener('change', function() {
                if (this.value === 'text') {
                    textContentDiv.style.display = 'block';
                    videoContentDiv.style.display = 'none';
                } else if (this.value === 'video') {
                    textContentDiv.style.display = 'none';
                    videoContentDiv.style.display = 'block';
                } else {
                    textContentDiv.style.display = 'none';
                    videoContentDiv.style.display = 'none';
                }
            });

            const acceptedVideoTypes = ['video/*']; // Accepts all video types

            // Register FilePond plugins
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageExifOrientation,
                FilePondPluginFileValidateSize,
                FilePondPluginImageEdit,
                FilePondPluginFileValidateType,
                FilePondPluginFileRename
            );


            const pondAdd = FilePond.create(document.querySelector('input[name="video"]'), {
                allowFileTypeValidation: true,
                acceptedFileTypes: acceptedVideoTypes,
                fileValidateTypeLabelExpectedTypes: 'Expected file type: Video'
            });


            const pondEdit = FilePond.create(document.querySelector('#edit-video'), {
                allowFileTypeValidation: true,
                acceptedFileTypes: acceptedVideoTypes,
                fileValidateTypeLabelExpectedTypes: 'Expected file type: Video'
            });

            // Function to clear modal contents
            function clearModal(modalId) {
                $(modalId + ' input[type="text"], ' + modalId + ' textarea').val('');
                if (modalId === '#addUnitModal') {
                    pondAdd.removeFiles();
                    quillAdd.setContents([]);
                } else {
                    pondEdit.removeFiles();
                    quillEdit.setContents([]);
                }
            }

            // Clear modals on close
            $('#addUnitModal, #editUnitModal').on('hidden.bs.modal', function() {
                clearModal('#' + this.id);
            });


            $('#addUnitForm').on('submit', function(e) {
                e.preventDefault();
                var originalFormData = new FormData(this);
                var newFormData = new FormData();

                for (var pair of originalFormData.entries()) {
                    if (pair[0] !== 'video') {
                        if (pair[0] !== 'content') {
                            newFormData.append(pair[0], pair[1]);
                        }
                    }
                }

                // Get the file from FilePond
                var file = pondEdit.getFile();
                if (file) {
                    newFormData.append('video', file.file);
                }


                newFormData.append('content', quillAdd.root.innerHTML);

                // AJAX request to server
                $.ajax({
                    url: '{{ route('admin.storeUnit') }}',
                    method: 'POST',
                    data: newFormData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#addUnitModal').modal('hide');
                        showAlert('success', 'Unit added successfully!');
                        clearModal('#addUnitModal');
                        table.ajax.reload();
                    },
                    error: function(response) {
                        showAlert('danger', 'Error adding unit');
                    }
                });
            });

            // Handle form submission for Edit Unit
            $('#editUnitForm').on('submit', function(e) {
                e.preventDefault();
                var originalFormData = new FormData(this);
                var newFormData = new FormData();

                for (var pair of originalFormData.entries()) {
                    if (pair[0] !== 'video') {
                        newFormData.append(pair[0], pair[1]);
                    }
                }

                // Get the file from FilePond
                var file = pondEdit.getFile();
                if (file) {
                    newFormData.append('video', file.file);
                }


                newFormData.append('content', quillEdit.root.innerHTML);

                // AJAX request to server
                $.ajax({
                    url: '/admin/units/' + $('#edit-unit-id').val(),
                    method: 'POST',
                    data: newFormData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editUnitModal').modal('hide');
                        showAlert('success', 'Unit updated successfully!');
                        clearModal('#editUnitModal');
                    },
                    error: function(response) {
                        showAlert('danger', 'Error updating unit');
                    }
                });
            });

            $(document).on('click', '.edit-unit', function() {
                var unitId = $(this).data('id');
                $.ajax({
                    url: '/admin/units/' + unitId + '/edit',
                    method: 'GET',
                    success: function(data) {
                        $('#edit-unit-id').val(data.id);
                        $('#edit-title').val(data.title);
                        $('#edit-subtitle').val(data.subtitle);
                        $('#edit-content_type').val(data.content_type).change();

                        if (data.content_type === 'text') {
                            $('#edit-text-content').show();
                            $('#edit-video-content').hide();
                            quillEdit.root.innerHTML = data.content;
                        } else if (data.content_type === 'video') {
                            $('#edit-video-content').show();
                            $('#edit-text-content').hide();
                        }

                        $('#editUnitModal').modal('show');
                    }
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

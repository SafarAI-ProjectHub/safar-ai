<?php $__env->startSection('styles'); ?>
    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <!-- Fabric.js -->
    <script src="https://cdn.jsdelivr.net/npm/fabric@5.2.4/dist/fabric.min.js"></script>

    <style>
        .modal-content {
            overflow-y: auto;
        }
        .index-0 {
            z-index: 0 !important;
        }
        #designCanvas {
            border: 1px dashed #ccc;
            cursor: default;
        }
        .shape-row, .text-row {
            margin-bottom: 10px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body">
        <h5>Lessons for Unit: <a href="<?php echo e(route('admin.courses')); ?>"><?php echo e($course->title); ?></a></h5>
        <div class="alert alert-info index-0" role="alert">
            <strong>Note:</strong> The script is used by the AI to check the correctness of student answers in quizzes.
            please make sure the script is accurate.
        </div>

        <?php if($course->completed): ?>
            <div class="alert alert-success index-0" role="alert">
                This Unit is marked as completed. You can no longer add Lessons to it.
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    Add New Lesson
                </button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="units-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
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

<!-- Add Lesson Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <form id="addUnitForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addUnitModalLabel">Add New Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Title, Subtitle -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Lesson Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="subtitle" class="form-label">Subtitle</label>
                        <input type="text" class="form-control" id="subtitle" name="subtitle">
                    </div>

                    <!-- Content Type -->
                    <div class="mb-3">
                        <label for="content_type" class="form-label">Content Type</label>
                        <select class="form-select" id="content_type" name="content_type" required>
                            <option value="" disabled selected>Select Content Type</option>
                            <option value="video">Video</option>
                            <option value="text">Text</option>
                            <option value="youtube">Youtube</option>
                        </select>
                    </div>

                    <!-- Text Content + Canvas Combined -->
                    <div id="text-and-canvas" style="display:none;">
                        <label class="form-label">Content</label>
                        <div id="editor"></div>
                        <!-- hidden field to store Quill HTML -->
                        <textarea name="content" id="content" style="display:none;"></textarea>

                        <hr>
                        <h5>Design Board</h5>
                        <div class="mb-2">
                            <label>Canvas Size:</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" id="designWidth" placeholder="Width" value="800"
                                       class="form-control" style="max-width:120px;">
                                <input type="number" id="designHeight" placeholder="Height" value="400"
                                       class="form-control" style="max-width:120px;">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="applySizeBtn">
                                    Apply Size
                                </button>
                            </div>
                        </div>

                        <!-- Shapes Row -->
                        <div class="shape-row d-flex gap-2 align-items-end flex-wrap">
                            <div>
                                <label>Shape:</label>
                                <select id="shapeType" class="form-select form-select-sm">
                                    <option value="rect">Rectangle</option>
                                    <option value="circle">Circle</option>
                                    <option value="ellipse">Ellipse</option>
                                    <option value="line">Line</option>
                                    <option value="star">Star</option>
                                    <option value="arrow">Arrow</option>
                                    <option value="diamond">Diamond</option>
                                    <option value="trapezoid">Trapezoid</option>
                                    <option value="zigzag">Zigzag</option>
                                </select>
                            </div>
                            <div>
                                <label>Fill:</label>
                                <input type="color" id="shapeFill" value="#00BFFF"
                                       class="form-control form-control-color" style="width:60px;">
                            </div>
                            <div>
                                <label>Stroke:</label>
                                <input type="color" id="shapeStroke" value="#000000"
                                       class="form-control form-control-color" style="width:60px;">
                            </div>
                            <div>
                                <label>Stroke W:</label>
                                <input type="number" id="shapeStrokeWidth" class="form-control form-control-sm"
                                       style="max-width:80px;" value="1">
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="addShapeBtn">
                                Add Shape
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" id="removeSelectedBtn">
                                Remove Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="bringToFrontBtn">
                                Bring Front
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="sendToBackBtn">
                                Send Back
                            </button>
                        </div>

                        <!-- Text Row -->
                        <div class="text-row d-flex gap-2 align-items-end flex-wrap mt-3">
                            <div>
                                <label>Text:</label>
                                <input type="text" id="textString" class="form-control form-control-sm"
                                       placeholder="Enter text...">
                            </div>
                            <div>
                                <label>Color:</label>
                                <input type="color" id="textColor" value="#000000"
                                       class="form-control form-control-color" style="width:60px;">
                            </div>
                            <div>
                                <label>Font Size:</label>
                                <input type="number" id="textSize" class="form-control form-control-sm"
                                       style="max-width:80px;" value="20">
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="addTextBtn">
                                Add Text
                            </button>
                        </div>

                        <canvas id="designCanvas" width="800" height="400" style="margin-top:10px;"></canvas>
                        <div class="mt-3">
                            <button type="button" class="btn btn-info" id="insertDesignBtn">
                                Insert Design to Editor
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="clearCanvasBtn">
                                Clear Canvas
                            </button>
                        </div>
                    </div>

                    <!-- Video Content -->
                    <div class="mb-3" id="video-content" style="display:none;">
                        <label for="video" class="form-label">Upload Video</label>
                        <input type="file" class="filepond" name="video" data-allow-reorder="true"
                               data-max-file-size="100MB" data-max-files="1">
                    </div>

                    <!-- Youtube Content -->
                    <div class="mb-3" id="youtube-content" style="display:none;">
                        <label for="youtube" class="form-label">Youtube Link</label>
                        <input type="text" class="youtube form-control" name="youtube">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Script Modal -->
<div class="modal fade" id="showScriptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="updateScriptForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="showScriptModalLabel">Script</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="script" class="form-label">Script</label>
                        <textarea class="form-control" id="script" name="script" rows="10" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Script</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lesson Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <form id="editUnitForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="unit_id" id="edit-unit-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editUnitModalLabel">Edit Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Title, Subtitle -->
                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Lesson Title</label>
                        <input type="text" class="form-control" id="edit-title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-subtitle" class="form-label">Subtitle</label>
                        <input type="text" class="form-control" id="edit-subtitle" name="subtitle">
                    </div>

                    <!-- Content Type -->
                    <div class="mb-3">
                        <label for="edit-content_type" class="form-label">Content Type</label>
                        <select class="form-select" id="edit-content_type" name="content_type" required>
                            <option value="" disabled selected>Select Content Type</option>
                            <option value="video">Video</option>
                            <option value="text">Text</option>
                            <option value="youtube">Youtube</option>
                        </select>
                    </div>

                    <!-- Edit: Text + Canvas combined -->
                    <div id="edit-text-and-canvas" style="display:none;">
                        <label class="form-label">Content</label>
                        <div id="edit-editor"></div>
                        <textarea name="content" id="edit-content" style="display:none;"></textarea>

                        <hr>
                        <h5>Design Board</h5>
                        <div class="mb-2">
                            <label>Canvas Size:</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" id="editDesignWidth" placeholder="Width" value="800"
                                       class="form-control" style="max-width:120px;">
                                <input type="number" id="editDesignHeight" placeholder="Height" value="400"
                                       class="form-control" style="max-width:120px;">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="editApplySizeBtn">
                                    Apply Size
                                </button>
                            </div>
                        </div>

                        <div class="shape-row d-flex gap-2 align-items-end flex-wrap">
                            <div>
                                <label>Shape:</label>
                                <select id="editShapeType" class="form-select form-select-sm">
                                    <option value="rect">Rectangle</option>
                                    <option value="circle">Circle</option>
                                    <option value="ellipse">Ellipse</option>
                                    <option value="line">Line</option>
                                    <option value="star">Star</option>
                                    <option value="arrow">Arrow</option>
                                    <option value="diamond">Diamond</option>
                                    <option value="trapezoid">Trapezoid</option>
                                    <option value="zigzag">Zigzag</option>
                                </select>
                            </div>
                            <div>
                                <label>Fill:</label>
                                <input type="color" id="editShapeFill" value="#00BFFF"
                                       class="form-control form-control-color" style="width:60px;">
                            </div>
                            <div>
                                <label>Stroke:</label>
                                <input type="color" id="editShapeStroke" value="#000000"
                                       class="form-control form-control-color" style="width:60px;">
                            </div>
                            <div>
                                <label>Stroke W:</label>
                                <input type="number" id="editShapeStrokeWidth" class="form-control form-control-sm"
                                       style="max-width:80px;" value="1">
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="editAddShapeBtn">
                                Add Shape
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" id="editRemoveSelectedBtn">
                                Remove Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="editBringToFrontBtn">
                                Bring Front
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="editSendToBackBtn">
                                Send Back
                            </button>
                        </div>

                        <div class="text-row d-flex gap-2 align-items-end flex-wrap mt-3">
                            <div>
                                <label>Text:</label>
                                <input type="text" id="editTextString" class="form-control form-control-sm"
                                       placeholder="Enter text...">
                            </div>
                            <div>
                                <label>Color:</label>
                                <input type="color" id="editTextColor" value="#000000"
                                       class="form-control form-control-color" style="width:60px;">
                            </div>
                            <div>
                                <label>Font Size:</label>
                                <input type="number" id="editTextSize" class="form-control form-control-sm"
                                       style="max-width:80px;" value="20">
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="editAddTextBtn">
                                Add Text
                            </button>
                        </div>

                        <canvas id="editDesignCanvas" width="800" height="400" style="margin-top:10px;"></canvas>
                        <div class="mt-3">
                            <button type="button" class="btn btn-info" id="editInsertDesignBtn">
                                Insert Design to Editor
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="editClearCanvasBtn">
                                Clear Canvas
                            </button>
                        </div>
                    </div>

                    <!-- Edit: Video Content -->
                    <div class="mb-3" id="edit-video-content" style="display:none;">
                        <label for="edit-video" class="form-label">Upload Video</label>
                        <input id="edit-video" type="file" class="filepond" name="video" multiple
                               data-allow-reorder="true" data-max-file-size="100MB" data-max-files="1">
                    </div>

                    <!-- Edit: Youtube Content -->
                    <div class="mb-3" id="edit-youtube-content" style="display:none;">
                        <label for="edit-youtube" class="form-label">Youtube Link</label>
                        <input type="text" class="edit-youtube form-control" name="youtube" id="edit-youtube">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Lesson Modal -->
<div class="modal fade" id="viewLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Lesson</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewLessonBody">
                <!-- سيتم تعبئة المحتوى جافاسكربتياً -->
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <!-- FilePond JS + Plugins -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-rename/dist/filepond-plugin-file-rename.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js"></script>

    <!-- Image Resize Module JS for Quill -->
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        let canvasAdd = null;
        let canvasEdit = null;
        let quillAdd, quillEdit;

        // حساب نقاط نجمة (عدد=spines)
        function starPolygonPoints(spines, outerR, innerR){
            let results = [];
            let angle = Math.PI / spines;
            for (let i = 0; i < 2 * spines; i++) {
                let r = (i & 1) === 0 ? outerR : innerR;
                let currX = Math.cos(i * angle) * r;
                let currY = Math.sin(i * angle) * r;
                results.push({ x: currX, y: currY });
            }
            return results;
        }
        // شكل سهم (تقريبي)
        function arrowPoints() {
            return [
                { x: 0,  y: 0 },
                { x: 60, y: 0 },
                { x: 60, y: -20 },
                { x: 100, y: 20 },
                { x: 60, y: 60 },
                { x: 60, y: 40 },
                { x: 0,  y: 40 }
            ];
        }
        // شكل ماسة
        function diamondPoints(w=80, h=80) {
            return [
                { x: 0,    y: -h/2},
                { x: w/2,  y: 0},
                { x: 0,    y: h/2},
                { x: -w/2, y: 0}
            ];
        }
        // شبه منحرف
        function trapezoidPoints(topW=50, bottomW=100, h=60) {
            let halfTop = topW/2, halfBottom= bottomW/2;
            return [
                { x: -halfTop,    y: 0 },
                { x: halfTop,     y: 0 },
                { x: halfBottom,  y: h },
                { x: -halfBottom, y: h }
            ];
        }
        // زكزاك
        function zigzagPoints(steps=5, w=100, h=50) {
            let points = [];
            let dx = w/steps, dy = h/(steps-1);
            for(let i=0; i<steps; i++){
                let x = i * dx;
                let y = i % 2===0 ? 0 : dy;
                points.push({ x, y });
            }
            return points;
        }

        $(document).ready(function() {
            // 1) Datatables
            var table = $('#units-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('admin.getUnits', $course->id)); ?>',
                columns: [
                    { data: 'id',           name: 'id' },
                    { data: 'title',        name: 'title' },
                    { data: 'subtitle',     name: 'subtitle' },
                    { data: 'content_type', name: 'content_type' },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '25%',
                    }
                ],
                columnDefs: [
                    { visible: false, targets: 0 }
                ],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy',  className: 'btn btn-outline-secondary' },
                    { extend: 'excel', className: 'btn btn-outline-secondary' },
                    { extend: 'pdf',   className: 'btn btn-outline-secondary' },
                    { extend: 'print', className: 'btn btn-outline-secondary' }
                ],
                lengthChange: false
            });
            table.buttons().container().appendTo('#units-table_wrapper .col-md-6:eq(0)');

            // 2) Delete
            $('#units-table').on('click', '.delete-unit', function() {
                var unitId = $(this).data('id');
                if(confirm('Are you sure you want to delete this Lesson?')){
                    $.ajax({
                        url: '/courses/units/delete/' + unitId,
                        type: 'DELETE',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function() {
                            table.ajax.reload(null, false);
                            Swal.fire("Success", "Lesson deleted", "success");
                        },
                        error: function(xhr) {
                            Swal.fire("Error", "Error: " + xhr.responseText, "error");
                        }
                    });
                }
            });

            // 3) Show Script
            $('#units-table').on('click', '.show-script', function(){
                var unitId = $(this).data('id');
                $.get('/units/' + unitId + '/script', function(resp){
                    $('#script').val(resp.script);
                    $('#updateScriptForm').data('unit-id', unitId);
                    $('#showScriptModal').modal('show');
                }).fail(function(){
                    showAlert('danger','Error fetching script','bx-error');
                });
            });
            $('#updateScriptForm').on('submit',function(e){
                e.preventDefault();
                let uid = $(this).data('unit-id');
                $.post('/units/'+uid+'/script', $(this).serialize())
                  .done(function(resp){
                      $('#showScriptModal').modal('hide');
                      showAlert('success', resp.success, 'bx-check');
                      table.ajax.reload();
                  })
                  .fail(function(){
                      showAlert('danger','Error updating script','bx-error');
                  });
            });

            // 4) View Lesson (new)
            $('#units-table').on('click', '.view-lesson', function(){
                var unitId = $(this).data('id');
                // جلب بيانات الدرس وعرضها
                $.ajax({
                    url: '/units/'+unitId, // روت showUnit
                    method: 'GET',
                    success:function(resp){
                        // الآن تعبئة محتوى المودال حسب نوع الدرس
                        let htmlContent = '';
                        htmlContent += '<h4>'+resp.title+'</h4>';
                        htmlContent += '<p><strong>Subtitle: </strong>'+ (resp.subtitle ? resp.subtitle : '') +'</p>';
                        htmlContent += '<p><strong>Type: </strong>'+ resp.content_type +'</p>';
                        if(resp.content_type === 'text'){
                            htmlContent += '<hr><div>'+ resp.content +'</div>';
                        } else if(resp.content_type === 'youtube'){
                            htmlContent += '<hr><p>YouTube Video ID: '+ resp.content +'</p>';
                            htmlContent += '<p>(Original Link: '+ resp.script +')</p>';
                        } else if(resp.content_type === 'video'){
                            htmlContent += '<hr><video width="100%" controls src="'+ (resp.content) +'">Video not supported</video>';
                        }
                        $('#viewLessonBody').html(htmlContent);
                        $('#viewLessonModal').modal('show');
                    },
                    error:function(err){
                        Swal.fire("Error", "Cannot fetch lesson data", "error");
                    }
                });
            });


            // 5) Quill Editors
            let advancedModules = {
                toolbar: [
                    [{ 'font': [] }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false]}],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet'}],
                    [{ 'indent': '-1'}, { 'indent': '+1'}],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],
                    ['clean']
                ],
            };

            quillAdd = new Quill('#editor', {
                theme: 'snow',
                modules: advancedModules
            });
            quillEdit = new Quill('#edit-editor', {
                theme: 'snow',
                modules: advancedModules
            });

            // 6) Toggle content types
            $('#content_type').on('change', function(){
                let val = $(this).val();
                $('#text-and-canvas, #video-content, #youtube-content').hide();
                if(val==='text'){
                    $('#text-and-canvas').show();
                } else if(val==='video'){
                    $('#video-content').show();
                } else if(val==='youtube'){
                    $('#youtube-content').show();
                }
            });
            $('#edit-content_type').on('change', function(){
                let val = $(this).val();
                $('#edit-text-and-canvas, #edit-video-content, #edit-youtube-content').hide();
                if(val==='text'){
                    $('#edit-text-and-canvas').show();
                } else if(val==='video'){
                    $('#edit-video-content').show();
                } else if(val==='youtube'){
                    $('#edit-youtube-content').show();
                }
            });

            // 7) Fabric.js (Add)
            let canvas = new fabric.Canvas('designCanvas',{
                backgroundColor:'#fff',
                preserveObjectStacking:true
            });
            canvasAdd = canvas;
            // Double click => edit text
            canvasAdd.on('mouse:dblclick', function(opt){
                if(opt.target && opt.target.type === 'textbox'){
                    opt.target.enterEditing();
                }
            });

            $('#applySizeBtn').click(function(){
                let w = parseInt($('#designWidth').val() || '800');
                let h = parseInt($('#designHeight').val() || '400');
                canvasAdd.setWidth(w);
                canvasAdd.setHeight(h);
                canvasAdd.renderAll();
            });

            // إضافة الأشكال
            $('#addShapeBtn').click(function(){
                let shapeType = $('#shapeType').val();
                let fill = $('#shapeFill').val();
                let stroke = $('#shapeStroke').val();
                let strokeW = parseInt($('#shapeStrokeWidth').val() || '1');

                let obj=null;
                switch(shapeType){
                    case 'rect':
                        obj = new fabric.Rect({ left:100,top:100, fill,stroke,strokeWidth:strokeW, width:100,height:80 });
                        break;
                    case 'circle':
                        obj = new fabric.Circle({ left:120,top:120, fill,stroke,strokeWidth:strokeW, radius:40 });
                        break;
                    case 'ellipse':
                        obj = new fabric.Ellipse({ left:140,top:140, fill,stroke,strokeWidth:strokeW, rx:60, ry:40 });
                        break;
                    case 'line':
                        obj = new fabric.Line([50,50,200,50], { stroke, strokeWidth:strokeW, fill });
                        break;
                    case 'star':
                        let starPts = starPolygonPoints(5, 50, 20);
                        obj = new fabric.Polygon(starPts,{ left:150,top:150, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'arrow':
                        obj = new fabric.Polygon(arrowPoints(),{ left:100,top:100, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'diamond':
                        obj = new fabric.Polygon(diamondPoints(),{ left:150,top:150, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'trapezoid':
                        obj = new fabric.Polygon(trapezoidPoints(),{ left:150,top:150, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'zigzag':
                        obj = new fabric.Polyline(zigzagPoints(6,100,50),{ left:150,top:150, fill:'',stroke,strokeWidth:strokeW });
                        break;
                }
                if(obj){
                    canvasAdd.add(obj).setActiveObject(obj);
                }
            });
            $('#removeSelectedBtn').click(function(){
                let act = canvasAdd.getActiveObject();
                if(act) canvasAdd.remove(act);
            });
            $('#bringToFrontBtn').click(function(){
                let act = canvasAdd.getActiveObject();
                if(act){ act.bringToFront(); canvasAdd.renderAll(); }
            });
            $('#sendToBackBtn').click(function(){
                let act = canvasAdd.getActiveObject();
                if(act){ act.sendToBack(); canvasAdd.renderAll(); }
            });
            // إضافة نص
            $('#addTextBtn').click(function(){
                let txt = $('#textString').val() || 'New Text';
                let color = $('#textColor').val();
                let fz = parseInt($('#textSize').val() || '20');
                let textbox= new fabric.Textbox(txt,{ left:100,top:100, fill:color, fontSize: fz });
                canvasAdd.add(textbox).setActiveObject(textbox);
                textbox.enterEditing();
            });
            // Insert Design
            $('#insertDesignBtn').click(function(){
                let dataURL = canvasAdd.toDataURL('image/png');
                let range = quillAdd.getSelection(true);
                
                $.ajax({
                    url: '/upload-canvas-image',
                    type: 'POST',
                    data: { imageBase64: dataURL },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(resp){
                    quillAdd.insertEmbed(range.index, 'image', resp.url, Quill.sources.USER);
                    quillAdd.setSelection(range.index+1, Quill.sources.SILENT);
                    }
                });
                });
            // Clear
            $('#clearCanvasBtn').click(function(){
                canvasAdd.clear();
            });

            // 8) Fabric.js (Edit)
            let canvas2 = new fabric.Canvas('editDesignCanvas',{
                backgroundColor:'#fff',
                preserveObjectStacking:true
            });
            canvasEdit = canvas2;
            canvasEdit.on('mouse:dblclick', function(opt){
                if(opt.target && opt.target.type === 'textbox'){
                    opt.target.enterEditing();
                }
            });

            $('#editApplySizeBtn').click(function(){
                let w = parseInt($('#editDesignWidth').val()||'800');
                let h = parseInt($('#editDesignHeight').val()||'400');
                canvasEdit.setWidth(w);
                canvasEdit.setHeight(h);
                canvasEdit.renderAll();
            });

            $('#editAddShapeBtn').click(function(){
                let shapeType = $('#editShapeType').val();
                let fill = $('#editShapeFill').val();
                let stroke = $('#editShapeStroke').val();
                let strokeW = parseInt($('#editShapeStrokeWidth').val()||'1');

                let obj=null;
                switch(shapeType){
                    case 'rect':
                        obj = new fabric.Rect({ left:100,top:100, fill,stroke,strokeWidth:strokeW, width:100,height:80 });
                        break;
                    case 'circle':
                        obj = new fabric.Circle({ left:120,top:120, fill,stroke,strokeWidth:strokeW, radius:40 });
                        break;
                    case 'ellipse':
                        obj = new fabric.Ellipse({ left:140,top:140, fill,stroke,strokeWidth:strokeW, rx:60, ry:40 });
                        break;
                    case 'line':
                        obj = new fabric.Line([50,50,200,50], { stroke, strokeWidth:strokeW, fill });
                        break;
                    case 'star':
                        obj = new fabric.Polygon(starPolygonPoints(5, 50, 20),{ left:150,top:150, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'arrow':
                        obj = new fabric.Polygon(arrowPoints(),{ left:100,top:100, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'diamond':
                        obj = new fabric.Polygon(diamondPoints(),{ left:150,top:150, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'trapezoid':
                        obj = new fabric.Polygon(trapezoidPoints(),{ left:150,top:150, fill,stroke,strokeWidth:strokeW });
                        break;
                    case 'zigzag':
                        obj = new fabric.Polyline(zigzagPoints(6,100,50),{ left:150,top:150, fill:'',stroke,strokeWidth:strokeW });
                        break;
                }
                if(obj){
                    canvasEdit.add(obj).setActiveObject(obj);
                }
            });
            $('#editRemoveSelectedBtn').click(function(){
                let act = canvasEdit.getActiveObject();
                if(act) canvasEdit.remove(act);
            });
            $('#editBringToFrontBtn').click(function(){
                let act = canvasEdit.getActiveObject();
                if(act){ act.bringToFront(); canvasEdit.renderAll(); }
            });
            $('#editSendToBackBtn').click(function(){
                let act = canvasEdit.getActiveObject();
                if(act){ act.sendToBack(); canvasEdit.renderAll(); }
            });
            $('#editAddTextBtn').click(function(){
                let txt = $('#editTextString').val()|| 'Edit text';
                let color = $('#editTextColor').val();
                let fs = parseInt($('#editTextSize').val()||'20');
                let tbox= new fabric.Textbox(txt,{ left:100,top:100, fill:color, fontSize: fs });
                canvasEdit.add(tbox).setActiveObject(tbox);
                tbox.enterEditing();
            });
            $('#editClearCanvasBtn').click(function(){
                canvasEdit.clear();
            });
            $('#editInsertDesignBtn').click(function(){
                let dataURL = canvasEdit.toDataURL('image/png');
                let range = quillEdit.getSelection(true);

                $.ajax({
                url: '/upload-canvas-image', 
                type: 'POST',
                data: { imageBase64: dataURL },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(resp){
                    quillEdit.insertEmbed(range.index, 'image', resp.url, Quill.sources.USER);
                    quillEdit.setSelection(range.index+1, Quill.sources.SILENT);
                }
                });
            });


            // 9) FilePond
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
                acceptedFileTypes: ['video/*']
            });
            const pondEdit = FilePond.create(document.querySelector('#edit-video'), {
                allowFileTypeValidation: true,
                acceptedFileTypes: ['video/*']
            });

            // 10) Clear modals on hide
            function clearModal(modalId){
                $(modalId + ' input[type="text"], '+modalId+' textarea').val('');
                if(modalId==='#addUnitModal'){
                    pondAdd.removeFiles();
                    quillAdd.setContents([]);
                    canvasAdd.clear();
                    canvasAdd.setWidth(800); canvasAdd.setHeight(400);
                } else {
                    pondEdit.removeFiles();
                    quillEdit.setContents([]);
                    canvasEdit.clear();
                    canvasEdit.setWidth(800); canvasEdit.setHeight(400);
                }
            }
            $('#addUnitModal, #editUnitModal').on('hidden.bs.modal', function(){
                clearModal('#'+this.id);
            });

            // 11) إضافة درس
            $('#addUnitForm').on('submit', function(e){
                e.preventDefault();
                let originalFormData = new FormData(this);
                let newFormData = new FormData();

                // انقل بقية الحقول عدا الفيديو وcontent
                for(let pair of originalFormData.entries()){
                    if(pair[0]!=='video' && pair[0]!=='content'){
                        newFormData.append(pair[0], pair[1]);
                    }
                }
                // الفيديو
                let file = pondAdd.getFile();
                if(file){
                    newFormData.append('video', file.file);
                }
                // محتوى quill
                newFormData.append('content', quillAdd.root.innerHTML);

                $.ajax({
                    url:'<?php echo e(route('admin.storeUnit')); ?>',
                    method:'POST',
                    data:newFormData,
                    processData:false,
                    contentType:false,
                    success:function(resp){
                        $('#addUnitModal').modal('hide');
                        showAlert('success','Lesson added successfully','bx-check');
                        clearModal('#addUnitModal');
                        table.ajax.reload();
                    },
                    error:function(resp){
                        showAlert('danger','Error adding lesson','bx-error');
                    }
                });
            });

            // 12) تعديل درس
            $('#editUnitForm').on('submit', function(e){
                e.preventDefault();
                let originalFormData = new FormData(this);
                let newFormData = new FormData();

                for(let pair of originalFormData.entries()){
                    if(pair[0]!=='video' && pair[0]!=='content'){
                        newFormData.append(pair[0], pair[1]);
                    }
                }
                let file2= pondEdit.getFile();
                if(file2){
                    newFormData.append('video', file2.file);
                }
                // quill
                newFormData.append('content', quillEdit.root.innerHTML);

                let uid= $('#edit-unit-id').val();
                $.ajax({
                    url:'/units/'+uid,
                    method:'POST',
                    data:newFormData,
                    processData:false,
                    contentType:false,
                    success:function(resp){
                        $('#editUnitModal').modal('hide');
                        showAlert('success','Lesson updated successfully','bx-check');
                        clearModal('#editUnitModal');
                        table.ajax.reload();
                    },
                    error:function(resp){
                        showAlert('danger','Error updating lesson','bx-error');
                    }
                });
            });

            // عند النقر على Edit
            $(document).on('click','.edit-unit', function(){
                let unitId= $(this).data('id');
                $.ajax({
                    url:'/units/'+unitId+'/edit',
                    method:'GET',
                    success:function(data){
                        $('#edit-unit-id').val(data.id);
                        $('#edit-title').val(data.title);
                        $('#edit-subtitle').val(data.subtitle);
                        $('#edit-content_type').val(data.content_type).change();

                        if(data.content_type==='text'){
                            quillEdit.root.innerHTML= data.content;
                        } else if(data.content_type==='youtube'){
                            // الدرس خزّن الvideoId في الحقل content
                            // بينما رابط اليوتيوب في script
                            // لعرضه للتعديل إن لزم
                            // هنا نفترض أنك تريد أن تعرض script بداخل الحقل
                            $('#edit-youtube').val(data.script || '');
                        }
                        // video => لا شيء مخصوص سوى ترك رفع فيديو جديد لو احتاج
                        $('#editUnitModal').modal('show');
                    }
                });
            });

            // تنبيه
            function showAlert(type, message, icon){
                let html= `
                <div class="alert alert-${type} border-0 bg-${type} alert-dismissible fade show py-2 position-fixed top-0 end-0 m-3" role="alert" style="z-index:9999;">
                    <div class="d-flex align-items-center">
                        <div class="font-35 text-white">
                            <i class="bx ${icon}"></i>
                        </div>
                        <div class="ms-3 text-white">
                            <h6 class="mb-0 text-white">${type.charAt(0).toUpperCase()+type.slice(1)}</h6>
                            ${message}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                `;
                $('body').append(html);
                setTimeout(()=>{$('.alert').alert('close');},5000);
            }

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/admin/units.blade.php ENDPATH**/ ?>
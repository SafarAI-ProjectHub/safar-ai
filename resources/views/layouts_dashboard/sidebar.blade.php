<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"><span style="color: #844DCD"><span style="color:#C45ACD">Safar</span> AI</span></h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('dashboard') }}">
                <div class="parent-icon"><i class='bx bx-tachometer'></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        @hasanyrole('Super Admin|Admin')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-user-circle'></i></div>
                    <div class="menu-title">Teachers</div>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('admin.applications') }}">
                            <div class="parent-icon"><i class="bx bx-file"></i></div>
                            <div class="menu-title">Applications Review</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.teachers') }}">
                            <div class="parent-icon"><i class="bx bx-check-circle"></i></div>
                            <div class="menu-title">Approved Teachers</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacherTests.index') }}">
                            <div class="parent-icon"><i class="bx bx-bar-chart-alt"></i></div>
                            <div class="menu-title">Manage Level Tests</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacherTest.addPage') }}">
                            <div class="parent-icon"><i class="bx bx-plus"></i></div>
                            <div class="menu-title">Add Level Test</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasanyrole

        @hasanyrole('Super Admin|Admin|Teacher')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-book-open'></i></div>
                    <div class="menu-title">Courses</div>
                </a>
                <ul>

                    <li>
                        <a href="{{ route('admin.courses') }}">
                            <div class="parent-icon"><i class="bx bx-book"></i></div>
                            <div class="menu-title">
                                @hasanyrole('Super Admin|Admin')
                                    Courses
                                @else
                                    My Courses
                                @endhasanyrole
                            </div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('quizzes.index') }}">
                            <div class="parent-icon"><i class="bx bx-brain"></i></div>
                            <div class="menu-title">Quizzes</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('quiz.addPage') }}">
                            <div class="parent-icon"><i class="bx bx-plus-circle"></i></div>
                            <div class="menu-title">Add Quiz</div>
                        </a>
                    </li>

                </ul>
            </li>
        @endhasanyrole

        @hasanyrole('Super Admin|Admin|Teacher')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-user'></i></div>
                    <div class="menu-title">Students</div>
                </a>
                <ul>
                    @hasanyrole('Super Admin|Admin')
                        <li>
                            <a href="{{ route('admin.students') }}">
                                <div class="parent-icon"><i class="bx bx-group"></i></div>
                                <div class="menu-title">Students List</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('studentTests.index') }}">
                                <div class="parent-icon"><i class="bx bx-bar-chart-alt"></i></div>
                                <div class="menu-title">Manage Level Tests</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('studentTest.addPage') }}">
                                <div class="parent-icon"><i class="bx bx-plus"></i></div>
                                <div class="menu-title">Add Level Test</div>
                            </a>
                        </li>
                    @endhasanyrole
                    @hasanyrole('Super Admin|Teacher')
                        <li>
                            <a href="{{ route('teacher.getStudentProfiles') }}">
                                <div class="parent-icon"><i class="bx bx-group"></i></div>
                                <div class="menu-title">Student Profiles</div>
                            </a>
                        </li>
                    @endhasanyrole
                </ul>
            </li>
        @endhasanyrole

        @hasanyrole('Super Admin|Teacher')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-video'></i></div>
                    <div class="menu-title">Zoom Meetings</div>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('teacher.scheduleZoomMeeting') }}">
                            <div class="parent-icon"><i class="bx bx-video"></i></div>
                            <div class="menu-title">Schedule Zoom Meeting</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasanyrole
    </ul>
    <!--end navigation-->
</div>

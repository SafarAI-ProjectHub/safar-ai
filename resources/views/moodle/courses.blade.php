<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Courses</title>
</head>
<body>
    <h1>Courses</h1>
    @if(!empty($courses))
        <ul>
            @foreach($courses as $course)
                <li>
                    <a href="{{ url('/moodle/course-details/' . $course['id']) }}">
                        {{ $course['fullname'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No courses found.</p>
    @endif
</body>
</html>

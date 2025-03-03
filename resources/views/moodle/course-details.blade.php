<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Course Details</title>
</head>
<body>
    <h1>Details for Course ID: {{ $courseId }}</h1>

    <h2>Quizzes</h2>
    @if(isset($quizzes['quizzes']) && count($quizzes['quizzes']) > 0)
        <ul>
            @foreach($quizzes['quizzes'] as $quiz)
                <li>
                    {{ $quiz['name'] }} 
                    (Time Limit: {{ $quiz['timelimit'] }})
                    <!-- رابط لتشغيل الكويز -->
                    <a href="{{ url('/moodle/course/' . $courseId . '/quiz/' . $quiz['id']) }}">Run Quiz</a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No quizzes found for this course.</p>
    @endif

    <h2>H5P Activities</h2>
    @if(isset($h5pactivities['h5pactivities']) && count($h5pactivities['h5pactivities']) > 0)
        <ul>
            @foreach($h5pactivities['h5pactivities'] as $activity)
                <li>
                    {{ $activity['name'] ?? 'No Name' }}
                    <!-- رابط لتشغيل نشاط H5P -->
                    <a href="{{ url('/moodle/course/' . $courseId . '/h5p/' . $activity['id']) }}">Run H5P Activity</a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No H5P activities found for this course.</p>
    @endif

    <a href="{{ url('/moodle/courses') }}">Back to Courses</a>
</body>
</html>

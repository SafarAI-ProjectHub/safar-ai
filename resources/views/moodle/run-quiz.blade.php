<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Run Quiz - {{ $quiz['name'] }}</title>
</head>
<body>
    <h1>Quiz: {{ $quiz['name'] }}</h1>
    <!-- تضمين صفحة الكويز باستخدام iframe.
         نستخدم معرف الوحدة الدراسية (coursemodule) الذي تم استرجاعه من Moodle -->
    <iframe src="https://moodle.safarai.org/mod/quiz/view.php?id={{ $quiz['coursemodule'] }}" 
            width="100%" height="600" frameborder="0">
    </iframe>
    <br>
    <a href="{{ url()->previous() }}">Back to Course Details</a>
</body>
</html>

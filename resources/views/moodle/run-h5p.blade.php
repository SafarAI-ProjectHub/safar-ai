<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Run H5P Activity - {{ $activity['name'] ?? 'H5P Activity' }}</title>
</head>
<body>
    <h1>H5P Activity: {{ $activity['name'] ?? 'No Title' }}</h1>
    <!-- تضمين صفحة نشاط H5P باستخدام iframe.
         هنا نفترض أن معرف الوحدة (cmid) موجود في النشاط، وإن لم يكن يمكنك استخدام id أو تعديل حسب البيانات المتوفرة -->
    <iframe src="https://moodle.safarai.org/mod/h5pactivity/view.php?id={{ $activity['cmid'] ?? $activity['id'] }}" 
            width="100%" height="600" frameborder="0">
    </iframe>
    <br>
    <a href="{{ url()->previous() }}">Back to Course Details</a>
</body>
</html>

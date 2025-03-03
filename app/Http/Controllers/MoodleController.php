<?php

namespace App\Http\Controllers;

use App\Services\MoodleService;
use Illuminate\Http\Request;

class MoodleController extends Controller
{
    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    public function getCourses()
    {
        $courses = $this->moodleService->getCourses();
        return response()->json($courses);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Offer;
use App\Models\Rate;
use App\Models\Unit;
use App\Models\Student;
use App\Models\Course;
use Carbon\Carbon;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $teachers = Cache::remember('Home_teachers', 60, function () {
            return Teacher::with('user')->where('approval_status', 'approved')->get();
        });

        $currentDateTime = Carbon::now();

        // Fetch active offers that are within the start and end dates
        $offers = Cache::remember('Home_offers', 60, function () use ($currentDateTime) {
            return Offer::where('is_active', 1)
                ->where(function ($query) use ($currentDateTime) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', $currentDateTime);
                })
                ->where(function ($query) use ($currentDateTime) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $currentDateTime);
                })
                ->get();
        });

        $reviews = Cache::remember('Home_reviews', 60, function () {
            return Rate::where('rate', '>=', 4)->latest()->limit(5)->get();
        });

        // Calculate learning hours
        $learningHours = Cache::remember('Home_learningHours', 60, function () {
            $units = Unit::all();
            $totalMinutes = 0;
            $ffmpeg = FFMpeg::create();

            foreach ($units as $unit) {
                if ($unit->content_type === 'text') {

                    $wordCount = str_word_count(strip_tags($unit->content));
                    $totalMinutes += $wordCount / 200;
                } elseif ($unit->content_type === 'video') {
                    if ($unit->content !== null) {
                        $path = public_path($unit->content);
                        $video = $ffmpeg->open($path);
                        $durationInSeconds = $video->getFormat()->get('duration');
                        $totalMinutes += $durationInSeconds / 60;
                    }
                }
            }

            return $totalMinutes / 60;
        });

        $totalStudents = Cache::remember('Home_totalStudents', 60, function () {
            return Student::count();
        });

        $totalCourses = Cache::remember('Home_totalCourses', 60, function () {
            return Course::count();
        });

        $totalTeachers = Cache::remember('Home_totalTeachers', 60, function () {
            return Teacher::where('approval_status', 'approved')->count();
        });

        return view('welcome', compact('teachers', 'offers', 'reviews', 'learningHours', 'totalStudents', 'totalCourses', 'totalTeachers'));
    }
    public function terms()
    {
        return view('terms_and_conditions');
    }

    public function privacy()
    {
        return view('privacy_policy');
    }
}
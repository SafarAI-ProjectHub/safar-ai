<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YoutubeVideo;
use Yajra\DataTables\DataTables;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Http;

class YoutubeVideoController extends Controller
{
    public function index(Request $request)
    {
        $ageGroups = ['1-5', '6-10', '10-14', '14-18', '18+'];

        if ($request->ajax()) {
            $query = YoutubeVideo::query();

            // Apply filters
            if ($request->has('age_group') && $request->age_group != '') {
                $query->where('age_group', $request->age_group);
            }

            $data = $query->latest()->get();
            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    return '
                    <button class="btn btn-sm btn-primary edit-btn" data-id="' . $data->id . '">Edit</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $data->id . '">Delete</button>
                ';
                })
                ->make(true);
        }

        return view('dashboard.admin.youtube.index', compact('ageGroups'));
    }


    public function show($id)
    {
        $data = YoutubeVideo::find($id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'age_group' => 'required|string',
        ]);

        $videoId = $this->extractVideoId($request->input('url'));
        $videoDetails = $this->fetchVideoDetails($videoId);
        if (isset($videoDetails['error'])) {
            return response()->json(['error' => $videoDetails['error']], 422);
        }
        YoutubeVideo::create([
            'title' => $request->input('title'),
            'url' => $request->input('url'),
            'video_id' => $videoId,
            'description' => $videoDetails['description'] ?? null,
            'thumbnail_url' => $videoDetails['thumbnail_url'] ?? null,
            'view_count' => $videoDetails['view_count'] ?? 0,
            'like_count' => $videoDetails['like_count'] ?? 0,
            'comment_count' => $videoDetails['comment_count'] ?? 0,
            'age_group' => $request->input('age_group'),
        ]);

        return response()->json(['success' => 'YouTube Video Added Successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'age_group' => 'required|string',
        ]);

        $videoId = $this->extractVideoId($request->input('url'));
        $videoDetails = $this->fetchVideoDetails($videoId);
        if (isset($videoDetails['error'])) {
            return response()->json(['error' => $videoDetails['error']], 422);
        }
        $video = YoutubeVideo::find($id);
        $video->update([
            'title' => $request->input('title'),
            'url' => $request->input('url'),
            'video_id' => $videoId,
            'description' => $videoDetails['description'] ?? null,
            'thumbnail_url' => $videoDetails['thumbnail_url'] ?? null,
            'view_count' => $videoDetails['view_count'] ?? 0,
            'like_count' => $videoDetails['like_count'] ?? 0,
            'comment_count' => $videoDetails['comment_count'] ?? 0,
            'age_group' => $request->input('age_group'),
        ]);

        return response()->json(['success' => 'YouTube Video Updated Successfully']);
    }

    public function destroy($id)
    {
        $video = YoutubeVideo::find($id);
        $video->delete();
        return response()->json(['success' => 'YouTube Video Deleted Successfully']);
    }

    private function extractVideoId($url)
{
    $parsedUrl = parse_url($url);

    if (isset($parsedUrl['host']) && (strpos($parsedUrl['host'], 'youtu.be') !== false)) {
        $path = trim($parsedUrl['path'], '/'); 
        return $path; 
    }

    parse_str($parsedUrl['query'] ?? '', $queryParams);
    return $queryParams['v'] ?? null;
}


    private function fetchVideoDetails($videoId)
    {
        $apiKey = env('YOUTUBE_API_KEY');
        // dd(env('YOUTUBE_API_KEY'));
        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&key={$apiKey}&part=snippet,statistics";

        $response = Http::get($apiUrl);
        // dd($response->json());
        if ($response->successful() && !empty($response->json()['items'])) {
            $item = $response->json()['items'][0];
            return [
                'status' => 'success',
                'description' => $item['snippet']['description'] ?? null,
                'thumbnail_url' => $item['snippet']['thumbnails']['high']['url'] ?? null,
                'view_count' => $item['statistics']['viewCount'] ?? 0,
                'like_count' => $item['statistics']['likeCount'] ?? 0,
                'comment_count' => $item['statistics']['commentCount'] ?? 0,
            ];
        }

        return [
            'status' => 'error',
            'error' => 'Invalid YouTube Video URL'
        ];
    }
}
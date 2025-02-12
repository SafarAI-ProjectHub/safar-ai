<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;
use Carbon\Carbon;

class FileUploadController extends Controller
{
    public function process(Request $request)
    {

        try {
            // dd($request->all(), $request->file());
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads', $filename, 'public');

                // Record metadata in the media table
                $media = Media::create([
                    'type' => 'video', // assuming type is video, update as needed
                    'link' => Storage::url($path),
                    'uploaded_by' => auth()->id(),
                    'upload_date' => Carbon::now(),
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'status' => 'available'
                ]);

                return response()->json(['id' => $media->id, 'url' => 'storage/' . $path]);
            } else {
                return response()->json(['error' => 'No file uploaded.'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'File upload failed.', 'message' => $e->getMessage()], 422);
        }
    }

    public function revert(Request $request)
    {
        $filename = $request->getContent();
        if (Storage::disk('public')->exists('uploads/' . $filename)) {
            Storage::disk('public')->delete('uploads/' . $filename);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'File not found.'], 404);
    }

    public function load($id)
    {
        if (Storage::disk('public')->exists('uploads/' . $id)) {
            return Storage::disk('public')->response('uploads/' . $id);
        }

        return response()->json(['error' => 'File not found.'], 404);
    }


    public function uploadCanvasImage(Request $request)
{
    $base64 = $request->input('imageBase64');
    
    $exploded = explode(',', $base64);
    $decoded = base64_decode(end($exploded));

    $fileName = uniqid().'.png';
    Storage::disk('public')->put('uploads/'.$fileName, $decoded);

    $fileUrl = asset('storage/uploads/'.$fileName);
    return response()->json(['url' => $fileUrl]);
}

}
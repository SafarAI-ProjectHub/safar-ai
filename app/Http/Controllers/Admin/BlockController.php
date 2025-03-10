<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Course;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BlockController extends Controller
{
    /**
     * صفحة عرض البلوكات الخاصة بكورس محدد
     */
    public function index($courseId)
    {
        $course = Course::findOrFail($courseId);
        // سنمرر الـ course حتى نعرض اسمه أو أي بيانات أخرى في الـ Blade
        return view('dashboard.admin.blocks', compact('course'));
    }

    /**
     * جلب بيانات البلوكات (لاستخدامها مع DataTables)
     */
    public function getBlocks(Request $request)
    {
        // نستقبل course_id من الـ request لفلترة البلوكات
        $courseId = $request->get('course_id');

        $query = Block::where('course_id', $courseId);

        return DataTables::of($query)
            ->addColumn('actions', function ($block) {
                // نضيف أزرار التعديل والحذف، وزر Show Units
                $editBtn = '<button class="btn btn-sm btn-primary edit-btn" data-id="'.$block->id.'">Edit</button>';
                $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$block->id.'">Delete</button>';
                // زر Show Units
                // نفترض أن لديك صفحة units.blade لعرض الوحدات، وربما تمرر block_id في الرابط
                $unitsUrl = route('admin.units.index', ['blockId' => $block->id]);
                $showUnitsBtn = '<a href="'.$unitsUrl.'" class="btn btn-sm btn-warning">Show Units</a>';

                return $editBtn . ' ' . $deleteBtn . ' ' . $showUnitsBtn;
            })
            ->editColumn('description', function($block) {
                if(!$block->description) return '';
                return mb_strimwidth($block->description, 0, 50, '...');
            })
            ->rawColumns(['actions'])  // لكي لا يتم هروب الـ HTML
            ->make(true);
    }

    /**
     * إضافة بلوك جديد
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'required|integer',
            'moodle_section_id' => 'nullable|integer',
            'visibility' => 'required|boolean'
        ]);

        Block::create($request->only([
            'course_id',
            'name',
            'description',
            'position',
            'moodle_section_id',
            'visibility'
        ]));

        return response()->json(['success' => 'Block created successfully!']);
    }

    /**
     * جلب بيانات بلوك للتعديل
     */
    public function edit($id)
    {
        $block = Block::findOrFail($id);
        return response()->json($block);
    }

    /**
     * تحديث بيانات بلوك
     */
    public function update(Request $request, $id)
    {
        $block = Block::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'required|integer',
            'moodle_section_id' => 'nullable|integer',
            'visibility' => 'required|boolean'
        ]);

        $block->update($request->only([
            'name',
            'description',
            'position',
            'moodle_section_id',
            'visibility'
        ]));

        return response()->json(['success' => 'Block updated successfully!']);
    }

    /**
     * حذف بلوك
     */
    public function destroy($id)
    {
        $block = Block::findOrFail($id);
        $block->delete();

        return response()->json(['success' => true, 'message' => 'Block deleted successfully!']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Services\MoodleService;
use Illuminate\Http\Request;

class CourseCategoryController extends Controller
{
    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    /**
     * عرض جميع التصنيفات في الصفحة الرئيسية
     */
    public function index()
    {
        $categories = CourseCategory::with('parent')->get();
        return view('admin.course_categories.index', compact('categories'));
    }

    /**
     * عرض نموذج إنشاء تصنيف جديد
     */
    public function create()
    {
        $categories = CourseCategory::all(); // لجلب جميع التصنيفات لإمكانية اختيار تصنيف رئيسي
        return view('admin.course_categories.create', compact('categories'));
    }

    /**
     * تخزين التصنيف الجديد في قاعدة البيانات و Moodle
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'parent_id'     => 'nullable|exists:course_categories,id',
            'age_group'     => 'nullable|in:6-10,10-14,14-18,18+',
            'general_category' => 'nullable|in:Mathematics,Science,Programming,Arts,Languages,Business',
        ]);

        $this->moodleService->createCategory($validatedData);

        session()->flash('success', 'تم إنشاء التصنيف بنجاح!');
        return redirect()->route('admin.course_categories.index');
    }

    /**
     * عرض نموذج تعديل التصنيف
     */
    public function edit(CourseCategory $category)
    {
        $categories = CourseCategory::all(); // لجلب جميع التصنيفات لإمكانية اختيار تصنيف رئيسي
        return view('admin.course_categories.edit', compact('category', 'categories'));
    }

    /**
     * تحديث التصنيف في Laravel و Moodle
     */
    public function update(Request $request, CourseCategory $category)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'parent_id'     => 'nullable|exists:course_categories,id',
            'age_group'     => 'nullable|in:6-10,10-14,14-18,18+',
            'general_category' => 'nullable|in:Mathematics,Science,Programming,Arts,Languages,Business',
        ]);

        $this->moodleService->updateCategory($category, $validatedData);

        session()->flash('success', 'تم تعديل التصنيف بنجاح!');
        return redirect()->route('admin.course_categories.index');
    }

    /**
     * حذف التصنيف من Laravel و Moodle
     */
    public function destroy(CourseCategory $category)
    {
        $this->moodleService->deleteCategory($category);

        session()->flash('success', 'تم حذف التصنيف بنجاح!');
        return redirect()->route('admin.course_categories.index');
    }

    public function sync()
{
    $this->moodleService->syncCategoriesFromMoodle();
    session()->flash('success', 'تم مزامنة التصنيفات بنجاح!');
    return redirect()->route('admin.course_categories.index');
}
// <a href="{{ route('admin.course_categories.sync') }}" class="btn btn-success">مزامنة من Moodle</a>

}

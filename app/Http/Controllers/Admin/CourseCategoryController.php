<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CourseCategoryController extends Controller
{
    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    /**
     * عرض صفحة التصنيفات
     */
    public function index()
    {
        // سنستخدم DataTables لجلب التصنيفات
        return view('dashboard.admin.categories');
    }

    /**
     * DataTables لجلب التصنيفات
     */
    public function getCategories()
    {
        $query = CourseCategory::with('parent'); // لجلب معلومات التصنيف الأب

        return DataTables::of($query)
            ->addColumn('parent_name', function($row){
                return $row->parent ? $row->parent->name : '-';
            })
            ->addColumn('actions', function($row){
                $editBtn   = '<button class="btn btn-sm btn-warning editBtn" data-id="'.$row->id.'">Edit</button>';
                $deleteBtn = '<button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>';
                // زر لنقلنا إلى صفحة الدورات (يمكن تمرير ?category_id=... أو أي طريقة)
                $coursesBtn= '<a href="'.route('admin.courses', ['category_id' => $row->id]).'"
                                class="btn btn-sm btn-info">Go to Courses</a>';

                return $coursesBtn.' '.$editBtn.' '.$deleteBtn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * عرض نموذج إنشاء تصنيف (إن استخدمته في صفحة أخرى)
     */
    public function create()
    {
        $categories = CourseCategory::all();
        return view('dashboard.admin.create_category', compact('categories'));
    }

    /**
     * تخزين التصنيف الجديد
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'              => 'required|string|max:255',
            'parent_id'         => 'nullable|exists:course_categories,id',
            'age_group'         => 'nullable|in:6-10,10-14,14-18,18+',
            'general_category'  => 'nullable|in:Mathematics,Science,Programming,Arts,Languages,Business',
        ]);

        // حفظه محليًا، ثم على Moodle (لو أردت) باستخدام الـ Service
        $this->moodleService->createCategory($validatedData);

        return response()->json(['success' => true, 'message' => 'Category created successfully!']);
    }

    /**
     * جلب بيانات تصنيف للتعديل Ajax
     */
    public function edit(CourseCategory $category)
    {
        return response()->json($category);
    }

    /**
     * تحديث التصنيف
     */
    public function update(Request $request, CourseCategory $category)
    {
        $validatedData = $request->validate([
            'name'              => 'required|string|max:255',
            'parent_id'         => 'nullable|exists:course_categories,id',
            'age_group'         => 'nullable|in:6-10,10-14,14-18,18+',
            'general_category'  => 'nullable|in:Mathematics,Science,Programming,Arts,Languages,Business',
        ]);

        // تحديث على Moodle أيضًا
        $this->moodleService->updateCategory($category, $validatedData);

        return response()->json(['success' => true, 'message' => 'Category updated successfully!']);
    }

    /**
     * حذف التصنيف
     */
    public function destroy(CourseCategory $category)
    {
        $this->moodleService->deleteCategory($category);

        return response()->json(['success' => true, 'message' => 'Category deleted successfully!']);
    }

    /**
     * مزامنة التصنيفات مع Moodle
     */
    public function sync()
    {
        $this->moodleService->syncCategoriesFromMoodle();
        return redirect()->route('admin.course_categories.index')
                         ->with('success','تمت مزامنة التصنيفات بنجاح!');
    }
}

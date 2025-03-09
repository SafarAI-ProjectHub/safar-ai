<?php

namespace App\Services\MoodleServices;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CourseCategory;

class CourseCategoryService
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url   = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->token = config('app.moodle_wstoken');
    }

    /**
     * إنشاء تصنيف جديد في Laravel + Moodle
     */
    public function createCategory(array $data)
    {
        $category = CourseCategory::create($data);
        $moodleCategoryId = $this->createCategoryInMoodle($category);

        if ($moodleCategoryId) {
            $category->update(['moodle_category_id' => $moodleCategoryId]);
        }

        return $category;
    }

    /**
     * تعديل التصنيف في Laravel + Moodle
     */
    public function updateCategory(CourseCategory $category, array $data)
    {
        $category->update($data);
        $this->updateCategoryInMoodle($category);

        return $category;
    }

    /**
     * حذف التصنيف من Laravel + Moodle
     */
    public function deleteCategory(CourseCategory $category)
    {
        if ($category->moodle_category_id) {
            $this->deleteCategoryInMoodle($category->moodle_category_id);
        }

        return $category->delete();
    }

    /**
     * إنشاء تصنيف في Moodle
     */
    protected function createCategoryInMoodle(CourseCategory $category)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_create_categories',
            'moodlewsrestformat' => 'json',
            'categories' => [[
                'name'       => $category->name,
                'parent'     => $category->parent ? $category->parent->moodle_category_id : 0,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data[0]['id'])) {
                return $data[0]['id'];
            }
        }

        Log::error('Failed to create category in Moodle.', ['response' => $response->body()]);
        return null;
    }

    /**
     * تحديث تصنيف في Moodle
     */
    protected function updateCategoryInMoodle(CourseCategory $category)
    {
        if (!$category->moodle_category_id) {
            return null;
        }

        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_update_categories',
            'moodlewsrestformat' => 'json',
            'categories' => [[
                'id'         => $category->moodle_category_id,
                'name'       => $category->name,
                'parent'     => $category->parent ? $category->parent->moodle_category_id : 0,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }

    /**
     * حذف تصنيف من Moodle
     */
    protected function deleteCategoryInMoodle($moodleCategoryId)
    {
        if (!$moodleCategoryId) {
            return false;
        }

        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_delete_categories',
            'moodlewsrestformat' => 'json',
            'categoryids'        => [$moodleCategoryId],
        ];

        $response = Http::asForm()->post($this->url, $params);

        if ($response->successful()) {
            return true;
        }

        Log::error('Failed to delete category from Moodle.', ['response' => $response->body()]);
        return false;
    }

    public function syncCategoriesFromMoodle()
{
    $params = [
        'wstoken'            => $this->token,
        'wsfunction'         => 'core_course_get_categories',
        'moodlewsrestformat' => 'json',
    ];

    $response = Http::get($this->url, $params);

    if ($response->successful()) {
        $categories = $response->json();

        foreach ($categories as $moodleCategory) {
            CourseCategory::updateOrCreate(
                ['moodle_category_id' => $moodleCategory['id']],
                [
                    'name'       => $moodleCategory['name'],
                    'parent_id'  => $this->getLocalParentId($moodleCategory['parent']),
                ]
            );
        }
    } else {
        Log::error('Failed to fetch categories from Moodle.', ['response' => $response->body()]);
    }
}

/**
 * جلب معرف التصنيف الأب المحلي بناءً على معرف Moodle
 */
private function getLocalParentId($moodleParentId)
{
    $parentCategory = CourseCategory::where('moodle_category_id', $moodleParentId)->first();
    return $parentCategory ? $parentCategory->id : null;
}

}

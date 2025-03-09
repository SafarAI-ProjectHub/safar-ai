<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'quiz_id',
        'question_text',
        'type',
        'order',
        'score',
        'moodle_question_id',
        'media_url'
    ];

    /**
     * علاقة السؤال بالكويز التابع له
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * علاقة السؤال بخيارات الإجابة
     */
    public function choices()
    {
        return $this->hasMany(Choice::class, 'question_id');
    }

    /**
     * علاقة السؤال بإجابات المستخدمين
     */
    public function userResponses()
    {
        return $this->hasMany(UserResponse::class, 'question_id');
    }

    /**
     * علاقة Moodle: ربط السؤال بأسئلة Moodle
     */
    public function moodleQuestion()
    {
        return $this->hasOne(MoodleQuestion::class, 'id', 'moodle_question_id');
    }

    /**
     * تعيين درجة السؤال
     */
    public function setScore($score)
    {
        $this->score = $score;
        $this->save();
    }

    /**
     * تعيين وسائط للسؤال
     */
    public function assignMedia($mediaUrl)
    {
        $this->media_url = $mediaUrl;
        $this->save();
    }
}

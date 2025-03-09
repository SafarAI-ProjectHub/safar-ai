<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'question_id',
        'choice_text',
        'is_correct',
        'order'
    ];

    /**
     * علاقة الخيار بالسؤال التابع له
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * تعيين هذا الاختيار كإجابة صحيحة
     */
    public function setCorrect()
    {
        $this->is_correct = true;
        $this->save();
    }
}

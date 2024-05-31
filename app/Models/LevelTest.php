<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'exam_type',
        'active',
    ];

    public function questions()
    {
        return $this->hasMany(LevelTestQuestion::class);
    }
}
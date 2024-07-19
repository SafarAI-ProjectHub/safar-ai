<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'company_name',
        'other_party_name',
        'contract_date',
        'salary',
        'salary_period',
        'contract_agreement',
        'employee_duties',
        'responsibilities',
        'employment_period',
        'compensation',
        'legal_terms',
        'signature',
        'status'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
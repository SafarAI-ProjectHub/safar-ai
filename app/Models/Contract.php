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
        'company_logo',
        'salary',
        'signature'
    ];

    public function rules()
    {
        return $this->hasMany(ContractRule::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractRule extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'rule'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
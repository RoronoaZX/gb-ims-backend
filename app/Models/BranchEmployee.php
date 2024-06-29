<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'time_shift',
    ];

}

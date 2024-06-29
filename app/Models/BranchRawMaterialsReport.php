<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchRawMaterialsReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'recipe_id',
        'baker_report_id',
        'ingredient_id',
    ];
}

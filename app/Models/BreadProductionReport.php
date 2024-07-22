<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreadProductionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'recipe_id',
        'initial_bakerreports_id',
        'bread_id',
        'bread_new_production',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function initialBakerReport()
    {
        return $this->belongsTo(InitialBakerreports::class, 'initial_bakerreports_id');
    }
}

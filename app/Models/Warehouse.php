<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'location',
        'person_incharge',
        'phone',
        'status',
    ];

    public function branches() {
        return $this->hasMany(Branch::class);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('name', 'LIKE', "%{$keyword}%")->orWhere('person_incharge','LIKE', "%{$keyword}%");
    }

}

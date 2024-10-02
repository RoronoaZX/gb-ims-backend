<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCredits extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'sales_report_id',
        'total_amount',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creditUserID()
    {
        return $this->belongsTo(User::class, 'credit_user_id', 'id');
    }
    public function salesReports()
    {
        return $this->belongsTo(SalesReports::class, 'sales_report_id');
    }

    public function creditProducts()
    {
        return $this->hasMany(EmployeeCreditProducts::class )->with('product', 'creditUserId');
    }
}

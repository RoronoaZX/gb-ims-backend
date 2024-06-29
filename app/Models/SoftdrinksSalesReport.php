<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftdrinksSalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'product_id',
        'sales_report_id',
        'beginnings',
        'remaining_stocks',
        'price',
        'softdrinks_sold',
        'sales',
        'branch_softdrinks_added_stocks',
    ];
}

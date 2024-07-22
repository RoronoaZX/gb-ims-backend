<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_report_id',
        'oneThousands',
        'fiveHundred',
        'twoHundred',
        'oneHundred',
        'fifty',
        'twenty',
        'twentyCoins',
        'tenCoins',
        'fiveCoins',
        'oneCoins',
        'twentyFiveCents',
    ];

    public function salesReports()
    {
        return $this->belongsTo(SalesReports::class, 'sales_report_id');
    }
}

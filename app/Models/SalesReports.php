<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesReports extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'products_total_sales',
        'expenses_total',
        'denomination_total',
        'charges_amount'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breadReports()
    {
        return $this->hasMany(BreadSalesReport::class, 'sales_report_id');
    }

    public function selectaReports()
    {
        return $this->hasMany(SelectaSalesReport::class, 'sales_report_id');
    }

    public function softdrinksReports()
    {
        return $this->hasMany(SoftdrinksSalesReport::class, 'sales_report_id');
    }

    public function expensesReports()
    {
        return $this->hasMany(ExpencesReport::class, 'sales_report_id');
    }

    public function denominationReports()
    {
        return $this->hasMany(Denomination::class, 'sales_report_id');
    }

}

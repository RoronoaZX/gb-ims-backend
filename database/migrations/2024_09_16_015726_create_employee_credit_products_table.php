<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_credit_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_credit_id')->unsigned();
            $table->foreign('employee_credit_id')->references('id')->on('branches');
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('branches');
            $table->decimal('price', 10,2)->nullable();
            $table->integer('pieces');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_credit_products');
    }
};

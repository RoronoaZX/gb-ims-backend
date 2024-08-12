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
        Schema::create('denominations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sales_report_id')->unsigned();
            $table->foreign('sales_report_id')->references('id')->on('sales_reports');
            $table->integer('oneThousands')->default(0)->nullable();
            $table->integer('fiveHundred')->default(0)->nullable();
            $table->integer('twoHundred')->default(0)->nullable();
            $table->integer('oneHundred')->default(0)->nullable();
            $table->integer('fifty')->default(0)->nullable();
            $table->integer('twenty')->default(0)->nullable();
            $table->integer('twentyCoins')->default(0)->nullable();
            $table->integer('tenCoins')->default(0)->nullable();
            $table->integer('fiveCoins')->default(0)->nullable();
            $table->integer('oneCoins')->default(0)->nullable();
            $table->integer('twentyFiveCents')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denominations');
    }
};

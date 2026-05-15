<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_summary', function (Blueprint $table) {
            $table->integer('product_id')->primary();
            $table->integer('total_purchased')->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->integer('total_sold')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('total_profit', 10, 2)->default(0);
            $table->integer('current_stock')->default(0);
            $table->date('last_purchase_date')->nullable();
            $table->date('last_sale_date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_summary');
    }
};

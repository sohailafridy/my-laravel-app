<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->integer('discount_percent');
            $table->decimal('final_amount', 10, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount_fixed', 10, 2)->comment('Will not update after payment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

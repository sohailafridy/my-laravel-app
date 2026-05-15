<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name', 100);
            $table->string('email', 20);
            $table->string('phone', 20)->nullable();
            $table->string('cnic', 20);
            $table->enum('customer_type', ['customer', 'distributor', 'outside'])->nullable();
            $table->string('from_add');
            $table->string('to_add');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};

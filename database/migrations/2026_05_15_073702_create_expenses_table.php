<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('expense_id');
            $table->integer('expense_type_id');
            $table->decimal('amount', 10, 0);
            $table->string('detail');
            $table->string('file');
            $table->string('date', 20);
            $table->integer('month');
            $table->integer('year');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};

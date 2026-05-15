<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expense_type', function (Blueprint $table) {
            $table->increments('exp_type_id');
            $table->string('expense_type', 20);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_type');
    }
};

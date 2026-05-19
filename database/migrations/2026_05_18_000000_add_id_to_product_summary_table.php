<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Drop primary key from product_id
        Schema::table('product_summary', function (Blueprint $table) {
            $table->dropPrimary(['product_id']);
        });

        // Step 2: Add id column as auto increment primary key
        Schema::table('product_summary', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_summary', function (Blueprint $table) {
            // Remove id column
            $table->dropColumn('id');
            // Restore product_id as primary key
            $table->primary('product_id');
        });
    }
};

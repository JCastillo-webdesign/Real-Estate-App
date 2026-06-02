<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AddImageToAddressesTable Migration
 * 
 * Adds support for storing image filenames in the addresses table.
 * Changes the 'image' column to TEXT type to accommodate JSON array of filenames.
 */
return new class extends Migration
{
    /**
     * Run the migration - Add/modify image column.
     * 
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Change image column to TEXT to store JSON array of image filenames
            $table->text('image')->change();
        });
    }


    /**
     * Rollback the migration - Revert image column changes.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Rollback logic (if needed)
        });
    }
};

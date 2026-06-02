<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RenameImageColumnInAddressesTable Migration
 * 
 * Renames the 'image' column to 'images' in the addresses table.
 * This better reflects that the column stores multiple image filenames as JSON.
 */
return new class extends Migration
{
    /**
     * Run the migration - Rename column from 'image' to 'images'.
     * 
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Rename singular 'image' to plural 'images' for clarity
            $table->renameColumn('image', 'images');
        });
    }

    /**
     * Rollback the migration - Rename column back to 'image'.
     * 
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Reverse the rename operation
            $table->renameColumn('images', 'image');
        });
    }
};

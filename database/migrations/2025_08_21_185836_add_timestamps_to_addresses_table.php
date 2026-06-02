<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AddTimestampsToAddressesTable Migration
 * 
 * Adds timestamp columns (created_at, updated_at) to the addresses table.
 * These track when each address record is created and last modified.
 */
return new class extends Migration
{
    /**
     * Run the migration - Add timestamp columns.
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Adds created_at and updated_at columns
            $table->timestamps();
        });
    }

    /**
     * Rollback the migration - Remove timestamp columns.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Drops created_at and updated_at columns
            $table->dropTimestamps();
        });
    }
};

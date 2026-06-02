<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateAddressesTable Migration
 * 
 * Creates the addresses table to store real estate property information.
 * Each address represents a unique property with street, city, state, and zipcode.
 */
return new class extends Migration
{
    /**
     * Run the migration - Create the addresses table.
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Address fields
            $table->string('street_address', 255);           // Main street address
            $table->string('street_address_2', 255)->nullable(); // Apt/Suite/Unit
            $table->string('city', 50);                      // City name
            $table->string('state', 2);                      // Two-letter state code
            $table->string('zipcode', 10);                   // Postal code
            
            // Timestamps for tracking creation and updates
            $table->timestamps();
        });
    }

    /**
     * Rollback the migration - Drop the addresses table.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
    
};

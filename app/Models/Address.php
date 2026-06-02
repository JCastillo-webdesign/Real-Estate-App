<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Address Model
 * 
 * Represents a real estate address with street information and associated images.
 * Images are stored as a JSON array of filenames.
 */
class Address extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     * These fields can be directly assigned without explicit validation per field.
     * 
     * @var array<string>
     */
    protected $fillable = [
        'street_address',      // Primary street address
        'street_address_2',    // Apartment, suite, or additional address info
        'city',                // City name
        'state',               // Two-letter state code
        'zipcode',             // Postal code
        'images'               // JSON array of image filenames
    ];

    /**
     * The attributes that should be hidden from serialization.
     * These timestamps are hidden when converting to array/JSON for API responses.
     * 
     * @var array<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Define custom attribute casting.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Define relationships here when needed
    // Example: if each address has many property listings
    // public function listings()
    // {
    //     return $this->hasMany(Listing::class);
    // }
}
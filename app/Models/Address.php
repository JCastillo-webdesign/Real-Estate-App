<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'addresses'; // Laravel will assume 'addresses' is the table name

    // Define the attributes that are mass assignable (to avoid mass-assignment vulnerabilities)
    protected $fillable = [
        'street_address',
        'street_address_2',
        'city',
        'state',
        'zipcode',
        'images'
    ];

    // Define the attributes that should be hidden when the model is converted to an array or JSON
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Define relationships (if any)
    // For example, if each address has many orders, you can define that here:
    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }
}
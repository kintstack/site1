<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'tbluser';           // Use the correct table name
    protected $primaryKey = 'id';           // Ensure it matches your table’s primary key
    public $timestamps = true;              // Set to true if you have timestamps

    protected $fillable = [
        'username', 
        'password',
        'gender'
    ];
}

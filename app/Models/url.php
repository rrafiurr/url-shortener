<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class url extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'hash'];
}

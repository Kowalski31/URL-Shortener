<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlMapping extends Model
{
    use HasFactory;

    protected $fillable = ['original_url', 'short_url', 'create_at'];

    public $timestamps = true;
}

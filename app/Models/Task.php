<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Task extends Model
{
    // protected $table = 'tasks';
    // protected $fillable = [
    //     'body',
    //     'user_id',
    // ];
    use HasFactory;
    protected $guarded = [
        // 'paid'
    ];

}

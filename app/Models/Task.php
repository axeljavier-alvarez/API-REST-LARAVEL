<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\FilterScope;
use App\Models\Scopes\SelectScope;
use App\Models\Scopes\SortScope;
use App\Models\Scopes\IncludeScope;

#[ScopedBy([
    FilterScope::class,
    SelectScope::class,
    SortScope::class,
    IncludeScope::class
])]

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

    public function user(){
        return $this->belongsTo(User::class);
    }

}

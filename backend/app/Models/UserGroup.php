<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;
    protected $table = 'user_group';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'user',
        'role',
        'group'
    ];
}

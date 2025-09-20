<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'account_id', 'full_name', 'phone', 'address', 'birthday', 'gender', 'created_at', 'updated_at'
    ];
}

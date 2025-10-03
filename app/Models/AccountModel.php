<?php
namespace App\Models;

use App\Core\Model;

class AccountModel extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'email',
        'password',
        'full_name',
        'created_at',
        'updated_at',
        'last_login',
        'role',
        'status'
    ];
}

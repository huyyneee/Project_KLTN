<?php
namespace App\Models;

use App\Core\Model;

class CartModel extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'created_at', 'updated_at'
    ];
}

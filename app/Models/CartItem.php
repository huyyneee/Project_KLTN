<?php
namespace App\Models;

use App\Core\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'cart_id', 'product_id', 'quantity', 'price', 'created_at', 'updated_at'
    ];
}

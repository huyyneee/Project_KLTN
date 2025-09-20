<?php
namespace App\Models;

use App\Core\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price', 'created_at', 'updated_at'
    ];
}

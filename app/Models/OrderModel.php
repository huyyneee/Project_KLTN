<?php
namespace App\Models;

use App\Core\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'order_code', 'status', 'total_amount', 'shipping_address', 'created_at', 'updated_at'
    ];
}

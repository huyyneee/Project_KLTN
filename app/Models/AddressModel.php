<?php
namespace App\Models;

use App\Core\Model;

class AddressModel extends Model
{
    protected $table = 'addresses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'phone', 'receiver_name', 'street', 'ward', 'district', 'city', 'province', 'type', 'is_default', 'created_at', 'updated_at'
    ];
}

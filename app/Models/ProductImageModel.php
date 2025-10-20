<?php

namespace App\Models;

use App\Core\Model;

class ProductImageModel extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id',
        'url',
        'is_main',
        'created_at',
        'updated_at'
    ];
}

<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code', 'name', 'price', 'description', 'specifications', 'usage', 'ingredients', 'category_id', 'created_at', 'updated_at', 'deleted_at'
    ];
}

<?php
namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
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

    /**
     * Return all categories.
     * @return array
     */
    public function getAllCategories(): array
    {
        return $this->findAll();
    }

    /**
     * Return single category by id or null.
     * @param int $id
     * @return array|null
     */
    public function getCategoryById(int $id): ?array
    {
        $result = $this->find($id);
        return $result === false ? null : $result;
    }
}
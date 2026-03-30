<?php
namespace App\Repositories;

class CategoryRepository extends BaseRepository
{
    public string $tableName = 'category';

    public function create(array $data): ?int
    {
        if (!isset($data['category_id'])) {
            throw new \Exception("CategoryRepository error: category_id is required.");
        }

        return parent::create($data);
    }

    public function getCategory(int $categoryId): array
    {
        $query = $this->select() . "WHERE id = $categoryId ORDER BY name";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
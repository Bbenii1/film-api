<?php
namespace App\Repositories;

class StudioRepository extends BaseRepository
{
    public string $tableName = 'studio';

    public function create(array $data): ?int
    {
        return parent::create($data);
    }

    public function getStar(int $id): array
    {
        $query = $this->select() . "WHERE id = $id ORDER BY name";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
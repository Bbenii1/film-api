<?php
namespace App\Repositories;

class StarRepository extends BaseRepository
{
    public string $tableName = 'star';

    public function create(array $data): ?int
    {
        if (!isset($data['star_id'])) {
            throw new \Exception("StarRepository error: star_id is required.");
        }

        return parent::create($data);
    }

    public function getStar(int $starId): array
    {
        $query = $this->select() . "WHERE id = $starId ORDER BY name";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
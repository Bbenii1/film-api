<?php
namespace App\Repositories;

class DirectorRepository extends BaseRepository
{
    public string $tableName = 'director';

    public function create(array $data): ?int
    {
        if (!isset($data['director_id'])) {
            throw new \Exception("DirectorRepository error: director_id is required.");
        }

        return parent::create($data);
    }

    public function getDirector(int $directorId): array
    {
        $query = $this->select() . "WHERE id = $directorId ORDER BY first_name, last_name";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
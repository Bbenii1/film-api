<?php
namespace App\Repositories;

class FilmRepository extends BaseRepository
{
    public string $tableName = 'film';

    public function getFilm(int $filmId): array
    {
        $query = $this->select() . "WHERE id = $filmId ORDER BY title";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
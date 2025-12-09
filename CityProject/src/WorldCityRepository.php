<?php
declare(strict_types=1);

class WorldCityRepository {

    public function __construct(private PDO $pdo) {}

    private function arrayToModel(array $entry): WorldCityModel {
        return new WorldCityModel(
            $entry['id'],
            $entry['city'],
            $entry['city_ascii'],
            (float) $entry['lat'],
            (float) $entry['lng'],
            $entry['country'],
            $entry['iso2'],
            $entry['iso3'],
            $entry['admin_name'],
            $entry['capital'],
            $entry['population']
        );
    }

    public function fetchById(int $id): ?WorldCityModel {
        $stmt = $this->pdo->prepare('SELECT * FROM `worldcities` WHERE `id` = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($entry)) {
            return $this->arrayToModel($entry);
        }
        else {
            return null;
        }
    }

    public function fetch(): array {
        $stmt = $this->pdo->prepare('SELECT * 
            FROM `worldcities` 
            ORDER BY `population`
            DESC LIMIT 10');

        $stmt->execute();

        $models = [];
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($entries AS $entry) {
            $models[] = $this->arrayToModel($entry);
        }

        return $models;
    }

    public function paginate(int $page, int $perPage = 15, string $search = ''): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        if (!empty($search)) {
            $sql = 'SELECT * FROM `worldcities` 
                    WHERE `city` LIKE :search 
                    ORDER BY `population` DESC 
                    LIMIT :limit OFFSET :offset';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':search', "%$search%"); 
        } else {
            $sql = 'SELECT * FROM `worldcities` 
                    ORDER BY `population` DESC 
                    LIMIT :limit OFFSET :offset';
            $stmt = $this->pdo->prepare($sql);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $models = [];
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($entries as $entry) {
            $models[] = $this->arrayToModel($entry);
        }

        return $models;
    }

    public function count(string $search = ''): int {
        if (!empty($search)) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) AS `count` FROM `worldcities` WHERE `city` LIKE :search');
            $stmt->bindValue(':search', "%$search%");
        } else {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) AS `count` FROM `worldcities`');
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function update(int $id, array $properties): WorldCityModel {
        $stmt = $this->pdo->prepare('UPDATE `worldcities` 
            SET 
                `city` = :city,
                `city_ascii` = :cityAscii,
                `country` = :country,
                `iso2` = :iso2, 
                `population` = :population
            WHERE `id` = :id');

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':city', $properties['city']);
        $stmt->bindValue(':cityAscii', $properties['cityAscii']);
        $stmt->bindValue(':country', $properties['country']);
        $stmt->bindValue(':iso2', $properties['iso2']);
        $stmt->bindValue(':population', $properties['population']);
        $stmt->execute();

        return $this->fetchById($id);
    }

}
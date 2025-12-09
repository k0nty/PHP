<?php 

namespace App\Repository;

use PDO;
use App\Model\PageModel;

class PagesRepository {

    public function __construct(private PDO $pdo) {}

    public function fetchForNavigation(): array {
        $stmt = $this->pdo->prepare('SELECT * FROM `pages` ORDER BY `id` ASC');
        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_CLASS, PageModel::class);
        return $entries;
    }

    public function fetchBySlug(string $slug): ?PageModel
    {
        $sql = 'SELECT * FROM pages WHERE slug = :slug LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':slug' => $slug]);
        } catch (\PDOException $e) {
            return null;
        }

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        // If PageModel provides a factory, use it
        if (method_exists(PageModel::class, 'fromArray')) {
            return PageModel::fromArray($row);
        }

        // If PageModel accepts an array in the constructor, try that
        try {
            $ref = new \ReflectionClass(PageModel::class);
            $ctor = $ref->getConstructor();
            if ($ctor && $ctor->getNumberOfParameters() === 1) {
                return $ref->newInstance($row);
            }
        } catch (\ReflectionException $e) {
            // ignore and try fallback
        }

        // Fallback: create instance and populate via setters or public properties
        $page = new PageModel();
        foreach ($row as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($page, $setter)) {
                $page->$setter($value);
            } elseif (property_exists($page, $key)) {
                $page->$key = $value;
            }
        }

        return $page;
    }
}
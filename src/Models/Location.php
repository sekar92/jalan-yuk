<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Location
{
    public static function allPublished(array $filters = []): array
    {
        $pdo = Database::getConnection();
        $isMySQL = Database::isMySQL();

        $sql = "
            SELECT 
                l.*,
                c.name AS category_name,
                c.slug AS category_slug,
                u.username AS author_name,
                (SELECT media_url FROM location_media WHERE location_id = l.id AND media_type = 'Image' LIMIT 1) AS thumbnail_url,
                (SELECT file_path FROM location_media WHERE location_id = l.id AND media_type = 'Image' LIMIT 1) AS thumbnail_path,
                (SELECT COUNT(*) FROM location_media WHERE location_id = l.id) AS media_count
            FROM locations l
            JOIN categories c ON l.category_id = c.id
            JOIN users u ON l.user_id = u.id
            WHERE l.approval_status = 'Published'
        ";

        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND l.category_id = :category_id";
            $params[':category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['visit_status'])) {
            $sql .= " AND l.visit_status = :visit_status";
            $params[':visit_status'] = $filters['visit_status'];
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            if ($isMySQL) {
                // MySQL Full-Text Search in BOOLEAN MODE
                $sql .= " AND MATCH(l.title, l.description, l.personal_notes) AGAINST(:search_fts IN BOOLEAN MODE)";
                // Append '*' to tokens for prefix matching
                $tokens = preg_split('/\s+/', $search);
                $ftsQuery = implode(' ', array_map(fn($t) => '+' . $t . '*', $tokens));
                $params[':search_fts'] = $ftsQuery;
            } else {
                // SQLite fallback
                $sql .= " AND (l.title LIKE :search_like OR l.description LIKE :search_like OR l.personal_notes LIKE :search_like)";
                $params[':search_like'] = '%' . $search . '%';
            }
        }

        $sql .= " ORDER BY l.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findWithDetails(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                l.*,
                c.name AS category_name,
                c.slug AS category_slug,
                u.username AS author_name,
                u.email AS author_email
            FROM locations l
            JOIN categories c ON l.category_id = c.id
            JOIN users u ON l.user_id = u.id
            WHERE l.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $location = $stmt->fetch();

        if (!$location) {
            return null;
        }

        // Fetch media
        $stmtMedia = $pdo->prepare("SELECT * FROM location_media WHERE location_id = :loc_id ORDER BY id ASC");
        $stmtMedia->execute([':loc_id' => $id]);
        $location['media'] = $stmtMedia->fetchAll();

        return $location;
    }

    public static function getByUser(int $userId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                l.*,
                c.name AS category_name,
                (SELECT COUNT(*) FROM location_media WHERE location_id = l.id) AS media_count
            FROM locations l
            JOIN categories c ON l.category_id = c.id
            WHERE l.user_id = :user_id
            ORDER BY l.created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function getPendingList(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
            SELECT 
                l.*,
                c.name AS category_name,
                u.username AS author_name,
                (SELECT COUNT(*) FROM location_media WHERE location_id = l.id) AS media_count
            FROM locations l
            JOIN categories c ON l.category_id = c.id
            JOIN users u ON l.user_id = u.id
            WHERE l.approval_status = 'Pending'
            ORDER BY l.created_at ASC
        ");
        return $stmt->fetchAll();
    }

    public static function allForModeration(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
            SELECT 
                l.*,
                c.name AS category_name,
                u.username AS author_name,
                (SELECT COUNT(*) FROM location_media WHERE location_id = l.id) AS media_count
            FROM locations l
            JOIN categories c ON l.category_id = c.id
            JOIN users u ON l.user_id = u.id
            ORDER BY 
                CASE l.approval_status 
                    WHEN 'Pending' THEN 1 
                    WHEN 'Rejected' THEN 2 
                    WHEN 'Published' THEN 3 
                END,
                l.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO locations (
                user_id, category_id, title, description, personal_notes,
                maps_url, latitude, longitude, visit_status, approval_status
            ) VALUES (
                :user_id, :category_id, :title, :description, :personal_notes,
                :maps_url, :latitude, :longitude, :visit_status, :approval_status
            )
        ");
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':category_id' => $data['category_id'],
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':personal_notes' => $data['personal_notes'] ?? null,
            ':maps_url' => $data['maps_url'],
            ':latitude' => $data['latitude'] ?? null,
            ':longitude' => $data['longitude'] ?? null,
            ':visit_status' => $data['visit_status'] ?? 'Wishlist',
            ':approval_status' => $data['approval_status'] ?? 'Pending',
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $fields = [
            'category_id = :category_id',
            'title = :title',
            'description = :description',
            'personal_notes = :personal_notes',
            'maps_url = :maps_url',
            'latitude = :latitude',
            'longitude = :longitude',
            'visit_status = :visit_status'
        ];

        $params = [
            ':id' => $id,
            ':category_id' => $data['category_id'],
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':personal_notes' => $data['personal_notes'] ?? null,
            ':maps_url' => $data['maps_url'],
            ':latitude' => $data['latitude'] ?? null,
            ':longitude' => $data['longitude'] ?? null,
            ':visit_status' => $data['visit_status'] ?? 'Wishlist',
        ];

        if (isset($data['approval_status'])) {
            $fields[] = 'approval_status = :approval_status';
            $params[':approval_status'] = $data['approval_status'];
        }

        $sql = "UPDATE locations SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function updateApprovalStatus(int $id, string $status): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE locations SET approval_status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM locations WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}

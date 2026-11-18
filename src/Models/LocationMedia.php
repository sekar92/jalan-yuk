<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class LocationMedia
{
    public static function getByLocation(int $locationId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM location_media WHERE location_id = :loc_id ORDER BY id ASC");
        $stmt->execute([':loc_id' => $locationId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO location_media (location_id, media_type, media_url, file_path)
            VALUES (:location_id, :media_type, :media_url, :file_path)
        ");
        $stmt->execute([
            ':location_id' => $data['location_id'],
            ':media_type' => $data['media_type'],
            ':media_url' => $data['media_url'],
            ':file_path' => $data['file_path'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        // Check if there is an uploaded file to unlink
        $stmt = $pdo->prepare("SELECT file_path FROM location_media WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if ($row && !empty($row['file_path'])) {
            $fullPath = dirname(__DIR__, 2) . '/public/uploads/' . $row['file_path'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $stmtDel = $pdo->prepare("DELETE FROM location_media WHERE id = :id");
        return $stmtDel->execute([':id' => $id]);
    }

    public static function deleteByLocation(int $locationId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT file_path FROM location_media WHERE location_id = :loc_id AND file_path IS NOT NULL");
        $stmt->execute([':loc_id' => $locationId]);
        $files = $stmt->fetchAll();
        foreach ($files as $f) {
            if (!empty($f['file_path'])) {
                $fullPath = dirname(__DIR__, 2) . '/public/uploads/' . $f['file_path'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        $stmtDel = $pdo->prepare("DELETE FROM location_media WHERE location_id = :loc_id");
        return $stmtDel->execute([':loc_id' => $locationId]);
    }
}

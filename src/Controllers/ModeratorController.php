<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Location;

class ModeratorController
{
    private function ensureModerator(): void
    {
        if (!Auth::check() || (!Auth::isModerator() && !Auth::isAdmin())) {
            http_response_code(403);
            require_once dirname(__DIR__) . '/Views/errors/403.php';
            exit;
        }
    }

    public function queue(): void
    {
        $this->ensureModerator();
        $pendingLocations = Location::getPendingList();
        $allLocations = Location::allForModeration();
        require_once dirname(__DIR__) . '/Views/moderator/queue.php';
    }

    public function approve(int $id): void
    {
        $this->ensureModerator();
        Location::updateApprovalStatus($id, 'Published');
        Auth::setFlash('success', 'Lokasi berhasil disetujui dan dipublikasikan.');
        header('Location: /moderator/queue');
        exit;
    }

    public function reject(int $id): void
    {
        $this->ensureModerator();
        Location::updateApprovalStatus($id, 'Rejected');
        Auth::setFlash('warning', 'Lokasi ditandai sebagai Ditolak (Rejected).');
        header('Location: /moderator/queue');
        exit;
    }
}

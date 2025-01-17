<?php

namespace TaskForce\classes\actions;

class AcceptAction extends AbstractAction
{
    public function getTitle(): string {
        return 'Принять задание';
    }

    public function checkRights(int $userId, int $customerId, int $performerId): bool {
        return $userId === $performerId;
    }
}

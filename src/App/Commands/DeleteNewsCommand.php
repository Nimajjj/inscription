<?php

namespace App\App\Commands;

use App\NewsEntityManager;


class DeleteNewsCommand
{
    private NewsEntityManager $newsEntityManager;

    public function __construct(NewsEntityManager $newsEntityManager)
    {
        $this->newsEntityManager = $newsEntityManager;
    }

    public function execute(array $input): array
    {
        try {
            $this->newsEntityManager->delete($input['id']);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
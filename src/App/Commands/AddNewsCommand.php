<?php

namespace App\App\Commands;

use App\NewsEntityManager;

class AddNewsCommand
{
    private NewsEntityManager $newsEntityManager;

    public function __construct(NewsEntityManager $newsEntityManager)
    {
        $this->newsEntityManager= $newsEntityManager;
    }

    public function execute(array $input): array
    {
        try {
            $news = $this->newsEntityManager->create($input['title'], $input['content']);
            return ['success' => true, 'news' => $news];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
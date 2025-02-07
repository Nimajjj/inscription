<?php

namespace App\App\Manager;

use App\App\Entity\News;
use App\VO\Uid;
use Exception;

class NewsEntityManager
{
    private array $newsStorage = [];

    public function create(News $news): void
    {
        $this->newsStorage[(string)$news->getId()] = $news;
    }

    /**
     * @throws Exception
     */
    public function update(News $news): void
    {
        if (!isset($this->newsStorage[(string)$news->getId()])) {
            throw new Exception("News not found.");
        }
        $this->newsStorage[(string)$news->getId()] = $news;
    }

    /**
     * @throws Exception
     */
    public function delete(News $news): void
    {
        if (!isset($this->newsStorage[(string)$news->getId()])) {
            throw new Exception("News not found.");
        }
        unset($this->newsStorage[(string)$news->getId()]);
    }

    public function getByID(Uid $id): ?News
    {
        return $this->newsStorage[(string)$id] ?? null;
    }
}
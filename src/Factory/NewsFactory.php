<?php

namespace App\Factory;

use App\Model\News;
use App\VO\UID;
use DateMalformedStringException;

final class NewsFactory implements IDataModelFactory
{
    public function createNews(
        ?UID $uid,
        string $content,
        \DateTimeImmutable $createdAt
    ): News {
        if (empty($content)) {
            throw new \InvalidArgumentException("Content is empty");
        }
        $news = new News();
        $news->setId($uid);
        $news->setContent($content);
        $news->setCreatedAt($createdAt);

        return $news;
    }

    /**
     * @throws DateMalformedStringException
     */
    public function create(array $data): News
    {
        return $this->createNews(
            isset($data['id']) ? new UID($data['id']) : null,
            $data['content'] ?? '',
            isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable()
        );
    }
}

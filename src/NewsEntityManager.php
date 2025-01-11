<?php

namespace App;

use App\VO\UID;
use App\Model\News;
use App\Adapter\MySQLAdapter;
use App\Query\QueryBuilder;
use App\Query\QueryAction;
use App\Query\QueryCondition;
use App\Repository\NewsRepository;
use DateMalformedStringException;
use Exception;

final class NewsEntityManager
{
    private MySQLAdapter $adapter;
    private NewsRepository $repository;

    public function __construct()
    {
        $this->adapter = new MySQLAdapter();
        $this->repository = new NewsRepository($this->adapter);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getByID(UID $id): News
    {
        return $this->repository->getById($id);
    }

    /**
     * @throws Exception
     */
    public function create(News $news): News
    {
        $query = (new QueryBuilder())
            ->buildAction(QueryAction::INSERT)
            ->buildTable("News")
            ->buildColumns(["id", "content", "created_at"])
            ->buildValues([$news->getId(), $news->getContent(), $news->getCreatedAt()])
            ->build();
        echo $query->toRawSql();

        $__ = [];
        $error = $this->adapter->executeQuery($query, $__);

        if ($error) {
            throw new Exception("Failed to execute query: " . $query->toRawSql());
        }

        var_dump($__);

        return $news;
    }

    /**
     * @throws Exception
     */
    public function update(News $news): News
    {
        $query = (new QueryBuilder())
            ->buildAction(QueryAction::UPDATE)
            ->buildTable("News")
            ->buildColumns(["id", "content", "created_at"])
            ->buildValues([$news->getId(), $news->getContent(), $news->getCreatedAt()])
            ->buildCondition("id", QueryCondition::IS_EQUAL, $news->getId()->getValue())
            ->build();

        $__ = [];
        $error = $this->adapter->executeQuery($query, $__);

        if ($error) {
            throw new Exception("Failed to execute query: " . $query->toRawSql());
        }

        return $news;
    }

    /**
     * @throws Exception
     */
    public function delete(News $news): void
    {
        if (!$news->getId() || !$news->getId()->getValue()) {
            throw new Exception("Invalid news ID for deletion.");
        }

        $query = (new QueryBuilder())
            ->buildAction(QueryAction::DELETE)
            ->buildTable("News")
            ->buildCondition("id", QueryCondition::IS_EQUAL, $news->getId()->getValue())
            ->build();

        $__ = [];
        $error = $this->adapter->executeQuery($query, $__);

        if ($error !== false) {
            throw new Exception(
                "Failed to execute query: " . $query->toRawSql() . " Error details: " . var_export(true, true)
            );
        }
    }
}
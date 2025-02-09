<?php

namespace App\EntityManager;

use App\Adapter\MySQLAdapter;
use App\Event\EventManager;
use App\Event\Events\EventNewsCreated;
use App\Model\News;
use App\Query\QueryAction;
use App\Query\QueryBuilder;
use App\Query\QueryCondition;
use App\Repository\NewsRepository;
use App\VO\UID;
use DateMalformedStringException;
use Exception;

const NEWS_TABLE = "news";
const NEWS_COLUMN_ID = "id";
const NEWS_COLUMN_CONTENT = "content";
const NEWS_COLUMN_CREATED_AT = "created_at";
const NEWS_COLUMNS = [NEWS_COLUMN_ID, NEWS_COLUMN_CONTENT, NEWS_COLUMN_CREATED_AT];


final class NewsEntityManager implements IEntityManager
{
    private EventManager $eventManager;
    private MySQLAdapter $adapter;
    private NewsRepository $repository;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
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
    public function create(News|\App\Model\DataModel $news): News
    {
        $query = (new QueryBuilder())
            ->buildAction(QueryAction::INSERT)
            ->buildTable(NEWS_TABLE)
            ->buildColumns(NEWS_COLUMNS)
            ->buildValues([$news->getId(), $news->getContent(), $news->getCreatedAt()])
            ->build();
        echo "[INFO] Executing " . $query->toRawSql() . "\n";

        $__ = [];
        $this->adapter->executeQuery($query, $__);
        $this->eventManager->notify(new EventNewsCreated($news));
        return $news;
    }

    /**
     * @throws Exception
     */
    public function update(News|\App\Model\DataModel $news): News
    {
        $query = (new QueryBuilder())
            ->buildAction(QueryAction::UPDATE)
            ->buildTable(NEWS_TABLE)
            ->buildColumns(NEWS_COLUMNS)
            ->buildValues([$news->getId(), $news->getContent(), $news->getCreatedAt()])
            ->buildCondition(NEWS_COLUMN_ID, QueryCondition::IS_EQUAL, $news->getId()->getValue())
            ->build();

        $__ = [];
        $error = $this->adapter->executeQuery($query, $__);

        if ($error === false)
        {
            throw new Exception("Failed to execute query: " . $query->toRawSql());
        }

        return $news;
    }

    /**
     * @throws Exception
     */
    public function delete(News|\App\Model\DataModel $news): void
    {
        if (!$news->getId() || !$news->getId()->getValue()) {
            throw new Exception("Invalid news ID for deletion.");
        }

        $query = (new QueryBuilder())
            ->buildAction(QueryAction::DELETE)
            ->buildTable(NEWS_TABLE)
            ->buildCondition(NEWS_COLUMN_ID, QueryCondition::IS_EQUAL, $news->getId()->getValue())
            ->build();

        $__ = [];
        $error = $this->adapter->executeQuery($query, $__);

        if ($error === false)
        {
            throw new Exception(
                "Failed to execute query: " . $query->toRawSql() . " Error details: " . var_export(true, true)
            );
        }
    }
}
<?php

namespace App\EntityManager;

use App\Adapter\MySQLAdapter;
use App\Event\EventManager;
use App\Event\Events\EventUserCreated;
use App\Event\Events\EventUserUpdated;
use App\Event\Events\EventUserDeleted;
use App\Model\News;
use App\Model\User;
use App\Query\QueryAction;
use App\Query\QueryBuilder;
use App\Query\QueryCondition;
use App\Repository\UserRepository;
use App\VO\UID;
use Exception;

final class UserEntityManager implements IEntityManager
{
    private EventManager $eventManager;
    private MySQLAdapter $adapter;
    private UserRepository $repository;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->adapter = new MySQLAdapter();
        $this->repository = new UserRepository($this->adapter);
    }

    public function getById(UID $id): User
    {
        return $this->repository->getById($id);
    }

    public function getByEmail(string $email): User
    {
        return $this->repository->getByEmail($email);
    }

    public function getByLogin(string $login): User
    {
        return $this->repository->getByLogin($login);
    }


    /**
     * Création d'un utilisateur avec vérification d'email.
     *
     * @throws Exception
     */
    public function create(User|\App\Model\DataModel $user): User
    {
        $existingUser = $this->repository->findByEmail($user->getEmail());
        if ($existingUser) {
            throw new Exception("A User with this mail '{$user->getEmail()}' already exists");
        }

        $query = (new QueryBuilder())
            ->buildAction(QueryAction::INSERT)
            ->buildTable("users")
            ->buildColumns(["id", "login", "email", "password", "created_at"])
            ->buildValues([
                $user->getId()->getValue(),
                $user->getLogin(),
                $user->getEmail(),
                $user->getPassword(),
                $user->getCreatedAt()->format('Y-m-d H:i:s')
            ])
            ->build();

        $__ = [];
        $this->adapter->executeQuery($query, $__);
        $this->eventManager->notify(new EventUserCreated($user));
        return $user;
    }

    /**
     * @throws Exception
     */
    public function update(User|\App\Model\DataModel $user): User
    {
        // Vérifier si un autre utilisateur utilise le même email
        $existingUser = $this->repository->findByEmail($user->getEmail());
        if ($existingUser && $existingUser->getId()->getValue() !== $user->getId()->getValue()) {
            throw new Exception("Un autre utilisateur utilise déjà cet email : " . $user->getEmail());
        }

        $query = (new QueryBuilder())
            ->buildAction(QueryAction::UPDATE)
            ->buildTable("users")
            ->buildColumns(["login", "email", "password", "created_at"])
            ->buildValues([
                $user->getLogin(),
                $user->getEmail(),
                $user->getPassword(),
                $user->getCreatedAt()->format('Y-m-d H:i:s')
            ])
            ->buildCondition("id", QueryCondition::IS_EQUAL, $user->getId()->getValue())
            ->build();

        // Debug : Afficher la requête générée
        echo "Requête générée : " . $query->toRawSql() . PHP_EOL;

        $__ = [];
        $this->adapter->executeQuery($query, $__);
        $this->eventManager->notify(new EventUserUpdated($user));
        return $user;
    }

    /**
     * @throws Exception
     */
    public function delete(User|\App\Model\DataModel $user): void {
        if (!$user->getId()) {
            throw new Exception("User ID is not set");
        }

        $query = (new QueryBuilder())
            ->buildAction(QueryAction::DELETE)
            ->buildTable("users")
            ->buildCondition("id", QueryCondition::IS_EQUAL, $user->getId()->getValue())
            ->build();

        $__ = [];
        $this->adapter->executeQuery($query, $__);
        $this->eventManager->notify(new EventUserDeleted($user));
    }
}

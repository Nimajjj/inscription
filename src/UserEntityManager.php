<?php

namespace App;

use App\Model\User;
use App\Adapter\MySQLAdapter;
use App\Query\QueryBuilder;
use App\Query\QueryAction;
use App\Query\QueryCondition;
use App\Repository\UserRepository;
use Exception;

final class UserEntityManager
{
    private MySQLAdapter $adapter;
    private UserRepository $repository;

    public function __construct()
    {
        $this->adapter = new MySQLAdapter();
        $this->repository = new UserRepository($this->adapter);
    }

    /**
     * Création d'un utilisateur avec vérification d'email.
     *
     * @throws Exception
     */
    public function create(User $user): User
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

        $outResult = [];
        $this->adapter->executeQuery($query, $outResult);
        return $user;
    }

    /**
     * @throws Exception
     */
    public function update(User $user): User
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

        $outResult = [];
        $this->adapter->executeQuery($query, $outResult);

        return $user;
    }

    /**
     * @throws Exception
     */
    public function delete(User $user): void {
        if (!$user->getId()) {
            throw new Exception("User ID is not set");
        }

        $query = (new QueryBuilder())
            ->buildAction(QueryAction::DELETE)
            ->buildTable("users")
            ->buildCondition("id", QueryCondition::IS_EQUAL, $user->getId()->getValue())
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

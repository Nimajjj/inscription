<?php

namespace App\Repository;

use App\Adapter\MySQLAdapter;
use App\Factory\UserFactory;
use App\Model\User;
use App\Query\QueryAction;
use App\Query\QueryBuilder;
use App\Query\QueryCondition;
use App\VO\Uid;
use DateMalformedStringException;

final class UserRepository
{
    private MySQLAdapter $adapter;

    public function __construct(MySQLAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @throws DateMalformedStringException
     */
    public final function getByEmail(string $email): User
    {
        $user = $this->findByEmail($email);

        if (!$user)
        {
            throw new \RuntimeException("No user found for mail: $email");
        }

        return $user;
    }

    /**
     * @throws DateMalformedStringException
     */
    public function findByEmail(string $email): ?User
    {
        $query = (new QueryBuilder())
            ->buildAction(QueryAction::SELECT)
            ->buildTable("users")
            ->buildColumns(["id", "login", "email", "password", "created_at"])
            ->buildCondition("email", QueryCondition::IS_EQUAL, $email)
            ->build();

        $results = [];
        $this->adapter->executeQuery($query, $results);

        if (empty($results)) {
            return null;
        }

        $outResult = $results[0];
        return (new User())
            ->setId(new Uid($outResult['id']))
            ->setEmail($outResult['email'])
            ->setLogin($outResult['login'])
            ->setPassword($outResult['password'])
            ->setCreatedAt(new \DateTimeImmutable($outResult['created_at']));
    }

    public final function getByLogin(string $login): User
    {
        $user = $this->findByLogin($login);

        if (!$user)
        {
            throw new \RuntimeException("No user found for login: $login");
        }

        return $user;
    }

    public function findByLogin(string $login): ?User
    {
        $query = (new QueryBuilder())
            ->buildAction(QueryAction::SELECT)
            ->buildTable("users")
            ->buildColumns(["id", "login", "email", "password", "created_at"])
            ->buildCondition("login", QueryCondition::IS_EQUAL, $login)
            ->build();

        $results = [];
        $this->adapter->executeQuery($query, $results);

        if (empty($results)) {
            return null;
        }

        $outResult = $results[0];
        return (new User())
            ->setId(new Uid($outResult['id']))
            ->setEmail($outResult['email'])
            ->setLogin($outResult['login'])
            ->setPassword($outResult['password'])
            ->setCreatedAt(new \DateTimeImmutable($outResult['created_at']));
    }
}

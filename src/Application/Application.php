<?php

namespace App\Application;

use App\App\Manager\NewsEntityManager;
use App\Application\CommandParser\CommandParser;
use App\Application\Enum\ApplicationCommand;
use App\Application\JsonHandler\JsonHandler;
use App\EntityManager\UserEntityManager;
use App\Event\EventManager;
use App\Event\Events\EventNewsCreated;
use App\Event\Events\EventUserCreated;
use App\Event\Events\EventUserUpdated;
use App\Event\Events\EventUserDeleted;
use App\Model\User;


final class Application
{
    private array $launchArgv;
    private CommandParser $commandParser;
    private NewsEntityManager $newsManager;
    private UserEntityManager $userManager;
    private EventManager $eventManager;


    public function __construct(array $argv)
    {
        $this->launchArgv = $argv;

        $this->commandParser = (new CommandParser())
            ->addCommand("add", ApplicationCommand::ADD)
            ->addCommand("update", ApplicationCommand::UPDATE)
            ->addCommand("delete", ApplicationCommand::DELETE);

        $this->eventManager = (new EventManager())
            ->subscribe(new EventUserCreated(null), function() { $this->onUserCreated(); })
            ->subscribe(new EventUserUpdated(null), function() { $this->onUserUpdated(); })
            ->subscribe(new EventUserDeleted(null), function() { $this->onUserDeleted(); })
            ->subscribe(new EventNewsCreated(null), function() { $this->onNewsCreated(); });

        $this->newsManager = new NewsEntityManager($this->eventManager);

        $this->userManager = new UserEntityManager($this->eventManager);
    }

    public function main(): void
    {
        $command = $this->commandParser->parseCommand($this->launchArgv);
        $filepath = $this->commandParser->parseFilename($this->launchArgv);

        try
        {
            $data = JsonHandler::loadJsonFile($filepath);
        }
        catch (\Exception $e)
        {
            echo "[ERROR] " . $e->getMessage() . PHP_EOL;
            return;
        }

        switch ($command)
        {
            case ApplicationCommand::ADD:
                $this->addUser($data);
                break;

            case ApplicationCommand::UPDATE:
                $this->updadteUser($data);
                break;

            case ApplicationCommand::DELETE:
                $this->deleteUser($data);
                break;

            default:
                echo "[ERROR] Unkown command : " . $lauchArgv . PHP_EOL;
                break;
        }
    }

    ###################### COMMANDS CALLBACKS ######################
    private function addUser(array $data): void
    {
        if (!JsonHandler::verifyKeys($data, ["login", "password", "email"]))
        {
            echo "[ERROR] Input file is invalid !" . PHP_EOL;
            return;
        }

        $user = new User(
            null,
            $data["login"],
            $data["email"],
            $data["password"],
            null
        );

        try
        {
            echo "[ INFO] add user " . $this->userManager->create($user) . PHP_EOL;
        }
        catch (\Exception $e)
        {
            echo "[ERROR] " . $e->getMessage() . PHP_EOL;
        }
    }

    private function updadteUser(array $data): void
    {
        if (!JsonHandler::verifyKeys($data, ["login", "password", "email"]))
        {
            echo "[ERROR] Input file is invalid !" . PHP_EOL;
            return;
        }
    }

    private function deleteUser(array $data): void
    {
        if (!JsonHandler::verifyKeys($data, ["id"]))
        {
            echo "[ERROR] Input file is invalid !" . PHP_EOL;
            return;
        }
    }

    ###################### EVENTS CALLBACKS ######################
    private function onUserCreated(): void
    {
        echo "[DEBUG] Event callback onUserCreated" . PHP_EOL;
    }

    private function onUserUpdated(): void
    {
        echo "[DEBUG] Event callback onUserUpdated" . PHP_EOL;
    }

    private function onUserDeleted(): void
    {
        echo "[DEBUG] Event callback onUserDeleted" . PHP_EOL;
    }

    private function onNewsCreated(): void
    {
        echo "[DEBUG] Event callback onNewsCreated" . PHP_EOL;
    }
}
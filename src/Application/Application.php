<?php

namespace App\Application;

use App\Application\CommandParser\CommandParser;
use App\Application\Enum\ApplicationCommand;
use App\Application\JsonHandler\JsonHandler;
use App\Application\EmailManager\EmailManager;
use App\EntityManager\UserEntityManager;
use App\EntityManager\NewsEntityManager;
use App\Event\EventManager;
use App\Event\Events\EventNewsCreated;
use App\Event\Events\EventUserCreated;
use App\Event\Events\EventUserUpdated;
use App\Event\Events\EventUserDeleted;
use App\Event\IEvent;
use App\Model\User;
use App\Model\Event;
use App\VO\Uid;


final class Application
{
    private array $launchArgv;
    private CommandParser $commandParser;
    private NewsEntityManager $newsManager;
    private UserEntityManager $userManager;
    private EventManager $eventManager;
    private EmailManager $emailManager;


    public function __construct(array $argv)
    {
        $this->launchArgv = $argv;

        $this->commandParser = (new CommandParser())
            ->addCommand("add", ApplicationCommand::ADD)
            ->addCommand("update", ApplicationCommand::UPDATE)
            ->addCommand("delete", ApplicationCommand::DELETE);

        $this->eventManager = (new EventManager())
            ->subscribe(new EventUserCreated(null), function(IEvent $event) { $this->onUserCreated($event); })
            ->subscribe(new EventUserUpdated(null), function(IEvent $event) { $this->onUserUpdated($event); })
            ->subscribe(new EventUserDeleted(null), function(IEvent $event) { $this->onUserDeleted($event); })
            ->subscribe(new EventNewsCreated(null), function(IEvent $event) { $this->onNewsCreated($event); });

        $this->newsManager = new NewsEntityManager($this->eventManager);

        $this->userManager = new UserEntityManager($this->eventManager);

        $this->emailManager = new EmailManager();
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
            return;
        }
    }

    private function updadteUser(array $data): void
    {
        if (!JsonHandler::verifyKeys($data, ["login", "password", "email"]))
        {
            echo "[ERROR] Input file is invalid !" . PHP_EOL;
            return;
        }

        try
        {
           $user = $this->userManager->getByEmail($data["email"]);
           $user->setLogin($data["login"]);
           $user->setPassword($data["password"]);
           $this->userManager->update($user);
           return;
        }
        catch (\Exception $e)
        {
            echo "[DEBUG] No user found for email " . $data["email"] . PHP_EOL;
        }

        try
        {
            $user = $this->userManager->getByLogin($data["login"]);
            $user->setEmail($data["email"]);
            $user->setPassword($data["password"]);
            $this->userManager->update($user);
            return;
        }
        catch (\Exception $e)
        {
            echo "[DEBUG] No user found for login " . $data["login"] . PHP_EOL;
        }

        echo "[ERROR] No user has been found with provided data" . PHP_EOL;
    }

    private function deleteUser(array $data): void
    {
        if (!JsonHandler::verifyKeys($data, ["id"]))
        {
            echo "[ERROR] Input file is invalid !" . PHP_EOL;
            return;
        }

        try
        {
            $user = $this->userManager->getById(new UID($data["id"]));
            echo "[DEBUG] " . $user . PHP_EOL;
            $this->userManager->delete($user);
        }
        catch (\Exception $e)
        {
            echo "[ERROR] " . $e->getMessage() . PHP_EOL;
        }
    }

    ###################### EVENTS CALLBACKS ######################
    private function onUserCreated(EventUserCreated $event): void
    {
        echo "[DEBUG] Event callback onUserCreated : " . $event->getUser() . PHP_EOL;
        $this->emailManager->send($event->getUser()->getEmail(), "Welcome !", "This is a welcome message for " . $event->getUser()->getLogin() . PHP_EOL);
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
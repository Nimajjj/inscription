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
use App\Factory\NewsFactory;
use App\Factory\UserFactory;
use App\Model\User;
use App\Model\News;
use App\VO\Uid;


final class Application
{
    private array $launchArgv;
    private CommandParser $commandParser;
    private NewsEntityManager $newsManager;
    private UserEntityManager $userManager;
    private EventManager $eventManager;
    private EmailManager $emailManager;
    private NewsFactory $newsFactory;
    private UserFactory $userFactory;


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
        $this->newsFactory = new NewsFactory();

        $this->userManager = new UserEntityManager($this->eventManager);
        $this->userFactory = new UserFactory();

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

        $user = $this->userFactory->create($data);

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
        $this->sendWelcomeEmail($event->getUser());

    }

    private function onUserUpdated(EventUserUpdated $event): void
    {
        echo "[DEBUG] Event callback onUserUpdated" . PHP_EOL;
        $UPDATE_MESSAGE = "Your account has been updated";
        $this->emailManager->send($event->getUser()->getEmail(), "Update", $UPDATE_MESSAGE);
    }

    private function onUserDeleted(): void
    {
        echo "[DEBUG] Event callback onUserDeleted" . PHP_EOL;
    }

    private function onNewsCreated(EventNewsCreated $event): void
    {
        echo "[DEBUG] Event callback onNewsCreated" . PHP_EOL;
        $this->sendNewsEmail($event->getNews());
    }

    ###################### HELPERS ######################
    private function sendWelcomeEmail(User $user): void
    {
        $WELCOME_MESSAGE_1 = "This is a welcome message for " . $user->getLogin() . PHP_EOL;
        $WELCOME_MESSAGE_2 = "A new user joined us : " . $user->getLogin() . PHP_EOL;

        $this->emailManager->send($user->getEmail(), "Welcome !", $WELCOME_MESSAGE_1);

        $news = $this->newsFactory->createNews(
            new Uid(),
            $WELCOME_MESSAGE_2,
            new \DateTimeImmutable("now")
        );
        $this->newsManager->create($news);
    }

    private function sendNewsEmail(News $news): void
    {
        $newUser = null;
        $newsContent = $news->getContent();
        $pattern = '/^A new user joined us : (.+)$/';
        if (preg_match($pattern, $newsContent, $matches))
        {
            $newUser = $this->userManager->getByLogin(trim($matches[1]));
        }

        foreach ($this->userManager->getAllEmails() as $email)
        {
            // Exception for welcome mail
            if ($newUser && $newUser->getEmail() == $email)
            {
                echo "[DEBUG] skipping " . $email . PHP_EOL;
                continue;
            }

            $this->emailManager->send($email, "A news has been published", $newsContent);
        }
    }
}
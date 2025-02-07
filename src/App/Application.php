<?php

namespace App\App;

use App\App\Commands\AddUserCommand;
use App\App\Commands\DeleteUserCommand;
use App\App\Commands\UpdateUserCommand;
use App\App\Commands\AddNewsCommand;
use App\App\Commands\UpdateNewsCommand;
use App\App\Commands\DeleteNewsCommand;
use App\App\Observers\UserObserver;
use App\App\Services\EmailService;
use App\App\Services\NewsService;
use App\App\Services\UserService;
use App\NewsEntityManager;

class Application
{
    private UserService $userService;
    private EmailService $emailService;
    private NewsEntityManager $newsEntityManager;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->emailService = new EmailService();
        $this->newsEntityManager = new NewsEntityManager();

        // Attach observers
        $this->userService->attach(new UserObserver($this->emailService, $this->newsService));
    }

    public function run(array $argv): void
    {
        if (count($argv) < 2) {
            echo "Erreur : Commande manquante.\n";
            return;
        }

        $command = $argv[1];
        $input = json_decode(file_get_contents('php://stdin'), true);

        switch ($command) {
            case 'add-user':
                $addUserCommand = new AddUserCommand($this->userService);
                $result = $addUserCommand->execute($input);
                break;
            case 'update-user':
                $updateUserCommand = new UpdateUserCommand($this->userService);
                $result = $updateUserCommand->execute($input);
                break;
            case 'delete-user':
                $deleteUserCommand = new DeleteUserCommand($this->userService);
                $result = $deleteUserCommand->execute($input);
                break;
            case 'add-news':
                $addNewsCommand = new AddNewsCommand($this->newsEntityManager);
                $result = $addNewsCommand->execute($input);
                break;
            case 'update-news':
                $updateNewsCommand = new UpdateNewsCommand($this->newsEntityManager);
                $result = $updateNewsCommand->execute($input);
                break;
            case 'delete-news':
                $deleteNewsCommand = new DeleteNewsCommand($this->newsEntityManager);
                $result = $deleteNewsCommand->execute($input);
                break;
            default:
                echo "Erreur : Commande inconnue.\n";
                return;
        }

        if (isset($result['error'])) {
            echo "Erreur : " . $result['error'] . "\n";
        } else {
            echo json_encode($result) . "\n";
        }
    }
}
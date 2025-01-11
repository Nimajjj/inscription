<?php

declare(strict_types=1);

namespace App;
require_once __DIR__ . '/../vendor/autoload.php';

use App\Factory\NewsFactory;
use App\VO\UID;
use DateMalformedStringException;
use DateTimeImmutable;
use App\Adapter\MySQLAdapter;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Exception;


$news = (new NewsFactory())->createNews(
    new UID(),
    "This is a news",
    new DateTimeImmutable("now")
);

$manager = new NewsEntityManager();

try {
    $manager->create($news);
} catch (\Exception $e) {

}
try {
    echo $manager->getByID($news->getId())->__toString() . "\n";
} catch (DateMalformedStringException $e) {

}

$news->setContent("This news has been updated.");
try {
    $manager->update($news);
} catch (\Exception $e) {

}
try {
    echo $manager->getByID($news->getId())->__toString() . "\n";
} catch (DateMalformedStringException $e) {

}

try {
    $manager->delete($news);
    echo $manager->getByID($news->getId())->__toString() . "\n";
} catch (\Exception $e) {
    echo "$e\n";
}

$userFactory = new UserFactory();
$adapter = new MySQLAdapter();
$userRepository = new UserRepository($adapter);
$managerUser = new UserEntityManager();
$user = $userFactory->createUser(
    new UID(),
    "trert",
    "ortfffff@example.com",
    "troucuil",
    new DateTimeImmutable("now")
);

try {
    echo "*User creation*" . "\n";

    $managerUser->create($user);
    echo "User created successfully : " . $user->__toString() . "\n";
} catch (\Exception $e) {
    echo "Error durung user creation : " . $e->getMessage() . "\n";
}

try {
    echo "*Update login* " . "\n";
    $user->setLogin("Nami");
    $managerUser->update($user);
    echo "user updated : " . $user->__toString() . "\n";
} catch (\Exception $e) {
    echo "Update error : " . $e->getMessage() . "\n";
}

echo $user -> getCreatedAt()->format("Y-m-d H:i:s") . "\n";
echo $user->getEmail() . "\n";

try {
    $user2 = $userRepository->findByEmail("sanchez@example.com");
} catch (DateMalformedStringException $e) {

}


try {
    echo "*Update alreadyexisting user* " . "\n";
    $ifExist = $userRepository->findByEmail("sanchez@example.com");
    if ($ifExist) {
        $user2->setLogin("Nami");
        $managerUser->update($user2);
        echo "user updated : " . $user2->__toString() . "\n";
    } else {
        echo "user not found" . "\n";
    }
} catch (\Exception $e) {}

$user3 = $userFactory->createUser(
    new UID(),
    "jevaisetresuppr",
    "suppression@example.com",
    "supprime",
    new DateTimeImmutable("now")
);

try {
    echo "*User creation*" . "\n";
    $managerUser->create($user3);
    echo "User created successfully : " . $user3->__toString() . "\n";
} catch (\Exception $e) {
    echo "Error durung user3creation : " . $e->getMessage() . "\n";
}

try {
    echo "*Delete user* " . "\n";
    $user3 = $userRepository->findByEmail("suppression@example.com");
    $managerUser->delete($user3);
    echo "user deleted : " . $user3->__toString() . "\n";
} catch (Exception $e) {
}

//$listeners = [
//    EventNewsCreated => [
//        MailManager::update
//    ]
//]
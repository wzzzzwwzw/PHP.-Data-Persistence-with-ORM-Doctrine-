<?php

/**
 * PHP version 7.4
 * src/create_user_admin.php
 *
 * @category Utils
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

use MiW\Results\Entity\User;
use MiW\Results\Utility\Utils;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(dirname(__DIR__, 2));

$entityManager = \MiW\Results\Utility\DoctrineConnector::getEntityManager();

if ($argc < 3 || $argc > 7) {
    $fichero = basename(__FILE__);
    echo <<< MARCA_FIN
    Usage: $fichero <Username> <Email> <Password> [<Enabled>] [<isAdmin>]
MARCA_FIN;
    exit(0);
}

$username    = (string) $argv[1];
$email       = (string) $argv[2];
$password    = (string) $argv[3];
$enabled     = (int) $argv[4]??1;
$admin     = (int) $argv[5]??0;

$user = new User();
$user->setUsername($username);
$user->setEmail($email);
$user->setPassword($password);
$user->setEnabled($enabled);
$user->setIsAdmin($admin);
try {
    $entityManager->persist($user);
    $entityManager->flush();
    if (in_array('--json', $argv, true)) {
        echo json_encode($user, JSON_PRETTY_PRINT). PHP_EOL;
    }
    echo 'Created  User with ID #' . $user->getId() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
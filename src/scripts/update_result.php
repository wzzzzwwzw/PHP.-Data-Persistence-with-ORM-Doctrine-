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
use MiW\Results\Entity\Result;
use MiW\Results\Utility\Utils;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(dirname(__DIR__, 2));

$entityManager = \MiW\Results\Utility\DoctrineConnector::getEntityManager();

if ($argc <2 || $argc >5) {
    $fich = basename(__FILE__);
    echo <<< MARCA_FIN
    Usage: $fich  <ResultId> [<userId>] [<result>] [<time>] 
MARCA_FIN;
    exit(0);
}

$resultId       = (int) $argv[1];
$userId       = (int) $argv[2];

/** @var Result $result */
$result = $entityManager
    ->getRepository(Result::class)
    ->findOneBy(['id' => $resultId]);
if (null === $result) {
    echo "Result con Id $resultId no encontrado" . PHP_EOL;
    exit(0);
}else{
    foreach ($argv as $k => $v) {
        if($v!=="--json") {
            if ($k === 2) {
                $resultUser = $entityManager
                    ->getRepository(User::class)
                    ->findOneBy(['id' => $userId]);
                if(null==$resultUser){
                    echo "User con Id $userId no encontrado" . PHP_EOL;
                    exit(0);
                }else{
                    $result->getUser($resultUser);
                }
            } else if ($k === 3) {
                $result->setResult($v);
            }else if ($k === 4) {
                $result->setTime($v);
            }
        }
    }
} try {
$entityManager->persist($result);
$entityManager->flush();
if (in_array('--json', $argv, true)) {
    echo json_encode($result, JSON_PRETTY_PRINT). PHP_EOL;
}
echo 'Update Result with ID #' . $resultId . PHP_EOL;
} catch (Exception $exception) {

    echo $exception->getMessage() . PHP_EOL;
}
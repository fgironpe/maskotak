<?php
/**
 * Obtiene un animal por su id
 */
require_once 'classes/Animal.php';
require_once '../../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logger = new Logger('animalGetByType');
$logger->pushHandler(new StreamHandler('lib/app.log', Logger::DEBUG));
$error = '';

try {
    $postdata = file_get_contents("php://input");
    $request  = json_decode($postdata, true);

    if ($request) {
        $animal = new Animal();
        $animal->setType($request);
        $animalGet = $animal->retrieveAnimalByType();
    }
} catch (Exception $e) {
    $error = 'No se puede obtener el animal';
    $logger->error($error);
}

if ($error == '') {
    $reply = array(
        'status'   => 'OK',
        'response' => $animalGet,
    );
    http_response_code(200); // 200 OK
} else {
    $reply = array(
        'status' => 'Error',
        'error'  => $error,
    );
    http_response_code(503); // 503 Service Unavailable
    foreach ($error as $err) {
        $logger->info("Error: $err");
    }
}

header('Content-type:application/json;charset=utf-8');
echo json_encode($reply, JSON_UNESCAPED_UNICODE);

// echo json_encode($animals);

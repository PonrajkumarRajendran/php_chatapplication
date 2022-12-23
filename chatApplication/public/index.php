<?php 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

//Database class and methods.
require __DIR__ . '/../src/database/database.php';

//Validatioon class and methods.
require __DIR__ . '/../src/validation/validation.php';

$app = AppFactory::create();

//API endpoints.
require __DIR__ . '/../src/routes/messageController.php';

$app->run();

?>

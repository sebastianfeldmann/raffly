<?php

require '../vendor/autoload.php';

use SebastianFeldmann\Raffly\Router;

define('ROOT_DIR', realpath(__DIR__ . '/../') . '/');
define('DATA_DIR', ROOT_DIR . 'data/');
define('PAGES_DIR', ROOT_DIR . 'pages/');
define('TPL_DIR', ROOT_DIR . 'templates/');

$router = new Router();

$router->addRoute('/',                         PAGES_DIR . 'homepage.php',                 'homepage'          );
$router->addRoute('/signup/*',                 PAGES_DIR . 'signup.php',                   'signup'            );
$router->addRoute('/qrcode/*',                 PAGES_DIR . 'qrcode.php',                   'qrcode'            );
$router->addRoute('/raffle/*',                 PAGES_DIR . 'raffle.php',                   'raffle'            );
$router->addRoute('/winner/*',                 PAGES_DIR . 'winner.php',                   'winner'            );
$router->addRoute('/admin/raffles',            PAGES_DIR . 'admin/raffles.php',            'admin'             );
$router->addRoute('/admin/raffle/*',           PAGES_DIR . 'admin/raffle.php',             'raffle'            );
$router->addRoute('/admin/participant/delete', PAGES_DIR . 'admin/participant_delete.php', 'delete_participant');
$router->addRoute('/admin/winner/delete',      PAGES_DIR . 'admin/winner_delete.php',      'delete_winner'     );

$router->handleRequest();

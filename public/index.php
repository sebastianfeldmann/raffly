<?php

require '../vendor/autoload.php';

use SebastianFeldmann\Raffly\Router;

$router = new Router();

// Define routes
$router->addRoute('/',              '../pages/homepage.php', 'homepage');
$router->addRoute('/signup/*',      '../pages/signup.php',   'signup'  );
$router->addRoute('/qrcode/*',      '../pages/qrcode.php',   'qrcode'  );
$router->addRoute('/raffle/*',      '../pages/raffle.php',   'raffle'  );
$router->addRoute('/winner/*',      '../pages/winner.php',   'winner'  );
$router->addRoute('/admin/raffles', '../pages/admin.php',    'admin'   );

$router->handleRequest();

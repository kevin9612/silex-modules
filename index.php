<?php

// Taller Silex - Usuarios
// =======================

// si la ruta termina en "/", cambia la ruta a "/index.php"
if ( substr($_SERVER["REQUEST_URI"], -1) == '/'
     && substr($_SERVER["REQUEST_URI"], -10) != 'index.php/') {
  header("Location: index.php");
  die();
}

// uso del autoload generado por composer
$app = require_once __DIR__.'/app/app.php';

// Ruta por defecto
// ================

// la ruta "/" redirige al formulario de login
$app->get('/', function() use($app) {
  // redirige el navegador a "/login"
  return $app->redirect( $app['url_generator']->generate('login'));
});

// Controladores por cada módulo
// =============================

$app->mount('/login', new App\Login\LoginController());
$app->mount('/users', new App\Users\UsersController());
$app->mount('/salones', new App\Salon\SalonesController());
// Corre la Aplicación
// ===================

$app['debug'] = true;
$app->run();
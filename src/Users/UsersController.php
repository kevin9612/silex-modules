<?php
namespace App\Users;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class UsersController implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    // creates a new controller based on the default route
    $controller = $app['controllers_factory'];
    // la ruta "/users/list"
    $controller->get('/list', function() use($app) {
      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');
      $users = $app['session']->get('users');
      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // muestra la plantilla
        return $app['twig']->render('Users/users.list.html.twig', array(
          'user' => $user,
          'users'=> $users
        ));
      } else {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }
    // hace un bind
    })->bind('users-list');
    // la ruta "/users/edit"
    $controller->get('/edit', function() use($app) {
      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');
      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // muestra la plantilla
        return $app['twig']->render('Users/users.edit.html.twig', array(
          'user' => $user
        ));
      } else {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }
    // hace un bind
    })->bind('users-edit');
    $controller->post('/users-save', function (Request $request) use ($app){
  $users = $app['session']->get('users');
  $users[] = array(
    'nombre' => $request->get('nombre'),
    'apellido' => $request->get('apellido'),
    'direccion' => $request->get('direccion'),
    'email' => $request->get('email'),
    'telefono' => $request->get('telefono')
  );
  $app['session']->set('users',$users);
  return $app->redirect( $app['url_generator']->generate('users-list'));
})->bind('users-save');
    return $controller;
  }
}
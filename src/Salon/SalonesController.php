<?php
namespace App\Salon;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class SalonesController implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    // creates a new controller based on the default route
    $controller = $app['controllers_factory'];
    // la ruta "/users/list"
    $controller->get('/saloneslist', function() use($app) {
      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');
      // obtiene el listado de usuarios
      $salones = $app['session']->get('salones');
      if (!isset($salones)) {
        $salones = array();
      }
      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // muestra la plantilla
        return $app['twig']->render('Salones/salones.list.html.twig', array(
          'user' => $user,
          'salones' => $salones
        ));
      } else {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }
    // hace un bind
    })->bind('salones-list');
    // la ruta "/users/new"
    $controller->get('/new', function() use($app) {
      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');
      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // muestra la plantilla
        return $app['twig']->render('Salones/salones.edit.html.twig', array(
          'user' => $user,
          'index' => '',
          'salon_to_edit' => array(
              'id' => '',
              'nombre' => '',
              'edificio' => '',
              'direccion' => '',
              'capacidad' => ''
            )
        ));
      } else {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }
    // hace un bind
    })->bind('salones-new');
    // la ruta "/users/edit"
    $controller->get('/edit/{index}', function($index) use($app) {
      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');
      // obtiene los usuarios de la sesión
      $salones = $app['session']->get('salones');
      if (!isset($salones)) {
        $salones = array();
      }
      // no ha ingresado el usuario (no ha hecho login) ?
      if ( !isset( $user ) || $user == '' ) {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      // no existe un usuario en esa posición ?
      } else if ( !isset($salones[$index])) {
        // muestra el formulario de nuevo usuario
        return $app->redirect( $app['url_generator']->generate('salones-new') );
      } else {
        // muestra la plantilla
        return $app['twig']->render('Salones/salones.edit.html.twig', array(
          'user' => $user,
          'index' => $index,
          'salon_to_edit' => $salones[$index]
        ));
      }
    // hace un bind
    })->bind('salones-edit');
    $controller->post('/save', function( Request $request ) use ( $app ){
      // obtiene los usuarios de la sesión
      $salones = $app['session']->get('salones');
      if (!isset($salones)) {
        $salones = array();
      }
      // index no está incluido en la petición
      $index = $request->get('index');
      if ( !isset($index) || $index == '' ) {
        // agrega el nuevo usuario
        $salones[] = array(
          'id' => $request->get('id'),
          'nombre' => $request->get('nombre'),
          'edificio' => $request->get('edificio'),
          'direccion' => $request->get('direccion'),
          'capacidad' => $request->get('capacidad')
        );
      } else {
        // modifica el usuario en la posición $index
        $salones[$index] = array(
         'id' => $request->get('id'),
          'nombre' => $request->get('nombre'),
          'edificio' => $request->get('edificio'),
          'direccion' => $request->get('direccion'),
          'capacidad' => $request->get('capacidad')
        );
      }
      // actualiza los datos en sesión
      $app['session']->set('salones', $salones);
      // muestra la lista de usuarios
      return $app->redirect( $app['url_generator']->generate('salones-list') );
    })->bind('salones-save');
    $controller->get('/delete/{index}', function($index) use ($app) {
      // obtiene los usuarios de la sesión
      $salones = $app['session']->get('salones');
      if (!isset($salones)) {
        $salones = array();
      }
      // no existe un usuario en esa posición ?
      if ( isset($salones[$index])) {
        unset ($salones[$index]);
        $app['session']->set('salones', $salones);
      }
      // muestra la lista de usuarios
      return $app->redirect( $app['url_generator']->generate('salones-list') );
    })->bind('salones-delete');
    return $controller;
  }
}
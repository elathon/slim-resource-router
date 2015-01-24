<?php namespace Thesenicethings\ResourceRouter;

/**
 * Slim Framework route generator for RESTful resources
 */
class ResourceRouter
{

  /**
   * Generates routes for a given resource and controller
   * @param  Slim\Slim  $app
   * @param  string     $route
   * @param  string     $controller
   */
  public static function create(Slim\Slim &$app, $route, $controller) {
    $ctrl = self::getController($controller);
    $route = self::generateRoute($route);

    // Index route without last :id
    $index = substr($route, 0, -4);

    // Get all* records
    $app->get($index, function() use ($ctrl) {
      call_user_func_array(array($ctrl(), "all"), func_get_args());
    });

    // Create a new record
    $app->post($index, function() use ($ctrl) {
      call_user_func_array(array($ctrl(), "create"), func_get_args());
    });

    // Get single record
    $app->get($route, function() use ($ctrl) {
      call_user_func_array(array($ctrl(), "read"), func_get_args());
    });

    // Update a record
    $app->put($route, function() use ($ctrl) {
      call_user_func_array(array($ctrl(), "update"), func_get_args());
    });

    // Delete a record
    $app->delete($route, function() use ($ctrl) {
      call_user_func_array(array($ctrl(), "delete"), func_get_args());
    });
      
  }

  /**
   * Create controller Closure for lazy loading
   * @param  string $ctrl
   * @return Closure
   */
  public static function getController($ctrl)
  {
    return function() use ($ctrl) {
      return new $ctrl();
    };
  }

  /**
   * Generate the full route based on the route summary
   * @param  string $route
   * @return string
   */
  public static function generateRoute($route)
  {
    $parts = (strpos($route, ".")) ? explode(".", $route) : array($route);
    $route = "";
    
    foreach ($parts as $k => $value) {
      $id_count = ($k !== 0) ? $k+1 : "";
      $route .= "/{$value}/:id{$id_count}";
    }

    return $route;
  }

}
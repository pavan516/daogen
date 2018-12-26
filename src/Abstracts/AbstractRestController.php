<?php declare(strict_types=1);

namespace App\Controllers;

use Spin\Core\Controller;

# Universal Metrics
use \App\Clients\Metrics\Universal\Client AS MetricsCollectorClient;

abstract class AbstractRestController extends Controller
{
  /** @var    object    Metrics Collector Client */
  protected $metricsCollectorClient;

  /**
   * Initialization method
   *
   * This method is called right after the controller has been created before
   * any route specific Middleware handlers
   *
   * @param      array  $args   Path variable arguments as name=value pairs
   */
  public function initialize(array $args)
  {
    parent::initialize($args);

    # Metrics Collector Client - Default values
    $this->metricsCollectorClient = new MetricsCollectorClient();

    # Default the Response to JSON
    $response = responseJson([],404)->withHeader('Content-Type','application/json');
    app()->setResponse($response);

    return ;
  }

/* ************************************************************************************************************** */
/* ************************************************************************************************************** */

  /**
   * Send Mapping Metrics
   *
   * @return  self
   */
  protected function sendMetrics(array $dimensions=[])
  {
    if ( config('metrics_collection.enabled') === true )
    {
      # Send Metrics
      $ok = $this->metricsCollectorClient->sendMetrics($dimensions);
    }

    return $this;
  }

}

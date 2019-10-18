<?php declare(strict_types=1);

namespace App\Models;

abstract class AbstractBaseEntity
{
  /**
   * Constructor
   *
   * @param array $args [description]
   */
  public function __construct($args=null)
  {
    if ( is_array($args) ) {
      # Decode from array
      $this->fromArray($args);
    } else
    if ( is_string($args) ) {
      # Decoe from JSON string
      $this->fromJSON($args);
    } else {
      # Just clear properties
      $this->clear();
    }
  }

  /**
   * Clear properties
   */
  public function clear()
  {
    return $this;
  }

  /**
   * Return properties as Array
   *
   * @return array              Array with properties
   */
  abstract public function fromArray(array $fields);

  /**
   * Return properties as Array
   *
   * @param  array $exclude     Array with fields to exclude from final output
   * @return array              Array with properties
   */
  abstract public function asArray(array $excluded=[]): array;

  /**
   * Decode a JSON document into the properties
   *
   * @param  string $json
   * @return self
   */
  public function fromJson(string $json)
  {
    return $this->fromArray( \json_decode($arr,true) );
  }

  /**
   * Return properties as JSON document
   *
   * @return string             JSON Document
   */
  public function asJson(array $exclude=[], $options=JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK): string
  {
    return \json_encode($this->asArray($exclude), $options);
  }
}

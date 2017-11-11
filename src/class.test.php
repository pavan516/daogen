<?php
/**
 * Nofuzz based Test Cases for a Table
 */
##############################################################################################################

class Test
{
  protected $table;
  protected $options;
  protected $namespace;

  public function __construct($table=null, array $options=[])
  {
    $this->table = $table;
    $this->options = $options;
    $this->namespace = $options['namespace'] ?? '\\App\\Tests';
  }


  /**
   * Output as a PHP Source
   *
   * @return string
   */
  public function getPhpSource()
  {
    global $daoGenVersion;

    $s  = '';
    $s .= "<?php declare(strict_types=1);".PHP_EOL;

    # DocBlock
    $s .= "/** ".PHP_EOL;
    $s .= " * ".$this->table->getClassName().'Test.php'.PHP_EOL;
    $s .= " *".PHP_EOL;
    $s .= " *    Unit Test for entity ".$this->table->getTableName().PHP_EOL;
    $s .= " *".PHP_EOL;
    $s .= " *  Generated with DaoGen v".$daoGenVersion.PHP_EOL;
    $s .= " *".PHP_EOL;
    $s .= " * @since    ".(new \DateTime('now',new \DateTimeZone("UTC")))->format("Y-m-d H:i:s").PHP_EOL;
    $s .= " * @package  Nofuzz Appliction".PHP_EOL;
    $s .= " */".PHP_EOL;
    $s .= "#########################################################################################".PHP_EOL;
    $s .= PHP_EOL;

    $s .= 'namespace '.ltrim($this->namespace,'\\').';'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= "class ".$this->table->getClassName()."Test extends \\PHPUnit\\Framework\\TestCase".PHP_EOL;
    $s .= "{".PHP_EOL;

    # Properties
    $s .= '  protected $app;';
    $s .= PHP_EOL;
    $s .= PHP_EOL;

    # Setup
    $s .= ' /**'.PHP_EOL;
    $s .= '   * Setup Test'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function setup()'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $this->app = new \Nofuzz\Application( realpath(__DIR__) );'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    # Test Method
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Test method for Entity'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function test'.$this->table->getClassName().'()'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $entity = new '.$this->namespace.'\\'.$this->table->getClassName().'Entity();'.PHP_EOL;
    $s .= '    $entity = $entity->fromArray($entity->asArray());'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    $this->assertTrue(!is_null($entity));'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '} // EOC'.PHP_EOL;

    return $s;
  }

} // EOC

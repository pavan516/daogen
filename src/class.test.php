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
  protected $package;

  /**
   * Constructor
   *
   * @param      [type]  $table    [description]
   * @param      array   $options  [description]
   */
  public function __construct($table=null, array $options=[])
  {
    $this->table = $table;
    $this->options = $options;
    $this->namespace = $this->formatNamespace($options['namespace'] ?? '');
    $this->package = $options['package'] ?? '[Package]';
  }

  /**
   * Formats the Namespace correctly
   *
   * Adds a "\" in front of the namespace if given, empty otherwise
   *
   * @param      string  $namespace  The namespace
   *
   * @return     string
   */
  protected function formatNamespace(string $namespace)
  {
    if (!empty($namespace)) {
      $namespace = '\\' . trim($namespace,'\\/');
    }

    return $namespace;
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
    $s .= ' * @since    '.(new \DateTime('now',new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z').PHP_EOL;
    $s .= " * @package  ".$this->package.PHP_EOL;
    $s .= " */".PHP_EOL;
    $s .= "#########################################################################################".PHP_EOL;
    $s .= PHP_EOL;

    $s .= 'namespace App\\Tests'.$this->namespace.';'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= 'use \\PHPUnit\\Framework\\TestCase;'.PHP_EOL;
    $s .= 'use \\App\\Models'.$this->namespace.'\\'.$this->table->getClassName().'Entity;'.PHP_EOL;
    $s .= 'use \\App\\Models'.$this->namespace.'\\Db\\'.$this->table->getClassName().'Dao;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= "class ".$this->table->getClassName()."EntityTest extends TestCase".PHP_EOL;
    $s .= "{".PHP_EOL;

    # Properties
    $s .= '  protected $app;';
    $s .= PHP_EOL;
    $s .= PHP_EOL;

    # Setup
    $s .= ' /**'.PHP_EOL;
    $s .= '   * Setup Test'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function setUp(): void'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    global $app;'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    if (!isset($app)) {'.PHP_EOL;
    $s .= '      $this->app = new \\Spin\\Application( realpath(__DIR__) );'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    # Test Method
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Test method for '.$this->table->getClassName().'Entity'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function test'.$this->table->getClassName().'Entity()'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $entity = new '.$this->table->getClassName().'Entity();'.PHP_EOL;
    $s .= '    $entity = $entity->fromArray($entity->asArray());'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    $this->assertTrue(!is_null($entity));'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '}'.PHP_EOL;
    $s .= PHP_EOL;

    return $s;
  }

} // EOC

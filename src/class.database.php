<?php
/**
 * Database Entity & Dao class generator
 *
 * Input
 *   POST body or $ddl variable in source
 *
 * Output:
 *   HTML page with 3 <textarea> tags
 *
 */
##############################################################################################################

require_once 'class.table.php';

/**
 * Represents a whole Database
 */
class Database
{
  protected $name;
  protected $type;
  protected $tables = null;

  public function __construct(string $name, string $ddl='', array $options=[])
  {
    $ddl = urldecode($ddl);

    $this->name = $name;
    $this->type = 'n/a';

    if ((stripos($ddl, 'INNODB') !== false) || (stripos($ddl, 'MYISAM') !== false)) {
      $this->type = 'MySql';
    } else
    if (stripos($ddl, 'SET TERM ^^ ;') !== false) {
      $this->type = 'Firebird';
    }

    set_time_limit(2);

    # Loop all Table Definitions
    while ($ddl = stristr($ddl, 'CREATE TABLE'))
    {
      # Extract the Tables DDL
      if ( ($table_ddl = stristr($ddl, ';', true)) === false) {
        $table_ddl = $ddl;
      }

      # Add a table
      $this->tables[] = new \Table($this, $table_ddl, $options);
      # Remove the just processed table
      $ddl = substr($ddl, mb_strlen($table_ddl));
    }
  }

  public function getName()
  {
    return $this->name;
  }

  public function getTables()
  {
    return $this->tables;
  }
}

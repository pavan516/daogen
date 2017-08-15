<?php
/**
 * Database Entity & Dao class generator
 *
 * @package    DaoGen   
 */
##############################################################################################################

require_once 'class.field.php';
require_once 'class.inflect.php';

/**
 * Represents a Table in a Database
 */
class Table
{
  protected $database = null;
  protected $ddl = '';
  protected $tableName = '';
  protected $className = '';
  protected $fields = null;

  /**
   * Constructor
   * 
   * @param [type] $database [description]
   * @param string $ddl      [description]
   * @param array  $options  [description]
   */
  public function __construct($database, string $ddl='', array $options=[])
  {
    $this->database = $database;

    # Store the Tables full name
    preg_match('/CREATE TABLE (\w*)/',trim($ddl),$match);
    $this->tableName = strtolower($match[1] ?? '');

    # Convert _ to Spaces, UCWords it and remove the spaces
    $this->className = str_replace(' ','',ucwords(str_replace('_',' ',$this->tableName)));

    # Make Plural words Singular
    $inflect = new Inflect();
    $this->className = $inflect->singularize($this->className);

    # Extract fields
    $lines = explode("\n", $ddl);
    array_shift($lines); // remove the CREATE TABLE line

    foreach ($lines as $line) {
      $count=1;
      while($count>0) {
        $line = str_replace('  ',' ',trim($line),$count);
      }
      if (trim($line)==='(') continue;
      if (substr(trim($line),0,1)===')') break;
      if (substr(trim($line),0,11)==='PRIMARY KEY') break;
      if (substr(trim($line),0,11)==='CONSTRAINT ') break;
      // if ( (trim($line)===')') || (trim($line)==='') || (strtoupper(trim($line))==='PRIMARY KEY (') ) break;
      $line_fields = explode(" ",rtrim(trim($line),','));
      if (!isset($line_fields[1])) {
        $line_fields[1] = '';
      }

      $this->fields[] = new \Field( implode(' ',$line_fields), $options );
    }
  }

  /**
   * Get Table Name
   * 
   * @return string
   */
  public function getTableName()
  {
    return $this->tableName;
  }

  /**
   * Get class Name
   * 
   * @return string
   */
  public function getClassName()
  {
    return $this->className;
  }

  /**
   * Get all fields
   * 
   * @return array
   */
  public function getFields()
  {
    return $this->fields;
  }

  /**
   * Check if table has a field by the name
   * 
   * @param  string  $fieldName [description]
   * @return boolean
   */
  public function hasField(string $fieldName)
  {
    $hasField = false;
    foreach ($this->getFields() as $field) {
      if (strcasecmp($fieldName,$field->getName())==0) {
        $hasField = true;
        break;
      }
    }

    return $hasField;
  }

}

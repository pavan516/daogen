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

require_once 'class.field.php';

/**
 * Represents a Field in a Table
 */
class Field
{
  protected $fieldDef;
  protected $name;
  protected $type;
  protected $length;
  protected $default;
  protected $notNull = false;

  public function __construct(string $fieldDef='', array $options=[])
  {
    # Remove double spaces
    while (strpos($fieldDef, '  ') !== false) {
      $fieldDef = str_replace('  ',' ',$fieldDef);
    }

    # Small fix for Firebird '( xx)' length fields
    while (strpos($fieldDef, '( ') !== false) {
      $fieldDef = str_replace('( ','(',$fieldDef);
    }

    $this->fieldDef = trim($fieldDef);

    # Explode the parts in the line
    $parts = explode(' ',$fieldDef);

    # Set Name
    $this->name = strtolower($parts[0] ?? '');
    $this->name = str_replace('"','',$this->name); // Remove enclosing "" chars
    $this->name = str_replace('`','',$this->name); // Remove enclosing `` chars

    # Set type
    $this->type = $parts[1] ?? '';

    # Check for Length (if present)
    if (strpos($this->type, '(') !== false) {
      preg_match('/\((.*)\)/',trim($this->type),$match);
      $this->length = $match[1] ?? '';
      preg_match('/(.*)\(/',trim($this->type),$match);
      $this->type = $match[1] ?? '';
    }

    # Extract default
    if (strpos($fieldDef, 'DEFAULT') !== false) {
      $s = stristr($fieldDef, 'DEFAULT');
      if (strpos($fieldDef, 'ON ') !== false)
          $s = stristr($s, 'ON ', true);
      preg_match('/DEFAULT (.*)/',trim($s),$match);
      $this->default = $match[1] ?? '';
    }

    # Check for NOT NULL
    if (strpos($fieldDef, 'NOT NULL') !== false) {
      $this->notNull = true;
    }

  }

  public function getFieldDef()
  {
    return $this->fieldDef;
  }

  public function getName()
  {
    return trim($this->name,'`');
  }

  public function getUcName()
  {
    return ucwords($this->getName());
  }

  public function GetUcwName()
  {
    $s = trim($this->getName(),'`');
    return str_replace(' ','',ucwords(str_replace('_',' ',$s)));
  }

  public function getType()
  {
    return $this->type;
  }

  public function isInt()
  {
    switch (strtoupper($this->getType())) {
      case 'TINYINT':
      case 'SMALLINT':
      case 'INTEGER':
      case 'BIGINT':
      case 'INT':
        return true;
        break;
    }

    return false;
  }

  public function isNumeric()
  {
    switch (strtoupper($this->getType())) {
      case 'NUMERIC':
      case 'DECIMAL':
      case 'MONEY':
      case 'DEC':
      case 'FIXED':
      case 'FLOAT':
      case 'DOUBLE':
      case 'REAL':
        return true;
        break;
    }

    return false;
  }

  public function isText()
  {
    switch (strtoupper($this->getType())) {
      case 'NVARCHAR':
      case 'VARCHAR':
      case 'NCHAR':
      case 'CHAR':
      case 'TINYBLOB':
      case 'BLOB':
      case 'MEDIUMBLOB':
      case 'LONGBLOB':
      case 'TINYTEXT':
      case 'TEXT':
      case 'MEDIUMTEXT':
      case 'LONGTEXT':
      case 'MULTILINETEXT':
        return true;
        break;
    }

    return false;
  }
  public function isDateTime()
  {
    switch (strtoupper($this->getType())) {
      case 'TIMESTAMP':
      case 'DATETIME':
        return true;
        break;
    }

    return false;
  }

  public function getLength()
  {
    return $this->length;
  }

  public function getDefault(string $language='json')
  {
    $s = 'null';

    if (strcasecmp($language,'json')==0) {
      switch ( substr(strtoupper($this->getType()),0,8) ) {
        case 'NVARCHAR':
        case 'VARCHAR':
        case 'TEXT':
        case 'MEDIUMTE':
        case 'LONGTEXT':
        case 'BLOB':
          $s = '""';
          break;
        case 'BIGINT':
        case 'SMALLINT':
        case 'INT':
        case 'INTEGER':
        case 'NUMERIC':
        case 'DECIMAL':
        case 'FLOAT':
          $s = '0';
          break;
        case 'DATE':
          $s = '"1970-01-01"';
          break;
        case 'TIME':
          $s = '"00:00:00"';
          break;
        case 'TIMESTAM':
        case 'DATETIME':
          $s = '"1970-01-01T00:00:00Z"';
          break;
        default:
          $s = '""';
          break;
      }
    }
    if (strcasecmp($language,'php')==0) {

      if ( strlen($this->default) == 0 ) {

        return 'null';
      } else
      if ( strcasecmp($this->default,'CURRENT_TIMESTAMP') == 0 ) {

        return ' (new \DateTime(\'@\'.time() ))->format(\'Y-m-d\TH:i:s\Z\') ';
      } else {

        return $this->default;
      }
    }

    return $s;
  }

  public function getNotNull()
  {
    return $this->notNull;
  }
}

<?php
/**
 * Represents an Entity (Database Class representing a row)
 */
##############################################################################################################

class Entity
{
  protected $table;
  protected $type;
  protected $options;
  protected $namespace;

  public function __construct($table=null, array $options=[])
  {
    $this->table = $table;
    $this->options = $options;
    $this->namespace = $options['namespace'] ?? '\\App\\Db';
  }


  /**
   * Output table as a PHP Source
   *
   * @return string
   */
  public function getPhpSource()
  {
    global $daoGenVersion;

    $s  = '';
    $s .= '<?php '.PHP_EOL;

    # DocBlock
    $s .= '/** '.PHP_EOL;
    $s .= ' * '.$this->table->getClassName().'.php'.PHP_EOL;
    $s .= ' *'.PHP_EOL;
    $s .= ' *    Entity for table '.$this->table->getTableName().PHP_EOL;
    $s .= ' *'.PHP_EOL;
    $s .= ' *  Generated with DaoGen v'.$daoGenVersion.PHP_EOL;
    $s .= ' *'.PHP_EOL;
    $s .= ' * @since    '.(new \DateTime('now',new \DateTimeZone('UTC')))->format('Y-m-d H:i:s').PHP_EOL;
    $s .= ' * @package  Nofuzz Appliction'.PHP_EOL;
    $s .= ' */'.PHP_EOL;
    $s .= '#########################################################################################'.PHP_EOL;

    # Generate JSON Model for the table
    $s .= "/*".PHP_EOL;
    $s .= "JSON Model:".PHP_EOL;
    $s .= "{".PHP_EOL;
    $str = '';
    foreach ($this->table->getFields() as $field) {
      $str .= '  "'.$field->getName().'": '.$field->getDefault('json').','.PHP_EOL;
    }
    $s .= rtrim($str,','.PHP_EOL).PHP_EOL;
    $s .= "}".PHP_EOL;
    $s .= "*/".PHP_EOL;
    $s .= "#########################################################################################".PHP_EOL;
    $s .= PHP_EOL;

    $s .= 'namespace '.ltrim($this->namespace,'\\').';'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '/** '.PHP_EOL;
    $s .= ' * Class representing rows in table "'.$this->table->getTableName().'"'.PHP_EOL;
    $s .= ' */'.PHP_EOL;
    $s .= 'class '.$this->table->getClassName().'Entity extends '.$this->namespace.'\\AbstractBaseEntity'.PHP_EOL;
    $s .= '{'.PHP_EOL;

    # Fields
    foreach ($this->table->getFields() as $field) {
      $s .= mb_str_pad('  protected $'.$field->getName().';',48,' ',STR_PAD_RIGHT).'// '.$field->getFieldDef().PHP_EOL;
    }
    $s .= PHP_EOL;

    # Clear
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Clear properties to default values'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return   self'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function clear()'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    foreach ($this->table->getFields() as $field) {
      $s .= '    $this->set'.$field->getUcwName().'('.$field->getDefault('php').');'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    return $this;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    # As array
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Return object as array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return   array'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function asArray(array $removedKeys=[]): array'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    foreach ($this->table->getFields() as $field) {
      $s .= '    $result[\''.$field->getName().'\'] = $this->get'.$field->getUcwName().'();'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    # Unset unwanted keys'.PHP_EOL;
    $s .= '    foreach ($removedKeys as $key) {'.PHP_EOL;
    $s .= '      unset($result[$key]);'.PHP_EOL;
    $s .= '    }'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    return $result;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    # from array
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Set properties from array'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return   self'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function fromArray(array $a)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    foreach ($this->table->getFields() as $field) {
      $s .= '    $this->set'.$field->getUcwName().'($a[\''.$field->getName().'\'] ?? '.$field->getDefault('php').');'.PHP_EOL;
    }
    $s .= PHP_EOL;
    $s .= '    return $this;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .= PHP_EOL;

    # Getters & Setters
    foreach ($this->table->getFields() as $field)
    {
      # Getter
      $s .= '  public function get'.$field->getUcwName().'()'.PHP_EOL;
      $s .= '  {'.PHP_EOL;
      if ($field->isInt()) {
        $s .= '    if (!is_null($this->'.$field->getName().')) return (int) $this->'.$field->getName().';'.PHP_EOL;
      }
      if ($field->isNumeric()) {
        $s .= '    if (!is_null($this->'.$field->getName().')) return (float) $this->'.$field->getName().';'.PHP_EOL;
      }
      $s .= PHP_EOL;
      $s .= '    return $this->'.$field->getName().';'.PHP_EOL;
      $s .= '  }'.PHP_EOL;
      $s .= PHP_EOL;

      # Setter
      $s .= '  public function set'.$field->getUcwName().'($'.$field->getName().')'.PHP_EOL;
      $s .= '  {'.PHP_EOL;
      // if ($field->isInt()) {
      //   $s .= '    $this->'.$field->getName().' = (int) $'.$field->getName().';'.PHP_EOL;
      // } else
      // if ($field->isNumeric()) {
      //   $s .= '    $this->'.$field->getName().' = (float) $'.$field->getName().';'.PHP_EOL;
      // } else
      if ($field->isDateTime()) {
        $s .= '    if (strcasecmp($'.$field->getName().',\'0000-00-00 00:00:00\')==0) $'.$field->getName().' = null;'.PHP_EOL;
        $s .= PHP_EOL;
        $s .= '    $this->'.$field->getName().' = $'.$field->getName().';'.PHP_EOL;
        $s .= PHP_EOL;
        $s .= '    if (!is_null($'.$field->getName().')) {'.PHP_EOL;
        $s .= '      $d = new \DateTime($'.$field->getName().');'.PHP_EOL;
        $s .= '      $d->setTimeZone(new \DateTimeZone("UTC"));'.PHP_EOL;
        $s .= '      $this->'.$field->getName().' = $d->format("Y-m-d H:i:s");'.PHP_EOL;
        $s .= '    }'.PHP_EOL;
      } else {
        $s .= '    $this->'.$field->getName().' = $'.$field->getName().';'.PHP_EOL;
      }
      $s .= PHP_EOL;
      $s .= '    return $this;'.PHP_EOL;
      $s .= '  }'.PHP_EOL;
      $s .= PHP_EOL;
    }
    $s .= '} // EOC'.PHP_EOL;
    $s .= PHP_EOL;

    return $s;
  }

}

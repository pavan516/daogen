<?php
/**
 * Generates a Dao class for the Table
 *
 * LIMIT keyword detects the following DB drivers (on the fly)
 *   - MySql (uses LIMIT, works in MySql v5.7.x and newer)
 *   - Firebird (uses ROWS, works in Firebird v2.5 or newer)
 *
 * Crapacle is not supported since it's stupid un-standard way using
 *   "OFFSET x ROWS FETCH NEXT y ROWS ONLY;"  -- https://docs.oracle.com/database/121/SQLRF/statements_10002.htm#SQLRF01702
 *
 */
##############################################################################################################

class Dao
{
  protected $table;
  protected $type;
  protected $options;
  protected $namespace;

  public function __construct($table=null, array $options=[])
  {
    $this->table = $table;
    $this->options = $options;
    $this->namespace = $options['namespace'] ?? '\\App\\Models';
  }

  /**
   * Output table as a PHP Source, Dao Class format
   *
   * @return string
   */
  public function getPhpSource()
  {
    global $daoGenVersion;

    $s  = '';
    $s .= "<?php ".PHP_EOL;

    # DocBlock
    $s .= '/** '.PHP_EOL;
    $s .= ' * '.$this->table->getClassName().'Dao.php'.PHP_EOL;
    $s .= ' *'.PHP_EOL;
    $s .= ' *    Dao class for table '.$this->table->getTableName().PHP_EOL;
    $s .= ' *'.PHP_EOL;
    $s .= ' *  Generated with DaoGen v'.$daoGenVersion.PHP_EOL;
    $s .= ' *'.PHP_EOL;
    $s .= ' * @since    '.(new \DateTime('now',new \DateTimeZone('UTC')))->format('Y-m-d H:i:s').PHP_EOL;
    $s .= ' * @package  App\\Db'.PHP_EOL;
    $s .= ' */'.PHP_EOL;
    $s .= '#########################################################################################'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= 'namespace '.ltrim($this->namespace,'\\').'\\Db;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= 'use \\'.ltrim($this->namespace,'\\').'\\AbstractBaseEntity;'.PHP_EOL;
    $s .= 'use \\'.ltrim($this->namespace,'\\').'\\Db\\AbstractBaseDao;'.PHP_EOL;
    $s .= 'use \\'.ltrim($this->namespace,'\\').'\\'.$this->table->getClassName().'Entity;'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '/** '.PHP_EOL;
    $s .= ' * Dao class for rows in table "'.$this->table->getTableName().'"'.PHP_EOL;
    $s .= ' */'.PHP_EOL;
    $s .= 'class '.$this->table->getClassName().'Dao extends AbstractBaseDao'.PHP_EOL;
    $s .= '{'.PHP_EOL;

    ## Constructor
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Constructor'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param string  $connectionname    Database ConnectionName'.PHP_EOL;
    $s .= '   * @param int     $cacheTTL          Seconds to Cache the entries. -1=off'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function __construct(string $connectionName=\'\', int $cacheTTL=-1)'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    parent::__construct($connectionName);'.PHP_EOL;
    $s .= '    $this->setTable(\''.$this->table->getTableName().'\');'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    /**'.PHP_EOL;
    $s .= '     * Set Cache TTL for items in Seconds'.PHP_EOL;
    $s .= '     *'.PHP_EOL;
    $s .= '     * -1 = Do not cache'.PHP_EOL;
    $s .= '     *  0 = Never expire'.PHP_EOL;
    $s .= '     *  n = Cache items for n seconds'.PHP_EOL;
    $s .= '     */'.PHP_EOL;
    $s .= '    $this->setCacheTTL($cacheTTL);'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .=  PHP_EOL;

    ## MakeEntity
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Make/Generate an Entity'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param  array  $fields             Array with key=value for fields'.PHP_EOL;
    $s .= '   * @return object'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  function makeEntity(array $fields=[]): AbstractBaseEntity'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $item = new '.$this->table->getclassName().'Entity(array_change_key_case($fields),CASE_LOWER);'.PHP_EOL;
    $s .= '    $this->cacheSetItem($item);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    return $item;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .=  PHP_EOL;

    ## fetchAll()
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Fetch all records in table'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @return array'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function fetchAll(): array'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    if ($items = $this->cacheGetAll()) return $items;'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    $items = '.PHP_EOL;
    $s .= '      $this->fetchCustom('.PHP_EOL;
    $s .= '        \'SELECT * FROM {table}\''.PHP_EOL;
    $s .= '      );'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    if ($items) $this->cacheSetAll($items);'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    return $items;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .=  PHP_EOL;

    ## fetchByKeywords()
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Fetch records by Keywords'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param  array $keywords            Array with keyword = value'.PHP_EOL;
    $s .= '   * @return array'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function fetchByKeywords(array $keywords=[]): array'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $where = \'\';'.PHP_EOL;
    $s .= '    $order = \'\';'.PHP_EOL;
    $s .= '    $limit = \'\';'.PHP_EOL;
    $s .= '    $binds = [];'.PHP_EOL;
    $s .= PHP_EOL;

    # Each FIELD in the table is added. Based on Type we generate different
    # conditions
    foreach ($this->table->getFields() as $field)
    {
      $s .= '    if (isset($keywords[\''.$field->getName().'\']) && strlen($keywords[\''.$field->getName().'\'])>0) {'.PHP_EOL;
      if ($field->isText()) {
        $s .= '      $where .= \'AND ('.$field->getName().' LIKE :'.strtoupper($field->getName()).') \';'.PHP_EOL;
      } else {
        $s .= '      $where .= \'AND ('.$field->getName().' = :'.strtoupper($field->getName()).') \';'.PHP_EOL;
      }
      $s .= '      $binds[\':'.strtoupper($field->getName()).'\'] = $keywords[\''.$field->getName().'\'];'.PHP_EOL;
      $s .= '    }'.PHP_EOL;
      $s .= PHP_EOL;
    }

    $s .= '    if (!empty($where))'.PHP_EOL;
    $s .= '      $where = \'WHERE \'.preg_replace(\'/^AND /\', \'\', $where);'.PHP_EOL;
    $s .= PHP_EOL;
    $s .= '    if (!empty($keywords[\'order\'])) // Note here that we use the $keyword[\'order\'] directly in SQL string.'.PHP_EOL;
    $s .= '      $order = \' ORDER BY \'.$keywords[\'order\'];'.PHP_EOL;
    $s .= PHP_EOL;

    $s .= '    if (!empty($keywords[\'limit\'])) { // Note here that we use the $keyword[\'limit\'] directly in SQL string.'.PHP_EOL;
    $s .= '      if (strcasecmp(\'mysql\',$this->getConnection()->getDriver())==0) {'.PHP_EOL;
    $s .= '        $limit = \' LIMIT \'.$keywords[\'limit\'];'.PHP_EOL;
    $s .= '      } else '.PHP_EOL;
    $s .= '      if (strcasecmp(\'firebird\',$this->getConnection()->getDriver())==0) {'.PHP_EOL;
    $s .= '        $limit = \' ROWS \'.$keywords[\'limit\'];'.PHP_EOL;
    $s .= '      }'.PHP_EOL;
    $s .= '    }'.PHP_EOL;

    $s .= PHP_EOL;
    $s .= '    return'.PHP_EOL;
    $s .= '      $this->fetchCustom('.PHP_EOL;
    $s .= '        \'SELECT * FROM {table} \'.$where.$order.$limit,'.PHP_EOL;
    $s .= '        $binds'.PHP_EOL;
    $s .= '      );'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .=  PHP_EOL;

    ## insert()
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Insert $item into database'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param  AbstractBaseEntity $item      The item we are inserting'.PHP_EOL;
    $s .= '   * @return bool'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function insert(AbstractBaseEntity &$item): bool'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $id ='.PHP_EOL;
    $s .= '      $this->execCustomGetLastId('.PHP_EOL;
    $s .= '        \'INSERT INTO {table} \'.'.PHP_EOL;
    $ss = '';
    foreach ($this->table->getFields() as $field) {
      $ss .= ' '.$field->getName().',';
    }
    $s .= '        \'('.rtrim($ss,',').') \'. '.PHP_EOL;
    $s .= '        \'VALUES \'.'.PHP_EOL;
    $ss = '';
    foreach ($this->table->getFields() as $field) {
      if ($field->isDateTime()) {
        $ss .= "STR_TO_DATE(:".strtoupper($field->getName()).",\\'%Y-%m-%dT%H:%i:%sZ\\'),";
      } else {
        $ss .= ':'.strtoupper($field->getName()).',';
      }
    }
    $s .= '        \'('.rtrim($ss,',').')\', '.PHP_EOL;
    $s .= '        ['.PHP_EOL;
    $ss = '';
    foreach ($this->table->getFields() as $field) {
      $ss .= '          \':'.strtoupper($field->getName()).'\' => $item->get'.$field->getUcwName().'(),'.PHP_EOL;
    }
    $s .=  rtrim($ss,','.PHP_EOL).PHP_EOL;
    $s .= '        ]'.PHP_EOL;
    $s .= '      );'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    $item->setId($id);'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    # Cache the item'.PHP_EOL;
    $s .= '    $this->cacheSetItem($item);'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    # Clear the fetchAll() cache'.PHP_EOL;
    $s .= '    $this->cacheClearAll();'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    return ($id !=0);'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .=  PHP_EOL;

    ## update()
    $s .= '  /**'.PHP_EOL;
    $s .= '   * Update $item in database'.PHP_EOL;
    $s .= '   *'.PHP_EOL;
    $s .= '   * @param  AbstractBaseEntity $item      The item we are updating'.PHP_EOL;
    $s .= '   * @return bool'.PHP_EOL;
    $s .= '   */'.PHP_EOL;
    $s .= '  public function update(AbstractBaseEntity $item): bool'.PHP_EOL;
    $s .= '  {'.PHP_EOL;
    $s .= '    $ok = '.PHP_EOL;
    $s .= '      $this->execCustom('.PHP_EOL;
    $s .= '        \'UPDATE {table} SET \'.'.PHP_EOL;
    $ss = '';
    foreach ($this->table->getFields() as $field) {
      if (strcasecmp($field->getName(),'id')==0) continue;
      $ss .= '        \' '.$field->getName().' = '; // :'.strtoupper($field->getName()).', \'. '.PHP_EOL;
      # Datetime fields special handling
      if ($field->isDateTime()) {
        $ss .= "STR_TO_DATE(:".strtoupper($field->getName()).",\\'%Y-%m-%dT%H:%i:%sZ\\'), '.".PHP_EOL;
      } else {
        $ss .= ':'.strtoupper($field->getName()).', \'.'.PHP_EOL;
      }
    }
    $s .=  rtrim($ss,", '. ".PHP_EOL).' \'. '.PHP_EOL;
    $s .= '        \'WHERE \'.'.PHP_EOL;
    $s .= '        \' id = :ID \','.PHP_EOL;
    // $ss = '';
    // foreach ($this->table->getFields() as $field) {
    //   if ($field->isDateTime()) {
    //     $ss .= "STR_TO_DATE(:".$field->getName().",\\'%Y-%m-%dT%H:%i:%sZ\\'),";
    //   } else {
    //     $ss .= ':'.$field->getName().',';
    //   }
    // }
    $s .= '        ['.PHP_EOL;
    $s .= '';
    $ss = '';
    foreach ($this->table->getFields() as $field) {
      if (strcasecmp($field->getName(),'id')==0) continue;
      $ss .= '          \':'.strtoupper($field->getName()).'\' => $item->get'.$field->getUcwName().'(),'.PHP_EOL;
    }
    $s .=  $ss;
    $s .= '          \':ID\' => $item->getId()'.PHP_EOL;
    $s .= '        ]'.PHP_EOL;
    $s .= '      );'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    if ($ok) $this->cacheSetItem($item);'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    # Clear the fetchAll() cache'.PHP_EOL;
    $s .= '    $this->cacheClearAll();'.PHP_EOL;
    $s .=  PHP_EOL;
    $s .= '    return $ok;'.PHP_EOL;
    $s .= '  }'.PHP_EOL;
    $s .=  PHP_EOL;

    $s .= '} // EOC'.PHP_EOL;

    $s .= PHP_EOL;
    return $s;
  }

}

<?php
/**
 * Database Entity & Dao class generator
 *
 * Input
 *   POST body or $ddl variable in source
 *
*/

##############################################################################################################

$daoGenVersion = '0.5.6';

require_once 'class.database.php';
require_once 'class.entity.php';
require_once 'class.dao.php';
require_once 'class.controller.php';
require_once 'class.test.php';

#####################################################################################################

/**
 * str_pad for multi-byte strings
 * 
 * @param  [type] $str      [description]
 * @param  [type] $pad_len  [description]
 * @param  string $pad_str  [description]
 * @param  [type] $dir      [description]
 * @param  [type] $encoding [description]
 * @return string
 */
function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL)
{
    $encoding = $encoding === NULL ? mb_internal_encoding() : $encoding;
    $padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
    $padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
    $pad_len -= mb_strlen($str, $encoding);
    $targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
    $strToRepeatLen = mb_strlen($pad_str, $encoding);
    $repeatTimes = ceil($targetLen / $strToRepeatLen);
    $repeatedString = str_repeat($pad_str, max(0, $repeatTimes)); // safe if used with valid utf-8 strings
    $before = $padBefore ? mb_substr($repeatedString, 0, floor($targetLen), $encoding) : '';
    $after = $padAfter ? mb_substr($repeatedString, 0, ceil($targetLen), $encoding) : '';

    return $before . $str . $after;
}

#####################################################################################################

  # Output format (depricated)
  $format = strtolower($_GET['format'] ?? 'html');
  if (empty($format)) $format = 'html';

  # The DDL as POST
  $ddl = file_get_contents('php://input');
  ($ddl) ?? $ddl = $_POST["ddl"];

  # Get the Namespace (GET, POST order)
  $namespace = $_GET['namespace'] ?? $_POST['namespace'] ?? '';
  if (empty($namespace)) {
    $namespace = '\\App\\Db';
  }

  # Show HTML form if no DDL sent via POST
  if (empty($ddl)) {
      header('Content-Type: text/html');

      echo '<!DOCTYPE html>';
      echo '<html>';
      echo '<head>';
      echo  '<title>Entity & Dao class generator</title>';
      echo '</head>';
      echo '<body>';
      echo '<h3>Database or Table DDL (MySql & Firebird accepted)<h3>';
      echo '<form method="post" action="DaoGen.php">';
      echo 'Namespace <input type="text" name="namespace" value="'.$namespace.'"></br>';
      echo '<textarea name="ddl" id="ddl" rows="30" cols="160" placeholder="Put DDL here">'.$ddl.'</textarea>';
      echo '<br/><input type="submit" value="Submit">';
      echo '</form>';
      echo '</body></html>';

      die;
  }

#####################################################################################################

  #
  # Create the files
  #

  # 
  $database = new \Database( 'Unknown', $ddl, ['namespace'=>$namespace] );

  echo 'DaoGen v'.$daoGenVersion.PHP_EOL;
  echo PHP_EOL;
  echo 'Generating files from '.$database->getName().'. '.count($database->getTables()).' tables'.PHP_EOL;

  $t1 = microtime(true);
  
  header('Content-Type: text/plain');

  # Make dirs
  if (!file_exists('output')) mkdir('output');
  if (!file_exists('output/App')) mkdir('output/App');
  if (!file_exists('output/App/Db')) mkdir('output/App/Db');
  if (!file_exists('output/Controllers')) mkdir('output/Controllers');
  if (!file_exists('output/Tests')) mkdir('output/Tests');

  if (count($database->getTables())>0) {
    # For each table ...
    foreach ($database->getTables() as $table)
    {
      echo 'Table '.$table->getTableName().PHP_EOL;

      # Generate Entity files
      $entity = new \Entity($table, ['namespace'=>$namespace]);
      $filename = $table->getClassName().'Entity.php';
      echo ' > Entity:     '.$filename.PHP_EOL;
      $source = $entity->getPhpSource();
      file_put_contents('Output/App/Db/'.$filename, $source );

      # Generate DAO files
      $dao = new \Dao($table, ['namespace'=>$namespace]);
      $filenameDao = $table->getClassName().'Dao.php';
      echo ' > Dao:        '.$filenameDao.PHP_EOL;
      $source = $dao->getPhpSource();
      file_put_contents('Output/App/Db/'.$filenameDao, $source );

      # Generate Conrollers
      $controller = new \Controller($table);
      $filenameController = $table->getClassName().'Controller.php';
      echo ' > Controller: '.$filenameController.PHP_EOL;
      $source = $controller->getPhpSource();
      file_put_contents('Output/Controllers/'.$filenameController, $source );

      # Generate tests
      $test = new \Test($table);
      $filenameTest = $table->getClassName().'EntityTest.php';
      echo ' > Test:       '.$filenameTest.PHP_EOL;
      $source = $test->getPhpSource();
      file_put_contents('Output/Tests/'.$filenameTest, $source );
    }

    # Copy AbstractBase* files to output
    copy ('AbstractBaseDao.php','Output/App/Db/AbstractBaseDao.php');
    copy ('AbstractBaseEntity.php','Output/App/Db/AbstractBaseEntity.php');
  }

  $t2 = microtime(true);
  $dur = $t2-$t1;

  # Estimated work neede to produce the same
  $hours = count($database->getTables()) * 5;        // 5 hours/table
  $days  = $hours/6;                                 // 6h effective workdays

  echo PHP_EOL;

  echo 'Operation took '.number_format($dur,3,'.','').' seconds'.PHP_EOL;
  echo 'Estimated saving '.$hours.' man-hours ('.number_format($days,1,'.','').' man-days)'.PHP_EOL;
  echo '> This was done ~'.number_format($hours*60*60 / $dur,3,'.','').' times faster than manually coding it'.PHP_EOL;

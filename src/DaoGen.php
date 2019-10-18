<?php  declare(strict_types=1);

/**
 * Database Entity & Dao class generator
 *
 * Input
 *   POST body or $ddl variable in source
 *
 * @package    DaoGen
*/

##############################################################################################################

$daoGenVersion = '0.5.17';

require_once 'includes/includes.php';
require_once 'class.database.php';
require_once 'class.entity.php';
require_once 'class.dao.php';
require_once 'class.controller.php';
require_once 'class.test.php';

#####################################################################################################

  # Output format (depricated)
  $format = strtolower($_GET['format'] ?? 'html');
  if (empty($format)) $format = 'html';

  # The DDL as POST
  $ddl = file_get_contents('php://input');
  ($ddl) ?? $ddl = $_POST["ddl"];

  # Get the Namespace (GET, POST order)
  #   This is used to prefix all MODEL & ENTITY names.
  #   Controllers : "\App\Controllers\v1\" . $namespace . "\"
  #   Models      : "\App\Models\" . $namespace . "\"
  #   DB          : "\App\Models\" . $namespace . "\Db"
  #   Tests       : "\App\Tests\" . $namespace . "\"
  $namespace = $_GET['namespace'] ?? $_POST['namespace'] ?? 'rest\\v1';
  $namespace = trim($namespace,'\\/'); // Remove leading+trailing \ and /

  # Package name
  $package = $_GET['package'] ?? $_POST['package'] ?? '<application>';
  $controllerExt = $_GET['controllerExt'] ?? $_POST['controllerExt'] ?? 'AbstractRestController';

  # Show HTML form if no DDL sent via POST
  if (empty($ddl)) {
      header('Content-Type: text/html');

      echo '<!DOCTYPE html>';
      echo '<html>';
      echo '<head>';
      echo  '<title>Entity & Dao class generator</title>';
      echo '</head>'.PHP_EOL.PHP_EOL;
      echo '<body>'.PHP_EOL;
      echo '<h1>DaoGen for Spin-Framework</h1>'.PHP_EOL;
?>
<h3>Database or Table DDL (MySql & Firebird accepted)</h3>
<p>
 <form method="post" action="DaoGen.php">

  <table>
    <thead>
    </thead>

    <tbody>
      <tr>
        <td><b>Namespace</b></td>
        <td><input type="text" name="namespace" size=32 value="<?php echo $namespace;?>"></td>
      </tr>
      <tr>
        <td><b>Controllers extend</b></td>
        <td><input type="text" name="controllerExt" size=32 value="<?php echo $controllerExt;?>"></td>
      </tr>
      <tr>
        <td><b>Package name</b></td>
        <td><input type="text" name="package" size=32 value="<?php echo $package;?>"></td>
      </tr>      <tr>
        <td><b>DDL</b></td>
        <td><textarea name="ddl" id="ddl" rows="30" cols="160" placeholder="Put DDL here"><?php echo $ddl;?></textarea></td>
      </tr>
    </tbody>
  </table>

  <input type="submit" value="Submit">
 </form>
</p>
</body></html>

<?php
      die;
  }

#####################################################################################################

  #
  # Create the files
  #

  $namespace = ucwords($namespace,'\\/');

  #
  $database = new \Database( 'Unknown', $ddl, ['namespace'=>$namespace] );

  echo 'DaoGen v'.$daoGenVersion.PHP_EOL;
  echo PHP_EOL;
  echo 'Generating files from Database `'.$database->getName().'`, '.count($database->getTables()).' tables'.PHP_EOL;
  echo PHP_EOL;

  $t1 = microtime(true);

  header('Content-Type: text/plain');

  # Make dirs
  @mkdir('output/src/app/Models'.namespaceFilename($namespace).'/Db',0755,true);
  @mkdir('output/src/app/Controllers'.namespaceFilename($namespace),0755,true);
  @mkdir('output/tests'.namespaceFilename($namespace),0755,true);

  if (count($database->getTables())>0) {
    # Options array
    $options['namespace'] = $namespace;
    $options['package'] = $package;
    $options['extends'] = $controllerExt;

    # For each table ...
    foreach ($database->getTables() as $table)
    {
      echo 'Table '.$table->getTableName().PHP_EOL;

      # Generate Entity files
      $entity = new \Entity($table, $options);
      $filename = $table->getClassName().'Entity.php';
      echo ' > Entity:     /src/app/Models'.formatNamespace($namespace).'/'.$filename.PHP_EOL;
      $source = $entity->getPhpSource();
      file_put_contents('Output/src/app/Models'.namespaceFilename($namespace).'/'.$filename, $source );

      # Generate DAO files
      $dao = new \Dao($table, $options);
      $filenameDao = $table->getClassName().'Dao.php';
      echo ' > Dao:        /src/app/Models'.formatNamespace($namespace).'/Db/'.$filenameDao.PHP_EOL;
      $source = $dao->getPhpSource();
      file_put_contents('Output/src/app/Models'.namespaceFilename($namespace).'/Db/'.$filenameDao, $source );

      # Generate Conrollers
      $controller = new \Controller($table, $options);
      $filenameController = $table->getClassName().'Controller.php';
      echo ' > Controller: /src/app/Controllers'.formatNamespace($namespace).'/'.$filenameController.PHP_EOL;
      $source = $controller->getPhpSource();
      file_put_contents('Output/src/app/Controllers'.namespaceFilename($namespace).'/'.$filenameController, $source );

      # Generate Entity tests
      $test = new \Test($table, $options);
      $filenameTest = $table->getClassName().'EntityTest.php';
      echo ' > Test:       /tests'.formatNamespace($namespace).'/'.$filenameTest.PHP_EOL;
      $source = $test->getPhpSource();
      file_put_contents('Output/tests'.namespaceFilename($namespace).'/'.$filenameTest, $source );
    }

    # Copy Abstract* files
    copy ('Abstracts/AbstractBaseDaoInterface.php', 'Output/src/app/Controllers/AbstractBaseDaoInterface.php');
    copy ('Abstracts/AbstractBaseDao.php',          'Output/src/app/Models/AbstractBaseDao.php');
    copy ('Abstracts/AbstractBaseEntity.php',       'Output/src/app/Models/AbstractBaseEntity.php');
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

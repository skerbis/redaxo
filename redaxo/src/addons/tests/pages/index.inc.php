<?php

echo rex_view::title('TestResults');

// load all required PEAR libs from vendor folder
$path = __DIR__. '/../vendor/';
set_include_path($path . PATH_SEPARATOR . get_include_path());

require_once('PHPUnit/Autoload.php');

$filter = PHP_CodeCoverage_Filter::getInstance();
$filter->addFileToBlacklist(__FILE__);
foreach(debug_backtrace(false) as $t)
{
  $filter->addFileToBlacklist($t['file']);
}

$testCollector = new PHPUnit_Runner_IncludePathTestCollector(
  array(__DIR__. '/../lib/tests/*'), array('_test.php', '.phpt')
);

/*
foreach($testCollector->collectTests() as $test){
  var_dump($test->__toString());
  echo '<br>';
}
*/

$suite  = new PHPUnit_Framework_TestSuite();
// disable backup of globals, since we have some rex_sql objectes referenced from variables in global space.
// PDOStatements are not allowed to be serialized
$suite->setBackupGlobals(false);
$suite->addTestFiles($testCollector->collectTests());

rex_logger::unregister();

$result = $suite->run();

$resultPrinter = new PHPUnit_TextUI_ResultPrinter(null, true);
echo '<pre>';
$resultPrinter->printResult($result);
echo '</pre>';

rex_logger::register();

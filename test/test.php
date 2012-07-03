#!/usr/bin/env php
<?php
error_reporting(E_STRICT);
require '../jsmin.php';

$libs = array(
  'dojo',
  'ext',
  'jquery',
  'mootools',
  'yui',
  'utf8-with-bom',
  'd3.v2',
  'd3.partial',
  'd3.partial2'
);

$descriptorspec = array(
  0 => array("pipe", "r"),  // stdin
  1 => array("pipe", "w"),  // stdout
  2 => array("pipe", "w"),  // stderr
);

foreach ($libs as $lib) {
  echo "Testing $lib ";

  // Using proc_open here so we can capture the stderr output separate from the stdout output
  // which allows us to see if jsmin.c is throwing any errors that we should be sure we're
  // throwing as well
  $process = proc_open(__DIR__ . "/jsmin < libs/$lib.js", $descriptorspec, $pipes, dirname(__FILE__), null);
  $jsmin_c = stream_get_contents($pipes[1]);
  $jsmin_err = str_replace('JSMIN Error: ', '', stream_get_contents($pipes[2]));
  fclose($pipes[1]);
  fclose($pipes[2]);

  try {
    $jsmin_php = JSMin::minify(file_get_contents(__DIR__ . "/libs/$lib.js"));

    if ($jsmin_err != '') {
      echo "[FAIL]\n";
      echo "==> jsmin.c threw an error but jsmin.php did not. Error: $jsmin_err\n";
    } else {
      if ($jsmin_c === $jsmin_php) {
        echo "[PASS]\n";
      } else {
        echo "[FAIL]\n";
        echo "==> Output differs between jsmin.c and jsmin.php.\n";
      }
    }
  } catch (Exception $e) {
    if ($jsmin_err != '' && trim($jsmin_err) == trim($e->getMessage())) {
      echo "[PASS]\n";
    } else {
      echo "[FAIL]\n";
      echo "==> Caught an exception processing file: " . $e->getMessage() . "\n";
    }
  }
}

echo "Done.\n";

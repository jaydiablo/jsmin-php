#!/usr/bin/env php
<?php
error_reporting(E_STRICT);
require '../jsmin.php';

function output($text, $status = null) {
  switch ($status) {
    case 'pass':
      $out = "[0;32m";
      break;
    case 'fail':
      $out = "[0;31m";
      break;
    default:
      $out = "[0m";
      break;
  }

  echo chr(27)."$out$text".chr(27)."[0m"."\n";
}

$libs = array(
  'dojo',
  'ext',
  'jquery',
  'mootools',
  'yui',
  'd3.v2',
  'modernizr',
  'mif.tree',
);

$cases = array(
  'inline-unary' => array(
    //'source' => 'inline-unary',
    //'expected' => 'inline-unary.expected',
    'exception' => false
  ),
  'utf8-with-bom' => array(
    'exception' => false
  ),
  'division-newline-with-comment' => array(
    'exception' => false
  ),
  'division-newline' => array(
    'expected' => null,
    'exception' => 'Unterminated Regular Expression literal.'
  ),
  'issue10' => array(
    'exception' => false
  ),
  'bootstrap-exclamation' => array(
    'exception' => false
  ),
);

$descriptorspec = array(
  0 => array("pipe", "r"),  // stdin
  1 => array("pipe", "w"),  // stdout
  2 => array("pipe", "w"),  // stderr
);

echo "\nJSMIN PHP Test Suite\n\n";
echo "Comparing PHP minification to C minification of defined libs\n\n";

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
      output("[FAIL]", 'fail');
      output("==> jsmin.c threw an error but jsmin.php did not. Error: $jsmin_err");
    } else {
      if ($jsmin_c === $jsmin_php) {
        output("[PASS]", 'pass');
      } else {
        output("[FAIL]", 'fail');
        output("==> Output differs between jsmin.c and jsmin.php.");
      }
    }
  } catch (Exception $e) {
    if ($jsmin_err != '' && trim($jsmin_err) == trim($e->getMessage())) {
      output("[PASS]", 'pass');
    } else {
      output("[FAIL]", 'fail');
      output("==> Caught an exception processing file: " . $e->getMessage());
    }
  }
}

echo "\nComparing minification to expected output of defined test cases\n\n";

foreach ($cases as $name => $case) {
  echo "Testing $name ";

  if (!isset($case['source'])) {
    $case['source'] = $name;
  }

  if (!isset($case['expected'])) {
    $case['expected'] = $name . '.expected';
  }

  try {
    $minified = JSMin::minify(file_get_contents(__DIR__ . "/cases/" . $case['source'] . ".js"));

    if ($minified === file_get_contents(__DIR__ . "/cases/" . $case['expected'] . ".js")) {
      output("[PASS]", 'pass');
    } else {
      output("[FAIL]", 'fail');
      output("==> Minified output didn't match the expected output");
    }
  } catch (Exception $e) {
    if ($case['exception'] !== false) {
      if ($case['exception'] === $e->getMessage()) {
        output("[PASS]", 'pass');
      } else {
        output("[FAIL]", 'fail');
        output("==> The exception thrown by JSMin (" . $e->getMessage() . ") didn't match the expected exception (" . $case['exception'] . ")");
      }
    } else {
      output("[FAIL]", 'fail');
      output("==> JSMin threw an Exception when minifying:" . $e->getMessage());
    }
  }
}

echo "\nDone.\n\n";

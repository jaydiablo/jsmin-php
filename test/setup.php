#!/usr/bin/env php
<?php
$url_jsmin = 'https://github.com/douglascrockford/JSMin/raw/master/jsmin.c';

// For libs we're just confirming that JSMin PHP minifies the files in the same
// way that JSMIN.c does
$libs = array(
  'dojo'     => 'https://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js.uncompressed.js',
  'ext'      => 'https://ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core-debug.js',
  'jquery'   => 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js',
  'mootools' => 'https://ajax.googleapis.com/ajax/libs/mootools/1.3.0/mootools.js',
  'yui'      => 'http://yui.yahooapis.com/3.3.0/build/yui/yui.js',
  'd3.v2'    => 'https://raw.github.com/mbostock/d3/master/d3.v2.js',
  'modernizr'=> 'https://raw.github.com/Modernizr/Modernizr/master/modernizr.js',
  'mif.tree' => 'http://mifjs.net/tree/Download/file/mif.tree-v1.2.6.4.js',
);

// Download latest JSMin and compile it.
echo "Fetching $url_jsmin...\n";
file_put_contents(__DIR__ . '/jsmin.c', file_get_contents($url_jsmin));

echo "Compiling jsmin.c...\n";
if (system('cc jsmin.c -o jsmin') === false) {
  die();
}

// Download libs.
@mkdir(__DIR__ . '/libs', 0755);

foreach($libs as $name => $url) {
  echo "Fetching $url...\n";
  file_put_contents(__DIR__ . "/libs/$name.js", file_get_contents($url));
}

echo "Done\n";

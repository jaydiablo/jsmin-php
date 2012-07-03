#!/usr/bin/env php
<?php
$url_jsmin = 'https://github.com/douglascrockford/JSMin/raw/master/jsmin.c';

$libs = array(
  'dojo'     => 'https://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js.uncompressed.js',
  'ext'      => 'https://ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core-debug.js',
  'jquery'   => 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js',
  'mootools' => 'https://ajax.googleapis.com/ajax/libs/mootools/1.3.0/mootools.js',
  'yui'      => 'http://yui.yahooapis.com/3.3.0/build/yui/yui.js',
  'd3.v2'    => 'https://raw.github.com/mbostock/d3/master/d3.v2.js',
  'modernizr'=> 'https://raw.github.com/Modernizr/Modernizr/master/modernizr.js',
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

// Copy utf-8 file to the libs directory
echo "Copying UTF-8 file with BOM...\n";
copy(__DIR__ . '/utf8-with-bom.js', __DIR__ . '/libs/utf8-with-bom.js');

echo "Copying D3 partial file...\n";
copy(__DIR__ . '/d3.partial.js', __DIR__ . '/libs/d3.partial.js');

echo "Copying other D3 partial file...\n";
copy(__DIR__ . '/d3.partial2.js', __DIR__ . '/libs/d3.partial2.js');

echo "Copying issue10 file...\n";
copy(__DIR__ . '/issue10.js', __DIR__ . '/libs/issue10.js');

echo "Done\n";

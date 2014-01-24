<?php
	error_reporting(E_ERROR | E_PARSE);
// You can use the script from the command line
// ie by running:\\
// {{{php -f test_pocco.php}}}\\
// the you'll generate a Docco.html documentation from the included Docco.php file
//
// You can also use it from the command line by putting you php script inside Pocco's directory,
// if your script is 'my_script.php', then run :\\
// {{{php -f test_docco.php my_script php}}}\\
// You'll then have a 'my_script.html' generated file
//
// Another one: to generate docco.html from docco.coffee
// type the following:
// {{{php -f test_docco.php docco coffee markdown}}}\\
include('Pocco.php');

// Gets the filename part, the extension & the markup.
$fname  = (isset($argv[1])) ?  preg_replace('/-/','',$argv[1]) : 'Pocco';
$fext   = (isset($argv[2])) ?  $argv[2] : 'php';
$markup = (isset($argv[3])) ?  $argv[3] : 'creole';

// Built a Pocco object instance and generates the doc
$p = new Pocco($fname, $fext, $markup);
$p->save_doc($fname);

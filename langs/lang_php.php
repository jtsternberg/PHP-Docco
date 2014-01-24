<?php

/* ====================================
                  PHP 
======================================= */
// Load all the keywords
include("kw_php.inc.php");

// CallBacks functions
function cb_herodoc_php($hl, $me) {
  $res = $me[4][2][0];
  $to_add = array(
    "1" => array( "pattern" => '/\{?\$\w+((->)([a-zA-Z0-9_\[\]]+))?\}?/', "style" => "Tag"),
    "2" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
    "3" => array( "pattern"   => '/\@(a(bstract|ccess|uthor)|c(ategory|opyright|ode)|example|global|internal|li(cense|nk)|pa(ckage|ram)|return|s(ee|ince|tatic|ubpackage)|t(hrows|odo)|v(ar|ersion)|uses|deprecated|final)\b/', "style" => "Entities2"),
    "4" => array(
      "pattern"  => '/' . $res . ';/',
      "style"    => "Operators",
      "action"   => "#pop",
      )
  );
  if (!array_key_exists('HerodocString',$hl->allRules)) {$hl->allRules["HerodocString"] = $to_add;}
  return $me;
}

function cb_nowdoc_php($hl, $me) {
  $res = $me[4][2][0];
  $to_add = array(
    "nowdoc_end" => array(
      "pattern"  => '/' . $res . ';/',
      "style"    => "Operators",
      "action"   => "#pop",
      )
  );
  if (!array_key_exists('NowdocString',$hl->allRules)) {$hl->allRules["NowdocString"] = $to_add;}
  return $me;
}

// === RULES

$php = array(
"php_root" => array(
  // Keywords
  "kw1" => array( "pattern" => '/\b(?:' . $php_kw0  . ')\b/i', "style" => "Keywords"),
  "kw2" => array( "pattern" => '/\b(?:' . $php_kw1  . ')\b/i', "style" => "Keywords2"),
  "kw3" => array( "pattern" => '/\b(?:' . $php_kw2  . ')\b/i', "style" => "Keywords2"),
  "kw4" => array( "pattern" => '/\b(?:' . $php_kw3  . ')\b/i', "style" => "Keywords2"),
  "kw5" => array( "pattern" => '/\b(?:' . $php_kw4  . ')\b/i', "style" => "Keywords2"),
  "kw6" => array( "pattern" => '/\b(?:' . $php_kw5  . ')\b/i', "style" => "Keywords2"),
  "kw7" => array( "pattern" => '/\b(?:' . $php_kw6  . ')\b/i', "style" => "Keywords2"),
  "kw8" => array( "pattern" => '/\b(?:' . $php_kw7  . ')\b/i', "style" => "Keywords2"),

  // Types
  "types" => array( "pattern" => '/\b(?:array|bool|boolean|char|const|double|float|int|integer|long|mixed|object|real|string)\b/', "style" => "Types"),

  "comment" => array( "pattern" => '/(\/\/.*)/', "prio" => TRUE, "style" => "Comment"),
  "bigcomment" => array( "pattern" => '/\/\*/', "style" => "BlockComment", "action" => "php_BigComment"),

  "heredoc" => array( "pattern" => '/(<<<)([a-zA-Z0-9_]+)/', "style" => "Operators", "action"  => "HerodocString", "callback" => 'cb_herodoc_php', "transit" => "SingleString"),
  "nowdoc" => array( "pattern" => "/(<<<)'([a-zA-Z0-9_]+)'/", "style" => "Operators", "action"  => "NowdocString", "callback" => 'cb_nowdoc_php', "transit" => "DoubleString"),


  //array( "pattern" => '/#.*/', "prio" => TRUE, "style" => "Comment"),
  "tag_php" => array( "pattern" => '/(<\?\s*php|\?>)/', "style" => "Entities"),

  "singlestring" => array( "pattern" => "/'/", "style" => "SingleString", "action" => "php_SingleString"),
  "doublestring" => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "php_DoubleString"),

  //"10" => array( "pattern" => '/\b[A-Z0-9_]+\b/', "style" => "Entities3"),

  "dollar" => array( "pattern" => '/\$/', "style" => "Tag"),
  "variable" => array( "pattern" => '/\$[a-zA-Z0-9_]+/', "style" => "Tag"),

  // Class and Functions
  "cl1" => array( "pattern" => '#\b(namespace|use|new)(\s+)([\\\a-zA-Z0-9_/]+)#', "style" => array("Special","Root","className")),
  "cl2" => array( "pattern" => '/\b(class|interface)\b(\s+)(\w+)/', "style" => array("Keywords3","Root","className")),
  "cl3" => array( "pattern" => '/\b(extends)\b(\s+)([\/a-zA-Z0-9_]+)/', "style" => array("Special","Root","className")),
  "cl4" => array( "pattern" => '/\b(?:implements|self|parent|private|protected|public|abstract|static|final|static|const)\b/', "style" => "Special"),
  // Functions
  "cl5" => array( "pattern" => '/\b(function)\b(\s+)(\w+)/', "style" => array("Keywords4","Root","funcName")),
 
  // Numbers
  "hexnumbers" => array( "pattern" => '/0x[0-9a-fA-F]+/', "style" => "Number"),
  "numbers" => array( "pattern" => '/\b[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?/', "style" => "Number"),

  // Superglobals
  "globals" => array( "pattern" => '/\b(_GET|_POST|_COOKIE|_SESSION|_ENV|GLOBALS|_SERVER|_FILES|_REQUEST)\b/', "style" => "Types"),

  "logic" => array( "pattern" => '/\b(?:FALSE|TRUE|NULL|OR|AND)\b/i', "style" => "Logic"),
  "parens" => array( "pattern" => '/(?:,|;|\[|\]|\(|\)|\{|\})/', "style" => "Pars"),
  "methods" => array( "pattern" => '/(->|::)([\/a-zA-Z0-9_]+)/', "style" => array("Operators","funcName")),
  "symbols" => array( "pattern" => '/(?:\||\\|\.|;|\&|@|=>|:|\+\+|\+|--|-|<=|>=|<|>|==|=|\*|\/|\^|\?|\!|%|\.)/', "style" => "Operators"),


),

// STATE php_DoubleString
"php_DoubleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "php_EscapeDoubleString"),
  "out" => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")),

// STATE php_SingleString
"php_SingleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "php_EscapeDoubleString"),
  "out" => array( "pattern"   => "/'/", "style" => "SingleString", "action" => "#pop")),

// STATE php_EscapeDoubleString
"php_EscapeDoubleString" => array(
  //array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
  "out" => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),


// php_BigComment
"php_BigComment" => array(
  "url" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  "params" => array( "pattern"   => '/\s+\@(a(bstract|ccess|uthor)|c(ategory|opyright)|example|global|internal|li(cense|nk)|pa(ckage|ram)|return|s(ee|ince|tatic|ubpackage)|t(hrows|odo)|v(ar|ersion)|uses|deprecated|final)\b/', "style" => "Entities2"),
  "out" => array( "pattern"   => '#\*\/#', "style" => "BlockComment", "action" => "#pop")
  )
);

?>
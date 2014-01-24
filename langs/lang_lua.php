<?php

/* ====================================
              LISP
======================================= */

$lua = array(  
// JS Root state  
"lua_root" => array(
  'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "lua_DoubleString"),
  's_string' => array( "pattern" => "/'/", "style" => "SingleString", "action" => "lua_SingleString"),
  'block_com' => array( "pattern" => '/--\[\[/', "style" => "BlockComment", "action" => "lua_MultilineComment"),
  'com' => array( "pattern" => '/--.*/', "style" => "Comment"),
  'hexnumbers' => array( "pattern" => '/0x[0-9a-fA-F]+/', "style" => "Number"),
  'numbers' => array( "pattern" => '/\b[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?/', "style" => "Number"),
  'kw1' => array( "pattern" => '/\b(?:and|break|do|else|elseif|end|false|for|if|in|local|nil|not|or|repeat|return|then|true|until|while)\b/', "style" => "Keywords"),
  'kw2' => array( "pattern" => '/\b(?:self|true|false|nil)\b/', "style" => "Keywords2"),
  'pars' => array( "pattern" => '/(,|;|\[|\]|\(|\)|\{|\})/', "style" => "Pars"),
  'ops' => array( "pattern" => '/(\||\\|;|<|>|@|=|==|~=|:|\+|-|\*|\/|\^|\?|\!|%|#|[.])/', "style" => "Operators"),
  'fun' => array( "pattern" => '/\b(function)\b(\s+)([a-zA-Z0-9_]+)/', "style" => array("Keywords","Root","funcName")),
  'spe' => array( "pattern" => '/([a-zA-Z_][a-zA-Z0-9_]*)(\s*)(=)/', "style" => array("Special","Root","Operators")),
  'begin_method1' => array( "pattern" => '/([a-zA-Z0-9_]+)(?=\.)/', "style" => "Keywords4"),
  'method1' => array( "pattern" => '/(\.)([a-zA-Z0-9_]+)/', "style" => array("Operators","funcName")),
  'begin_method2' => array( "pattern" => '/([a-zA-Z0-9_]+)(?=:)/', "style" => "Keywords3"),
  'method2' => array( "pattern" => '/(:)([a-zA-Z0-9_]+)/', "style" => array("Operators","funcName")),
),


// Double quoted string state
"lua_DoubleString" => array(
  'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "lua_Escape"),
  'out' => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// Single quoted string state
"lua_SingleString" => array(
  'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "lua_Escape"),
  'out' => array( "pattern"   => "/'/", "style" => "SingleString", "action" => "#pop")),

// Escape in double quoted strings
"lua_Escape" => array(
  'out' => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

// Comment block state
"lua_MultilineComment" => array(
  'urls' => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\)\s!\"',.:;?])#", "style" => "Url"),
  // Comments can nest
  'block_com' => array( "pattern" => '/--\[\[/', "style" => "BlockComment", "action" => "lua_MultilineComment"),
  'out' => array( "pattern"   => '/-?-?]]/', "style" => "BlockComment", "action" => "#pop")
  )
);

?>
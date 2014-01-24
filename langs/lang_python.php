<?php

/* ====================================
              PYTHON
======================================= */

$python = array(
// =================  
// PYTHON Root state  
"python_root" => array(
  "comment" => array( "pattern" => '/#.*/', "style" => "Comment"),
  "3d_string" => array( "pattern" => '/"""/', "style" => "DoubleString", "action" => "python_TripleDoubleString"),
  "3s_string" => array( "pattern" => "/'''/", "style" => "SingleString", "action" => "python_TripleSingleString"),
  "doublestring" => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "python_DoubleString"),
  "singlestring" => array( "pattern" => "/'/", "style" => "SingleString", "action" => "python_SingleString"),
  "class" => array( "pattern" => '/\b(class)\b(\s+)(\w+)/', "style" => array("Keywords3","Root","className")),
  "defs" => array( "pattern" => '/\b(def)\b(\s+)(\w+)/', "style" => array("Keywords3","Root","funcName")),
  "hexnumbers" => array( "pattern" => '/0x[0-9a-fA-F]+/', "style" => "Number"),
  "numbers" => array( "pattern" => '/\b[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?/', "style" => "Number"),
  "kw1" => array( "pattern" => '/\b(and|as|assert|break|continue|del|elif|else|except|exec|finally|for|from|global|if|import|in|is|lambda|not|or|pass|raise|return|try|while|with|yield)\b/', "style" => "Keywords"),
  "kw2" => array( "pattern" => '/\b(abs|all|any|basestring|bin|bool|callable|chr|classmethod|cmp|compile|complex|delattr|dict|dir|divmod|enumerate|eval|execfile|file|filter|float|format|frozenset|getattr|globals|hasattr|hash|help|hex|id|input|int|isinstance|issubclass|iter|len|list|locals|long|map|max|min|next|object|oct|open|ord|pow|print|property|range|raw_input|reduce|reload|repr|reversed|round|set|setattr|slice|sorted|staticmethod|str|sum|super|tuple|type|unichr|unicode|vars|xrange|zip|__import__|apply|buffer|coerce|intern)\b/', "style" => "Keywords2"),
  "types" => array( "pattern" => '/\b(?:self|True|False|None)\b/', "style" => "Keywords3"),
  "private" => array( "pattern" => '/\b(?:__\w+__)\b/', "style" => "Keywords5"),
  "pars" => array( "pattern" => '/(,|;|\[|\]|\(|\)|\{|\})/', "style" => "Pars"),
  "ops" => array( "pattern" => '/(\||\\|;|<|>|@|=|==|~=|:|\+|-|\*|\/|\^|\?|\!|%|#|[.])/', "style" => "Operators"),
  "special" => array( "pattern" => '/@\w+\b/', "style" => "Special"), // Decorators
),

// =================
// Double quoted string state
"python_TripleSingleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "python_EscapeDoubleString"),
  "tag" => array("pattern" => '/\s*@\w+/', "style" => "Tag"),
  "urls" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  "out" => array("pattern" => "/'''/", "style" => "SingleString", "action" => "#pop")
),

// =================
// Single quoted string state
"python_TripleDoubleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "python_EscapeDoubleString"),
  "tag" => array("pattern" => '/\s*@\w+/', "style" => "Tag"),
  "urls" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  "out" => array( "pattern"   => '/"""/', "style" => "DoubleString", "action" => "#pop")),

// =================
// Double quoted string state
"python_DoubleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "python_EscapeDoubleString"),
  "vars1" => array("pattern" => '/(%)(\w+)/', "style" => array("Special","Entities1")),
  "vars2" => array("pattern" => '/(%)(\()(\w+)(\))/', "style" => array("Special","Pars","Entities2","Pars")),
  "out" => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// =================
// Single quoted string state
"python_SingleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "python_EscapeDoubleString"),
  "vars1" => array("pattern" => '/(%)(\w+)/', "style" => array("Special","Entities1")),
  "vars2" => array("pattern" => '/(%)(\()(\w+)(\))/', "style" => array("Special","Pars","Entities2","Pars")),
  "out" => array( "pattern" => "/'/", "style" => "SingleString", "action" => "#pop")
  ),

// =================
// STATE php_EscapeDoubleString
"python_EscapeDoubleString" => array(
  "out" => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),
);
?>
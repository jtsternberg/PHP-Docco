<?php

/* ====================================
              LISP
======================================= */

$lisp = array( 
// =============== 
// LISP Root state  
"lisp_root" => array(
  'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "lisp_DoubleString"),
  'block_comment' => array( "pattern" => '/#[|]/', "style" => "BlockComment", "action" => "lisp_MultilineComment"),
  'comment' => array( "pattern" => '/;.*/', "style" => "Comment"),

  //array( "pattern" => '/:\\/' . $lisp_symbol  . '/' , "style" => "Special"),

  'num' => array( "pattern" => '/\b(\#(x|o|b|X|O|B))?[+-]?((0x\d+)|((\d*\.)?\d+([eE][+-]?\d+)?))u?((int(?:8|16|32|64))|L)?\b/', "style" => "Number"),

  'kw1' => array( "pattern" => '/\b(?:defconstant|defvar|defparameter|case|cond|in-package|deftype|definline|declare|when|defgeneric|defmethod|defpackage|defstruct|defclass|loop|let\*|let|list|progn|push|pop|cons|car|setf|getf|cdr|if|equal|eql|eq)\b/', "style" => "Keywords"),

  'defs' => array( "pattern" => '/(\()(\s*)(defun|defmacro)(\s+)([*a-zA-Z0-9_+=-]+)/' , "style" => array("Pars","Root","Keywords2","Root","funcName")),
  'types' => array( "pattern" => '/\*[a-zA-Z0-9-]+\*/', "style" => "Types"),
  'method' => array( "pattern" => '/(\()(\s*)([*\'a-zA-Z0-9_+=-]+)/', "style" => array("Pars","Root", "className")),
  'symbol' => array( "pattern" => '/:[a-zA-Z0-9_-]+/', "style" => "Keywords3"),
  'pars' => array( "pattern" => '/(,|;|\[|\]|\(|\)|\{|\})/', "style" => "Pars"),
  'ops' => array( "pattern" => '/\s+([+]|\||\\|\.|<|>|@|=|:|-|\*|\^|\?|\!|%)\s+/', "prio" => TRUE, "style" => array("Root", "Operators","Root")),
),

// ===============
// Double string state
"lisp_DoubleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "lisp_EscapeDoubleString"),
  'out' => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// ===============
// STATE php_EscapeDoubleString
"lisp_EscapeDoubleString" => array(
  "out" =>array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

// ===============
// Comment block state
"lisp_MultilineComment" => array(
  // Comments can nest
  'block_com' => array( "pattern" => '/#[|]/', "style" => "BlockComment", "action" => "lisp_MultilineComment"),
  'out' => array( "pattern"   => '/[|]#/', "style" => "BlockComment", "action" => "#pop")
  )
);

?>
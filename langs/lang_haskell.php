<?php

/* ====================================
              LISP
======================================= */

$haskell = array(  
// JS Root state  
"haskell_root" => array(
  'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "haskell_DoubleString"),
  's_string' => array( "pattern" => "/'/", "style" => "SingleString", "action" => "haskell_SingleString"),
  'block_comment' => array( "pattern" => '/\{-/', "style" => "BlockComment", "action" => "haskell_MultilineComment"),
  'comment' => array( "pattern" => '/--.*/', "style" => "Comment"),

  'num1' => array( "pattern" => '/0x[0-9a-fA-F]+/', "style" => "Number"),
  'num2' => array( "pattern" => '/\b[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?/', "style" => "Number"),

  'kw1' => array( "pattern" => '/\b(?:as|case|of|class|data|data family|data instance|default|deriving|deriving instance|do|forall|foreign|hiding|if|then|else|import|infix|infixl|infixr|instance|let|in|mdo|module|newtype|proc|qualified|rec|type|type family|type instance|where)\b/', "style" => "Keywords"),

  'types' => array( "pattern" => '/\b(?:_*[A-Z_][\w]*\b)/', "style" => "Types"),
  'logic' => array( "pattern" => '/\b(?:True|False)\b/', "style" => "Logic"),
  'pars' => array( "pattern" => '/(,|;|\[|\]|\(|\)|\{|\})/', "style" => "Pars"),
  'ops' => array( "pattern" => '/(-<|-<<|->|<-|\(`|\||\\|@|=|==|~=|:|\+|-|\*|[\/]|\^|\?|\!|%|#|<|>|[.])/', "style" => "Operators"),
),


// Double quoted string state
"haskell_DoubleString" => array(
  'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "haskell_Escape"),
  'out' => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// Single quoted string state
"haskell_SingleString" => array(
  'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "haskell_Escape"),
  'out' => array( "pattern"   => "/'/", "style" => "SingleString", "action" => "#pop")),

// Escape in double quoted strings
"haskell_Escape" => array(
  'out' => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

// Comment block state
"haskell_MultilineComment" => array(
  'urls' => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  'out' => array( "pattern"   => '/[\s\S]*?-\}/', "style" => "BlockComment", "action" => "#pop")
  )
);

?>
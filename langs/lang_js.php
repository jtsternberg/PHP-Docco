<?php

/* ====================================
              JAVASCRIPT 
======================================= */

$js = array(
// =============
// JS Root state  
"js_root" => array(
  "block_comment" => array( "pattern" => '/\/\*/', "style" => "BlockComment", "action" => "js_MultilineComment"),
  "comment" => array( "pattern" => '/(\/\/.*)/', "style" => "Comment"),

  "kw1" => array( "pattern" => '/\b(?:abstract|break|case|catch|class|continue|debugger|default|delete|do|else|export|extends|final|finally|for|goto|if|implements|import|in|instanceof|interface|native|new|package|private|protected|public|return|static|super|switch|synchronized|this|throw|throws|transient|try|typeof|var|void|while|with)\b/', "style" => "Keywords"),

  "kw2" => array( "pattern" => '/(\b(?:boolean|byte|char|const|double|enum|float|int|long|short|volatile)\b)/', "style" => "Types"),
  "kw3" => array( "pattern" => '/(\b(?:true|false|null)\b)/', "style" => "Logic"),
  "ops1" => array( "pattern" => '/(\||\\|\.|<|>|@|&|===|==|=|:|\+\+|\+|--|-|\*|\^|\?|\!|%)/', "style" => "Operators"),
  "ops2" => array( "pattern" => '/([a-zA-Z0-9_]+)(:)/', "style" => array("funcName","Operators")),
  // Regexps ARE the big problem here: how to differentiate them from the division ?
  // So I took GNU Source Highlight rules wich seems the best here
  
  "spe1" => array( "pattern" => '#(\+\+|--|\)|\])(\s*)(/=?(?![*/]))#', "style" => array("Operators", "Root", "Operators")),
  "num_div" => array( "pattern" => '#(0x\d+|(?:\d*\.)?\d+(?:[eE][+-]?\d+)?)(\s*)(/(?![*/]))#', "style" => array("Number", "Root", "Operators")),
  //array( "pattern" => '#(-?[0-9][0-9xA-F]*)(\s*)(/(?![*/]))#', "style" => array("Number", "Root", "Operators")),
  "var_div" => array( "pattern" => '#([a-zA-Z$_][a-zA-Z0-9$_]*\s*)(/=?(?![*/]))#', "style" => array("Root", "Operators", "Operators")),

  //array( "pattern" => '#/(\.|[^*\/])(\.|[^\/])*(?<![\\\])/[gim]*#', "style" => "Regex"),
  "regexp" => array( "pattern" => '#/#', "style" => "Regex", "action" => "js_Regexp"),

  "ops3" => array( "pattern" => '/\$/', "style" => "Operators"),
  // highlight something like foo.bar.baz
  "class1" => array( "pattern" => '/([a-zA-Z0-9_]+)(?=[\.])/', "style" => "className"),
  "func1" => array( "pattern" => '/([\.])([a-zA-Z0-9_]+)/', "style" => array("Operators","funcName")),

  // method
  "class2" => array( "pattern" => '/([a-zA-Z0-9_]+)(?=\s*\()/', "style" => "className"),
  "func2" => array( "pattern" => '/\b(function)(\s*)([a-zA-Z0-9_]+)/', "style" => array("Keywords3", "Root", "funcName")),
  "num" => array( "pattern" => '/\b-?[0-9][0-9.xA-F]*\b/', "style" => "Number"),
  "d_string" => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "js_DoubleString"),
  "s_string" => array( "pattern" => "/'/", "style" => "SingleString", "action" => "js_SingleString"),
  //"pars" => array( "pattern" => '/(,|;|\[|\]|\(|\)|\{|\})/', "style" => "Pars"),

  
),

// =============
"js_Regexp" => array(
  "esc" => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "js_EscapeDoubleString"),
  "out" => array("pattern" => '#/[gim]*#', "style" => "Regex", "action" => "#pop")
),

// =============
// Single string state
"js_SingleString" => array(
    "esc" => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "js_EscapeDoubleString"),
    "out" => array("pattern" => "/'/", "style" => "SingleString", "action" => "#pop")
),

// =============
"js_DoubleString" => array(
  "esc" => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "js_EscapeDoubleString"),
  "out" => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// =============
// Escape in double quoted strings
"js_EscapeDoubleString" => array(
  "out" => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

// =============
// Comment block state
"js_MultilineComment" => array(
  "urls" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  "out" => array( "pattern"   => '#\*\/#', "style" => "BlockComment", "action" => "#pop")
  )
);

?>
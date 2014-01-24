<?php

/* =========================================
                  coffeescript 
  See GEdit one here: http://goo.gl/ydXqL
  See Pygment one here: http://goo.gl/j0bqx
============================================ */

$coffee = array(
// =======================  
// coffee Root state  
  "coffee_root" => array(
    "block_comment" => array( "pattern" => '/###/', "style" => "BlockComment", "action" => "coffee_BigComment"),
    'comment' => array( "pattern" => '/#.*/', "style" => "Comment"),
    'regexp0' => array( "pattern" => '#/(?![\s/\*\+\{\}\?]).*?[^\\\]/[igmy]{0,4}(?![a-zA-Z0-9])#' , "style" => "Regex"),
    'regexp' => array( "pattern" => '#///#', "style" => "Regex", "action" => "coffee_regexp"),
    'ops' => array( "pattern" => '#\+\+|--|\$|~|&&|\band\b|\bor\b|\bis\b|\bisnt\b|\bnot\b|\?|:|==?|=|\|\||(<<|>>>?|!=?|[-<>+*%&\|\^/])=?#', 
      "style" => "Operators"),

    'func' => array( "pattern" => '/\([^()]*\)\s*->/', "style" => "funcName"),
    'pars1' => array( "pattern" => '/[{(\[;,]/', "style" => "Pars"),
    'pars2' => array( "pattern" => '/[})\].]/', "style" => "Pars"),
    'kw1' => array( "pattern" => 
      '/\b(?:for|in|of|while|break|return|continue|switch|when|then|if|else|throw|try|catch|finally|new|delete|typeof|instanceof|super|this|by)\b/', 
      "style" => "Keywords"),
    'kw2' => array( "pattern" => 
      '/\b(?:true|false|yes|no|on|off|null|NaN|Infinity|undefined)\b/', 
      "style" => "Keywords2"),
    'kw3' => array( "pattern" => 
      '/\b(?:Array|Boolean|Date|Error|Function|Math|netscape|Number|Object|Packages|RegExp|String|sun|decodeURI|decodeURIComponent|encodeURI|encodeURIComponent|eval|isFinite|isNaN|parseFloat|parseInt|document|window)\b/', 
      "style" => "Keywords3"),

    "cl1" => array( "pattern" => '/\b(class)\b(\s+)(\w+)/', "style" => array("Keywords3","Root","className")),
    "cl2" => array( "pattern" => '/\b(extends)\b(\s+)([\/a-zA-Z0-9_]+)/', "style" => array("Special","Root","className")),
    //Methods highlights 'foo' in foo.bar
    "met1" => array( "pattern" => '/([a-zA-Z0-9_]+)(?=[\.]|::)/', "style" => "funcName"),
    //Methods highlights '.bar' in foo.bar
    "met2" => array( "pattern" => '/([\.]|::)([a-zA-Z0-9_]+)/', "style" => array("Operators","funcName")),

    'var1' => array( "pattern" => '/@[a-zA-Z_][a-zA-Z0-9_]*/', "style" => "Tag"),
    'var2' => array( "pattern" => '/[$a-zA-Z_][a-zA-Z0-9_]*:/', "style" => "Entities2"),
    // Numbers
    'num1' => array( "pattern" => '/[0-9][0-9]*\.[0-9]+([eE][0-9]+)?[fd]?/', "style" => "Number"),
    'num2' => array( "pattern" => '/0x[0-9a-fA-F]+/', "style" => "Number"),
    'num3' => array( "pattern" => '/\d+/', "style" => "Number"),
    "3s_string" => array( "pattern" => "/'''/", "style" => "Special", "action" => "coffee_TripleSingleString"),
    'js_string' => array( "pattern" => '/`/', "style" => "Types", "action" => "coffee_JSString"),
    'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "coffee_DoubleString"),
    's_string' => array( "pattern" => "/'/", "style" => "SingleString", "action" => "coffee_SingleString"),
  ),
  
// ============== 
// State coffee_slash_Starts_regex
"coffee_regexp" => array(
  'comment' => array( "pattern" => '/#.*\n/', "style" => "Comment"),
  'out3' => array( "pattern" => '#///#', "style" => "Regex", 'action' => "#pop"),
),

// ====================== 
// State coffee_DoubleString
  "coffee_JSString" => array(
    'out' => array( "pattern" => '/`/', "style" => "Types", "action" => "#pop")),

// =================
// Sate Triple Single String
"coffee_TripleSingleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "coffee_EscapeDoubleString"),
  "urls" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  "out" => array("pattern" => "/'''/", "style" => "Special", "action" => "#pop")
),
    
// ====================== 
// State coffee_DoubleString
  "coffee_DoubleString" => array(
    "vars1" => array("pattern" => '/(#)(\{.*?\})/', "style" => array("Special","Entities")),
    'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "coffee_Escape"),
    'out' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")),
  
// ====================== 
// State coffee_SingleString
  "coffee_SingleString" => array(
    'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "coffee_Escape"),
    'out' => array( "pattern"   => "/'/", "style" => "SingleString", "action" => "#pop")),

// ===================== 
// State coffee_Escape
  "coffee_Escape" => array(
    'out' => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
  ),

// coffee_BigComment
"coffee_BigComment" => array(
  "url" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  "out" => array( "pattern"   => '/###/', "style" => "BlockComment", "action" => "#pop"),
  )
);

?>
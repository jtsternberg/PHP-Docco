<?php

/* ====================================
              RUBY
======================================= */

/* ===== Callbacks Methods ===== */
function cb_gds($hl, $me)
  // General Delimited Strings callback
  {
    $the_pars = array( '[', '(', '{' );
    $pars_hash = array('[' => ']', '(' => ')', '{' => '}');
    $pattern = $me[4][2][0];

    if (in_array($pattern,$the_pars))
      {
        $pattern = $pars_hash[$pattern];
      }
    $s = '/' . preg_quote($pattern) . '/';

    $hl->allRules['ruby_gdsString'] = array(
        array( "pattern" => '/\#\{/', 'style' => "Pars", 'transit' => "Entities", 'action' => "ruby_NestedI"),
        array( "pattern" => $s, 'style' => "Operators", 'prio' => TRUE, 'action' => "#pop"),
      );
    return $me;
  }

function cb_hd($hl, $me)
  // Heredoc callback
  {
    //$pattern = $me['reg'][2];
    //$pattern = $me['matches'][1];
    $pattern = $me[4][1][2];
    $pattern = str_replace(array("'",'"','`','-'), "", $pattern);
    $res = '/\s*' . preg_quote($pattern) . '/';
    $to_add = array(
        array( "pattern" => '/\\\\/', 'style' => "Entities"),
        array( "pattern" => $res, 'style' => "Operators", 'action' => "#pop"),
      );
    $hl->allRules['ruby_hdString'] = $to_add;
    return $me;
  }

// Regexps callback
function cb_check_if_regexp($hl, $arr) {
  $nb_elem = count($hl->last_tokens);
  if ($nb_elem > 0) {
    foreach(explode('|', ">|(|=|~|when|or|and|index|scan|sub|sub!|gsub|gsub!|if|elsif") as $t) {
      if ($hl->last_tokens[$nb_elem-1] == $t) {
          $arr[3]['style']   = "Regex";
          $arr[3]['action']  = 'ruby_Regexp';
          return $arr;
      }
    }
  }
  return $arr;
}


/* ===== Ruby Rulez (the world) ===== */
$ruby = array( 
// =============== 
// RUBY Root state  
"ruby_root" => array(
  'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "ruby_DoubleString"),
  's_string' => array( "pattern" => "/'/", "style" => "SingleString", "action" => "ruby_SingleString"),
  // General Delimited Strings:
  'gd_string1' => array( "pattern" => '/(%)([\[\(!{])/', "style" => "Operators", "transit" => "SingleString", 'action' => "ruby_gdsString", 'callback' => 'cb_gds'),
  'gd_string2' => array( "pattern" => '/(%)[Qqxsr]([\[\(!{])/', "style" => "Operators", "transit" => "SingleString", 'action' => "ruby_gdsString", 'callback' => 'cb_gds'),

  'gd_string3' => array( "pattern" => '/(%)[Ww]([\[\(!{])/', "style" => "Operators", "transit" => "SingleString", 'action' => "ruby_gdsString", 'callback' => 'cb_gds'),

  // Heredoc
  'heredoc' => array( "pattern" => '/(<<[-]?)([a-zA-Z0-9_"`\']+)/', "style" => "Operators", "transit" => "SingleString", 'action' => "ruby_hdString", 'callback' => 'cb_hd'),

  // Block comments
  'block_comment' => array( "pattern" => '/=begin/', "style" => "BlockComment", 'action' => "ruby_BlockComment"),
  // Shell commands
  'shell' => array( 'pattern' => '/(`)(.*?)(`)/', 'style' => array("Operators","Shebang","Operators")),
  // Regexps
  'regexp' => array( "pattern" => '#\/#', 'style' => 'Operators', 'callback' => "cb_check_if_regexp"),
  // Others
  'class1' => array( "pattern" => '/\b(class)\b(\s+)([a-zA-Z0-9_]+)/', "style" => array("Entities3","Root","className")),
  'class2' => array( "pattern" => '/\b(class)\b(\s+)([a-zA-Z0-9_]+)(\s+?)(<?)(\s+?)([a-zA-Z0-9_:]+)/', "style" => array("Entities3","Root","className","Root","Operators","Root","className")),

  'def' => array( "pattern" => '/\b(def)\b(\s+)(\w+)/', "style" => array("Entities2","Root","funcName")),
  'module' => array( "pattern" => '/\b(module)\b(\s+)([a-zA-Z0-9_]+)/', "style" => array("Entities2","Root","className")),
  'attribs' => array( 'pattern' => '/attr_accessor|attr_reader/', 'style' => "Entities2"),
  'shebang' => array( 'pattern' => '/#!\/usr\/bin\/ruby\s*/', 'style' => "Shebang"),
  'comment' => array( 'pattern' => '/#.*/', 'style' => "Comment"),
  'num' => array( 'pattern' => '/\b-?[0-9][0-9.xA-F]*\b/', 'prio' => TRUE, 'style' => "Number"),
  'spe' => array( "pattern" => '/\b[A-Z0-9_]+\b/', "style" => "Special"),
  'kw1' => array( "pattern" => '/\b(alias|and|BEGIN|begin|break|case|define_method|defined|each|each_with_index|else|elsif|END|end|ensure|for|if|include|in|new|next|not|or|puts|raise|redo|rescue|retry|require|return|super|then|throw|undef|unless|until|when|while|yield)\b/', "style" => "Keywords"),

  'kw2' => array( "pattern" => '/\b(Array|Bignum|Binding|Class|Continuation|Dir|Exception|FalseClass|File::Stat|File|Fixnum|Fload|Hash|Integer|IO|MatchData|Method|Module|NilClass|Numeric|Object|Proc|Range|Regexp|String|Struct::TMS|Symbol|ThreadGroup|Thread|Time|TrueClass)\b/', "style" => "Keywords2"),

  'kw3' => array( "pattern" => '/\bdo\b/', "style" => "Keywords3"),

  'symbols' => array( "pattern" => '/(:)([A-Za-z][A-Za-z0-9_]*)/', "style" => array("Operators","Types")),
  'dict' => array( "pattern" => '/(@[a-z][A-Za-z0-9_]*)/', "style" => "Dico"),
  'ops' => array( "pattern" => '/[.]|~|\||\\\\|<|>|=|\/|:|\+|-|\*|\^|\$|\?|\!|%/', "style" => "Operators"),
  'pars' => array( "pattern" => '/,|;|\[|\]|\(|\)|\{|\}/', "style" => "Pars"),
  'logic' => array( "pattern" => '/\b(true|false|nil|self)\b/', "style" => "Logic"),
),


// Double quoted string state
"ruby_DoubleString" => array(
    'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "ruby_Escape"),
    'vars' => array( "pattern" => '/\#\{.*?\}/', "style" => "Entities"),
    'out' => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// Single quoted string state
"ruby_SingleString" => array(
  'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "ruby_Escape"),
  'vars1' => array("pattern" => '/(%)(\w+)/', "style" => array("Special","Entities1")),
  'vars2' => array("pattern" => '/(%)(\()(\w+)(\))/', "style" => array("Special","Pars","Entities2","Pars")),
  'out' => array( "pattern" => "/'/", "style" => "SingleString", "action" => "#pop")),

// Comment block state
"ruby_BlockComment" => array(
  'urls' => array("pattern" => "/#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#/", "style" => "Url"),
  'out' => array( "pattern" => '=end', "style" => "BlockComment", "action" => "#pop")),

// Regexps
'ruby_Regexp' => array(
  'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "ruby_Escape"),
  'spe' => array('pattern' => '/#\{/', 'style' => "Pars", 'transit' => "Entities", 'action' => "ruby_NestedI"),
  'out' => array('pattern' => '#(\/)([eimnosux]*)#', 'style' => array("Regex","RegexOptions"), 'action' => "#pop"),
),

// Escape in double quoted strings
"ruby_Escape" => array(
  'out' => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

# ToEnd
'ruby_ToEnd' => array(),

# NestedI (Used inside %Q{...} strings wich can contain #{...} expansions )
'ruby_NestedI' => array(
  'out' => array('pattern' => '/\}/', 'style' => "Pars", 'action' => "#pop")),

);

?>
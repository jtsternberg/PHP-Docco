<?php

/* ====================================
                  XML 
======================================= */

$xml = array(
// ==============  
// XML Root state  
  "xml_root" => array(
    'xml_prolog' => array( "pattern" => '/<\?/', "style" => "Shebang", "action" => "xml_Prologue"),
    'xml_com' => array( "pattern" => '/<\!--/', "style" => "BlockComment", "action" => "xml_Comment"),
    //array( "pattern" => '#(</?)([a-zA-Z0-9_]+)#', "style" => array("Entities1","Entities3"), "action" => "xml_Tag"),
    'xml_tag' => array( "pattern" => '#(</?)([^ >]+)#', "style" => array("Entities1","Entities3"), "action" => "xml_Tag"),
    //array( "pattern" => '#</?#', "style" => "Entities1", "action" => "xml_Tag"),
    'e2' => array( "pattern" => '/\&(.*?);/', "style" => "Entities2"),
    'd_string' => array( "pattern" => '/(?<!\\\\)"/', "style" => "DoubleString", "action" => "xml_DoubleString"),
  ),
  
// ============== 
// State xml_Tag
  "xml_Tag" => array(
    'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "xml_DoubleString"),
    's_string' => array( "pattern" => "/'/", "style" => "SingleString", "action" => "xml_SingleString"),
    'spe1' => array( "pattern" => '/([^ >]+)(\s*)(=)/', "style" => array("funcName","Root","Operators")),
    'out1' => array( "pattern" => '/>/', "style" => "Entities1", "action" => "#pop"),
    'out2' => array( "pattern" => '#/>#', "style" => "Entities1", "action" => "#pop")),
  
// ================= 
// State xml_Comment
  "xml_Comment" => array(
    'out' => array( "pattern" => '/-->/', "style" => "BlockComment", "action" => "#pop")),
  
// ================== 
// State xml_Prologue
  "xml_Prologue" => array(
    'spe2' => array( "pattern" => '/([a-zA-Z0-9_-]+)(\s*)(=)/', "style" => array("funcName","Root","Operators")),
    'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "xml_DoubleString"),
    's_string' => array( "pattern" => "/'/", "style" => "SingleString", "action" => "xml_SingleString"),
    'out' => array( "pattern" => '/\?>/', "style" => "Shebang", "action" => "#pop")),
    
// ====================== 
// State xml_DoubleString
  "xml_DoubleString" => array(
    'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "xml_Escape"),
    'out' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")),
  
// ====================== 
// State xml_SingleString
  "xml_SingleString" => array(
    'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "xml_Escape"),
    'out' => array( "pattern"   => "/'/", "style" => "SingleString", "action" => "#pop")),

// ===================== 
// State xml_Escape
  "xml_Escape" => array(
    'out' => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
  ),

);

?>
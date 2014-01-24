<?php

/* ====================================
              F#
======================================= */

$fsharp= array(
// =============  
// F# Root state  
"fsharp_root" => array(
  'l_string' => array( "pattern" => '/@"/', "style" => "DoubleString", "action" => "fsharp_LiteralString"),
  'd_string' => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "fsharp_DoubleString"),
  'comment' => array( "pattern" => '/#.*/', "prio" => TRUE, "style" => "Comment"), // PreProcessor

  // Tree types of comments
  'block_comment' => array( "pattern" => '/\(\*/', "style" => "BlockComment", "action" => "fsharp_BlockComment"), // block comments: (*...*)
  'xml_comment' => array( "pattern" => '/(\/\/\/)/', "prio" => TRUE, "style" => "Comment", "action" => "fsharp_XmlDocumentation"), // doc comments: ///...
  'line_com' => array( "pattern" => '/(\/\/)/', "style" => "Comment", "action" => "fsharp_LineComment"),   // line comments: //..

  'num1' => array( "pattern" => '/\b(0x[a-f\d]+|\d+)(ul|lu|u|l)?/', "style" => "Number"),
  'num2' => array( "pattern" => '/\b\d*(\.\d+)?\d(e[+-]?\d+)?[fdm]?/', "style" => "Number"),

  'kw1' => array( "pattern" => '/\b(?:asr|land|lor|lsl|lsr|lxor|mod|sig)\b/', "style" => "Keywords4"),

  // keywords
  'kw2' => array( "pattern" => '/\b(?:as|is|new|sizeof|typeof)\b/', "style" => "Keywords"),
  'kw3' => array( "pattern" => '/\b(?:else|if|then|elif|match\||default)\b/', "style" => "Keywords3"),
  'kw4' => array( "pattern" => '/\b(?:true|false|null)\b/', "style" => "Logic"),
  'kw5' => array( "pattern" => '/\b(?:try|throw|catch|finally)\b/', "style" => "Keywords4"),
  'kw6' => array( "pattern" => '/\b(?:yield|with|when)\b/', "style" => "Keywords5"),
  'kw7' => array( "pattern" => '/\b(?:break|continue|return)\b/', "style" => "Keywords5"),
  'kw8' => array( "pattern" => '/\b(?:do|for|foreach|in|while|done|downto)\b/', "style" => "Keywords"),
  'kw9' => array( "pattern" => '/\b(?:yield|with|when)\b/', "style" => "Keywords"),
  'kw10' => array( "pattern" => '/\b(?:abstract|override|static|mutable|extern|public|protected|private)\b/', "style" => "Keywords"),
  'kw11' => array( "pattern" => '/\b(?:class|interface|type|inherit|begin|module|let|fun|delegate|namespace|open)\b/', "style" => "Keywords2"),
  'kw12' => array( "pattern" => '/\b(?:bool|byte|char|ieee32|ieee64|enum|float|bytearray|int|bigint|sbyte|short|struct|uint|ushort|ulong|obj|string|vector|array|list|seq)\b/', "style" => "Types"),
  'kw13' => array( "pattern" => '/\b(?:exception|lazy|of|rec|inline|and|or|assert|downcast|upcast|use)\b/', "style" => "Logic"),

  // These ones are not in use, but reserved in the future
  'kw14' => array( "pattern" => '/\b(?:atomic|break|checked|component|const|constraint|constructor|continue|eager|event|external|fixed|functor|global|include|method|mixin|object|parallel|process|protected|pure|sealed|tailcall|trait|virtual|volatile)\b/', "style" => "Keywords5"),

  'pars' => array( "pattern" => '/(,|;|\[|\]|\(|\)|\{|\})/', "style" => "Pars"),
  //array( "pattern" => '/(\||\\|;|<|>|@|=|:|&|[+]|-|[*]|\/|\^|\?|\!|%|#|[.])/', "style" => "Operators"),
  'ops' => array( "pattern" => '/(\||[+]|-|[*]|\/|=|::|:|:=|<|>|:\?>|:\?|[.])/', "style" => "Operators"),
),

// ============= 
// Literal string state
"fsharp_LiteralString" => array(
  'esc' => array("pattern" => '#""#', "style" => "StringEsc"),
  'out' => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// ============= 
// Double quoted string state
"fsharp_DoubleString" => array(
  'esc' => array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "fsharp_EscapeDoubleString"),
  'regexp' => array("pattern" => '/%[ds]/', "style" => "Regex"),
  'out' => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// ============= 
// Escape in double quoted strings
"fsharp_EscapeDoubleString" => array(
  'out' => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

// ============= 
"fsharp_LineComment" => array(
  'regexp' => array("pattern" => '/(TODO|FIXME|HACK|UNDONE)/', "style" => "Regex"),
  'out' => array("pattern" => '/\n/', "style" => "Comment", "action" => "#pop"),
),

// ============= 
"fsharp_XmlDocumentation" => array(
  'xml_doc' => array("pattern" => '/</', "prio" => TRUE, "style" => "Pars", "transit" => "Regex", "action" => "fsharp_XmlDocumentationTag"),
  'out' => array("pattern" => "/\n/", "style" => "Root", "action" => "#pop"),
),

// ============= 
"fsharp_XmlDocumentationTag" => array(
  'out' => array("pattern" => '/>/', "style" => "Pars", "action" => "#pop"),
),

// ============= 
// Comment block state
"fsharp_BlockComment" => array(
  'urls' => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  'out' => array( "pattern"   => '/\*)/', "style" => "BlockComment", "action" => "#pop")
  )
);

?>
<?php

/* ====================================
              CSS 
======================================= */

// Callback when coming out from PropertyValue
// Not used anymore : was inside the PropertyValue State
function cb_css_ppt_value($hl, $me)
{
  if (count($hl->states) > 1) {array_pop($hl->states);}
  if (count($hl->styles) > 1) {array_pop($hl->styles);}
  $hl->styles = array('Root');
}

// This is not a callback function, it just returns an array of keywords
function css_colorNames()
{
  $cn = 'aliceblue antiquewhite aqua aquamarine azure beige bisque black blanchedalmond blue blueviolet brown burlywood cadetblue chartreuse chocolate coral cornflowerblue cornsilk crimson cyan darkblue darkcyan darkgoldenrod darkgray darkgrey darkgreen darkkhaki darkmagenta darkolivegreen darkorange darkorchid darkred darksalmon darkseagreen darkslateblue darkslategray darkslategrey darkturquoise darkviolet deeppink deepskyblue dimgray dimgrey dodgerblue firebrick floralwhite forestgreen fuchsia gainsboro ghostwhite gold goldenrod gray grey green greenyellow honeydew hotpink indianred indigo ivory khaki lavender lavenderblush lawngreen lemonchiffon lightblue lightcoral lightcyan lightgoldenrodyellow lightgray lightgrey lightgreen lightpink lightsalmon lightseagreen lightskyblue lightslategray lightslategrey lightsteelblue lightyellow lime limegreen linen magenta maroon mediumaquamarine mediumblue mediumorchid mediumpurple mediumseagreen mediumslateblue mediumspringgreen mediumturquoise mediumvioletred midnightblue mintcream mistyrose moccasin navajowhite navy oldlace olive olivedrab orange orangered orchid palegoldenrod palegreen paleturquoise palevioletred papayawhip peachpuff peru pink plum powderblue purple red rosybrown royalblue saddlebrown salmon sandybrown seagreen seashell sienna silver skyblue slateblue slategray slategrey snow springgreen steelblue tan teal thistle tomato turquoise violet wheat white whitesmoke yellow yellowgreen';
  $cn = implode ("|", explode(' ',$cn));
  return '/\b(' . $cn . ')\b/';
}

// CSS RULES
$css = array(
// ==============  
// CSS Root state  
"css_root" => array(
  // MEDIA : Really needed ?
  array( "pattern" => '/@media/', "style" => "Keywords5", "action" => "css_root"),
  
  array( "pattern" => '/\/\*/', "style" => "BlockComment", "action" => "css_BigComment"),
  array( "pattern" => "/\b(?:a|abbr|acronym|address|applet|area|b|base|big|blockquote|body|br|button|caption|cite|code|col|colgroup|dd|del|dfn|div|dl|dt|em|fieldset|font|form|frame|frameset|(h[1-6])|head|hr|html|i|iframe|img|input|ins|kbd|label|legend|li|link|map|meta|noframes|noscript|object|ol|optgroup|option|p|param|pre|q|samp|s|script|select|small|span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|title|tr|tt|ul|var)\b/", "style" => "Tag"),

  // CSS Selectors
  array( "pattern" => '/(\[)/', "style" => "Pars", "action" => "css_Selectors"),
  array( "pattern" => '/([:]{1,2}[)\(a-zA-Z0-9_-]+)/', "style" => "Keywords5"),

  array( "pattern" => '/@[a-zA-Z0-9_-]+/', "style" => "Tag"),
  array( "pattern" => '/\.[a-zA-Z0-9_-]+/', "style" => "className"),
  array( "pattern" => '/#[a-zA-Z0-9_-]+/', "style" => "funcName"),
  array( "pattern" => '/\*/', "style" => "Keywords4"),
  array( "pattern" => '/:\b(?:active|after|before|first-letter|first-line|focus|hover|link|visited)\b/', "style" => "Keywords5"),
  array( "pattern" => '/[+>~]/', "style" => "Operators"),
  array( "pattern" => '/\{/', "style" => "Pars", "action" => "css_PropertyName"),
),

// ============== 
// css_Selectors state
"css_Selectors" =>array( 
  array( "pattern" => '/"/', "style" => "DoubleString", "action" => "css_DoubleString"),
  array( "pattern" => '/[=]/', "style" => "Operators"),
  array( "pattern" => '/[a-zA-Z0-9_]+/', "style" => "Keywords3"),
  array( "pattern" => '/]/', "style" => "Pars", "action" => "#pop"),
),

// ============== 
// PropertyName State
"css_PropertyName" =>array( 
  array( "pattern" => '/\/\*/', "style" => "BlockComment", "action" => "css_BigComment"),
  array( "pattern" => '/[a-zA-Z0-9_-]+/', "style" => "Keywords2"),
  array( "pattern" => '/:/', "style" => "Pars", "action" => "css_PropertyValue", "transit" => "Root"),
  array( "pattern" => '/}/', "style" => "Pars", "action" => "#pop"),
),

// ============== 
// PropertyValue State {...}
"css_PropertyValue" =>array( 
  array( "pattern" => '/(url)(\s*)(\()(.*)([)])/', "style" => array('Keywords5','Root', 'Pars','Url','Pars')),
  array( "pattern" => '/"/', "style" => "DoubleString", "action" => "css_DoubleString"),
  array( "pattern" => "/'/", "style" => "SingleString", "action" => "css_SingleString"),
  array( "pattern" => '/\/\*/', "style" => "BlockComment", "action" => "css_BigComment"),
  array( "pattern" => '/\b(?:absolute|all-scroll|always|auto|baseline|below|bidi-override|block|bold|bolder|both|bottom|break-all|break-word|capitalize|center|char|circle|col-resize|collapse|crosshair|dashed|decimal-leading-zero|decimal|default|disabled|disc|distribute-all-lines|distribute-letter|distribute-space|distribute|dotted|double|e-resize|ellipsis|fixed|groove|hand|help|hidden|horizontal|ideograph-alpha|ideograph-numeric|ideograph-parenthesis|ideograph-space|inactive|inherit|inline-block|inline|inset|inside|inter-ideograph|inter-word|italic|justify|keep-all|left|lighter|line-edge|line-through|line|list-item|loose|lower-alpha|lower-roman|lowercase|lr-tb|ltr|medium|middle|move|n-resize|ne-resize|newspaper|no-drop|no-repeat|nw-resize|none|normal|not-allowed|nowrap|oblique|outset|outside|overline|pointer|progress|relative|repeat-x|repeat-y|repeat|right|ridge|row-resize|rtl|s-resize|scroll|se-resize|separate|small-caps|solid|square|static|strict|super|sw-resize|table-footer-group|table-header-group|tb-rl|text-bottom|text-top|text|thick|thin|top|transparent|underline|upper-alpha|upper-roman|uppercase|vertical-ideographic|vertical-text|visible|w-resize|wait|whitespace)\b/', "style" => "Keywords4"),
  array( "pattern" => '/!important\b/', "style" => "Keywords5"),
  array( "pattern" => '/\b(?:arial|century|comic|courier|garamond|georgia|helvetica|impact|lucida|symbol|system|tahoma|times|trebuchet|utopia|verdana|webdings|sans-serif|serif|monospace)\b/', "style" => "Keywords3"),
  array( "pattern" => '/(-|\+)?[0-9]*\.[0-9]+/', "style" => "Number"),
  array( "pattern" => '/(-|\+)?[0-9]+/', "style" => "Integer"),
  array( "pattern" => '/(?:px|pt|cm|mm|in|em|ex|pc)\b|%/', "style" => "Types"),
  array( "pattern" => '/,/', "style" => "Pars"),
  array( "pattern" => '/#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})\b/', "style" => "Special"),
  array( "pattern" => css_colorNames(), "style" => "Comment"),
  array( "pattern" => '/;/', "style" => "Pars", "action"=> "#pop") ,// "callback" => "cb_css_ppt_value", "action" => "#pop"),
),

// ============== 
// Double quoted string state
"css_DoubleString" => array(
    array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "css_Escape"),
    array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// ============== 
// Single quoted string state
"css_SingleString" => array(
  array("pattern" => '#\\\#', "style" => "StringEsc", "action" => "css_Escape"),
  array( "pattern"   => "/'/", "style" => "SingleString", "action" => "#pop")),

// ============== 
// Escape in double quoted strings
"css_Escape" => array(
  array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

// ============== 
// Comment block state
"css_BigComment" => array(
  array("pattern" => "#@\w+#", "style" => "Special"),
  array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  array( "pattern"   => '#\*\/#', "style" => "BlockComment", "action" => "#pop")
  ),
);

?>
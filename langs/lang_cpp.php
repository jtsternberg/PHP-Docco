<?php

/* ====================================
              LISP
======================================= */

$cpp = array( 
// ==============
// CPP Root state  
"cpp_root" => array(
  "d_string" => array( "pattern" => '/"/', "style" => "DoubleString", "action" => "cpp_DoubleString"),
  "s_string" => array( "pattern" => "/'/", "style" => "SingleString", "action" => "cpp_SingleString"),
  "block_com" => array( "pattern" => '/\/\*/', "style" => "BlockComment", "action" => "cpp_BlockComment"),
  "comment1" => array( "pattern" => '/(\/\/.*)/', "prio" => TRUE, "style" => "Comment"),

  "hexnumbers" => array( "pattern" => '/0x[0-9a-fA-F]+/', "style" => "Number"),
  "numbers" => array( "pattern" => '/\b[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?/', "style" => "Number"),

  "spe1" => array( "pattern" => '/[*&]\w+/', "style" => "Special"),
  "spe2" => array( "pattern" => '/\w+[*]/', "style" => "Special"),

  "class" => array( "pattern" => '/(class)(\s+)/', "style" => array("Keywords2","Root"), "action" => "cpp_ClassName"),

  // Keywords
  "kw1" => array( "pattern" => '/\b(?:asm|auto|bool|break|case|catch|char|class|const|const_cast|continue|default|delete|do|double|dynamic_cast|else|enum|explicit|export|extern|false|float|for|friend|goto|if|inline|int|long|mutable|namespace|new|operator|private|protected|public|register|reinterpret_cast|return|short|signed|sizeof|static|static_cast|struct|switch|template|this|throw|true|try|typedef|typeid|typename|union|unsigned|using|virtual|void|volatile|wchar_t|while|and|and_eq|bitand|bitor|compl|not|not_eq|or|or_eq|xor|xor_eq)\b/', "style" => "Keywords"),
  // Doxygen
  "kw2" => array( "pattern" => '/\b(?:a|addindex|addtogroup|anchor|arg|attention|author|b|brief|bug|c|code|date|def|defgroup|deprecated|dontinclude|e|em|endcode|endhtmlonly|endif|endlatexonly|endlink|endverbatim|enum|example|exception|f$|f[|f]|file|fn|hideinitializer|htmlinclude|htmlonly|if|image|include|ingroup|internal|invariant|interface|latexonly|li|line|link|mainpage|name|namespace|nosubgrouping|note|overload|p|page|par|param|post|pre|ref|relates|remarks|return|retval|sa|section|see|showinitializer|since|skip|skipline|struct|subsection|test|throw|todo|typedef|union|until|var|verbatim|verbinclude|version|warning|weakgroup)\b/', "style" => "Keywords2"),
  // Idl
  "kw3" => array( "pattern" => '/\b(?:aggregatable|allocate|appobject|array|async|async_uuid|auto_handle|bindable|boolean|broadcast|byte|byte_count|call_as|callback|char|coclass|code|comm_status|const|context_handle|context_handle_noserialize|context_handle_serialize|control|cpp_quote|custom|decode|default|defaultbind|defaultcollelem|defaultvalue|defaultvtable|dispinterface|displaybind|dllname|double|dual|enable_allocate|encode|endpoint|entry|enum|error_status_t|explicit_handle|fault_status|first_is|float|handle_t|heap|helpcontext|helpfile|helpstring|helpstringcontext|helpstringdll|hidden|hyper|id|idempotent|ignore|iid_as|iid_is|immediatebind|implicit_handle|import|importlib|in|include|in_line|int|__int64|__int3264|interface|last_is|lcid|length_is|library|licensed|local|long|max_is|maybe|message|methods|midl_pragma|midl_user_allocate|midl_user_free|min_is|module|ms_union|ncacn_at_dsp|ncacn_dnet_nsp|ncacn_http|ncacn_ip_tcp|ncacn_nb_ipx|ncacn_nb_nb|ncacn_nb_tcp|ncacn_np|ncacn_spx|ncacn_vns_spp|ncadg_ip_udp|ncadg_ipx|ncadg_mq|ncalrpc|nocode|nonbrowsable|noncreatable|nonextensible|notify|object|odl|oleautomation|optimize|optional|out|out_of_line|pipe|pointer_default|pragma|properties|propget|propput|propputref|ptr|public|range|readonly|ref|represent_as|requestedit|restricted|retval|shape|short|signed|size_is|small|source|strict_context_handle|string|struct|switch|switch_is|switch_type|transmit_as|typedef|uidefault|union|unique|unsigned|user_marshal|usesgetlasterror|uuid|v1_enum|vararg|version|void|wchar_t|wire_marshal)\b/', "style" => "Keywords3"),

  "kw4" => array( "pattern" => '/\b(?:bool|int|long|float|short|double|char|unsigned|signed|void|wchar_t)\b/', "style" => "Types"),
  "kw5" => array( "pattern" => '/\b__(?:asm|int8|based|except|int16|stdcall|cdecl|fastcall|int32|declspec|finally|int64|try|leave|wchar_t|w64|virtual_inheritance|uuidof|unaligned|super|single_inheritance|raise|noop|multiple_inheritance|m128i|m128d|m128|m64|interface|identifier|forceinline|event|assume)\b/', "style" => "Keywords4"),

  "pars" => array( "pattern" => '/[\(\)\[\]\{\},.;]/', "style" => "Pars"),
  "ops" => array( "pattern" => '/(~|\||\\|;|<|>|@|=|==|~=|:|&|\+|-|\*|\/|\^|\?|\!|%|#)/', "style" => "Operators"),
),

// ==============
// ClassName state
"cpp_ClassName" => array(
    "name" => array("pattern" => '/[a-zA-Z_][a-zA-Z0-9_]*/', "style" => "className", "action" => "#pop"),
    // template specification
    "out" => array("pattern" => '/\s*(?=>)/', "style" => "Root", "action" => "#pop")
),

// ==============
// Double quoted string state
"cpp_DoubleString" => array(
    "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "cpp_EscapeDoubleString"),
    "out" => array("pattern" => '/"/', "style" => "DoubleString", "action" => "#pop")
),

// ==============
// Single quoted string state
"cpp_SingleString" => array(
  "esc" => array( "pattern" => '#\\\#', "style" => "StringEsc", "action" => "cpp_EscapeDoubleString"),
  "out" => array( "pattern"   => "/'/", "style" => "SingleString", "action" => "#pop")),

// ==============
// STATE cpp_EscapeDoubleString
"cpp_EscapeDoubleString" => array(
  "out" => array("pattern" => '/./', "style" => "StringEsc", "action" => "#pop"),
),

// ==============
// Comment block state
"cpp_BlockComment" => array(
  // nested comments are allowed
  "block_com" => array( "pattern" => '/\/\*/', "style" => "BlockComment", "action" => "cpp_BlockComment"),
  "urls" => array("pattern" => "#(\b(?:(?:https?|ftp):\/\/|mailto:)\S*[^\s!\"',.:;?])#", "style" => "Url"),
  "out" => array( "pattern"   => '#\*\/#', "style" => "BlockComment", "action" => "#pop")
  )
);

?>
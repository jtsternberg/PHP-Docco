<?php

/*
* Pocco is a PHP port of [[http://jashkenas.github.com/docco/|Docco]]
* under a The MIT License.
*
* Copyright (C) 2011 Kibleur C.
* 
* Docco is a quick-and-dirty, literate-programming-style 
* documentation generator. It produces HTML that displays your comments 
* alongside your code. Comments are passed through [[http://www.wikicreole.org/|Creole]]
* thanks to a modified version of Ivan Fomichev's [[http://goo.gl/HQGkt|Creole 1.0]] extensible parser,
* or Michel Fortin's [[http://michelf.com/projects/php-markdown/|PHP-Markdown Extra]] 
* and the code is passed through the [[http://kib2.free.fr/Prism/php/|Prism]] syntax highlighter.
*
* This page is the result of running 
* Pocco against its own source file.
*
* == Another Sample
* 
* You can see the result of running Pocco against the original {{{docco.coffee}}} file
* just [[http://kib2.free.fr/Pocco/docco.html|here]]
*
* == Download
*
* You can [[http://kib2.free.fr/Pocco/Pocco.zip|Download Pocco]] here.
*
* In the future, [[http://kib2.free.fr/Pocco/Pocco.html|Pocco]] will support as many languages 
* its internal highlighter Prism.\\
* At the moment, Pocco only supports PHP.
*/

// = Let's look at our source code

// We need to include Creole, Markdown and Prism modules
include_once('libs/creole.php');
include_once('libs/markdown.php');
include_once('libs/Prism.php');

// The main class
class Pocco {
  // Constructor takes a {{{$filename}}} as argument
  function __construct($file_name, $file_extension, $markup_engine = 'creole')
  {
    $this->markup_engine = $markup_engine;
    $this->filename = $file_name;
    $this->file_extension = $file_extension;
    $this->source   = file_get_contents($file_name.'.'.$file_extension);

    // This sets a Prism instance with an HtmlFormatter
    $this->form     = new HtmlFormatter();
    $this->hil      = new Prism($this->form, $file_extension);

    // All supported languages are set inside an hash-table
    $this->langs = array(
      "php"    =>  array( 'single' => '/^\s*\/\//',  
                          'multi' => array( 'start' => '#/\*#', 'middle' => '#\*#', 'end' => '#\*/#'), 
                          'ignore' => '/<\?php|\?>/'),
      "coffee" =>  array( 'single' => '/^#/'),   
    );

    // Sets the language dictionnary
    $this->set_language();
  }

  function set_language()
  {
    $dico = $this->langs[$this->file_extension];

    // It is assumed that each language provides a {{{single_line_comment}}}.
    $this->single_line_comment = $dico['single'];
    if (array_key_exists('ignore',$dico)) {
      $this->ignore = $dico['ignore'];
    }
    // Now, check if {{{multi}}} is set
    if (array_key_exists('multi',$dico)) {
      $this->block_comment_start = $dico['multi']['start'];
      $this->block_comment_mid   = $dico['multi']['middle'];
      $this->block_comment_end   = $dico['multi']['end'];  
    }
  }

  function parse() {
    $sections = array();
    $docs     = array();
    $code     = array();
    $lines    = explode("\n", $this->source);
    // Skip the first line if it is a shebang.
    if (preg_match('/^\#\!/', $lines[0])) { array_shift($lines); }
    $in_comment_block = false;

    foreach($lines as $line) {
      // If a language sets an {{{ignore}}} key, then the line matching it
      // is just removed from the output.
      if (isset($this->ignore) AND preg_match($this->ignore, $line)) {
        continue;
      }
      // If we're currently in a comment block, check whether the line matches
      // the end of a comment block.
      if ($in_comment_block) {
        if (isset($this->block_comment_end) AND preg_match($this->block_comment_end, $line)) {
          $in_comment_block = false;
        }
        else {
          if (isset($this->block_comment_mid)) {$docs[] = preg_replace($this->block_comment_mid, '', $line);}
          else { $docs[] = $line;}
          
        }
      }
      // Otherwise, check whether the line matches the beginning of a block, or
      // a single-line comment all on it's lonesome.  In either case, if there's
      // code, start a new section.
      else {
        if (isset($this->block_comment_start) AND preg_match($this->block_comment_start, $line)) {
          $in_comment_block = true;
          if (!empty($code)) {
            $sections[] = array($docs, $code);
            $docs = array();
            $code = array();
          }
        }
        else if (isset($this->single_line_comment) AND preg_match($this->single_line_comment, $line)) {
          if (!empty($code)) {
            $sections[] = array($docs, $code);
            $docs = array();
            $code = array();
          }
          $docs[] = preg_replace($this->single_line_comment, '', $line);
        }
        else {
          $code[] = $line;
        }
      }
    } // end foreach
    if ( (!empty($docs)) OR (!empty($code)) ) {
      $sections[] = array($docs, $code);
    }
    if ($this->markup_engine == 'markdown') {
      $sections = $this->normalize_leading_spaces($sections);
    } 
    return $sections;
  }

  function normalize_leading_spaces($sections) {
    $sec = array();
    foreach($sections as $section) {
      if ( (!empty($section)) AND (!empty($section[0])) ) {
        if (preg_match('/^\s+/', $section[0][0], $matches)) {
          $fist_line_blanks = '/^'.$matches[0].'/';
          $res = array();
          foreach($section as $line) { 
            $res[] = preg_replace($fist_line_blanks , '', $line);
          }
          $truc = $res;
        }     
      }
      else { $truc = $section;}
      $sec[] = $truc;
    }
    return $sec;
  }

  // Get the contents of our given {{{$filename}}} template.
  // Then //replace// **any** placeholder like {{{[[place_holder]]}}} inside
  // At the moment, there is only one placeholder inside the templates: 
  // the {{{[[filename]]}}}.
  function get_tpl_contents($filename)
  {
    $output = file_get_contents('templates/'.$filename . '.tpl');
    $regex = '/\[\[([a-zA-Z0-9_]+)\]\]/';
    while(preg_match($regex, $output)) {
      $output = preg_replace_callback(
        $regex, 
        array($this, "cb_replace")
        ,$output);
    }
    return $output;
  }

  // Callback method for {{{preg_replace_callback}}} used
  // inside the {{{get_file_contents}}} method. 
  function cb_replace($matches) {
    $dico = array(
    'filename' => $this->filename
    );
    return $dico[$matches[1]];
  }

  // Take the list of paired **sections** two-tuples and split into two
  // separate lists: one holding the comments with leaders removed and
  // one with the code blocks.
  function split($sections) {
    $docs_blocks = array();
    $code_blocks = array();
    foreach($sections as $sec) {
      $docs = implode("\n",$sec[0]);
      $code = implode("\n",$sec[1]);
      $docs_blocks[] = $docs;
      $code_blocks[] = $code;
    }
    return array($docs_blocks, $code_blocks);
  }

  // The main method
  function build_doc()
  {
    // Separate docs & source
    $res = $this->split($this->parse());
    $db  = $res[0]; // docs blocks
    $cb  = $res[1]; // code blocks

    // Computes the lenght
    $nb = max(count($db), count($cb));

    // Our output will contain sections
    $out = array();

    // The result is starting here: we place the HTML header 
    $result = $this->get_tpl_contents('head');

    // Iterate over all found sections
    for($i=0;$i < $nb; $i++) {
      $source = $db[$i];
      //echo "'$source'\n";
      // Process the docs with **Creole** markup
      if ($this->markup_engine == 'creole') {
        $cr = new creole();
        $out['doc'][$i] = $cr->parse($source);     
      }
      else { // We use Markdown
        $out['doc'][$i] = Markdown($source);        
      }

      // Process the highlighting of the source code with **Prism**
      $out['src'][$i] = $this->hil->from_string($cb[$i]);

      // Now build each section
      $doc = $out['doc'][$i];
      $src = $out['src'][$i];

      // Using a Heredoc string with variables inside
      // to build each section inside an HTML table
      $section =<<<EOF
<tr id='section-$i'> 
  <td class=docs>
    $doc
  </td> 
  <td class=code> 
    <div class='highlight'><pre class="code">$src</pre></div> 
  </td> 
</tr>
EOF;

      $result .= $section;
    } // end for
    $result .= $this->get_tpl_contents('foot');
    return $result;
  }

  // Save the doc to HTML given a {{{$output_name}}} filename
  // **Note:** the {{{.html}}} extension is automatically added.
  function save_doc($output_name)
  {
    $res = $this->build_doc($this->source);
    $out = $output_name . '.html';
    $fp = fopen($out, 'w');
    fwrite($fp,$res);
    fclose($fp);
    echo "Your file '$out' has been saved.\n  Thanks for using Pocco.\n";
  }

}

?>

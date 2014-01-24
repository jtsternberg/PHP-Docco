<?php

/*
Prism: a syntax highlighting engine for PHP under (MIT license).
@author: C.Kibleur

Copyright (C) 2011 Kibleur Christophe.

The MIT License

Copyright (c) <year> <copyright holders>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

// = HtmlFormatter Class
//
// The Prism class needs a formatter instance to work with, ie:
// {{{$p = new Prism($formatter, $language)}}}
//
// The following is one specially handling HTML.
// It has no arguments.
class HtmlFormatter {
  // {{{$parent}}} will be set to a Prism's instance.
  protected $parent = NULL;

  // This is the method used to output one line in HTML
  // if a rule matched without declaring groups inside.
  // It escapes special chars and takes care of URLs
  function highlight($text, $the_style) {
    $q = htmlentities($text, ENT_NOQUOTES, 'UTF-8');
    if ($the_style == 'Root') {$ln = $q;}
    elseif ($the_style == 'Url') {
        $ln = "<span class=\"$the_style\"><a href=\"$q\">$q</a></span>";
    }
    else {
      $ln = "<span class=\"$the_style\">$q</span>";
    }
    $k = $this->parent;
    echo $k;
    //$k->last_tokens[] = $text;
    return $ln;
  }

  // This is the method used to output one line in HTML
  // if a rule matched by declaring groups inside.
  // It escapes special chars and takes care of URLs
  // because it uses {{{highlight}}}.
  function highlight_with_groups($me) {
    // Build an array with all matching groups
    $res = array();
    foreach ($me[4] as $km) {
      $res[] = $km[0];
    }
    array_shift($res); // we don't want group 0
        
    $out = '';
    $st = $me[3]['style'];
    $nb = count($res);

    for ($n=0;$n<$nb; $n++) {
      $out .= $this->highlight($res[$n], $st[$n]);
    }
    return $out; 
  }
}

// = Prism Class
//
// {{{Prism}}}: This is the main class, ie the one you'll use in practice.
// It needs a formatter (HTMLFormatter atm) and a language (String) as constructor.
class Prism {
  // If set, {{{$reduce}}} will beautify the HTML output
  // by factorizing redondant tags.
  protected $reduce = TRUE;
  // Our formatter instance
  public    $formatter = NULL;
  // The source language (String)
  public    $language = NULL;
  // {{{$me}}} stands for matched element
  public    $me = array(); // our matched object
  // {{{allRules}}} contains the syntax rules for a given language 
  public    $allRules = NULL;
  // {{{states}}} a stack for the internal state machine.
  public    $states = array();
  // {{{styles}}} a stack for managing the output styles. 
  public    $styles = array('Root');
  // Some syntax rules may have a {{{transit}}} attribute
  // telling what to colorize in between transitions.
  public    $was_transit = FALSE;
  // A reference to the last used style 
  public    $lastStyle;
  // The generated output
  protected $out = array();
  // Keeps track of the last tokens
  public    $last_tokens = array();
  // For performance reason, a cache system is used to keep track of wich rules
  // matched the first time on a given line. It is reseted once we change line. 
  protected $cache = array();
  
  // Constructor
  // # **$formatter** (HTMLFormatter instance) 
  // # **$language** (String).
  function __construct($formatter, $language) {
    // Sets the given formatter
    // Sets the formatter's parent to self
    // Sets the given language
    $this->formatter->parent = $this;
    $this->formatter         = $formatter;
    $this->language          = $language;

    // Loads a syntax-rule file dynamically.
    // 
    // This is a bit tricky, but it works.
    // We need to write the following because overwise,
    // using the same language 2 times will raise an error.
    global ${$language};
    include_once("langs/lang_" . $language . '.php');
    $this->allRules = ${$language};
    
    // We'll start parsing within the {{{$language_root}}} state     
    $this->states[] = $language . '_root';
  }

  // Calling this method with a given source code as a string 
  // will ouput the result.
  function from_string($code_in_text) {
    $code_in_text = preg_replace("/\r\n?/", "\n", $code_in_text);
    return $this->from_list(explode("\n",$code_in_text));
  }

  // Calling this method with a given source code as a list 
  // will ouput the result.
  function from_list($code_in_array)
  {
    $num_of_lines = 0;
    $out = array();
    // This may seem hawful at first, but if a line is really big
    // (ie the jquery minified version has a line 83996 chars long)
    // PHP/Prism will get confused. We will then cut it in chunks of
    // {{{$chars_line_limit}}} chars long.
    $chars_line_limit = 500;
    foreach($code_in_array as $line)
      {
        if (strlen($line)>$chars_line_limit) {
          $parts = explode("\n", wordwrap($line, $chars_line_limit, "\n"));
          $sortie = '';
          foreach ($parts as $p) {
            $sortie .= $this->highlightLine($p);
          }
          $out[] = $sortie;
        }
        else {
          $num_of_lines++ ;
          $out[] = $this->highlightLine($line . "\n");
        }
      }

    $output = implode("",$out);

    // Reduce the HTML ouput if the $reduce parameter is set to TRUE
    // All thanks goes to Shawncplus user at Reddit.
    if ($this->reduce) {
      $regex = '#<span class="([A-Za-z_]+?)">([^<]+?)\</span>(\s*)<span class="\1">([^<]+?)</span>#';
      while(preg_match($regex, $output)) {
        $output = preg_replace($regex, '<span class="\1">\2\3\4</span>', $output);
      }
    }
    return rtrim($output);
  }

  // This allows Prism to skip between syntax rules
  function changeLanguage($language) {
    include_once("langs/lang_" . $language . '.php');
    // Set the new rules for the new language
    $this->language  = $language;
    $this->states[] = $language . '_root' ;
  }
    
  // Given a syntax-rule, checks wether the regexp pattern matches.
  //
  // If so, it will return an array of the following form:\\
  // {{{[$left, $m, $right, $rule, $matches]}}} where
  // $left is the part before the match
  // $right the part after the match
  // $rule is the rule who matched against the given subject
  // $matches an array containing all the needed infos about the match.
  //
  // If not, returns FALSE.
  function find_best_match($rule, $subject) {
    if (preg_match( $rule['pattern'] , $subject, $matches, PREG_OFFSET_CAPTURE)) {
      $res = array();
      foreach ($matches as $km) {
        $res[] = $km[0];
      }
      array_shift($res);

      $m = $matches[0][0];
      $start = $matches[0][1];
      $len = strlen($m);

      $left  = substr($subject, 0, $start);
      $right = substr($subject, $start+$len);

      return array($left, $m, $right, $rule, $matches);
    }
    else { return FALSE; }
  }
  
  // Given a $line of text, this method will try to match all the regexps inside 
  // a given syntax rule (for a given state) of a language and choose the best one.
  // The //best// one is the one with the lowest prefix or
  // if two rules matches at the same prefix, the one with the longuest suffix.
  // We can shortcut the last behaviour if a rule declares a 'prio' attribute (set to TRUE).
  function getNextMatch($line)
    {
      $lowest  = strlen($line);
      $longest = 0;
      $match   = FALSE;
      
      // Set the cache: if it's the first time we match against the line, the we use all the language
      // rules from a given state. If a rule match, then it is added to the cache for the next time
      // we scan the rest of the line.
      if (empty($this->cache)) {$rules_of_current_state = $this->allRules[end($this->states)];}
      else {$rules_of_current_state = $this->cache;}

      // The return value is an array of the form {{{[0:left, 1:matched, 2:right, 3:rule, 4:matches]}}}
      // In case no rule match the line, the default value is set to the following array.
      $ret = array('', $line, '', array('style' => end($this->styles)), null);
      $m = array(); // this will be set inside the following if statement

      foreach ($rules_of_current_state as $k => $r) {
        if ($m = $this->find_best_match($r, $line)) {
          // We are sure a rule matched. We have to determine if it is the best one.
          $m0    = strlen($m[0]); // left match length
          $m1    = strlen($m[1]); // match length
          $prio  = isset($r['prio']) ? $r['prio'] : FALSE;

          // We put the rule in the cache only if its key is not already registred
          //
          // //You should then take care of the uniqueness of each syntax rule key.//
          if (!array_key_exists($k, $this->cache)) { $this->cache[$k] = $r; }
                    
          if (($m0 < $lowest OR ($m0 == $lowest && $m1 > $longest)) OR ($m0 == $lowest && $prio == TRUE)) {
            $match = TRUE;
            $ret = $m;
            $lowest = $m0;
            $longest = $m1;
          }
        }
      }
      
      // If a candidate rule has been taken and has a callback attribute,
      // we apply the callback imediatly. 
      // 
      // A callback function takes 2 parameters:
      // # the Prism instance
      // # the {{{[0:left, 1:matched, 2:right, 3:rule, 4:matches]}}} array we talked above.
      // and returns a new array of the same form. It is then possible inside a callback to
      // add syntax rules dynamically. 
      if ($match) {
        // Add this token to the list
        $this->last_tokens[] = $m[1];
        $ret = $this->process_rule_callback($ret);
      }
      return $ret; // returns an array( 0:left, 1:matched, 2:right, 3:rule, 4:matches)
  }

  // Applies a callback method to a given rule if a callback attribute is set.
  // The argument $ret is an array {{{[0:left, 1:matched, 2:right, 3:rule, 4:matches]}}}
  // and each callback should return it back.
  function process_rule_callback($ret) {
    $rule = $ret[3];
    // Check for any callback method inside the rule
    if (array_key_exists('callback',$rule)) {
      // We get the name of the callback method and then apply it
      $met = $rule['callback'];
      $ret = $met($this, $ret);
    }
    return $ret;
  }
  
  // The main routine. Given a {{{$line}}}, we first check for the best match.
  //
  // If found then:
  // * the prefix is coloured with the last used style;
  // * the match is coloured with the rule style(s) [//Several styles are possible if the style attribute is an array//];
  // * the {{{$line}}} is set to the remaining part, and we iterate the procedure until no rule matches.
  // If no found, 
  function highlightLine($line) {
      if ($line == '') { return '';}
      $me = array();
      $out = array();

      while ($me = $this->getNextMatch($line)) {
          if ($line == '') { break;}

          // Highlights the part before the match (prefix)
          if ($me[0]) {
            $sty = $this->get_current_style();
            $out[] = $this->formatter->highlight( $me[0], $sty);
          }

          // Are we highlighting with regexp groups or not ?
          $with_groups = false;
          if (gettype($me[3]['style']) == 'array') {
            $with_groups = true;
          }

          // Highlights with groups
          if ($with_groups) {
            $out[] = $this->formatter->highlight_with_groups($me);
            $this->handle_push_pop($me);
          }
          // Highlights without groups
          else { 
            $out[] = $this->formatter->highlight($me[1], $me[3]['style']);
            $this->handle_push_pop($me);
          }

          if (array_key_exists("goto", $me[3])) {
            $this->changeLanguage($me[3]['goto']);
          }
          $line = $me[2]; // $line is set to the remaining part (the one after the match).
      }
      // We must empty the cache now!
      $this->cache = array();
      // Keeps track of the result
      $this->out[] = implode( '', $out );
      return end($this->out); // implode( '', $out );
    }
  
  // This method handles the state machine
  // If an {{{action}}} attribute is set inside a rule, then it is a state change.
  //
  // Particular cases: 
  // # if attribute is set to the string {{{#pop}}}, then the current state
  // is popped from the stack.
  // # if attribute is set to an array, then we push all the given states to the stack.
  // TODO: implement '#pop_n' to pop() n times from the stack.
  function handle_push_pop($me) {
      $rule = $me[3];
      // We enter a new state, so we have to save the laststyle
      if (array_key_exists("action", $rule)) {
        $act = $rule['action'];

        if ($act != "#pop") {
          switch (gettype($act)) {
              case 'array':
                foreach ($act as $a) {
                  $this->states[] = $a;
                }
                $this->cache = array();
                break;
              default:
                $this->states[] = $act;
                $this->cache = array();
                break;
          }
          $this->update_style($me);
        }
        else {
          if (count($this->states) > 1) {
            $this->cache = array();
            array_pop($this->states);}
          if (count($this->styles) > 1) {array_pop($this->styles);}
        }

      }
  }
  
  // Takes care of the styles. If a {{{transit}}} attribute is found, then it will
  // be used by default.
  // If {{{style}}} attribute is an array, the next applied style will be the last array element.
  function update_style($me)
  {
    // When we change state, the next style is set to the previous one by default.
    // Using transit rule, you can overide this behaviour.
    $rule = $me[3];

    if (array_key_exists('transit',$rule)) {
      $this->styles[] = $rule['transit'];
      $this->was_transit = TRUE;
      return;
    }
    switch (gettype($rule['style'])) {
      case 'array':
        $this->styles[] = end($rule['style']); //[count($rule['style'])-1]);
        break;
      default:
        $this->styles[] = $rule['style'];
        break;
    }
  }
  
  // This method is only used by {{{highlightLine}}}
  // This reset Prism's {{{lastStyle}}} attribute to NULL if it was already set.
  function get_current_style()
  {
    if (!is_null($this->lastStyle)) {
      $r = $this->lastStyle;
      $this->lastStyle = NULL;
    }
    else {$r= end($this->styles);}
    return $r;
  }
}

?>
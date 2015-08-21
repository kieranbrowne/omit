<?php

function omit($string) {
  $tags = explode('>', $string);
  echo oParse($tags);
}

function oParse($tags) {
  $tag = array_shift($tags); // pop tag at top of nest
  if(sizeof($tags) > 0)
    return startTag($tag) . oParse($tags) . endTag($tag);
  else
    return startTag($tag) . endTag($tag);
  
}

function startTag($str) { return '<' . parseId(parseClass($str)) . '>'; }
function endTag($str) { return '</' . tagOnly($str) . '>'; }
function parseId($str) { return str_replace('#', ' id=', $str); }
function parseClass($str) { return str_replace('.', ' class=', $str); }
function tagOnly($str) { return preg_split('/[^\w]/', $str)[0]; }
function oMult($s) { return ((strpos($s,'*')!==false) ? 
  intval(preg_replace('/[^0-9+]/','',$s)) : 1); }
function parseAsterisk($s) { 
  return implode('+', array_fill(0,oMult($s),preg_replace('/[0-9+\*]+/', '', $s)));
}

omit('div#wrapper>span.title>ul>li*3');

?>

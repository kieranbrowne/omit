<?php

function omit($string, $content = []) {
  $tags = explode('>', $string);
  echo oParse($tags);
}

function oParse($tags) {
  $tag = array_shift($tags); // pop tag at top of nest
  return implode('',array_map(function($t) use ($tags) {
    return startTag($t) . parseContent($t) . ((sizeof($tags)>0)?oParse($tags):'') . endTag($t);
  },parsePlus(parseAsterisk($tag))));
}

function startTag($str) { return '<' . parseId(parseClass($str)) . '>'; }
function endTag($str) { return '</' . tagOnly($str) . '>'; }
function parseId($str) { return str_replace('#', ' id=', $str); }
function parseClass($str) { return str_replace('.', ' class=', $str); }
function tagOnly($str) { return preg_split('/[^\w]/', $str)[0]; }
function oMult($s) { return ((strpos($s,'*')!==false) ? 
  intval(preg_replace('/[^0-9+]/','',$s)) : 1); }
function parsePlus($s) { return explode('+',$s); }
function parseAsterisk($s) { 
  return implode('+', array_fill(0,oMult($s),preg_replace('/[0-9*]+/', '', $s)));}
function oContent($s,$content) {
  return implode('+', array_map(function($c) use ($s) { return preg_replace('/[\$]/','',$s) .'{'.$c.'}'; },$content));}
function parseContent($s) { preg_match('~{(.*?)}~',$s, $out);
  return ((sizeof($out)>0) ? $out[1] : ''); }


omit('div#wrapper>span.title{Title}+ul>li*3');

assert(parsePlus('span.class{this}+ul') === ['span.class{this}','ul'], "parsePlus");
assert(parseContent('span.class{this is the content}') === 'this is the content', "parseContent");
assert(parsePlus(parseAsterisk('span.title{Title}+ul')) === ['span.title{Title}','ul']);
?>

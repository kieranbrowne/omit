<?php

function omit($string, $content = []) {
  $tags = explode('>', $string);
  echo oParse($tags);
}

function oParse($tags) {
  $parts = parsePlus(parseAsterisk(array_shift($tags)));
  $last = array_pop($parts);
  return array_reduce($parts,
    function ($carry, $item) use ($tags) { return $carry . toHtml($item); } )
    . startTag($last) . ((sizeof($tags)>0)?oParse($tags):'') .oTag($last)['end']; 
}
function toHtml($tag) { return startTag($tag) . parseContent($tag) . oTag($tag)['end']; }

function startTag($str) { return '<' . parseId(parseClass($str)) . '>'; }
//function endTag($str) { return '</' . tagOnly($str) . '>'; }
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
function oTag($t) { return array( 'name' => tagOnly($t), 'id' => '', 'class' => '', 'attr' => '', 'content' => '', 'start' => '', 'end' => '</'.tagOnly($t).'>'); }

?>

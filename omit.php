<?php

function omit($string, $content = []) {
  $tags = explode('>', $string);
  echo oParse($tags,$content);

}

function oParse($tags,$content) {
  $parts = parsePlus(oContent(parseAsterisk(array_shift($tags),$content),$content));
  $last = array_pop($parts);
  return array_reduce($parts,
    function ($carry, $item) use ($tags) { return $carry . toHtml($item); } ) . oTag($last)['start'] . parseContent($last) . ((sizeof($tags)>0)?oParse($tags,$content):'') .oTag($last)['end']; 
}

function toHtml($tag) { return oTag($tag)['start'] . parseContent($tag) . oTag($tag)['end']; }

function startTag($str) { return '<' . parseId(parseClass($str)) . '>'; }
//function endTag($str) { return '</' . tagOnly($str) . '>'; }
function parseId($str) { return str_replace('#', ' id=', $str); }
function parseClass($str) { return str_replace('.', ' class=', $str); }
function tagOnly($str) { return preg_split('/[^[:alnum:]]+/', $str)[0]; }
function oMult($s) { return ((strpos($s,'*')!==false) ? 
  intval(preg_replace('/[^0-9+]/','',$s)) : 1); }
function parsePlus($s) { return explode('+',$s); }
function parseAsterisk($s) { 
  return implode('+', array_fill(0,oMult($s),preg_replace('/^[*]+/', '', $s)));}
function oContent($s,$content) { return ((strpos($s,'$')!==false)?implode('+', array_map(function($c) use ($s) { return preg_replace('/[\$]/','',$s) .'{'.$c.'}'; },$content)):$s);}
function parseContent($s) { preg_match('~{(.*?)}~',$s, $out);
  return ((sizeof($out)>0) ? $out[1] : ''); }
function oTag($t) { return array( 'name' => tagOnly($t), 'id' => '', 'class' => '', 'attr' => '', 'content' => '', 'start' => '<'.tagOnly($t).'>', 'end' => '</'.tagOnly($t).'>'); }

?>

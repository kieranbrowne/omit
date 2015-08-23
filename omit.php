<?php

function omit($string, $content = []) {
  $tags = explode('>', $string);
  echo oParse($tags,$content);
}

function oParse($tags,$content) {
  $parts = explode('+',(oFunc(oContent(parseAsterisk(array_shift($tags)),$content),$content)));
  $last = array_pop($parts);
  return array_reduce($parts,
    function ($carry, $item) use ($tags) { return $carry . toHtml($item); } ) . startTag($last) . oGet($last) . ((sizeof($tags)>0)?oParse($tags,$content):'') .endTag($last); 
}

function toHtml($tag) { return startTag($tag) . oGet($tag) . endTag($tag); }
function startTag($str) { return '<' . oTag($str)['name'] . (ohas($str,'#')?' id="'.oTag($str)['id'].'"':'') .(ohas($str,'.')?' class="'.oTag($str)['class'].'"':'') . '>'; }
function endTag($str) { return '</' . oTag($str)['name'] . '>'; }
//function parseId($str) { return str_replace('#', ' id=', $str); }
//function parseClass($str) { return str_replace('.', ' class=', $str); }
//function tagOnly($str) { return preg_split('/[^[:alnum:]]+/', $str)[0]; }
//function idOnly($str) { return preg_split('/#/', $str)[1]; }
function oMult($s) { return ((strpos($s,'*')!==false) ? intval(preg_replace('/[^0-9+]/','',$s)) : 1); }
//function parsePlus($s) { return explode('+',$s); }
function parseAsterisk($s) { return implode('+', array_fill(0,oMult($s),preg_replace('/^[*]+/', '', $s)));}
function oContent($s,$content) { return ((strpos($s,'$')!==false)?implode('+', array_map(function($c) use ($s) { return preg_replace('/[\$]/','',$s) .'{'.$c.'}'; },$content)):$s);}
function oGet($str,$start='{',$end='}') {
  return ((strpos($str,str_replace('\\','',$start))!==false)?preg_split('/'.$end.'/',preg_split('/'.$start.'/',$str)[1])[0]:'');}
function oTag($t) { 
  return array( 
  'name' => preg_split('/[^[:alnum:]]+/', $t)[0], 
  'id' => oGet($t,'#','\W'), 
  'class' => oGet($t,'\.','\W'), 
  'attr' => '', 
  'content' => '', 
  ); 
}
function oHas($tag,$str) { return ((strpos($tag,$str)!==false)?true:false); }
function oFunc($t,$content) { return ((strpos($t,'|')!==false)?implode('+',array_map(function($c) use ($t) { return preg_replace('/\|([^\|]+)\|/','',$t).'{'.$c.'}';},array_map(oGet($t,'\|','\|'),$content))):$t);}
function strInside($str,$start,$end){
  return ((strpos($str,str_replace('\\','',$start))!==false)?preg_split('/$end/',preg_split('/'.$start.'/',$str)[1])[0]:'fail');}

//echo preg_replace('/\|([^\|]+)\|/','','div|this|that');
//echo oFunc('div|getUrl|',[['url'=>'www.github.com']]);
?>

<?php

function omit($string, $content = []) {
  echo implode('',array_map(function($x) use ($content) { 
    $tags = explode('>', $x);
    return oParse($tags, $content); }, parseParentheses($string)));
}

function oParse($tags,$content=[]) {
  $parts = explode('+',(oFunc(oContent(parseAsterisk(array_shift($tags)),$content),$content)));
  $last = array_pop($parts);
  return array_reduce($parts,
    function ($carry, $item) use ($tags) { return $carry . toHtml($item); } ) . startTag($last) . oGet($last) . ((sizeof($tags)>0)?oParse($tags,$content):'') .endTag($last); 
}

function toHtml($tag) { return startTag($tag) . oGet($tag) . endTag($tag); }
function startTag($str) { return '<' . oTag($str)['name'] . (ohas($str,'#')?' id="'.oTag($str)['id'].'"':'') .(ohas($str,'.')?' class="'.oTag($str)['class'].'"':'') . '>'; }
function endTag($str) { return '</' . oTag($str)['name'] . '>'; }
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

function parseParentheses($str) {
  if(strpos($str,'(') !== false) {
    $open = strpos($str,'(');
    $closed = strrpos($str,')');
    $head = substr($str, 0, $open);
    $midd = substr($str, $open+1, $closed-$open-1);
    $tail = substr($str, $closed,-1);
    return (($tail !== '')?array_merge([$head],parseParentheses($midd),[$tail]):array_merge([$head],parseParentheses($midd)));
  }
  else {return [$str];}
}

echo '<br><br>';
//var_dump(array_map(function ($x) { 
//  $tags = explode('>', $x);
//  return oParse($tags); }, parseParentheses('this>ul>(li>a{this})')));
//echo "this test:";
omit('this>ul>(li>a{this})');
omit('div(this)');
?>

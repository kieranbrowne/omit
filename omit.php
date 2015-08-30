<?php

function omit($string, $content = []) {
  echo parseNest($string,$content);
}

function oParse($tags,$content=[]) {
  $parts = explode('+',(oFunc(oContent(parseAsterisk(array_shift($tags)),$content),$content)));
  $last = array_pop($parts);
  return array_reduce($parts,
    function ($carry, $item) use ($tags) { return $carry . toHtml($item); } ) . startTag($last) .  ((sizeof($tags)>0)?oParse($tags,$content):'') .endTag($last); 
}

function toHtml($tag) { return startTag($tag) . endTag($tag); }
function startTag($str) { return '<' . oTag($str)['name'] . (ohas($str,'#')?' id="'.oTag($str)['id'].'"':'') .(ohas($str,'.')?' class="'.oTag($str)['class'].'"':'') .(ohas($str,'[')?' '.oTag($str)['attr']:''). '>'.oGet($str); }
function endTag($str) { return '</' . oTag($str)['name'] . '>'; }
function oMult($s) { return ((strpos($s,'*')!==false) ? intval(preg_replace('/[^0-9+]/','',$s)) : 1); }
//function parsePlus($s) { return explode('+',$s); }
function parseAsterisk($s) { return implode('+', array_fill(0,oMult($s),preg_replace('/^[*]+/', '', $s)));}
function oContent($s,$content) { return ((strpos($s,'$')!==false)?implode('+', array_map(function($c) use ($s) { return preg_replace('/[\$]/','',$s) .'{'.$c.'}'; },$content)):$s);}
function oGet($str,$start='{',$end='}') {
  return ((strpos($str,str_replace('\\','',$start))!==false)?preg_split('/'.$end.'/',preg_split('/'.$start.'/',$str)[1])[0]:'');}
function oTag($t) { 
  preg_match_all("/\[([^\]]*)\]/", $t, $attrs);
  return array( 
  'name' => preg_split('/[^[:alnum:]]+/', $t)[0], 
  'id' => oGet($t,'#','\W'), 
  'class' => oGet($t,'\.','\W'), 
  'attr' => implode(' ',array_map(function($x){return pre($x,'=').'="'.post($x,'=').'"';},$attrs[1])), 
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

function occurences($char,$str) { 
  return array_values(array_filter(array_map(function($x,$y)use($char){return (($x==$char)?$y:false);},str_split($str),array_keys(str_split($str)))));
}
function mapIndexes($list,$str) {
  return array_combine($list,array_map(function($sub) use ($str) { return strpos($str,$sub);},$list)); }
function firstOf($list,$str) {
  $array = array_filter(mapIndexes($list,$str), function($x){return $x !== FALSE;}); asort($array);
  return key($array);
}

function pre($str,$char){return substr($str,0,strpos($str,$char));}
function post($str,$char){return substr($str,strpos($str,$char)+1);}
function flip($char){ return ['('=>')',')'=>'(','['=>']','{'=>'}'][$char];}
function match($str,$char) {
  $depth = 0;
  $i = 0;
  foreach (str_split($str) as $s) {
    if(($depth == 1) && ($s == flip($char))) return $i;
    if($s == $char) $depth++;
    if($s == flip($char)) $depth--;
    $i++;
  }
}
function inParen($str) {
  return substr($str,strpos($str,'(')+1,match($str,'(')-strpos($str,'(')-1); }
  
function parseNest($str,$content=[]) {
  switch (firstOf(['(','>','+'],$str)) {
  case '(': 
    return startTag(pre($str,'(')).parseNest(inParen($str)).parseNest(substr($str,match($str,'(')+1)).endTag(pre($str,'(')); break;
  case '>': return startTag(pre($str,'>')).parseNest(post($str,'>')).endTag(pre($str,'>')); break;
  case '+': return startTag(pre($str,firstOf(['(','>','+'],substr($str,1)))).endTag(pre($str,firstOf(['(','>','+'],substr($str,1)))).parseNest(post($str,firstOf(['(','>','+'],substr($str,1)))); break;
  default: return startTag($str).endTag($str); break;
  }
}
echo '<br><br>';
//echo $test[match($test,'(')].' at '.match($test,'(');

print_r(parseNest('div>span[type=blank][href=dis](div.class{winn}>div#what?)span+spin>this{that}'));
var_dump(oTag('this[src=blah][sauce=blar]')['attr']);
?>

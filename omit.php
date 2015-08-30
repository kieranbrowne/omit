<?php

function omit($string, $content = []) {
  echo oParse(explode('>',$string),$content);
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
//function oMatch($str,$char,$match,$offset=0) {
  //if(firstOf([$char,$match],post($str,$char)) === $char)
    //return oMatch(substr_replace($str,'',occurences('(',$str)[1],oMatch(),$char,$match) + strpos($str,$match)+1;
  //else return strpos($str,$match);
//}
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
  
function parseNest($str) {
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

print_r(parseNest('div>span(this>that)span+spin>this'));
echo 'test of inParen: '.inParen('this(that()this)that()');
?>

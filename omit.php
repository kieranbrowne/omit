<?

$omit_register = [];

function omit($oStr, $content = []) {
  return parseNest($oStr,$content);
  if(!is_object($content))
    return parseNest($oStr,(array) $content);
  else 
    return parseNest($oStr,[$content]);
}

function O($oStr, $content = []) {
  return omit($oStr,$content);
}

/* return a mappable omit function */
function ofn($oStr) {
  return function ($content = []) use ($oStr) {
    return omit($oStr, $content);
  };
}

// register an O function;
// after this it can be called in any other O function.
function oReg($oStr,$name) {
  global $omit_register;
  $omit_register[$name] = ofn($oStr);
  /* $omit_register[$name] = $oStr; */
}


function startTag($str,$c) { 
  /* var_dump($str); */
  $str = oFunc($str,$c);
  $str = expandVars($str,$c);
  /* var_dump($str); */
  if(depthBool(function($x){return $x=='>' || $x=='+';},$str)) {
    return parseNest($str,$c);
  }
  return (strlen($str)!==0?'<' . oTag($str)['name'] . (ohas($str,'#')?' id="'.oTag($str)['id'].'"':'') .(ohas($str,'.')?' class="'.oTag($str)['class'].'"':'') .(ohas($str,'[')?' '.oTag($str)['attr']:''). '>'.oTag($str,$c)['content']:'');
}

function endTag($str,$c) { return '</' . oTag($str)['name'] . '>'; }

function oHas($tag,$str) { 
  if(!is_string($tag)) return false;
  return strpos($tag,$str)!==false; }

function oGet($str,$start='{',$end='}') {
  return ((strpos($str,str_replace('\\','',$start))!==false)?preg_split('/'.$end.'/',preg_split('/'.$start.'/',$str)[1])[0]:'');}

function oMatch($str,$regex) {
  preg_match_all($regex,$str,$out);
  return (sizeof($out)==2?$out[1]:$out[0]);
}

function oTag($t,$c=[]) { 
  preg_match_all("/\[([^\]]*)\]/", $t, $attrs);
  return array( 
    'name' => preg_split('/[^[:alnum:]]+/', $t)[0], 
    'id' => oGet($t,'#','[^-_a-z-A-Z-0-9]'), 
    'class' => implode(' ',oMatch(preg_replace("/\[([^\]]*)\]/",'',$t),'/\.([-_a-zA-Z0-9]*)/')),
    'attr' => implode(' ',array_map(function($x){return pre($x,'=').'="'.str_replace('}','',str_replace('{','',post($x,'='))).'"';},$attrs[1])), 
    'content' => oGet(preg_replace('/\[([^\]]*)\]/','',$t)), 
    /* 'content' => (oHas($t,'$')?getContent('',$c):oGet($t)), */ 
  ); 
}

function oFunc($t, $content = []) {
  if (oHas($t,'%')) {
    /* preg_match('/\%([^\%]+)\%/',$t, $f); */
    /* matchFn($t) */
    return oFunc(str_replace('%'.matchFn($t).'%',
      maybeJoin(expandFns(matchFn($t), $content))
      ,$t));
  } else return $t;
}

function maybeJoin($x) {
  if(is_array($x))
    return implode('+',$x);
  else return $x;
}

function expandFns($str, $content = []) {
  $parts = depthSplit($str,'.');
  /* var_dump($parts); */
  $last = array_pop($parts);

  /* var_dump($last); */

  if(oHas($last,'map(')) {
    $mapstr = inParen($last);
    /* var_dump($mapstr); */
    /* var_dump(array_map(ofn($mapstr),expandFns(implode('.',$parts),$content))); */
    /* return implode('',array_map(ofn($mapstr),expandFns(implode('.',$parts),$content))); */

    return '('.implode(')+(',
      array_map(function($x) use ($mapstr) {
        return expandVars($mapstr,$x);
      },$content)).')';
    /* global $omit_register; */

    /* if(array_search($mapstr,array_keys($omit_register)) !== false) { */
    /*   return implode('', */
    /*     array_map($omit_register[$mapstr] */
    /*       ,$content)); */
    /* }else{ */
    /*   return implode('+', */
    /*     array_map( */
    /*       function($x) use ($last) { */
    /*         return expandVars(oGet($last,'\(','\)'),$x); */
    /*       }, */
    /*       $content)); */
    /* } */
  }
  /* var_dump('got through'); */
  /* var_dump($last); */

  if (oHas($last,'$')){ 
    return getContent(oGet($last,'\$','\$'),$content);
    /* return $last; */
    /* var_dump(getContent('',$content)); */
    /* if(is_array($last)) return implode('+',$last); */
    /* if(empty($parts) && is_array($last)) return implode('+',$last); */
  }


  if(is_callable($last)) {
    /* var_dump($last); */
    /* var_dump(call_user_func($last,implode('.',$parts))); */
    if (count($parts) > 0)
      return call_user_func($last,expandFns(implode('.',$parts),$content));
    else return call_user_func($last);
  } else {
    return $last;
  }
}


function expandVars($str, $content = []) {
  if (oHas($str,'$')) {
    preg_match_all('/\$([^\$]*)\$/',$str, $f);
    for ($i = 0; $i < sizeof($f[0]); $i++) {
      $str = str_replace($f[0][$i], 
          getContent($f[1][$i],$content)
          ,$str);
    }
    return $str;
  } 
  
  else return $str;
}
function getContent($key,$content) {
  if ($key == ''){
    return $content;
  } else if (array_search($key,array_keys($content)) !== false) {
    return $content[$key];
  } else {
    trigger_error("The key '$key' was not included in content");
    return null;
  }
}




function mapIndexes($list,$str) {
  return array_combine($list,array_map(function($sub) use ($str) { return strpos($str,$sub);},$list)); }

function firstOf($list,$str) {
  $array = array_filter(mapIndexes($list,$str), function($x){return $x !== FALSE;}); asort($array);
  return key($array);
}

function pre($str,$char){return (oHas($str,$char)?substr($str,0,strpos($str,$char)):'');}

function post($str,$char){return (oHas($str,$char)?substr($str,strpos($str,$char)+strlen($char)):'');}

function flip($char){ return ['('=>')',')'=>'(','['=>']','{'=>'}'][$char];}

function match($str,$char) {
  $depth = 0; $i = 0;
  foreach (str_split($str) as $s) {
    if(($depth == 1) && ($s == flip($char))) return $i;
    if($s == $char) $depth++;
    if($s == flip($char)) $depth--;
    $i++;
  }
}

function matchFn($str) {
  $depth = 0; $out = []; $on = false;
  foreach (str_split($str) as $s) {
    if((($on==true) && ($depth == 0)) && ($s == '%')) break;
    if ($on) array_push($out,$s);
    if(in_array($s,['(','{','['])) $depth++;
    if(in_array($s,[')','}',']'])) $depth--;
    if($s == '%') $on = true;
  }
  return implode('',$out);
}
function depthSplit($str,$char) {
  $depth = 0; $group = []; $out = [];
  foreach (str_split($str) as $s) {
    /* array_push($group,$s); */
    if(($depth == 0) && ($s == $char)){
      if(!empty($group)) array_push($out,implode('',$group));
      $group = [];
    } else array_push($group,$s);
    if(in_array($s,['(','{','['])) $depth++;
    if(in_array($s,[')','}',']'])) $depth--;
  }
  if(!empty($group)) array_push($out,implode('',$group));
  return $out;
}

function depthBool($fn,$str) {
  $depth = 0;
  foreach (str_split($str) as $s){
    if($depth == 0 && $fn($s)) return true;
    if(in_array($s,['(','{','['])) $depth++;
    if(in_array($s,[')','}',']'])) $depth--;
  }
  return false;
}

ini_set('memory_limit','1M');

function inParen($str) {
    return ((oHas($str,'(')&&oHas($str,')'))?substr($str,strpos($str,'(')+1,match($str,'(')-strpos($str,'(')-1):$str); }

function getMatchedParen($str,$type = '(') {
  if(oHas($str,$type))
    return ((oHas($str,$type)&&oHas($str,flip($type)))?substr($str,strpos($str,$type),match($str,$type)-strpos($str,$type)+1):$str);
  else return '';
}

function getTop($oStr) {
  //substitue parentheses
  $id2 = uniqid();
  $save2 = getMatchedParen($oStr);
  $sub = str_replace($save2,$id2,$oStr);

  //substitue curly braces
  $id3 = uniqid();
  $save3 = getMatchedParen($sub,'{');
  $sub = str_replace($save3,$id3,$sub);

  //substitue braces
  $id4 = uniqid();
  $save4 = getMatchedParen($oStr,'[');
  $sub = str_replace($save4,$id4,$sub);

  //substitue function parts
  $id = uniqid();
  preg_match('/\%([^\%]+)\%/',$oStr, $safe);
  if(!empty($safe)) $safe = $safe[0]; else $safe = '';
  $sub = @preg_replace('/\%([^\%]+)\%/',$id,$sub);

  // get top part
  preg_match('/^.*?[^>+(]+[>+]/',$sub,$toplevel);
  if(!empty($toplevel[0])) {
    $out = $toplevel[0];
    $out = str_replace($id,$safe,$out);
    $out = str_replace($id4,$save4,$out);
    $out = str_replace($id3,$save3,$out);
    $out = str_replace($id2,$save2,$out);
    return (string) $out;
  } else return (string) $oStr;
}

function parseNest($str,$c) {
  $top = getTop($str);
  $rest = substr($str,strlen($top));
  $splitter = substr($top,-1);
  if($splitter == '>' || $splitter == '+')
    $top = substr(getTop($str),0,-1);

  if(substr($top,0,1) == '(') $top = inparen($top);
  /* var_dump($top,$splitter,$rest,'<br><br>'); */

  switch ($splitter) {

    case '>': 
      return startTag($top,$c).parseNest($rest,$c).endTag($top,$c); break;

    case '+': 
      return startTag($top,$c).endTag($top,$c).parseNest($rest,$c); break;

    default: return startTag($top,$c).endTag($top,$c); break;
  }
}
?>

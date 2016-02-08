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
  if(substr($str,0,1) == '%')
    return oFunc($str,$c);
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
    /* 'content' => oGet(preg_replace('/\[([^\]]*)\]/','',$t)), */ 
    'content' => (oHas($t,'$')?getContentP('',$c):''), 
  ); 
}

function oFunc($t, $content = []) {
  if (oHas($t,'%')) {
    preg_match('/\%([^\%]+)\%/',$t, $f);
    return oFunc(str_replace($f[0],
      maybeJoin(expandFns($f[1], $content))
      ,$t));
  } else return $t;
}

function maybeJoin($x) {
  if(is_array($x))
    return implode('+',$x);
  else return $x;
}

function expandFns($str, $content = []) {
  /* $parts = explode('.',expandVars($str,$content)); */
  $parts = explode('.',$str);
  $last = array_pop($parts);

  /* var_dump($last); */

  if(oHas($last,'map(')) {
    $mapstr = oGet($last,'\(','\)');
    /* var_dump($mapstr); */
    /* var_dump(array_map(ofn($mapstr),expandFns(implode('.',$parts),$content))); */
    if (true) // if string is ostring
    return implode('',array_map(ofn($mapstr),expandFns(implode('.',$parts),$content)));
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
    return getContentP(oGet($last,'\$','\$'),$content);
    /* return $last; */
    /* var_dump(getContentP('',$content)); */
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
function getContentP($key,$content) {
  if ($key == ''){
    return $content;
  } else if (array_search($key,array_keys($content)) !== false) {
    return $content[$key];
  } else {
    trigger_error("The key '$key' was not included in content");
    return null;
  }
}

function getContent($key,$content=[]) {
  /* if ($key == '') return $content[0]; */
  if (($key == '') && is_string($content)){
    return $content;
  }
  if (($key == '') && is_array($content)){
    return $content;
  }
  if(array_search($key,array_keys($content)) !== false) {
    return $content[$key];
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

ini_set('memory_limit','1M');

function inParen($str) {
    return ((oHas($str,'(')&&oHas($str,')'))?substr($str,strpos($str,'(')+1,match($str,'(')-strpos($str,'(')-1):$str); }

function getMatchedParen($str) {
  if(oHas($str,'('))
    return ((oHas($str,'(')&&oHas($str,')'))?substr($str,strpos($str,'('),match($str,'(')-strpos($str,'(')+1):$str);
  else return '';
}

function getTop($oStr) {
  $id = uniqid();
  //substitue function parts
  preg_match('/\%([^\%]+)\%/',$oStr, $safe);
  if(!empty($safe)) $safe = $safe[0]; else $safe = '';
  $sub = @preg_replace('/\%([^\%]+)\%/',$id,$oStr);

  //substitue parentheses
  $id2 = uniqid();
  $save = getMatchedParen($oStr);
  $sub = str_replace($save,$id2,$sub);

  // get top part
  preg_match('/^.*?[^>+(]+[>+]/',$sub,$toplevel);
  if(!empty($toplevel[0])) {
    $out = $toplevel[0];
    $out = str_replace($id2,$save,$out);
    $out = str_replace($id,$safe,$out);
    return (string) $out;
  } else return (string) $oStr;
}

function parseNest($str,$c) {
  /* var_dump(getTop($str)); */

  /* if(!empty($toplevel) && is_string($toplevel[0])) */
  /*   $str = preg_replace('/^.*?[^>+(]+/',oFunc($toplevel[0],$c),$str); */
  /* if(oHas($str,'%')) return parseNest(oFunc($str,$c),$c); */
  /* if(oHas($str,'$')) return parseNest(expandVars($str,$c),$c); */

  /* global $omit_register; */
  /* var_dump(array_keys($omit_register)); */
  /* foreach(array_keys($omit_register) as $key) { */
  /*   $str = str_replace($key,$omit_register[$key],$str); */
  /*   /1* var_dump($omit_register[$key]); *1/ */
  /* } */
  /* $top = expandVars(oFunc(getTop($str),$c),$c); */
  $top = getTop($str);
  $rest = substr($str,strlen($top));
  $splitter = substr($top,-1);
  /* $top = expandVars(oFunc(getTop($str),$c),$c); */
  /* $top = oFunc(getTop($str),$c); */
  /* var_dump($rest); */

  /* switch (firstOf(['>','+'],$top)) { */
  switch ($splitter) {

    case '>': 
      return startTag($top,$c).parseNest($rest,$c).endTag($top,$c); break;

    case '+': 
      return startTag(pre($top,'+'),$c).endTag(pre($top,'+'),$c).parseNest($rest,$c); break;

    default: return startTag($top,$c).endTag($top,$c); break;
  }
}
?>

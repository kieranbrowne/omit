<?

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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
  /* $omit_register[$name] = ofn($oStr); */
  $omit_register[$name] = $oStr;
}


function startTag($str) { 
  return (strlen($str)!==0?'<' . oTag($str)['name'] . (ohas($str,'#')?' id="'.oTag($str)['id'].'"':'') .(ohas($str,'.')?' class="'.oTag($str)['class'].'"':'') .(ohas($str,'[')?' '.oTag($str)['attr']:''). '>'.oTag($str)['content']:''); }

function endTag($str) { return '</' . oTag($str)['name'] . '>'; }

function oHas($tag,$str) { 
  return strpos($tag,$str)!==false; }

function oGet($str,$start='{',$end='}') {
  return ((strpos($str,str_replace('\\','',$start))!==false)?preg_split('/'.$end.'/',preg_split('/'.$start.'/',$str)[1])[0]:'');}

function oTag($t) { 
  preg_match_all("/\[([^\]]*)\]/", $t, $attrs);
  return array( 
    'name' => preg_split('/[^[:alnum:]]+/', $t)[0], 
    'id' => oGet($t,'#','\W'), 
    'class' => oGet($t,'\.','\W'), 
    'attr' => implode(' ',array_map(function($x){return pre($x,'=').'="'.str_replace('}','',str_replace('{','',post($x,'='))).'"';},$attrs[1])), 
    'content' => oGet(preg_replace('/\[([^\]]*)\]/','',$t)), 
  ); 
}

function oFunc($t, $content = []) {
  if (oHas($t,'%')) {
    preg_match('/\%([^\%]+)\%/',$t, $f);
    return oFunc(str_replace($f[0],
      expandFns($f[1], $content)
      ,$t));
  } else return $t;
}

function expandFns($str, $content = []) {
  $parts = explode('.',$str);
  $last = array_pop($parts);

  if(oHas($last,'map(')) {
    /* var_dump('testing'); */
    /* var_dump(array_map(ofn('div'),['a'])); */
    /* var_dump(implode('',array_map(ofn('div>span{$$}'),$content))); */
    /* echo O(oGet($last,'\(','\)'),$content[0]); */
    return implode('+',
      array_map(
        function($x) use ($last) {
          return expandVars(oGet($last,'\(','\)'),$x);
        },
        $content));
  }
  if (oHas($last,'$')) $last = expandVars($last, $content);

  else if(is_callable($last)) {
    if (count($parts) > 0)
      return call_user_func($last,expandFns(implode('.',$parts),$content));
    else return call_user_func($last);
  } else return $last;

}

ini_set('memory_limit','1M');

function expandVars($str, $content = []) {
  if (oHas($str,'$')) {
    preg_match_all('/\$([^\$]*)\$/',$str, $f);
    /* var_dump($str); */
    for ($i = 0; $i < sizeof($f[0]); $i++) {
      $str = str_replace($f[0][$i], 
          (string)getContent($f[1][$i],$content)
          ,$str);
      /* var_dump($str); */
    }
    return $str;
    
  } else return $str;
}

function getContent($key,$content=[]) {
  /* if ($key == '') return $content[0]; */
  if (($key == '') && is_string($content)){
    return $content;
  }
  
  return (string) $content[$key];

  /* try { */
  /*   return get_object_vars($content)[$key]; */
  /*   $e = "Nooooo"; */
  /*   throw new Exception($e); */
  /* } catch (Exception $e) { */
  /*   try { */
  /*   } catch (Exception $e) { */
  /*     return (string) $content[$key]; */
  /*     throw $e; */
  /*     return ''; */
  /*   } */
  /*   return ''; */
  /* } */
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


function parseNest($str,$c) {
  global $omit_register;
  /* var_dump(array_keys($omit_register)); */
  foreach(array_keys($omit_register) as $key) {
    $str = str_replace($key,$omit_register[$key],$str);
    /* var_dump($omit_register[$key]); */
  }

  if(oHas($str,'%')) return parseNest(oFunc($str,$c),$c);
  if(oHas($str,'$')) return parseNest(expandVars($str,$c),$c);

  switch (firstOf(['(','>','+'],$str)) {
    case '(': 
      if(oHas(pre($str,'('),'%')) return parseNest(pre($str,'('),$c);
      return startTag(pre($str,'(')).parseNest(oGet($str,'\('.'\)'),$c).parseNest(substr($str,match($str,'(')+1),$c).endTag(pre($str,'(')); break;

    case '>': 
      if(oHas(pre($str,'>'),'%')) return parseNest(pre($str,'>'),$c);
      return startTag(pre($str,'>')).parseNest(post($str,'>'),$c).endTag(pre($str,'>')); break;

    case '+': 
      if(oHas(pre($str,firstOf(['(','>','+'],substr($str,1))) ,'%')) return parseNest(pre($str,firstOf(['(','>','+'],substr($str,1))),$c);
      return startTag(pre($str,firstOf(['(','>','+'],substr($str,1)))).endTag(pre($str,firstOf(['(','>','+'],substr($str,1)))).parseNest(post($str,firstOf(['(','>','+'],substr($str,1))),$c); break;
    default: return startTag($str).endTag($str); break;
  }
}

?>

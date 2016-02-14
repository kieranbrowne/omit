<?
include 'omit.php';

oReg('li+li{test}','lis');
timeit("omit('div.grid>div.span-12>%$$.map(lis)%',['yo','yo','yo']);");
timeit("omit('div.grid>div.span-12>%$$.map(lis)%',['yo','yo','yo']);");


function timeit($str) {
  $start = microtime(true);
  eval($str);
  $elapsed = microtime(true) - $start;
  echo "<br><br>The code:<br> $str <br> took $elapsed seconds";
}
?>

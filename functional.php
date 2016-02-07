<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
</head>
<body>
  

  <h2>Plan</h2>
  <ul>
    <li>[x] map</li>
    <li>[ ] partial</li>
    <li>[x] function registration</li>
  </ul>

  <?
    include 'omit.php';


    /* function get_field($content) { */
    /*   if ($content == 'page_title') return 'Functional Win'; */
    /*   return 'Functional Fail'; */
    /* } */

    oReg('div>span{$$}','piece');
    echo O('piece','hi there');

    echo O('ul>%$$.map(piece)%',['a','b','c']);

    echo O('ul>li{$2$}',['a','b','c']);
    echo O('ul>%$$.map(li{$$})%',['a','b','c']);
  ?>

</body>
</html>

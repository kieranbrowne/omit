<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
</head>
<body>
  <?
  include 'omit.php';
  function getPageName() { return 'My Blog'; }
  function get_permalink($id) { 'http://google.com'; }

  echo O('h2{%getPageName.strtoupper%}');
  echo O('h2{%getPageName.strtoupper.str_shuffle%}');
  echo O('div>h2{$$}',getPageName());

  /* $postbox = ofn('a[href=%$ID$.get_permalink%]>h2{$$}'); */

  /* echo implode('',array_map($postbox,['this','that'])); */
  ?>
  
  <?
  
  ?>

</body>
</html>

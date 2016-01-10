<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
</head>
<body>
  <?
  $exampleObj = new stdClass;
  $exampleObj->ID = 12;
  $exampleObj->title = 'Hamlet';

  include 'omit.php';
  function getPageName() { return 'Lorem Ipsum'; }
  function get_permalink($id) { return 'http://google.com'; }

  echo O('h3{%getPageName.strtoupper.strtolower%}');
  echo O('h2{%getPageName.strtoupper.str_shuffle%}');
  echo O('div>h2{$$}',getPageName());
  echo O('div>h2.$ID$#$title${%$ID$.strtoupper.get_permalink%}',$exampleObj);

  /* $postbox = ofn('a[href=%$ID$.get_permalink%]>h2{$$}'); */

  /* echo implode('',array_map($postbox,['this','that'])); */
  ?>
  
  <?
  
  ?>

</body>
</html>

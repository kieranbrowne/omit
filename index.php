<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Omit Example</title>
</head>
<body>

<?php
include 'omit.php';
omit('div#wrapper>span>h1{This is my Title}+h2{not bad eh}+ul.list>li.this$', ['this','that','the other']);
?>
  
</body>
</html>

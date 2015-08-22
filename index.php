<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Omit Example</title>
</head>
<body>

<?php

include 'omit.php';
omit('div#wrapper>span.title{Title}+ul>li*3');

?>
  
</body>
</html>

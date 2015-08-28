<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Omit Example</title>
</head>
<body>

<?php
include 'omit.php';

$content1 = ['this','that','the other'];

omit('div#wrapper>span>h1{This is my Title}+h2{not bad eh}+ul.list>li.this$', ['this','that','the other']);

$content2 = [
  ['title' => 'Github', 'url' => 'www.github.com'],
  ['title' => 'Stack Overflow', 'url' => 'www.stackoverflow.com']];

function getUrl($item) { return $item['url']; }

omit('div.urls>ul>(li>a)',$content2);
?>
  
</body>
</html>

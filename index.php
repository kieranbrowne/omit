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

echo omit('div#wrapper>span>h1{This is my Title}+h2{not bad eh}+ul.list>li.this$$', ['this','that','the other']);

$content2 = [
  ['title' => 'Github', 'url' => 'http://www.github.com'],
  ['title' => 'Stack Overflow', 'url' => 'http://www.stackoverflow.com']];

function getUrl($item) { return $item['url']; }
function getTitle($item) { return $item['title']; }

echo omit('div.urls>ul>(li>a[href=|getUrl|]|getTitle|)',$content2);
?>


<?
$post = ofn('div.this>span$$');

echo $post(['this','that']);

?>
  
</body>
</html>

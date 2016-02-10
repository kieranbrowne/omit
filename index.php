<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Omit Example</title>
</head>
<body>

<?
  include 'omit.php';

  $content1 = ['this','that','the other'];

  echo omit('div#wrapper>span>h1{This is my Title}+h2{not bad eh}+ul.list>%$$.map(li{$$})%', ['this','that','the other']);

  $content2 = [
    ['title' => 'Github', 'url' => 'http://www.github.com'],
    ['title' => 'Div>Span', 'url' => 'div>span'],
    ['title' => 'Stack Overflow', 'url' => 'http://www.stackoverflow.com']];

  /* // single line */ 
  echo oFunc('%$$.map(li>a[href=$url$]{$title$})%',$content2);
  echo omit('div.urls.second-class>ul>%$$.map(li>a[href=$url$]{$title$})%',$content2);

  /* // split out */
  /* oReg('li>a.%$title$.strtolower%[href=$url$]{$title$}','sitelink'); */
  /* echo omit('div.urls.second-class>ul>%$$.map(sitelink)%',$content2); */

  $post = ofn('div.this>%$$.map(span{$$ })%');
  echo $post(['this','that']);
?>
  
</body>
</html>

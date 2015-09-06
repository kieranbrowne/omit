#Omit

*write less php*

```php
include 'omit.php'; 

$content1 = ['this','that','the other'];

omit('div#wrapper>span>h1{This is my Title}+h2{not bad eh}+ul.list>li.item$', $content1);
```
produces:
```html
<div id="wrapper">
  <span>
    <h1>This is my Title</h1>
    <h2>not bad eh</h2>
    <ul class="list">
      <li class="item">this</li>
      <li class="item">that</li>
      <li class="item">the other</li>
    </ul>
  </span>
</div>
```

###Supports functions over data
```php
$content2 = [
  ['title' => 'Github', 'url' => 'www.github.com'],
  ['title' => 'Stack Overflow', 'url' => 'www.stackoverflow.com']];

function getUrl($item) { return $item['url']; }
function getTitle($item) { return $item['title']; }

omit('div.urls>ul>(li>a[href=|getUrl|]|getTitle|)',$content2);
```
produces:
```html
<div class="urls">
  <ul>
    <li>
      <a class="github" href="http://www.github.com">Github</a>
    </li>
    <li>
      <a class="stackoverflow" href="http://www.stackoverflow.com">Stack Overflow</a>
    </li>
  </ul>
</div>
```

###Syntax 
Omit uses a variation on [Emmet syntax](http://docs.emmet.io/abbreviations/syntax/).

###Injecting Content
- `$$` is used to inject the content from the given array not for numbering posts.
```php
$data = ['this','that','the other'];
omit('ul>li$$',$data);
```
produces:
```html
<ul>
  <li>this</li>
  <li>that</li>
  <li>the other</li>
</ul>
```
- Strings between `|` characters are run as functions on the given array. 
```php
$data = ['this','that','the other'];
omit('ul>li|strtoupper|',$data);
```
produces:
```html
<ul>
  <li>THIS</li>
  <li>THAT</li>
  <li>THE OTHER</li>
</ul>
```

###Wordpress Example
```php
include 'omit.php'; 

$posts = array_map(function($x) { return $x->ID; }, get_posts(['post_type'=>'news']));

omit('div.widget>h3{My Widget}+ul>(li>a[href=|get_permalink|]|get_the_title|)',$posts);
```



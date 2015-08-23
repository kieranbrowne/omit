#Omit

*write less php*

```php
include 'omit.php'; 

$content1 = ['this','that','the other'];

omit('div#wrapper>span>h1{This is my Title}+h2{not bad eh}+ul.list>li.this$', $content1);
```
produces:
```html
<div id="wrapper">
  <span>
    <h1>This is my Title</h1>
    <h2>not bad eh</h2>
    <ul class="list">
      <li class="this">this</li>
      <li class="this">that</li>
      <li class="this">the other</li>
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

omit('div.urls>ul>li|getUrl|',$content2);
```
produces:
```html
<div class="urls">
  <ul>
    <li class="github">www.github.com</li>
    <li class="stackoverflow">www.stackoverflow.com</li>
  </ul>
</div>
```

###Syntax 
Omit uses a variation on [Emmet syntax](http://docs.emmet.io/abbreviations/syntax/).

The major differences are as follows:
- `$` is used to inject the content from the given array not for numbering posts.
- Strings between `|` characters are run as functions on the given array. 
```php
omit('ul>(li>a|the_permalink|)', $wp-posts);
```


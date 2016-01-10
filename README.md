#Omit

*write less php*

Use [Emmet syntax](http://docs.emmet.io/abbreviations/syntax/) to program the web in a functional, composable and highly succinct syntax.

```php
include 'omit.php'; 


O('div#wrapper>span>h1{This is my Title}');
```
produces:
```html
<div id="wrapper">
  <span>
    <h1>This is my Title</h1>
  </span>
</div>
```

### Pass In Variables 
```php
$content = ['title' => 'Github', 'url' => 'www.github.com']

omit('div.urls>ul>(li>a[href=$url$]{$title$})',$content);
```
produces:
```html
<div class="urls">
  <ul>
    <li>
      <a href="http://www.github.com">Github</a>
    </li>
    <li>
      <a href="http://www.stackoverflow.com">Stack Overflow</a>
    </li>
  </ul>
</div>
```

### Use all the functions you know and love 
*Simply write your function names between two `%`.*
```php
omit('div{%get_page_title%',$content);
```
Functions can be chained with `.` notation.
```php
omit('div{%get_page_title.strtoupper%',$content);
```
They can also be chained to your content variables.
```php
omit('div{%$title$.strtoupper%',$content);
```

###Syntax 
Omit uses a variation on [Emmet syntax](http://docs.emmet.io/abbreviations/syntax/).


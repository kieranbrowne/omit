##Omit

*write less php*

Use [Emmet syntax](http://docs.emmet.io/abbreviations/syntax/) to program the web in a functional, composable and highly succinct syntax.

```php
echo O('div>span>h1{This is my Title}+p{This is my content}');
```
produces:
```html
<div>
  <span>
    <h1>
        This is my Title
    </h1>
    <p>
        This is my content
    </p>
  </span>
</div>
```

##### Pass Variables as an optional second value
```php
$content = array(
    array('title' => 'Github', 'url' => 'http://www.github.com')
    array('title' => 'Stack Overflow', 'url' => 'http://www.stackoverflow.com'));

echo O('div.urls>ul>%$$.map(li>a[href=$url$]{$title$})%',$content2);
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

##### Functions 
*Simply write your function names between two `%`.*
```php
O('span{%date%}');
```
Functions can be chained with `.` notation.
```php
O('div{%get_page_title.strtoupper%}');
```
They can also be chained to your content variables.
```php
omit('div{%$title$.strtoupper%',$content);
```


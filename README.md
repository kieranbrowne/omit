#Omit

```php
<?php
include 'omit.php'; 

$links = array_map('the_permalink',get_posts(['posts_per_page' => 5]));

omit('div.container>ul.posts>li$' , $links);
?>
```

###Syntax 
Omit uses a variation on [Emmet syntax](http://docs.emmet.io/abbreviations/syntax/).

The major differences are as follows:
- `$` is used to inject the content from the given array not for numbering posts.
- Strings between `|` characters are run as functions on the given array. 
```php
omit('ul>(li>a|the_permalink|)', $wp-posts);
```

###Content

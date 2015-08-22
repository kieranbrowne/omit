#Omit

```php
<?php
include 'omit.php'; 

$links = array_map('the_permalink',get_posts(['posts_per_page' => 5]));

omit('div.container>ul.posts>li$' , $links);
?>
```

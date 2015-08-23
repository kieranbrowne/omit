<?php

include 'omit.php';

//assert(parseContent('span.class{this is the content}') === 'this is the content', "parseContent");
//assert(parsePlus(parseAsterisk('span.title{Title}+ul')) === ['span.title{Title}','ul']);
assert(strInside('this|func|that','\|','\|') === 'func');

?>

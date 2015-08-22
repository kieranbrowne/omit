<?php

include 'omit.php';

assert(parsePlus('span.class{this}+ul') === ['span.class{this}','ul'], "parsePlus");
assert(parseContent('span.class{this is the content}') === 'this is the content', "parseContent");
assert(parsePlus(parseAsterisk('span.title{Title}+ul')) === ['span.title{Title}','ul']);

?>

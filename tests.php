<?php

include 'omit.php';

//guess section
assert(contSect('this>that|fn|') === 'that|fn|');
assert(contSect('(this>that|fn|)') === 'this>that|fn|');
assert(contSect('this(this|fn|>that|fn|)') === 'this|fn|>that|fn|');
assert(contSect('this(this|fn|+that|fn|)') === 'this|fn|');

assert(contSect('this>that$$') === 'that$$');
assert(contSect('(this>that$$)') === 'this>that$$');
?>

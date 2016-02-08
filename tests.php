<style>
* {
  font-size: 16px;
  font-family: mono;
  line-height: 30px;
}
i {
  font-weight: bold;
}
.code {
  font-size: 19px;
}
.red {
  color: rgb(200,0,30);
}
body {
	background: rgb(230,230,230);
}
</style>

<?php

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

function my_assert_handler($file, $line, $code)
{
    echo "<hr><span class='red'>Assertion Failed:</span>
        <i>Line:</i> $line<br />
        <i>Code:</i> <span class='code'>$code</span><br /><hr />";
}

assert_options(ASSERT_CALLBACK, 'my_assert_handler');


include 'omit.php';

/* var_dump(getContent('0',['test'])); */


/* var_dump(expandFns(expandVars('$$.strtoupper','test'),'test')); */
/* var_dump(expandVars('$$.strtoupper','test')); */
/* var_dump(expandFns('$$.strtoupper','test')); */

// oFunc
/* var_dump(oFunc('%test%',['a','b','c'])); */
/* assert('array_map(ofn("li{$$}"),["a","b"]) === "<li>a</li><li>b</li>"'); */
/* var_dump(oFunc('%$$%',['a','b','c'])); */
var_dump(expandFns('$$.map(li{$$})',['a','b','c']));
/* var_dump(['a','b','c']); */
assert("oFunc('%test.strtoupper%') === 'TEST'");
assert("oFunc('%test.strtoupper.strtolower%') === 'test'");
assert("oFunc('div.%$$.strtoupper%','test') === 'div.TEST'");

// expandVars
assert("expandVars('$$.strtoupper','test') === 'test.strtoupper'");


// getContent
assert("getContent('','test') === 'test'");
assert("getContent('',['test']) === ['test']");
assert("getContent('key',['key' => 'test']) === 'test'");
assert("getContent('0',['test0','test1']) === 'test0'");
assert("getContent('1',['test0','test1']) === 'test1'");


// getTop
assert("getTop('div>li') === 'div>'");
assert("getTop('li+li+li') === 'li+'");
assert("getTop('div.%abc%+span') === 'div.%abc%+'");
assert("getTop('div') === 'div'");
assert("getTop('(ul>li>span)+div') === '(ul>li>span)+'");
assert("getTop('(ul>li>span)+div') === '(ul>li>span)+'");
assert("getTop('(ul>li>span)') === '(ul>li>span)'");


/* assert("inParen('(ul>li>span)+div') === '(ul>li>span)'"); */
/* assert("inParen('div>(ul>li>span)+div') === '(ul>li>span)'"); */
/* assert("inParen('div>(ul>(li)>span)+div') === '(ul>(li)>span)'"); */
/* assert("inParen('div>(ul>(li)>span)+(div>div)') === '(ul>(li)>span)'"); */


// oFunc
?>

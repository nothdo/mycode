<?php
/**
 * author: selfimpr
 * mail: lgg860911@yahoo.com.cn
 * blog: http://blog.csdn.net/lgg201
 * 下面提到的代码在PHP5.3以上版本运行通过.
 */
 
function callback($callback) {
    $callback();
}
 
//输出: This is a anonymous function.<br />\n
//这里是直接定义一个匿名函数进行传递, 在以往的版本中, 这是不可用的.
//现在, 这种语法非常舒服, 和javascript语法基本一致, 之所以说基本呢, 需要继续向下看
//结论: 一个舒服的语法必然会受欢迎的.
callback(function() {
    print "This is a annction.<br />\n";
});
$msg = "Hello, everyone";
$callback = function () use ($msg) {
    print "This is a closure use string value lazy bind, msg is: $msg. <br />\n";
};
$msg = "Hello, everybody";
callback($callback);

function counter() {
    $counter = 1;
    return function() use(&$counter) {return $counter ++;};
}
$counter1 = counter();
$counter2 = counter();
echo "counter1: " . $counter1() . "<br />\n";
echo "counter1: " . $counter1() . "<br />\n";
echo "counter1: " . $counter1() . "<br />\n";
echo "counter1: " . $counter1() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";

class CallableClass{

　　public function __invoke($x){

　　var_dump($x);

　　}
　　}
　　$obj = new CallableClass;
　　$obj(5);
　　var_dump(is_callable($obj));










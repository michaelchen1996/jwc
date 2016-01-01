<?php

//用户名、密码、课程号、课序号
$zjh = $_POST['zjh'];
$mm = $_POST['mm'];
$kch = $_POST['kch'];
$kxh = $_POST['kxh'];
//echo $zjh.$mm.$kch.$kxh;

//第一次登录获得cookies
set_time_limit(0);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.145:9003/loginAction.do?zjh=' . $zjh .
    '&mm=' . $mm);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($curl);
//正则表达式匹配cookies
preg_match("!Set-Cookie: (.*)!", $data, $matches);
$cookies = $matches[1];
curl_close($curl);

//count为循环推出标记，作用类似flag
$count = 0;
//sum为循环计数器
$sum = 0;
//刷课循环
while ($count == 0) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.145:9003/xkAction.do?actionType=6');
    curl_setopt($curl, CURLOPT_COOKIE, $cookies);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.145:9003/xkAction.do?kch=' . $kch .
        '&cxkxh=' . $kxh . '&kcm=&skjs=&kkxsjc=&skxq=&skjc=&pageNumber=-2&preActionType=3&actionType=5');
    $data = curl_exec($curl);
    //一旦发现有多选框，说明有课可选，之后循环会退出
    $count = preg_match("!checkbox!", $data, $hello);
    //echo 'hello</br>' . $count;
    curl_close($curl);
    $sum++;
}
//输出刷课次数
echo '</br>' . $sum;

//选课操作
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.145:9003/xkAction.do');
curl_setopt($curl, CURLOPT_COOKIE, $cookies);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($curl);
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.145:9003/xkAction.do?kcId=' . $kch .
'_' . $kxh . '&preActionType=5&actionType=9');
$data = curl_exec($curl);
curl_close($curl);
//输出选课结果
echo '</br>'.$data;





?>

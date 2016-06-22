<?php


//用户名、密码、课程号、课序号
$zjh = '';
$mm = '';
$kch = '';
$kxh = '';

//说了最好用脚本运行
//$zjh = $_POST['zjh'];
//$mm = $_POST['mm'];
//$kch = $_POST['kch'];
//$kxh = $_POST['kxh'];

//curl函数初始化，获取cookies需要HEADER
$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 1);

while (true) {
	//初次登陆以获取cookies
	curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/loginAction.do?zjh=' . $zjh . '&mm=' . $mm);
	$data = curl_exec($curl);
	preg_match("!Set-Cookie: (.*)!", $data, $matches);
	$cookies = $matches[1];
	echo "Login! Cookies: ".$cookies."";

	curl_setopt($curl, CURLOPT_COOKIE, $cookies);
	curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/xkAction.do');
	$data = curl_exec($curl);
	echo "Init len: ".strlen($data)."\n";

	//count为循环推出标记，作用类似flag
	$flag = 0;
	//sum为循环计数器
	$sum = 0;
	//刷课循环
	while ($flag == 0) {
		if (preg_match("!alert.gif!", $data)==1) {
			echo "fuck!!! Re-login.\n";
			break;
		}
		curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/xkAction.do?kch=' . $kch . '&cxkxh=' . $kxh . '&kcm=&skjs=&kkxsjc=&skxq=&skjc=&pageNumber=-2&preActionType=3&actionType=5');
		$data = curl_exec($curl);
		//一旦发现有多选框，说明有课可选，之后循环会退出
		$flag = preg_match("!checkbox!", $data);
		$sum = $sum + 1;
		echo $sum.": ".$flag." len: ". strlen($data)."\n";
	}
	if ($count == 1) {
		echo "Found it!\n";
		break;
	}
}

//选课操作
//curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/xkAction.do');
//$data = curl_exec($curl);
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/xkAction.do?kcId=' . $kch . '_' . $kxh . '&preActionType=5&actionType=9');
$data = curl_exec($curl);
echo $data."\nFinished!\n";
curl_close($curl);
?>

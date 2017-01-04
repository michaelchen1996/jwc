<?php

$zjh = $_POST['zjh'];
$mm = $_POST['mm'];

//SCU GPA
function toGPA($from) {
	if ($from>=95) return 4;
	if ($from>=90) return 3.8;
	if ($from>=85) return 3.6;
	if ($from>=80) return 3.2;
	if ($from>=75) return 2.7;
	if ($from>=70) return 2.2;
	if ($from>=65) return 1.7;
	if ($from>=60) return 1;
	return 0;
}

date_default_timezone_set("Asia/Shanghai");
set_time_limit(0);
$curl = curl_init();
//curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/loginAction.do?zjh=' . $zjh . '&mm=' . $mm);
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/loginAction.do');
curl_setopt($curl, CURLOPT_POST, 1);
$post_data = array('zjh'=>$zjh, 'mm'=>$mm);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($curl);
preg_match("!Set-Cookie: (.*)!", $data, $matches);
$encode = mb_convert_encoding($data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
$cookies = $matches[1];
curl_close($curl);
if (preg_match("/(请您重新输入)/", $encode)) {
	echo "<script>window.location='./index.php';</script>";
}

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/menu/s_top.jsp');
curl_setopt($curl, CURLOPT_COOKIE, $cookies);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($curl);
curl_close($curl);
$encode = mb_convert_encoding($data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
preg_match("/欢迎光临&nbsp;([\S]+)&nbsp;\|&nbsp;/", $encode, $matches);
$name = $matches[1];

$curl = curl_init();
//curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/reportFiles/cj/cj_zwcjd.jsp');//?LS_XH=' . $zjh);
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/bxqcjcxAction.do?pageSize=300');
curl_setopt($curl, CURLOPT_COOKIE, $cookies);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($curl);
curl_close($curl);
$encode = mb_convert_encoding($data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

$reg1 = "/(<tr class=\"odd\" onMouseOut=\"this.className='even';\" onMouseOver=\"this.className='evenfocus';\">)([\s\S]*?)(<\/tr>)/";
$reg2 = "/(<td align=\"center\">)([\s\S]*?)(<\/td>)/";

$numOfCourse = preg_match_all($reg1, $encode, $matches, PREG_SET_ORDER);

$total = 0;
$totalScore1 = 0;
$totalPoint1 = 0.0;
$totalGPA1 = 0.0;
$totalScore2 = 0;
$totalPoint2 = 0.0;
$totalGPA2 = 0.0;
echo "<html><head><meta charset='utf-8'><title>本学期成绩</title>".
"<link href='http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.css' rel='stylesheet'>".
"<meta name='viewport' content='width=device-width, initial-scale=1.0'>".
"</head><body><div class='container'><div class='row'>".
"<table class='table table-striped table-bordered table-hover' align='center'>\n".
"<tr><td colspan='3'>学号: ".$zjh."<br>姓名: ".$name."</td></tr>\n".
"<tr><th width='70%'>课程名</th><th width='15%'>学分</th><th width='15%'>成绩</th></tr>\n";
foreach ($matches as $val) {
	preg_match_all($reg2, $val[2], $matches2, PREG_SET_ORDER);
	echo "<tr>";
	for ($i=2; $i<6; $i=$i+2) {
		echo "<td>".preg_replace(["/[(\r\n)\t]/","/(  +)/"], [""," "], $matches2[$i][2])."</td>\n";
	}
	echo "<td>";
	if (preg_match("/[\S]+/", $matches2[6][2], $score)) {
		if ($score[0]=="优秀") $scoreNum = 95;
		else if ($score[0]=="良好") $scoreNum = 85;
		else if ($score[0]=="中等") $scoreNum = 75;
		else if ($score[0]=="通过") $scoreNum = 60;
		else if ($score[0]=="未通过") $scoreNum = 0;
		else $scoreNum = intval($score[0]);
		echo $score[0];
		$total = $total + 1;
		preg_match("/[\S]+/", $matches2[4][2], $point);
		$pointNum = floatval($point[0]);
		preg_match("/[\S]+/", $matches2[5][2], $type);
		if (preg_match("/必修/", $type[0])) {
			$totalPoint1 = $totalPoint1 + $pointNum;
			$totalScore1 = $totalScore1 + $scoreNum * $pointNum;
			$totalGPA1 = $totalGPA1 + toGPA($scoreNum) * $pointNum;
		}
		$totalPoint2 = $totalPoint2 + $pointNum;
		$totalScore2 = $totalScore2 + $scoreNum * $pointNum;
		$totalGPA2 = $totalGPA2 + toGPA($scoreNum) * $pointNum;
	}
	echo "</td></tr>\n\n";
}
if ($totalPoint1>0) 
	$averageScore1 = $totalScore1/$totalPoint1;
else 
	$averageScore1 = 0;

if ($totalGPA1>0) 
	$averageGPA1 = $totalGPA1/$totalPoint1;
else 
	$averageGPA1 = 0;

if ($totalPoint2>0) 
	$averageScore2 = $totalScore2/$totalPoint2;
else 
	$averageScore2 = 0;

if ($totalGPA2>0) 
	$averageGPA2 = $totalGPA2/$totalPoint2;
else 
	$averageGPA2 = 0;

echo "<tr><td colspan='3'>获取到了 (".$total."/".$numOfCourse.") 门课的成绩<br>".
"必修课的加权平均分: ".number_format($averageScore1, 3, '.', '')." , GPA: ".number_format($averageGPA1, 3, '.', '')."<br>".
"所有课的加权平均分: ".number_format($averageScore2, 3, '.', '')." , GPA: ".number_format($averageGPA2, 3, '.', '')."<br>".
"表格生成时间: ".date("Y-m-d H:i:s")."</td></tr></table>";
echo "</div></div><script src='http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js'></script>".
"<script src='http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js'></script></body></html>";

?>

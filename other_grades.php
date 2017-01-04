<?php

$zjh = $_POST['zjh'];
$mm = $_POST['mm'];
$LS_XH = $_POST['LS_XH'];

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
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_USERAGENT, 'fuck you');

//curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/loginAction.do?zjh=' . $zjh . '&mm=' . $mm);
curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/loginAction.do');
curl_setopt($curl, CURLOPT_POST, 1);
$post_data = array('zjh'=>$zjh, 'mm'=>$mm);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
$data = curl_exec($curl);
preg_match("!Set-Cookie: (.*)!", $data, $matches);
$encode = iconv('GBK', 'UTF-8', $data);
$cookies = $matches[1];
if (preg_match("/alert.gif/", $encode)) {
	echo "<script>window.location='./index.php';</script>";
}
curl_setopt($curl, CURLOPT_COOKIE, $cookies);

$curr_page = 1;
$encode = "";
while (!preg_match('/\|下一页\|最后页\|/', $encode)) {
	curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/reportFiles/cj/cj_zwcjd.jsp?LS_XH='.$LS_XH.'&report1_currPage='.$curr_page);
	$curr_page = $curr_page + 1;
	$data = curl_exec($curl);
	$encode = $encode.iconv('GBK', 'UTF-8', $data);
}
echo "!!!";
function getInfo($key, $encode) {
	$tag = "\<[\/]?td[^\>]*\>";
	$reg1 = "/({$tag}{$key}{$tag}[\s]*{$tag})([\s\S]*?)({$tag})/";
	preg_match($reg1, $encode, $matches);
	$value = $matches[2];
	return $value;
}
/*
echo getInfo("姓名", $encode).' '.getInfo("学号", $encode).' '
	.getInfo("性别", $encode).' '.getInfo("身份证号", $encode).' '
	.getInfo("班级", $encode)."\n";
*/
/*
<td colSpan=4 class="report1_2_1">翻译与文化潜在关系</td>
		<td class="report1_8_5">0.5</td>
		<td class="report1_2_1">80</td>
		<td class="report1_2_1">正常</td>
		<td class="report1_2_1">任选</td>
		<td colSpan=2 class="report1_2_1">201506</td>
*/
$reg1 = "/(<td colSpan=4 class=\"report1_2_1\">)([^\<]*?)(<\/td>[\s]*)"
			."(<td class=\"report1_8_5\">)([\s\S]*?)(<\/td>[\s]*)"
			."(<td class=\"report1_2_1\">)([\s\S]*?)(<\/td>[\s]*)"
			."(<td class=\"report1_2_1\">)([\s\S]*?)(<\/td>[\s]*)"
			."(<td class=\"report1_2_1\">)([\s\S]*?)(<\/td>[\s]*)"
			."(<td colSpan=2 class=\"report1_2_1\">)([\s\S]*?)(<\/td>)/";
$course_num = preg_match_all($reg1, $encode, $matches, PREG_SET_ORDER);
/*
foreach ($matches as $i) {
	echo "{$i[2]} {$i[5]} {$i[8]} {$i[11]} {$i[14]} {$i[17]}\n";
}*/

$total = 0;
$totalScore1 = 0;
$totalPoint1 = 0.0;
$totalGPA1 = 0.0;
$totalScore2 = 0;
$totalPoint2 = 0.0;
$totalGPA2 = 0.0;

?>

<html>
	<head>
		<meta charset='utf-8'><title>所有成绩</title>
		<link href='http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.css' rel='stylesheet'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	</head>
	<body>
		<div class='container'><div class='row'>
		<table class='table table-striped table-bordered table-hover' align='center'>
<?php
	echo "			<tr><td colspan='6'>姓名: ".getInfo("姓名", $encode)."<br>
				学号: ".getInfo("学号", $encode)."<br>
				身份证号: ".getInfo("身份证号", $encode)."<br>
				班级: ".getInfo("班级", $encode)."<br>
				</td></tr>
			<tr><th width='40%'>课程名</th><th width='10%'>学分</th><th width='10%'>成绩</th>
				<th width='10%'>修读方式</th><th width='10%'>课程属性</th><th width='20%'>考试时间</th></tr>";

foreach ($matches as $val) {
	/*
	课程名{$i[2]} 学分{$i[5]} 成绩{$i[8]}
	修读方式{$i[11]} 课程属性{$i[14]} 考试时间{$i[17]}*/
	$score_out = "";
	if (!preg_match("/[\S]+/", $val[8], $score)) {
		$course_num = $course_num - 1;
		//echo "hhhhhhhhhhhhhhhh==================================================";
		continue;
	}

	if ($score[0]=="优秀") $scoreNum = 95;
	else if ($score[0]=="良好") $scoreNum = 85;
	else if ($score[0]=="中等") $scoreNum = 75;
	else if ($score[0]=="通过") $scoreNum = 60;
	else if ($score[0]=="未通过") $scoreNum = 0;
	else $scoreNum = intval($score[0]);
	preg_match("/[\S]+/", $val[5], $point);
	$pointNum = floatval($point[0]);
	if ($scoreNum >= 60 && preg_match("/正常/", $val[11])) {
		$total = $total + 1;
		if (preg_match("/必修/", $val[14])) {
			$totalPoint1 = $totalPoint1 + $pointNum;
			$totalScore1 = $totalScore1 + $scoreNum * $pointNum;
			$totalGPA1 = $totalGPA1 + toGPA($scoreNum) * $pointNum;
		}
		$totalPoint2 = $totalPoint2 + $pointNum;
		$totalScore2 = $totalScore2 + $scoreNum * $pointNum;
		$totalGPA2 = $totalGPA2 + toGPA($scoreNum) * $pointNum;
	}
echo "
			<tr><td>".$val[2]."</td><td>".$val[5]."</td><td>".$val[8]."</td>
				<td>".$val[11]."</td><td>".$val[14]."</td><td>".$val[17]."</td></tr>";
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

echo "
			<tr><td colspan='6'>获取到了{$course_num}项成绩<br>
				必修课 共{$totalPoint1}学分, 加权平均分: ".number_format($averageScore1, 3, '.', '')." , GPA: ".number_format($averageGPA1, 3, '.', '')."<br>
				所有课 共{$totalPoint2}学分, 加权平均分: ".number_format($averageScore2, 3, '.', '')." , GPA: ".number_format($averageGPA2, 3, '.', '')."<br>
				表格生成时间: ".date("Y-m-d H:i:s")."
			</td></tr>\n";
?>
		</table>
		</div></div>
		<script src='http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js'></script>
		<script src='http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
	</body>
</html>

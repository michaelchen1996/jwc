<html>
<head>
    <meta charset="utf-8">
</head>

<body>

                    <table>
                        <tbody>
                            <tr>
                                <th>课程名</th>
                                <th>状态</th>
                            </tr>
				<?php
    				require "./func.php";
					$zjh = $_POST['zjh'];
					$mm = $_POST['mm'];

					$tr_template='<tr>
						<td>%s</td>
						<td>%s</td>
					</tr>';
					$trs = '';

					$curl = curl_init();

				    // 第一次抓取COOKIES
					curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/loginAction.do?zjh='.$zjh.'&'.'mm='.$mm);
					curl_setopt($curl, CURLOPT_HEADER, 1);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					$data = curl_exec($curl);
					preg_match("!Set-Cookie: (.*)!", $data, $matches);
					$cookies=$matches[1];
					curl_close($curl);

					// 第二次抓取待评教教师列表
				    $curl = curl_init();
				    curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/jxpgXsAction.do?oper=listWj');
					curl_setopt($curl, CURLOPT_COOKIE, $cookies);
					curl_setopt($curl, CURLOPT_HEADER, 1);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					$data = curl_exec($curl);
				    curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/jxpgXsAction.do?totalrows=0&page=1&pageSize=300');
				    $data = curl_exec($curl);
				    preg_match_all("!<img name=\"(.*)\" style!", $data, $matches);

				    $pjtinfo=$matches[1];
				    $iminfo=fetchImportant($pjtinfo);

				    $pj['0000000005']='10_1';
				    $pj['0000000006']='10_1';
					$pj['0000000007']='10_1';
					$pj['0000000008']='10_1';
					$pj['0000000009']='10_1';
					$pj['0000000010']='10_1';
					$pj['0000000035']='10_1';
					$pj['zgpj']='very good!';

					// $pj['0000000039']='10_1';
					// $pj['0000000049']='10_1';
					// $pj['0000000042']='10_1';
					// $pj['0000000050']='10_1';
					// $pj['0000000048']='10_1';
					// $pj['0000000051']='10_1';

					$pj2['0000000028']='10_1';
					$pj2['0000000029']='10_1';
					$pj2['0000000030']='10_1';
					$pj2['0000000031']='10_1';
					$pj2['0000000032']='10_1';
					$pj2['0000000033']='10_1';
					$pj2['zgpj']='very good!';

					foreach($iminfo as $key=>$val)
					{
					    // 进入评教页面（不进入的话不能评教）
						$tinfo=$iminfo[$key];
						$postdata=$tinfo;
						$postdata['oper']='wjShow';
						$postdata=http_build_query($postdata);

						curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/jxpgXsAction.do');
					    curl_setopt($curl, CURLOPT_POST,1);
					    curl_setopt($curl, CURLOPT_POSTFIELDS,$postdata);
					    curl_setopt($curl, CURLOPT_REFERER, 'http://202.115.47.141/jxpgXsAction.do?totalrows=0&page=1&pageSize=200');
					    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
					    $data=curl_exec($curl);

					    // 构建评教信息，进行评教
					    if(stripos($tinfo['bpr'],"zj",0)===0)
					    	$pjb=$pj2;
					    else
					    	$pjb=$pj;

					    $pjb['wjbm']=$tinfo['wjbm'];
						$pjb['bpr']=$tinfo['bpr'];
						$pjb['pgnr']=$tinfo['pgnr'];

					    $postdata=http_build_query($pjb);

					    curl_setopt($curl, CURLOPT_URL, 'http://202.115.47.141/jxpgXsAction.do?oper=wjpg');
					    curl_setopt($curl, CURLOPT_POST,1);
					    curl_setopt($curl, CURLOPT_POSTFIELDS,$postdata);
					    curl_setopt($curl, CURLOPT_REFERER, 'http://202.115.47.141/jxpgXsAction.do');
					    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
					    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					    $data=curl_exec($curl);

						preg_match("!alert((.*))!", $data, $matches);

						if(strpos(iconv('GB2312', 'UTF-8',$matches[1]), "成功", 0))
							$trs .= sprintf($tr_template, iconv('GB2312', 'UTF-8', $tinfo['pgnrm']), '<p>Success</p>');
						else
							$trs .= sprintf($tr_template, iconv('GB2312', 'UTF-8', $tinfo['pgnrm']), '<p>Fail</p>');
					}

					curl_close($curl);

					echo $trs;
				?>
					</tbody>
				</table>
</body>
</html>

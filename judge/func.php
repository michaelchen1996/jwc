<?php

 function fetchImportant($pjtinfo)
 {
 	$iminfo=array();
 	foreach($pjtinfo as $key=>$info)
 	{
		$start=0;
		$end=0;
		$end=strpos($info,"#@",$start);
		$sub=substr($info,$start,$end-$start);
		$iminfo[$key]['wjbm']=$sub;

		$start=$end+2;
		$end=strpos($info,"#@",$start);
		$sub=substr($info,$start,$end-$start);
		$iminfo[$key]['bpr']=$sub;

		$start=$end+2;
		$end=strpos($info,"#@",$start);
		$sub=substr($info,$start,$end-$start);
		$iminfo[$key]['bprm']=$sub;

		$start=$end+2;
		$end=strpos($info,"#@",$start);
		$sub=substr($info,$start,$end-$start);
		$iminfo[$key]['wjmc']=$sub;


		$start=$end+2;
		$end=strpos($info,"#@",$start);
		$sub=substr($info,$start,$end-$start);
		$iminfo[$key]['pgnrm']=$sub;

		$start=$end+2;
		$sub=substr($info,$start);
		$iminfo[$key]['pgnr']=$sub;

 	}

 	return $iminfo;
 }
?>

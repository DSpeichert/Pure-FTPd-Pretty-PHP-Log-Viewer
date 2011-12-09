<?php
	function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}

	function cleanRow($text){
		if(strstr($text, 'GET ') !== false)
			return null;
		elseif(trim($text) == '')
			return null;
		
		$text = explode(' - ', $text);
		$ip = $text[0];

		$text = explode(' [', $text[1]);
		$account = $text[0];

		$text = explode('] "', $text[1]);
		$date = date('H:i:s (Y-m-d)', strtotime($text[0]));

		$file = str_replace(array('PUT ', 'GET '), '', $text[1]);
		$file = str_replace('/home/ftp-accounts/', '', $file);
		$file = explode('/', $file);
		$file[0] = null;
		$file = implode('', $file);

		$file = explode('" ', $file);
		$totalXpl = count($file)-1;
		$statusOffset = $totalXpl < 0 ? 0 : $totalXpl;

		$status = $file[$totalXpl];
		$file[$totalXpl] = '';

		$file = urldecode(implode('', $file));

		$filesize = explode(' ', $status);
		$status = $filesize[0];
		$filesize = $filesize[1];

		return array(
			'ip' => $ip,
			'account' => $account,
			'date' => $date,
			'file' => $file,
			'status' => $status,
			'filesize' => $filesize,
		);
	}

	$logData = (string)@file_get_contents('/var/log/pure-ftpd/transfer.log');
	$logData = array_reverse(explode("\n", $logData));
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Refresh" content="10">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
	<title>FTP Logg</title>
		<style type="text/css">
	# Reset
	html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none}:focus{outline:0}ins{text-decoration:none}del{text-decoration:line-through}table{border-collapse:collapse;border-spacing:0}a img{border:0;}
	
	body{
		font: 14px/1.3 'Helvetica Neue',Arial,'Liberation Sans',FreeSans,sans-serif;
		background-color: #fbfbfb;
		color: #0e0e0e;
	}
	
	h1{
		font-size: 30px;
	}
	
	h2{
		font-size: 20px;
	}
	
	table{
		width: 100%;
		box-shadow: 5px 5px 0 #b0afaf;
		color: #121619;
	}
	
	ul{
		padding: 0;
		margin: 0;
		list-style: disc url(data:image/png;base64,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) inside;
		line-height: 25px;
		font-size: 16px;
		text-indent: 10px;
	}
	
	a, a:hover{
		color: #121619;
		text-decoration: none;
	}
	
	a:hover{
		background: #fff;
	}
	
	.centerWrapper{
		width: 940px;
		margin: 0 auto;
	}
	.methodWrapper{
		padding: 0px 25px 30px 25px;
		margin-top: 35px;
		border: 1px solid #cecece;
		border-radius: 4px;
		box-shadow: 3px 3px 0 #cecece;
		background-color: #e6e4e4;
	}
	
	td{
		padding: 10px 10px;
	}
	
	td a{
		font-weight: bold;
	}
	
	td p{
		margin: 0;
		padding: 5px;
	}
	
	tr td:last-child {
		width: 100%;
	}
	
	tr td:first-child {
		width: 15%;
	}
	
	thead tr td{
		font-size: 16px;
		background-color: #839AA5;
		font-weight: bold;
	}
	
	tbody tr:nth-child(odd){
		background: #B6C2C9;
	}
	
	tbody tr:nth-child(even){
		background: #97ABB4;
	}
	
	h1, h2, h3 {
	  text-shadow: 1px 1px 0 #f9f9f9;
	}
	
	div.yes {
		width:24px;
		height:30px;
 		background-image:url(data:image/png;base64,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);
		background-repeat: no-repeat;
		font-weight: bold;
		padding: 0 0 0 35px;
		line-height: 24px;
	}
	
	div.no {
		width:24px;
		height:24px;
		background-image:url(data:image/png;base64,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);
		background-repeat: no-repeat;
		font-weight: bold;
		padding: 0 0 0 35px;
		line-height: 24px;
	}
	
	</style>
</head>
<body>
	<div class="centerWrapper">
		<div class="methodWrapper">
			<h1>FTP Logg</h1>
			<table>
				<thead>
					<tr>
						<td style="width:10%">Account</td>
						<td style="width:45%">File</td>
						<td style="width:20%">Date</td>
						<td style="width:5%">Filesize</td>
						<td style="width:5%">IP</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($logData as $row): ?>
					<?php $data = cleanRow($row); ?>
					<?php if($data): ?>
					<tr>
						<td><?=$data['account']?></td>
						<td><?=$data['file']?></td>
						<td><?=$data['date']?></td>
						<td><?=human_filesize($data['filesize'])?></td>
						<td><?=$data['ip']?></td>
					</tr>
					<?php endif; ?>	
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
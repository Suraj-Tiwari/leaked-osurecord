<?php
			
mysql_connect("localhost", "osu", "3jtys90eghjs90");
mysql_select_db("osu");
	
$res = mysql_query("
	select count(*)
	from record_log
	inner join beatmaps on record_log.beatmap_id = beatmaps.id
	where youtube_url > '' and youtube_user <> '****DELETED****'
") or die(mysql_error());

$count = mysql_fetch_array($res);
$count = $count[0];		

$page = intval($_GET['page']);
if($page != 0)
	$page --;

$page *= 20;
if($page < 0) $page = 0;
				
			   ?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>osu!record gallery</title>
<style type="text/css">
html,body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,p,blockquote,th,td{margin:0;padding:0;font-weight:400;}img,body,html,table,tr,td,th,a,a:hover,a:active,a:focus,a:link{border:0;outline:0}address,caption,cite,code,dfn,em,strong,th,var,a{font-style:normal;font-weight:normal;text-decoration:none}ol,ul{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;}q:before,q:after{content:'';}
* { font-family: Verdana, sans-serif; }
#container { text-align: center; }
h1, h3, h3 a { color: #FF227A !important; font-weight: bold; }
h1 { letter-spacing: 8px; margin: 0; font-size: 26px; font-weight: bold; }
h3 { font-size: 11px; margin-top: 0; font-weight: bold; }
.ib { display: inline-block; }
.video { border: 1px solid #ddd; background: #f7f7f7; padding: 7px; margin: 4px 10px; display: inline-block; width: 320px; }
.video > .info { display: block; font-size: 11px; font-family: Verdana, sans-serif; }
.yt { width: 320px; height: 170px; background-position: center; cursor: pointer; }
.pagesholder { margin: 10px auto; text-align: center; }
.pages { display: inline-block !important; }
</style>
<link rel="stylesheet" type="text/css" href="simplePagination.css" />
<script type='text/javascript' src='jquery-1.8.3.min.js'></script>
<!--<script type='text/javascript' src='jquery.lazyload.js'></script>-->
<script type='text/javascript' src='jquery.simplePagination.js'></script>

<script type='text/javascript'>
$(document).ready(function(){
	//$(".yt").lazyload();
	$(".yt").click(function(){
		var url = $(this).data("youtubeid");
		$(this).replaceWith('<iframe width="320" height="240" src="http://www.youtube.com/embed/'+url+'?vq=hd720&theme=light&autoplay=1" frameborder="0" allowfullscreen></iframe>');
	});
	
	
	$(".pages").pagination({
		items: parseInt("<?=$count?>", 10),
		itemsOnPage: 20,
		cssStyle: 'light-theme',
		hrefTextPrefix: "?page=",
		currentPage: parseInt("<?=intval($_GET['page'])?>", 10) || 1
	});
	
});
</script>
</head>

<body>
<div id='container'>

<div class='pagesholder'>
		<h1><a style='text-decoration: none; color: inherit; font-weight: inherit' href='/'>osu!record</a></h1>
		<h3 style='margin-bottom: 7px; margin-left: -7px;'>By &lt;<a href='http://osu.ppy.sh/u/10886'>Darkimmortal</a>&gt;</h3>
<div class='pages'></div>
</div>


<?php

function time2str($ts)
{
	if(!ctype_digit($ts))
		$ts = strtotime($ts);

	$diff = time() - $ts;
	if($diff == 0)
		return 'now';
	elseif($diff > 0)
	{
		$day_diff = floor($diff / 86400);
		if($day_diff == 0)
		{
			if($diff < 60) return 'just now';
			if($diff < 120) return '1 minute ago';
			if($diff < 3600) return floor($diff / 60) . ' minutes ago';
			if($diff < 7200) return '1 hour ago';
			if($diff < 86400) return floor($diff / 3600) . ' hours ago';
		}
		if($day_diff == 1) return 'yesterday';
		if($day_diff < 7) return $day_diff . ' days ago';
		if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
		if($day_diff < 60) return 'last month';
		return date('F Y', $ts);
	}
	else
	{
		$diff = abs($diff);
		$day_diff = floor($diff / 86400);
		if($day_diff == 0)
		{
			if($diff < 120) return 'in a minute';
			if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
			if($diff < 7200) return 'in an hour';
			if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
		}
		if($day_diff == 1) return 'Tomorrow';
		if($day_diff < 4) return date('l', $ts);
		if($day_diff < 7 + (7 - date('w'))) return 'next week';
		if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
		if(date('n', $ts) == date('n') + 1) return 'next month';
		return date('F Y', $ts);
	}
}

//http://gdata.youtube.com/feeds/api/videos?category=osurecord&alt=jsonc&v=2

// MORE LAZINESS FRIENDLY THAN PDO
//var_dump($page);
$res = mysql_query("
		select record_log.id,UNIX_TIMESTAMP(date) as date,UNIX_TIMESTAMP(finished) as finished,beatmap_id,difficulty_id,record_log.osu_id,osu_username,maphash,youtube_url,youtube_user, filename, title, creator, artist
		from record_log
		inner join beatmaps on record_log.beatmap_id = beatmaps.id
		where youtube_url > '' and youtube_user <> '****DELETED****'
		order by record_log.id desc
		limit {$page},20
") or die(mysql_error());

while($row = mysql_fetch_array($res)){
	$youtubeId = str_replace("http://www.youtube.com/watch?v=", "", $row['youtube_url']);
	
	if((empty($row['youtube_user']) || mt_rand(0, 4) == 2) && $row['finished'] + 300 < time()){		
		
		$fuckYoutube = @file_get_contents("https://gdata.youtube.com/feeds/api/videos/".rawurlencode($youtubeId)."?v=2&alt=jsonc");
		
		if($fuckYoutube === false)
			$videoJson['data']['uploader'] = '****DELETED****';
		else {
			$videoJson = json_decode($fuckYoutube, true);
		
			// video deleted brah
			if(empty($videoJson['data']['uploader']))
				$videoJson['data']['uploader'] = '****DELETED****';
		}
				
		mysql_query("update record_log set youtube_user = '".mysql_real_escape_string($videoJson['data']['uploader'])."' where id={$row['id']}") or die(mysql_error());			
		$row['youtube_user'] = $videoJson['data']['uploader'];
	}
	
	if($row['youtube_user'] == '****DELETED****')
		continue;

	$took = date("i:s", $row['finished'] - $row['date']);
		//<img src='http://i.ytimg.com/vi/".urlencode($youtubeId)."/hqdefault.jpg' width='480' height='360' />
	echo "<div class='video'> <div class='yt' data-youtubeid='".htmlspecialchars($youtubeId)."' style='background-image:url(http://i.ytimg.com/vi/".rawurlencode($youtubeId)."/hqdefault.jpg)'></div>
	<div class='info'><a href='http://osu.ppy.sh/b/{$row['difficulty_id']}'>".htmlspecialchars($row['artist'])." - ".htmlspecialchars($row['title'])."</a>, <div class='ib'>played by <a href='http://osu.ppy.sh/u/".rawurlencode($row['osu_username'])."'>".htmlspecialchars($row['osu_username'])."</a></div></div>
	<div class='info'><a href='".htmlspecialchars($row['youtube_url'])."'>Recorded</a> ".time2str($row['date'])." (took {$took})</div></div>";
	
}


?>
<div class='pagesholder'>
<div class='pages'></div>
</div>
</div><script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4255822-8']);
  _gaq.push(['_trackPageview']);

  (function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
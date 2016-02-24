<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>osu!record gallery</title>
<style type="text/css">

</style>
<script type='text/javascript' src='jquery-1.8.3.min.js'></script>

<script type='text/javascript'>
$(document).ready(function(){
	
});
</script>
</head>

<body>
<div id='container'>



<?php


//http://gdata.youtube.com/feeds/api/videos?category=osurecord&alt=jsonc&v=2

// MORE LAZINESS FRIENDLY THAN PDO
mysql_connect("localhost", "osu", "3jtys90eghjs90");
mysql_select_db("osu");


$res = mysql_query("
	select record_log.id,date,finished,beatmap_id,difficulty_id,record_log.osu_id,osu_username,maphash,youtube_url,youtube_user, filename, title, creator, artist
	from record_log
	left join beatmaps on record_log.beatmap_id = beatmaps.id
	where youtube_url > ''
	order by record_log.id desc
") or die(mysql_error());



while($row = mysql_fetch_array($res)){
	$youtubeId = str_replace("http://www.youtube.com/watch?v=", "", $row['youtube_url']);
	
	if(empty($row['youtube_user'])){		
		
		$fuckYoutube = @file_get_contents("https://gdata.youtube.com/feeds/api/videos/".urlencode($youtubeId)."?v=2&alt=jsonc");
		
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

		
	echo "<a href='http://osu.ppy.sh/b/{$row['difficulty_id']}'>".htmlspecialchars($row['artist'])." - ".htmlspecialchars($row['title'])."</a>, played by <a href='http://osu.ppy.sh/u/".urlencode($row['osu_username'])."'>".htmlspecialchars($row['osu_username'])."</a>";
	
}


?>
</div>
</body>
</html>
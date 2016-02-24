<?php
session_Start();

function authenticated()
{
	if (isset($_SESSION['sessionToken'])) {
		return true;
	}
}

function PsExec($commandJob) {

	$command = $commandJob.' > /dev/null 2>&1 & echo $!';
	exec($command, $op);
	//var_Dump($op);
	$pid = (int)$op[0];

	if($pid!="") return $pid;

	return false;
} 


mysql_connect("localhost", "osu", "3jtys90eghjs90");
mysql_select_db("osu");

?>



<style type="text/css">

h1 { letter-spacing: 8px; margin: 0; }
h3 { font-size: 11px; margin-top: 0; }
h1, h3, h3 a { color: #FF227A !important }
* { font-family: Verdana, sans-serif; }
body { font-size: 13px; }
#left { width: 500px; float: left; }
#right { width: 510px; float: right; }
#container { width: 1020px; margin: 0 auto; }
.box { border: 1px solid #ddd; background: #f7f7f7; border-radius: 5px; padding: 10px; margin: 10px 0; }
h4 { font-size: 14px; font-weight: bold; margin: 3px 0 12px 7px; }
ul { margin: 0 0 5px 0; }
</style>

<?php
//die("Disabled.");
//die("Disabled for a few days due to RAM constraints");
//die("Disabled until 21/10/2013 due to server migration");
//die("Currently broken.");
//die("Disabled for a while (maybe up to 2 weeks) as I need resources for other much more important things.");

//die("disabled for a while...");
//die("brb");
//die("temp disabled");

//if($_SERVER['REMOTE_ADDR'] != '10.8.0.6')
//	die("brb");

//die("Significant improvements coming, disabled in the meantime");

//die("testing some shit brb");

//die("osu! is still crashing since latest update (not entirely sure it is osu!'s fault), but yeah no idea how to fix this.");

//die("Yay more crashes");

//die("Uploading disabled for the next few minutes, sorry!");

//die("GPU acceleration has broken again, no ETA as I've got absolutely no idea how to fix it or even where to begin (last time was just luck).");


$res  = mysql_query("select count(*) from record_log where finished > date and (ip = '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."' or youtube_session='".mysql_real_escape_string($_SESSION['sessionToken'])."') and datediff(NOW(), date) = 0");
$row = mysql_fetch_array($res);
if($row[0] >= 4)
	die("You have reached your max recordings (4) for today, come back tomorrow.");

if(empty($_FILES) || empty($_FILES['replay']))
	die("what the christ");
	
if(!authenticated())
	die("pls authenticate with Youtube first");

$mem = new Memcache();
$mem->connect("localhost");
$recording = $mem->get("recording");

if(!empty($recording))
	die("Someone's already using the system, wait your turn");
	
//die("Temp disabled because high load + warm weather + shit datacenter = cpu overheating");
	
$load = sys_getloadavg();

if($load[0] > 3.8){
	die("The server is a little busy atm with other tasks, try again later. (Load currently {$load[0]}, needs to drop below 3.8)");
}

$servers = $mem->get("gm_servers");
foreach($servers as $server){
	if($server[1]['numplayers'] > 15){
		//die("<a href='http://www.gamingmasters.org/' target='_blank'>Game servers</a> are too active (osu!record makes them lag like fuck), please try again later when there is < 15 players/server.");
	}
}

	
@mkdir("/tmp/osurecord");
if(move_uploaded_file($_FILES['replay']['tmp_name'], "/dolan/osurecord/replay.osr")){

	if(file_exists($_FILES['osk']['tmp_name']) && is_uploaded_file($_FILES['osk']['tmp_name'])){
		if(!move_uploaded_file($_FILES['osk']['tmp_name'], "/dolan/osurecord/replay.osk")){
			die("osk upload fail");
		}
		chmod("/dolan/osurecord/replay.osk", 0666);
	}
		
	chmod("/dolan/osurecord/replay.osr", 0666);
	$mem->set("recording", true);
	//$mem->set("redownload", $_POST['update'] == 'derp');
	$mem->set("youtubeSessionToken", $_SESSION['sessionToken']);
	$mem->set("recordingIp", $_SERVER['REMOTE_ADDR']);
	PsExec("sudo /usr/local/bin/osurecord");
	echo "Upload successful, recording started.";
} else {
	die("pls upload .osr file");
}
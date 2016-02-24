<?php
				
			
session_start();
	
require_once 'Zend/Loader.php';
//Zend_Loader::loadClass('Zend_Gdata_YouTube');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');


$mem = new Memcache();
$mem->connect("localhost");

$_SESSION['developerKey'] = 'AI39si423IX1VxvyaXb3Pz_LJ8crNHtrM2qqyZ4w9PrE86E8TWoNLc-OrESmZSd0GIsovBH6bt6vSL894L4484SvqLDP-Yz-bg';

function authenticated()
{
	if (isset($_SESSION['sessionToken'])) {
		return true;
	}
}
				

function getAuthSubRequestUrl()
{
	$next = 'http://osurecord.weeaboo.com/';
	$scope = 'http://gdata.youtube.com';
	$secure = false;
	$session = true;
	return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, $session);
}				
			
	
if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
	$_SESSION['sessionToken'] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
	header("Location: /");
	exit;
}

			   
?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>osu!record</title>
<style type="text/css">
html,body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,p,blockquote,th,td{margin:0;padding:0;font-weight:400;}img,body,html,table,tr,td,th,a,a:hover,a:active,a:focus,a:link{border:0;outline:0}address,caption,cite,code,dfn,em,strong,th,var,a{font-style:normal;font-weight:normal;text-decoration:none}ol,ul{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;}q:before,q:after{content:'';}
h1 { letter-spacing: 8px; margin: 0; font-size: 26px; font-weight: bold; }
h3 { font-size: 11px; margin-top: 0; font-weight: bold; }
h1, h3, h3 a { color: #FF227A !important; font-weight: bold; }
a { text-decoration: underline; }
* { font-family: Verdana, sans-serif; }
body { font-size: 13px; }
#left { width: 500px; float: left; }
#right { width: 660px; float: right; margin-top: 20px; }
#container { width: 1170px; margin: 0 auto; }
.box, .termframe { border: 1px solid #ddd; background: #f7f7f7; border-radius: 5px; padding: 10px; margin: 10px 0; }
h4, #terminal > p { font-size: 14px; font-weight: bold; margin: 3px 0 12px 7px; }
ul { margin: 0 0 5px 20px; list-style: disc; }
#lazyframe { background: #FFFFFF; border: 1px solid #AAAAAA; margin-top: 10px; width: 475px; }
em { font-style: italic }
.term { font-size: 11px; font-family: "Consolas", "Courier New", monospace; }
</style>
<meta name="description" content="osu!record records and converts your osu! replay files into videos on your Youtube channel." />
<link rel="stylesheet" type="text/css" href="anyterm/anyterm.css?<?=filemtime("anyterm/anyterm.css")?>" />
<script type='text/javascript' src='jquery-1.8.3.min.js'></script>
<script type="text/javascript" src="anyterm/anyterm.js"></script>

<script type='text/javascript'>
$(document).ready(function(){
	var oldSrc = $("#refreshme").attr("src");
	setInterval(function(){
		$("#refreshme").attr("src", oldSrc+"?"+new Date().getTime());
	}, 1000);
	
	$("iframe[data-src]").each(function(){
		$(this).attr("src", $(this).data("src"));
	});
	
	create_term("terminal","Live status display",25,105,"","",50);
});
var on_close_goto_url = "";
</script>
</head>

<body>
<div id='container'>
	<div id='left'>
		<h1><a style='text-decoration: none; color: inherit; font-weight: inherit' href='/'>osu!record</a></h1>
		<h3 style='margin-left: 2px;'>By &lt;<a href='http://osu.ppy.sh/u/10886'>Darkimmortal</a>&gt;</h3>

		<div class='box'>
			<h4>What is this?</h4>
			<p>osu!record allows you to upload a tiny (usually under 50KB) <a href="http://osu.ppy.sh/">osu!</a> replay file, which will be automatically recorded and converted server-side, then uploaded to <em>your</em> Youtube channel - all in the space of 5-10 minutes.</p>
			<p><br /><form name="_xclick" action="https://www.paypal.com/uk/cgi-bin/webscr" method="post">
<input type="hidden" name="business" value="admin@imgkk.com">
<input type="hidden" name="item_name" value="Thanks for osu!record">
<input type="hidden" name="currency_code" value="GBP">
<input type="hidden" name="cmd" value="_donations">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="no_note" value="0">
<input type="image" src="Paypal_DonateButton.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form></p>
		</div>
			
		<div class='box'>
			<h4>Upload replay:</h4>
			
			<?php 
			if(authenticated()){
			?>
				<form enctype="multipart/form-data" action="upload.php" method="POST" target="lazyframe">

				<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
				.osr: <input name="replay" type="file" /> <br />
				.osk: <input name="osk" type="file" /> (optional, .zip ok too) <br />
				<?php /*<input name="update" type="checkbox" id="update" value="derp" /> <label for="update">Redownload beatmap before recording (use as last resort only)</label>
				<br /> */ ?><br />				
				<input type="submit" value="Upload Replay" style='font-weight: bold' />
				<button onclick='location.href="logout.php";return false;'>Youtube Logout</button>
				</form>

				<iframe src="about:blank" data-src="blank2.html" width="686" height="48" frameborder="0" name="lazyframe" id="lazyframe"></iframe>
			<?php 
			} else {
			?>
				<button onclick='location.href="<?php echo getAuthSubRequestUrl(); ?>";return false;'>Authenticate via Youtube first</button>
			<?php
			}
			?>
			
			
			<!--<h1>Discontinued :(</h1>
			<p>Unfortunately osu!record has been discontinued due to lack of server GPU power, any server offers would be very welcome!</p>-->
		</div>
		
		
		<div class='box'>
			<h4>Latest recording:</h4>
			<?php
			$recordingInfo = $mem->get("recordingInfo");
			if($recordingInfo)
			echo "<a href='http://osu.ppy.sh/b/{$recordingInfo['difficulty']}'>".htmlspecialchars($recordingInfo['artist'])." - ".htmlspecialchars($recordingInfo['title'])."</a>, played by <a href='http://osu.ppy.sh/u/".rawurlencode($recordingInfo['username'])."'>".htmlspecialchars($recordingInfo['username'])."</a>";
			?>
			<br /><br />
			<a href='gallery.php'>View all recordings</a>
		</div>
		
		<div class='box'>
			<h4>Last successful video:</h4>
			<?php
			if( $mem->get("lastVideoUrl")){ ?>
			<iframe width="480" height="360" src="about:blank" data-src="http://www.youtube.com/embed/<?=str_replace("http://www.youtube.com/watch?v=", "", $mem->get("lastVideoUrl"))?>?vq=hd720&theme=light" frameborder="0" allowfullscreen></iframe>
			<?php } ?>
		</div>
		
		<div class='box'>
			<h4>Notes:</h4>
			<ul>
				<li>This can only be used by <b>one person at a time</b> so wait patiently for your turn!</li>
				<li>You may encounter <b>lagspikes</b> in your recordings due to CPU/IO spikes, as it's a server.</li>
				<li>Framerate is fairly poor as there's no GPU acceleration AT ALL - purely llvmpipe software rendering</li>
				<li>You can <b>leave the page</b> after uploading and it will continue by itself</li>
		<?php /*			<!--<li>Anyone doing stupid shit like uploading continually will have their IP and Youtube accounts <b>banned</b>. (unless prior agreement is made)</li>
				<li><b>Audio offset</b> might not be perfect, can someone skilled at timing please contact me if you think it needs improved (currently 0ms)</li>--> */ ?>
				<li><a href='http://osu.ppy.sh/forum/t/108092/start=0'>osu! forum discussion thread</a></li>
			</ul>
		</div>
		
	</div>
	<div id='right'>
	<?php /*
<div style='width:468px;height:60px;margin:0 auto;' class='ad'>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-9201828541367271";
google_ad_slot = "1924207814";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
*/ ?>

		<div id='terminal' style='width:670px;height:405px;'></div>
				<?php /*
<div style='width:468px;height:60px;margin:0 auto;' class='ad'>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-9201828541367271";
google_ad_slot = "1924207814";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
*/ ?>

		<div class='box' style='width:500px;height:375px;'><img id='refreshme' src='screenshot.jpg' width='500' height='375' alt="osu!'s not running or something has gone horribly wrong" /></div>
		
<div style='width:468px;height:60px;margin:0;' class='ad'>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-9201828541367271";
google_ad_slot = "1924207814";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
	</div>
</div>
<script type="text/javascript">

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
<?php
  
	// screen -S osu -p 0 -X stuff "php /home/darkisock/osu/osurecord.php`echo -ne '\015'`"


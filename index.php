<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head>
<!-- http://www.huement.com/code/jPlayer2 -->
<title>Huement | $(jPlayer2).ajax();</title>
<meta http-equiv="Content-Type" content="text/html;">
<link rel="stylesheet" media="screen" type="text/css" href="css/jgrowl.css" />
<link rel="stylesheet" media="screen" type="text/css" href="skin/lucky2.css" />
<link rel="stylesheet" media="screen" type="text/css" href="css/ajaxPlayer2.css" />
<script type="text/javascript" src="js/jquery-1.5.js"></script>
<script type="text/javascript" src="js/jgrowl.min.js"></script>
<script type="text/javascript" src="js/jquery.select.js"></script>
<script type="text/javascript" src="js/jquery.jplayer.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/beatMachine.js"></script>
</head>
<body style="overflow-x:hidden;overflow-y:auto;" >
<div class='shadow' style="width:100%;height:60px;margin:0;padding:10px;background:#3b3b3b;border-bottom:1px solid #999;">
<h2 class='textShadow' style="text-align:center">ajaxPlayer 2 [v3]</h2>
</div>
<div id="alphaSticky" class="boxF" style="width:98%;margin-left:1%;min-width:1100px;">
	<div id="rightToolBox" style="width:600px;background:#FFF;">
		<div id="genres">
			<h2>Control.Panel</h2>
				<hr />
				
				<p class="mailTools" style="margin:10px 0 0 20px">
					<button id="addPHP" class='MAC'>addSongs</button>
					<button id="Infinity" class='MAC' onclick="return false;">infinitePlay</button>
					<button id="shuffleBtn" class='MAC' onclick="return false;">Shuffle</button>
					<button id="getSomePHP" class='MAC' onclick="return false;">Reset</button>
				</p>
				<br />
				<p class="footnote" style="width:500px;margin:0 auto;font-size:14px">hopefully the functions of these buttons come as no suprise. all the buttons manipulate the playlist with ajax in some form another. PHP5 is required for the backend handeling.</p><br /><hr />
				<div id="difDelete" style="width:200px;margin:0 auto;">
				<select id="deleteSelector" name="example" style="margin:10px 0 0 20px" class="round_sb" size="1">
				<!-- Added via jQuery 
				<option style="font-size:13px;" value="redHead">redHead</option> -->
				</select>
				<button class="MAC" style="margin-left:10px;height:36px;margin-top:5px" id="deleteBtn">delete #</button>
				</div><br />
				<p class="footnote" style="width:500px;margin:0 auto;font-size:14px">this is the another varition for deleting songs. it actually works alot better than a delete button for ever song (in terms of efficiency and amount of code required).</p>
				<br />
				<br />
				<button class="MAC" style="height:30px;width:30px;display:none" onclick=" deleteRoundTwo();return false">??</button>
		</div>
	</div>
<!-- CLEAR -->
	<div id="content_player" style="margin:50px 0 0 10px">
		<!-- jPlayer 2 -->
			<div id="jquery_jplayer_3" class="jp-jplayer"></div>
			  <div class="jp-audio">
				<div class="jp-type-playlist">
					<div id="jp_interface_3" class="jp-interface">
						<ul class="jp-controls">
							<li><a href="#" class="jp-play" tabindex="1">play</a></li>
							<li><a href="#" class="jp-pause" tabindex="1" style="display: none; ">pause</a></li>
							<li><a href="#" class="jp-stop" tabindex="1">stop</a></li>
							<li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
							<li><a href="#" class="jp-unmute" tabindex="1" style="display: none; ">unmute</a></li>
							<li><a href="#" class="jp-previous" tabindex="1">previous</a></li>
							<li><a href="#" class="jp-next" tabindex="1">next</a></li>
						</ul>
						<div class="jp-progress">
							<div class="jp-seek-bar" style="width: 100%; ">
								<div class="jp-play-bar" style="width: 0%; "></div>
							</div>
						</div>
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value" style="width: 80%; "></div>
						</div>
						<div class="jp-current-time">00:00</div>
						<div class="jp-duration">04:20</div>
					</div>
					
					<div id="panel">
						<div id="jp_playlist_3" class="jp-playlist">
							<ul class='playList'>
							</ul>
						</div>
					</div>
					
					<div class="slide btn-slide"><div id="current-time"></div><div id="duration"></div></div>
				</div>
			</div>
		<!-- jPlayer 2 -->
	</div>
</div>
</body>
</html>
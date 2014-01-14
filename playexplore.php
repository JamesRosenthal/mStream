<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mstream Media Player - Brainchild of Paul Sori</title>

    <script src="js/modernizr.js"></script>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

<!-- The following are all for jplayer -->
<link href="/mstream/jPlayer24/skin/prettify-jPlayer.css" rel="stylesheet" type="text/css" />
<link href="/mstream/jPlayer24/skin/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/mstream/jPlayer24/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="/mstream/jPlayer24/add-on/jplayer.playlist.min.js"></script>

<link rel="stylesheet" href="css/foundation.css" />
<link rel="stylesheet" href="css/master.css">

<script type="text/javascript">
$(document).ready(function(){
//this sets up the first dir you see when you long on.   
	//EDIT THESE VALUES:
		//'startdir' is the location of the initial directory from your computer's root.  PHP needs this
	var startdir = '/Applications/MAMP/htdocs/audiofiles/';
		//'startdirstripped' is the location of the initial directory from your server's webroot
	var startdirstripped = '/audiofiles/';
		//this is as far as you want the user going back.  A value of '/' means you want your user going back as far as your webroot
	var rootdir='/';
	//DONE WITH EDITTING



//initialize jPlayer. This needs to be done before doing naything else
	var myPlaylist = new jPlayerPlaylist({
			jPlayer: "#jquery_jplayer_N",
			cssSelectorAncestor: "#jp_container_N"
		}, 

		[], //feed the playlsit an empty json value

		{
			playlistOptions: {
				enableRemoveControls: true
			},
			swfPath: "/CSS3MusicList/jPlayer24/Jplayer.swf",
			supplied: "mp3",
			smoothPlayBar: true,
			keyEnabled: true,
			audioFullScreen: true
		});



//this code sets up the file browser.  It runs once when the page loads and is never used again
	//set a hidden input to the curent directory values
	$('#currentdir').val(startdirstripped);
	$('#currentdirlong').val(startdir);
	//send this directory to be parsed and displayed
	senddir(startdir);



//when you click an mp3, add it to the playlist
	$("#filelist").on('click', 'div.filez', function() {
		//get the mp3 name and the directory its in
		var addfile=$(this).html();
		var thedir=$('#currentdir').val();
		//put these together to send to jPlayer
		var mp3location = thedir+addfile;

		//add it to the playlist
		myPlaylist.add({
			title: addfile,
			//artist:"The Stark Palace",
			mp3: mp3location
			//poster: "http://www.jplayer.org/audio/poster/The_Stark_Palace_640x360.png"
		});
	});

//when you click 'add direcotry', add entire directory to the playlist
	$("#addall").on('click', function() {
		//make an array of all the mp3 files in the curent directory
		var elems = document.getElementsByClassName('filez');
   		var arr = jQuery.makeArray(elems);

   		//var with the current directory
		var thedir=$('#currentdir').val();

		//loop throug harray and add eah file to the playlist
		$.each( arr, function() {
			var addfile=this.innerHTML;
			var mp3location= thedir+addfile

			// add it to the playlist
			myPlaylist.add({
				title: addfile,
				//artist:"The Stark Palace",
				mp3: mp3location
				//poster: "http://www.jplayer.org/audio/poster/The_Stark_Palace_640x360.png"
			});
		});
	});

//when you click on a directory, go to that directory
	$("#filelist").on('click', 'div.dirz', function() {
		//get the html of that class
		var adddir=$(this).html();
		var curdirlong=$('#currentdirlong').val();
		var curdir=$('#currentdir').val();
		var location = curdirlong+adddir+'/';

		//update the hidden fileds with the new location
		$('#currentdirlong').val(location);
		$('#currentdir').val(curdir+adddir+'/');

		//pass this value along
		senddir(location);
	});

// when you click the back directory
	$("#filelist").on('click', 'div.back', function() {
		if($('#currentdir').val()!=rootdir){
			//get the html of that class
			var adddir=$(this).html();
			var curdirlong=$('#currentdirlong').val();
			var location = curdirlong+adddir+'/';

			//break apart the directory into an array of strings.  This will be used to chop off the last directory
			var arrayOfStrings = curdirlong.split('/');

			var builddirlong='/';
			var builddir='/';

			//loop through an construct a new directory
			for (var i=0; i < arrayOfStrings.length-2; i++){
				if(i!=0){
					builddirlong=builddirlong+arrayOfStrings[i]+'/';
				}
				if(i>3){
					builddir=builddir+arrayOfStrings[i]+'/';
				}
			}
			//console.log(builddirlong);
			//console.log(builddir);

		$('#currentdirlong').val(builddirlong);
		$('#currentdir').val(builddir);

		senddir(location);
		}
		});



//send a new directory to be parsed.
	function senddir(dirr){
		$.post('/mstream/dirparser.php', { dir: dirr}, function(response) {
		    //console.log("Response: "+response);
		    //hand this data off to be printed on the page
		    printdir(response);
		});
	}

//function that will recieve JSON from dirparser.php.  It will then make a list of the directory and tack on classes for functionality
	function printdir(dir){
		//console.log(jQuery.parseJSON(dir));
		var dirty = jQuery.parseJSON(dir);

		//clear the list
		$('#filelist').empty();

		//parse through the json array and make an array of corresponding divs
		var filelist = [];
		$.each(dirty, function() {
			if(this.type=='mp3'){
				filelist.push('<div class="filez">'+this.link+'</div>');
			}
			if(this.type=='dir'){
				filelist.push('<div class="dirz">'+this.link+'</div>');
			}
		});

		//add a listing to go back
		if($('#currentdir').val()!=rootdir){
			filelist.push('<div class="back">..</div>');
		}

		//console.log(filelist);
		$('#filelist').html(filelist);
	}
});

</script>
  </head>

<body>

<?php
	session_start();
	if($_SESSION["login"]==1){

	}
	else{
		header("Location: index.php");
		exit;
	}
?>

<body>

<div class="off-canvas-wrap">
  <div class="inner-wrap">
    <nav class="tab-bar">

      <section class="left-small">
        <a class="left-off-canvas-toggle menu-icon" ><span></span></a>
      </section>

      <section class="right tab-bar-section">
        <h1 class="title"><img src="img/mstream-logo.png" class="logo" alt="MStream" width="181" height="auto"></h1>
      </section>

    </nav>

    <!-- Off Canvas Menu -->
    <aside class="left-off-canvas-menu">
      <ul class="off-canvas-list">
        <li><label>Subdirectories(?)</label></li>
        <li><a href="#">The Psychohistorians</a></li>
        <li><a href="#">The Psychohistorians</a></li>
        <li><a href="#">The Psychohistorians</a></li>
        <li><a href="#">The Psychohistorians</a></li>
        <li><a href="#">The Psychohistorians</a></li>
        <li><a href="#">The Psychohistorians</a></li>
        <li><a href="#">The Psychohistorians</a></li>
        ...
      </ul>
    </aside>

    <section class="main-section">
      <!-- content goes in here -->
    	<div class="row">
	
		<div class="large-6 columns">
			<h3>Your Library</h3>
			<div class='masterlist' id='filelist'>
				<div class="filez">beanz</div>
			</div>
		</div><!-- /6 columns -->

		<div class="large-6 columns">

				<input type="hidden" id="currentdir"></input>
				<input type="hidden" id="currentdirlong"></input>
				
				<h3>Your Playlist</h3>
				
				<div class='controls' id='controls'>
					<div id='addall' class="add_all">add directory</div>
				</div>

				<div id="jp_container_N" class="jp-video jp-video-270p">
					<div class="jp-type-playlist">
						<div id="jquery_jplayer_N" class="jp-jplayer" style="width: 480px; height: 270px;"><img id="jp_poster_0" src="http://www.jplayer.org/audio/poster/The_Stark_Palace_640x360.png" style="width: 480px; height: 270px; display: inline;"><audio id="jp_audio_0" preload="metadata" src="http://www.jplayer.org/audio/ogg/TSP-01-Cro_magnon_man.ogg"></audio><video id="jp_video_0" preload="metadata" style="width: 0px; height: 0px;"></video></div>
						<div class="jp-gui" style="">
							<div class="jp-video-play" style="display: none;">
								<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
							</div>
							<div class="jp-interface">
								<div class="jp-progress">
									<div class="jp-seek-bar" style="width: 100%;">
										<div class="jp-play-bar" style="width: 0%;"></div>
									</div>
								</div>
								<div class="jp-current-time">00:00</div>
								<div class="jp-duration">04:27</div>
								<div class="jp-controls-holder">
									<ul class="jp-controls">
										<li><a href="javascript:;" class="jp-previous" tabindex="1">previous</a></li>
										<li><a href="javascript:;" class="jp-play" tabindex="1" style="">play</a></li>
										<li><a href="javascript:;" class="jp-pause" tabindex="1" style="display: none;">pause</a></li>
										<li><a href="javascript:;" class="jp-next" tabindex="1">next</a></li>
										<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
										<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute" style="">mute</a></li>
										<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute" style="display: none;">unmute</a></li>
										<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume" style="">max volume</a></li>
									</ul>
									<div class="jp-volume-bar" style="">
										<div class="jp-volume-bar-value" style="width: 80%;"></div>
									</div>
									<ul class="jp-toggles">
										<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen" style="">full screen</a></li>
										<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen" style="display: none;">restore screen</a></li>
										<li><a href="javascript:;" class="jp-shuffle" tabindex="1" title="shuffle" style="">shuffle</a></li>
										<li><a href="javascript:;" class="jp-shuffle-off" tabindex="1" title="shuffle off" style="display: none;">shuffle off</a></li>
										<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat" style="">repeat</a></li>
										<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off" style="display: none;">repeat off</a></li>
									</ul>
								</div>
								<div class="jp-title" style="display: none;">
									<ul>
										<li>Cro Magnon Man <span class="jp-artist">by The Stark Palace</span></li>
									</ul>
								</div>
							</div>
						</div>
						<div class="jp-playlist">
							<ul style="display: block;"><li class="jp-playlist-current"><div><a href="javascript:;" class="jp-playlist-item-remove" style="">×</a><a href="javascript:;" class="jp-playlist-item jp-playlist-current" tabindex="1">Cro Magnon Man <span class="jp-artist">by The Stark Palace</span></a></div></li></ul>
						</div>
						<div class="jp-no-solution" style="display: none;">
							<span>Update Required</span>
							To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
						</div>
					</div>
					</div>
		</div><!-- /6 columns -->
	</div><!-- /row -->
    </section>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>


	
    <script src="js/jquery.js"></script>
    <script src="js/foundation.min.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
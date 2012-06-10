//Stock JSON formatted Playlist
var alpha = [
{name: "Isaac Graham - Photographs And Histories",mp3: "http://huement.s3.amazonaws.com/demos/songs/Isaac%20Graham%20-%20Empty%20Vessels/01%20Photographs%20And%20Histories.mp3"},
{name: "Isaac Graham - Gold And Steel",mp3: "http://huement.s3.amazonaws.com/demos/songs/Isaac%20Graham%20-%20Empty%20Vessels/02%20Gold%20And%20Steel.mp3"},
{name: "Isaac Graham - Karl Marx And The Reds",mp3: "http://huement.s3.amazonaws.com/demos/songs/Isaac%20Graham%20-%20Empty%20Vessels/03%20Karl%20Marx%20And%20The%20Reds.mp3"}];

/* jPlayer2 - Playtime is Over */
function Playlist(instance, playlist, options) {
		//alert("i" + instance + " p" + playlist + " o" + options);
		//if (var === undefined)
		var self = this;

		this.instance = instance; // String: To associate specific HTML with this playlist
		this.playlist = playlist; // Array of Objects: The playlist
		this.options = options; // Object: The jPlayer constructor options for this playlist

		this.current = 0;

		this.cssId = {
			jPlayer: "jquery_jplayer_",
			interface: "jp_interface_",
			playlist: "jp_playlist_"
		};
		this.cssSelector = {};
		
		
		$.each(this.cssId, function(entity, id) {
			self.cssSelector[entity] = "#" + id + self.instance;
		});
		
		//alert(this.options);
		this.options.cssSelectorAncestor = this.cssSelector.interface;
		if(!this.options.cssSelectorAncestor) {
			this.options.cssSelectorAncestor = this.cssSelector.interface;
		}

		$(this.cssSelector.jPlayer).jPlayer(this.options);

		$(this.cssSelector.interface + " .jp-previous").click(function() {
			self.playlistPrev();
			$(this).blur();
			return false;
		});

		$(this.cssSelector.interface + " .jp-next").click(function() {
			self.playlistNext();
			$(this).blur();
			return false;
		});
	};

function getThisShitBumping(){
	Playlist.prototype = {
		displayPlaylist: function() {
			var self = this;
			$("select#deleteSelector").empty();
			//alert(self);
			$(this.cssSelector.playlist + " ul").empty();
			for (i=0; i < this.playlist.length; i++) {
				var colorArr = ["redFloyd","orangeFloyd","yellowFloyd","greenFloyd","blueFloyd","purpleFloyd"];
				if(i == 0){ var colorClass = colorArr[0]; }
				if(i == 1){ var colorClass = "orangeFloyd"; }
				if(i == 2){ var colorClass = "yellowFloyd"; }
				if(i == 3){ var colorClass = "greenFloyd"; }
				if(i == 4){ var colorClass = "blueFloyd"; }
				if(i == 5){ var colorClass = "purpleFloyd"; }
				if(i >= 6){
				shuffleArr = $.shuffle(colorArr);
				var colorClass = colorArr[0];
				}
				
				var listItem = (i === this.playlist.length-1) ? "<li class='jp-playlist-last' id='S"+ i +"'>" : "<li id='S"+ i +"'>";
				listItem += "<a href='#' id='" + this.cssId.playlist + this.instance + "_item_" + i +"'class='" + i + " " + colorClass + "' tabindex='1'>"+ this.playlist[i].name +"</a><button class='negative' value='" + i +"' id='" + i +"' onclick=\"return false;\">x</button>";
				
				$("#deleteSelector").addOption(i, i+1);
				// Create links to free media
				if(this.playlist[i].free) {
					var first = true;
					listItem += "<div class='jp-free-media'>(";
					$.each(this.playlist[i], function(property,value) {
						if($.jPlayer.prototype.format[property]) { // Check property is a media format.
							if(first) {
								first = false;
							} else {
								listItem += " | ";
							}
							listItem += "<a id='" + self.cssId.playlist + self.instance + "_item_" + i + "_" + property + "' href='" + value + "' tabindex='1'>" + property + "</a>";
						}
					});
					listItem += ")</span>";
				}

				listItem += "</li>";

				// Associate playlist items with their media
				$(this.cssSelector.playlist + " ul").append(listItem);
				$(this.cssSelector.playlist + "_item_" + i).data("index", i).click(function() {
					var index = $(this).data("index");
					if(self.current !== index) {
						self.playlistChange(index);
					} else {
						$(self.cssSelector.jPlayer).jPlayer("play");
					}
					$(this).blur();
					return false;
				});
				
				//setTimeout("deleteRoundTwo()", 2000);
				
				// Disable free media links to force access via right click
				if(this.playlist[i].free) {
					$.each(this.playlist[i], function(property,value) {
						if($.jPlayer.prototype.format[property]) { // Check property is a media format.
							$(self.cssSelector.playlist + "_item_" + i + "_" + property).data("index", i).click(function() {
								var index = $(this).data("index");
								$(self.cssSelector.playlist + "_item_" + index).click();
								$(this).blur();
								return false;
							});
						}
					});
				}
			}
		},
		playlistInit: function(autoplay) {
			if(autoplay) {
				this.playlistChange(this.current);
			} else {
				this.playlistConfig(this.current);
			}
		},
		playlistConfig: function(index) {
			$(this.cssSelector.playlist + "_item_" + this.current).removeClass("jp-playlist-current").parent().removeClass("jp-playlist-current");
			$(this.cssSelector.playlist + "_item_" + index).addClass("jp-playlist-current").parent().addClass("jp-playlist-current");
			this.current = index;
			$(this.cssSelector.jPlayer).jPlayer("setMedia", this.playlist[this.current]);
				
				showDeleteBtn(index);
				//$('button#'+index).fadeIn();
				//alert(index);
				//alert(this + " showDelete");
		},
		playlistChange: function(index) {
			this.displayPlaylist();
			this.playlistConfig(index);
			$(this.cssSelector.jPlayer).jPlayer("play");
		},
		playlistNext: function() {
			this.displayPlaylist();
			var index = (this.current + 1 < this.playlist.length) ? this.current + 1 : 0;
			this.playlistChange(index);
			//alert(index);
		},
		playlistPrev: function() {
			this.displayPlaylist();
			var index = (this.current - 1 >= 0) ? this.current - 1 : this.playlist.length - 1;
			this.playlistChange(index);
		},
		addMedia: function(media) { 
  		this.playlist.push(media); 
  		this.displayPlaylist(); 
		}
	};
}

//ADD SONG FUNCTION
//Used in infinity Playlist
//Merges new songs into old array.
function addANOTHERSONG(audioPlaylist) {
	//console.debug(alpha);
	//console.debug(audioPlaylist);
	var oldArray = audioPlaylist;
		$.ajax({
	 		 type:"POST",
   			 url: 'js/generate.php',
			 dataType:'json',
  			 success: function(data) {
  			 var alpha = $.merge(oldArray, data);
  			 deleteRoundTwo();
			 }
  		});
	$.jGrowl("Songs Will Load when player<br />updates (next prev finish)");
}

//CONSIDER DELETING THIS
function deleteRoundTwo(){
 	
 	 // This Function Applies what would be the $(doc).ready() click function
 	 // However calling it in the Doc Ready Isn't and Option
 	 // Because the content is ajax. and not present at doc ready
 	 
	 //Prolly a better way to set this up?. JS isn't my 
	 //best language. this way gets the job done tho.
	 //Just constantly reapply the click function to 
	 //any ajax content.
	 $('button.negative').click(function deleteSong(){ 	
		var songI = $(this).val();
		var targetSong = audioPlaylist.playlist[songI];
		var fullPlaylist = audioPlaylist.playlist;
		var arr = jQuery.grep(fullPlaylist , function(songI) {
		return songI != targetSong;
		});
		
			audioPlaylist = new Playlist('3', arr, {
				ended: function() {
				audioPlaylist.playlistNext();
				},
				play: function() {
				$(this).jPlayer("pauseOthers");
				},
				supplied: "mp3"
				});
				audioPlaylist.displayPlaylist();
				$('button.negative').hide();
				audioPlaylist.playlistNext();
				audioPlaylist.playlistPrev();
			return false;
	});
	
	//INFIITE PLAYLIST ACTIVE?
	//if it is, must stop and restart it.
	if(infin == 1){
	alert(infin);
	}
}

function showDeleteBtn(index) {
	//This function hides all none active
	//delete buttons from view.
	$('button.negative').hide();
	$('button#'+index).fadeIn();
}

function togglePlaylist(){
	
	$("a.jp-toggle-on").click(function(){
		$("a.jp-toggle-on").hide();
		$("a.jp-toggle-off").show();
		
			//Switch To Directory View
			$("ul.playList").fadeSliderToggle();
			//$("ul.directoryView").fadeToggle();
			
			
		return false;
	});
	
	$("a.jp-toggle-off").click(function(){
		$("a.jp-toggle-off").hide();
		$("a.jp-toggle-on").show();
			
			//Switch To Playlist View
			$("ul.playList").fadeSliderToggle();
			$("ul.directoryView").fadeSliderToggle();
		
		return false;
	});
}
function directoryView() {
	$(".directoryView").fadeSliderToggle();
	$.ajax({
		type:"POST",
		url: 'http://dev.huement.com/ajaxplayer/songs/index.php',
		success: function(data) {
		$("ul.directoryView").html(data);
		//	console.debug(data);
		//console.debug($(".directoryView").html());
		//$(".directoryView").html(" ");
		setTimeout("faceboxClick()", 1000);
		}
	});
	//setTimeout("faceboxClick()", 1300);
}
var texttext;
function faceboxClick(data){
	
	$('.faceboxMore').click(function(){
		var idDiv = $(this).attr("title");
		jQuery.facebox({ div: idDiv});
		console.debug($(idDiv).html());
		return false;
	});
	
}

function shuffleToggle(){
	if(stog == 1){
		$(".jp-shuffle").addClass("active");
		stog=0;
	} else {
		$(".jp-shuffle").removeClass("active");
		stog=1;
	}
}

var audioPlaylist;
var counter = 0;
var infin = 0;
var stog = 1;
//DOCUMENT READY  	
$(document).ready(function(){
		
	//active delete for the first time. CONSIDER DELETING
  setTimeout("deleteRoundTwo()", 2000);
  
	//Setup Directory View via Ajax
	$(".jp-toggle-on").click(function(){
			directoryView();
	});

	$(".jp-shuffle").click(function(){
			shuffleToggle();
			return false
	});

    //Call the Prototype Object Into Exsistance.
	getThisShitBumping();	
	
	//Use the object...
	//INTIAL PLAYLIST SETUP FROM GIVEN STOTIC PLAYLIST
  audioPlaylist = new Playlist("3", alpha, {
		ready: function() {
		audioPlaylist.displayPlaylist();
		audioPlaylist.playlistInit(false); // Parameter is a boolean for autoplay.
		},
		ended: function() {
		audioPlaylist.playlistNext();
		},
		play: function() {
		$(this).jPlayer("pauseOthers");
		deleteRoundTwo();
		},
		supplied: "mp3"
	});
	/* JPLAYER PLAYLIST MANIPULATION */
	
	/* ADD RESET SHUFFLE DELETE INIFITE */
	//Toggle Playlist Button
	togglePlaylist();
	
	//ADD PHP SONGS ON CLICK
	$('#addPHP').click(function addPHP(){
	 var oldArray = audioPlaylist.playlist;
		deleteRoundTwo();
		$.ajax({
	 		 type:"POST",
   			 url: 'js/generate.php',
			 dataType:'json',
  			 success: function(data) {
  			 var alpha = $.merge(oldArray, data);	 
  			 $('button.negative').hide();
			 audioPlaylist.displayPlaylist();	 
			 audioPlaylist.playlistPrev();	
			 audioPlaylist.playlistNext();
			 deleteRoundTwo();
			 }
  		}); 
  		setTimeout("deleteRoundTwo()", 1500);
  		return false;
	 });
	 
	//Get DYNAMIC PHP FILE
	//RESET PLAYLIST W/ 2 New Songs
	$('#getSomePHP').click(function(){
	if(infin == 1){
	clearInterval(refreshIntervalId);
	}
	 $.ajax({
	 		 type:"POST",
   			 url: 'js/generate.php',
			 dataType:'json',
  			 success: function(data) {
  			 		//Build New Plyalist Object w/ Results
				    audioPlaylist = new Playlist('3', data, {
					ended: function() {
					audioPlaylist.playlistNext();
					},
					play: function() {
					$(this).jPlayer("pauseOthers");
					},
					supplied: "mp3"
					});
					$.jGrowl("New Playlist Added");
					audioPlaylist.displayPlaylist();
					if(infin == 1){
  					GoGetEm = audioPlaylist.playlist;
					refreshIntervalId = setInterval('addANOTHERSONG(GoGetEm)', 5000);
					}
					$('button.negative').hide();
					audioPlaylist.displayPlaylist();	 
					audioPlaylist.playlistPrev();	
					audioPlaylist.playlistNext();
					deleteRoundTwo();
			 }
  			});
  		if(deleteBtn == 0){
			$("#deleteBar").fadeIn();
			deleteRoundTwo();
		}
  		return false;
	 });
	
	
	//SHUFFLE TRACKS
	$('#shuffleBtn').click(function (){
		$.shuffle(audioPlaylist.playlist);
		audioPlaylist.current = 0;
		audioPlaylist.displayPlaylist();
		deleteRoundTwo();
		$('button.negative').hide();
		return false;
	});
	
	 //DELETE SONG based on Selector Value
	 $('#deleteBtn').click(function deleteSong(){
	 	
		var songI = $('#deleteSelector').val();
		var targetSong = audioPlaylist.playlist[songI];
		var fullPlaylist = audioPlaylist.playlist;
		var arr = jQuery.grep(fullPlaylist , function(songI) {
   		return songI != targetSong;
		});	
		 	audioPlaylist = new Playlist('3', arr, {
				ended: function() {
				audioPlaylist.playlistNext();
				},
				play: function() {
				$(this).jPlayer("pauseOthers");
				},
				supplied: "mp3"
				});
				audioPlaylist.displayPlaylist();
				audioPlaylist.playlistNext();
				audioPlaylist.playlistPrev();
				deleteRoundTwo();
		return false;
	});
	
	//INFINITE PLAYLIST TOGGLE. Very Tricky.
	var infin = 0;
	$('#Infinity').toggle(
	function infinityPlaylist() {
		//console.debug(audioPlaylist);
		GoGetEm = audioPlaylist.playlist;
		setTimeout('addANOTHERSONG(GoGetEm)', 2500);
		refreshIntervalId = setInterval('addANOTHERSONG(GoGetEm)', 180000);
		infin = 1;
		$.jGrowl("Continous Play<br/>Enabled",{ easing: 'easeInOutElastic', life: 2300 });
	}, 
	function infinityPlaylistOFF() {
		clearInterval(refreshIntervalId);
		$.jGrowl("Continous Play<br/>Disabled",{ life: 2300 });
		infin = 0;
	});
	
	/* END JPLAYER PLAYLIST MANIPULATION */
	
	//Playlist Toggle
	$(".btn-slide").click(function(){
		$("#panel").fadeSliderToggle("hide");
		$(".jp-directoryView").fadeSliderToggle("hide");
		return false;
	});
	
	//MouseOver Fade
	$("img.fadeImg").animate({"opacity": "0.7"}, "slow");
	$("img.fadeImg").hover(
	function() {
	$(this).stop().animate({"opacity": "1"}, "slow");
	},
	function() {
	$(this).stop().animate({"opacity": "0.7"}, "slow");
	});

	firstTime = 0;
	var counter = 0;
    
    $("input:checkbox").click(function() {
 	 var bol = $("input:checkbox:checked").length >= 6;     
 	 $("input:checkbox").not(":checked").attr("disabled",bol);
	});
    
    var options = { 
        success:      
        function(data) { 
        var queryString = $('#social').formSerialize(); 
        $.jGrowl(data);        
    	}  // post-submit callback 
    }; 

	$(".tipTip").tipTip();
});

var xmlPlaylist = [{}];
			
function Playlist(instance, playlist, options) {
		var self = this;
		this.instance = instance; // String: To associate specific HTML with this playlist
		this.playlist = playlist; // Array of Objects: The playlist
		this.options = options; // Object: The amPlayer constructor options for this playlist

		this.current = 0;

		this.cssId = {
			ampPlayer: "amp_",
			interface: "amp_interface_",
			playlist: "amp_playlist_"
		};
		this.cssSelector = {};

		$.each(this.cssId, function(entity, id) {
			self.cssSelector[entity] = "#" + id + self.instance;
		});

		if(!this.options.cssSelectorAncestor) {
			this.options.cssSelectorAncestor = this.cssSelector.interface;
		}

		$(this.cssSelector.amPlayer).jPlayer(this.options);

		$(this.cssSelector.interface + " .amp-previous").click(function() {
			self.playlistPrev();
			//$(this).blur();
			return false;
		});

		$(this.cssSelector.interface + " .amp-next").click(function() {
			self.playlistNext();
			//$(this).blur();
			return false;
		});
};

function xmlJamOut() {
	Playlist.prototype = {
		displayPlaylist: function() {
			var self = this;
			$(this.cssSelector.playlist + " ul").empty();
			for (i=0; i < this.playlist.length; i++) {
				var listItem = (i === this.playlist.length-1) ? "<li class='amp-playlist-last'>" : "<li>";
				listItem += "<a href='#' id='" + this.cssId.playlist + this.instance + "_item_" + i +"' tabindex='1' class='hue'>"+ this.playlist[i].name +"</a>";

				// Create links to free media
				if(this.playlist[i].free) {
					var first = true;
					listItem += "<div class='amp-free-media'>(";
					$.each(this.playlist[i], function(property,value) {
						if($.amPlayer.prototype.format[property]) { // Check property is a media format.
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
						$(self.cssSelector.amPlayer).jPlayer("play");
					}
					$(this).blur();
					return false;
				});

				// Disable free media links to force access via right click
				if(this.playlist[i].free) {
					$.each(this.playlist[i], function(property,value) {
						if($.amPlayer.prototype.format[property]) { // Check property is a media format.
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
			$(this.cssSelector.playlist + "_item_" + this.current).removeClass("amp-playlist-current").parent().removeClass("amp-playlist-current");
			$(this.cssSelector.playlist + "_item_" + index).addClass("amp-playlist-current").parent().addClass("amp-playlist-current");
			this.current = index;
			$(this.cssSelector.amPlayer).jPlayer("setMedia", this.playlist[this.current]);
		},
		playlistChange: function(index) {
			this.playlistConfig(index);
			$(this.cssSelector.amPlayer).jPlayer("play");
		},
		playlistNext: function() {
			var index = (this.current + 1 < this.playlist.length) ? this.current + 1 : 0;
			this.playlistChange(index);
		},
		playlistPrev: function() {
			var index = (this.current - 1 >= 0) ? this.current - 1 : this.playlist.length - 1;
			this.playlistChange(index);
		}
	};
}

/* declaring here allows for resuse */
var audioPlaylist;
var obj = new Array();

$(document).ready(function(){

	//Call the Prototype Object Into Exsistance.
	xmlJamOut();
	
	//This is the ajax call to get the Playlist Data
	//Then Create the amPlayer object with the data
	var obj = new Array();
		$.ajax({
			type: "GET",
			url: "playlist.xml",
			dataType: "xml",
			success: function(xml) {
				delete xmlPlaylist;
				xmlPlaylist = [];
				$(xml).find('item').each(function(){
					var title = $(this).find('title').text();
					var mp3 = $(this).find('mp3').text();
					var ogg = $(this).find('ogg').text();
					obj = {'name':title, 'mp3':mp3, 'ogg':ogg};
					xmlPlaylist.push(obj);
				});	
				
				audioPlaylist = new Playlist("1", xmlPlaylist, 
					{
					ready: function() {
						audioPlaylist.displayPlaylist();
						audioPlaylist.playlistInit(false); // Parameter is a boolean for autoplay.
					},
					ended: function() {
						audioPlaylist.playlistNext();
					},
					play: function() {
						$(this).jPlayer("pauseOthers");
					},
					swfPath: "js",
					supplied: "mp3, ogg"
				});
				//console.debug(audioPlaylist.playlist);
				audioPlaylist.displayPlaylist();	
				audioPlaylist.playlistPrev();	
				audioPlaylist.playlistNext();
				audioPlaylist.playlistInit(false);
			}
		});
		
	$(".amp-play-bar").progressbar({
		value: 0
	}).width(450);
	
	//#audiovolume = div.jp-volume-bar for audioplayer
	$('.amp-volume-bar').slider({
		range: "min",
		value: 80,
		change: function(){ 
			var newVolume = $(".amp-volume-bar").slider("value");
			var Volume = newVolume / 100;
			$("#amp_1").jPlayer("volume", Volume);
			//return false;
			if(newVolume > 0){
			$("#amp_1").jPlayer("unmute");
			$('#amp_1 .ai-maxvol').hide();
			$('#amp_1 .ai-novol').show();
			} else {
			$("#amp_1").jPlayer("mute");
			$('#amp_1 .ai-maxvol').show();
			$('#amp_1 .ai-novol').hide();
			}
		}
	}).width(180);
	
});
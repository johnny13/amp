<?php
//Directories for your files. Current layout is media/$option.
//inside these general directories you place your folder with the layout
// $artistName - $albumName 
// that format for the title will be parsed by php properly. 

$genres = array('one','two','three');
shuffle($genres);
$sponsor = $genres[0];

//Using absolute urls is a must for mp3 playback.
$webby = "http://www.huement.com/code/ajaxPlayer2/media/";
$locally = "/Library/WebServer/Documents/Huement/code/ajaxPlayer2/media/";

if ($sponsor == 'one'){
//echo $locally."punk";
	$relMusicDir= $locally."one";
	$webMusicDir= $webby."one/";
}
if ($sponsor == 'two'){
	$relMusicDir= $locally."two";
	$webMusicDir= $webby."two/";
}
if ($sponsor == 'three'){
	$relMusicDir= $locally."three";
	$webMusicDir= $webby."three/";
}

//Get Genre and scan for artists
$dir = opendir($relMusicDir);
$count = 0; 
while ($read = readdir($dir)) {
	if ($read!='.' && $read!='..'  && $read!='.DS_Store') {
			//store filename(two directories) in fileinfo array.
			$songinfo[$count]=$read;
		}//end if
		$count++;
	}//end while
closedir($dir); 

shuffle($songinfo);
//echo "<h2>Artist Directory One: $songinfo[0]</h2>";
$first = "$relMusicDir/$songinfo[0]";
//echo "<h2>Artist Directory Two: $songinfo[1]</h2>";
$second = "$relMusicDir/$songinfo[1]";
$dir = opendir($first);	
while (false !==  ($readSong = readdir($dir))) {
	if ($readSong!='.' && $readSong!='..'  && $readSong!='.DS_Store' && strpos($readSong, '.mp3')) {
			//store all possible tracks in array.
			$songTracks[]= $webMusicDir . "$songinfo[0]/$readSong";
		}	
	}	
closedir($dir);		

$dirTwo = opendir($second);	
while (false !==  ($readSongTwo = readdir($dirTwo))) {
	if ($readSongTwo !='.' && $readSongTwo !='..'  && $readSongTwo !='.DS_Store' && strpos($readSongTwo, '.mp3')) {
			//store all possible tracks in array.
			$songTracksTwo[]= $webMusicDir . "$songinfo[1]/$readSongTwo";
		}	
	}	
closedir($dirTwo);		

shuffle($songTracks);
shuffle($songTracksTwo);
shuffle($songTracks);
shuffle($songTracksTwo);
//echo "<h3>FIRST SONG: $songTracks[0]</h3>";
//echo "<h3>Second SONG: $songTracksTwo[0]</h3>";

$fileinfo[0] = $songTracks[0];
$fileinfo[1] = $songTracksTwo[0];

foreach ($fileinfo as $value) {	
		$string = $value;
		$pattern = "$webMusicDir";
		$replacement = "";
		$valueOne = eregi_replace($pattern, $replacement, $string);
		$patternOne = ".mp3";
		$replacementOne = "";
		$valueNamed = eregi_replace($patternOne, $replacementOne, $valueOne);
		// w/o this we have really long names in jplayer
		//$patternTwo = "/";
		//$replacementTwo = "<br />";
		//$valueNamedFinal = eregi_replace($patternTwo, $replacementTwo, $valueNamed);
		$arraySongs[] = array('name' => $valueNamed , 'mp3' => $value);
		}

		$Mumford = json_encode($arraySongs);
		echo $Mumford;
		
		//return $Mumford;
?>
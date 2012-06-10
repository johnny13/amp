<?php
/**
 * IndexRemix 0.1
 * based on Indexr 1.1 [firedev.com]
 * Fancy directory lister with thumbnails
 *
 * $Rev: 01 $
 */

/**
 * Prefix for thumbnails
 */
define('THUMB_PREFIX', 'tn_');

/**
 * Thumbnail dimensions in format of 'WIDTHxHEIGHT'
 * If only the width is specified (e.g. '160x'), all thumbnails will be one width but different height.
 * If only the height is specified (e.g. 'x120'), all thumbnails will be one height but different width.
 * If width and height will be empty both, thubnail will be a copy of original image.
 */
define('THUMB_DIMENSIONS', '50x');

/**
 * Thumbnail quality
 */
define('THUMB_QUALITY', '85');

/**
 * Date format
 */
define('DATE_FORMAT', 'd M Y');

/**
 * List of masks for allowed files
 * 
 * Empty string means all files allowed.
 * You can use shell wildcards:
 *   * - zero or more characters (any);
 *   ? - exactly one character (any).
 * Divide masks with | character.
 */
define('ALLOWED_FILES', '*.png|*.jpg|*.gif|*.mp3|*.htm*|*.txt|*.zip|*.flv|*.swf|*.pdf|*.doc');

/**
 * Sorting method
 * 
 * Possible values:
 *   - name+ - sort by name ascending 
 *   - name- - sort by name descending 
 *   - date+ - sort by date ascending 
 *   - date- - sort by date descending 
 */
define('SORT_METHOD', 'date-');

/**
 * Number of columns in directory list
 * 
 */
define('NUMBER_OF_COLUMNS', 3);

define('DIRNAME', dirname(__FILE__) . '/');
define('CURRENT_FILENAME', basename(__FILE__));

/*-- Set of functions --*/

if (!function_exists('fnmatch')) {
	/**
	 * Match filename against a pattern
	 *
	 * @param  string  $pattern
	 * @param  string  $string
	 * @return boolean
	 */
	function fnmatch($pattern, $string) {
		return @preg_match(
			'/^' . strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'),
			array('*' => '.*', '?' => '.?')) . '$/i', $string
		);
	}
}

/**
 * Checks if given file is allowed
 *
 * @param  string  $filename
 * @return boolean
 */
function isAllowedFile($filename) {
	if (!defined('ALLOWED_FILES')) return true;
	$allowedFiles = explode('|', ALLOWED_FILES);
	foreach ($allowedFiles as $allowedFile) {
		if (fnmatch($allowedFile, strtolower($filename))) {
			return true;
		}
	}
	return false;
}

/**
 * Sorts files (callback)
 *
 * @param  string  $file1
 * @param  string  $file2
 * @return integer
 */
function sortContent($file1, $file2)
{
	$asc = '-' != substr(SORT_METHOD, -1);
	switch (strtolower(substr(SORT_METHOD, 0, 4))) {
		case 'name':
			return compare($asc ? $file1 : $file2, $asc ? $file2 : $file1);
			break;
		case 'date':
			return compare(
				filectime(DIRNAME . ($asc ? $file1 : $file2)),
				filectime(DIRNAME . ($asc ? $file2 : $file1))
			);
			break;
	}
	return 0;
}

/**
 * Compare function
 *
 * @param  mixed   $value1
 * @param  mixed   $value2
 * @return integer
 */
function compare($value1, $value2)
{
	if ($value1 < $value2) {
		return -1;
	} elseif ($value1 > $value2) {
		return 1;
	}
	return 0;
}

/**
 * Normalizes file size
 *
 * @param  integer $size
 * @return string
 */
function normalizeSize($size)
{
	if ($size > 1048576) {
		return round($size / 1048576, 1) . ' MB';
	}
	if ($size > 1024) {
		return round($size / 1024, 1) . ' kB';
	}
	return $size . ' B';
}

/**
 * Normalizes date
 *
 * @param  integer $timestamp
 * @return string
 */
function normalizeDate($timestamp)
{
	return date(DATE_FORMAT, $timestamp);
}

/**
 * Creates file data object
 *
 * @param  string   $fileName
 * @param  array    $content
 * @return stdClass
 */
function fileFactory($fileName, $content)
{
	$fullPath = DIRNAME . $fileName;
	$file = new stdClass();
	$file->name = $fileName;
	preg_match('/\.(\w+)$/', $fileName, $matches);
	$file->extension = isset($matches[1]) ? $matches[1] : false;
	$file->size = filesize($fullPath);
	$file->date = filemtime($fullPath);
	$file->thumbnail = "?thumb=$fileName";
	if (in_array(strtolower($file->extension), array('jpg', 'jpeg', 'jpe', 'png', 'gif', 'bmp')) && $params = @getimagesize($fullPath)) {
		$file->isDimensional = true;
		$file->width = $params[0];
		$file->height = $params[1];
		$userThumbPath = DIRNAME . getThumbName($fileName);
		if (file_exists($userThumbPath)) {
			$file->thumbnail = getThumbName($fileName);
		} elseif (mkdirRecursive(DIRNAME . 'thumbs/')) {
			$thumbPath = DIRNAME . 'thumbs/' . getThumbName($fileName);
			if (file_exists($thumbPath)) {
				$file->thumbnail = 'thumbs/' . getThumbName($fileName);
			}
		}
	} else {
		$file->isDimensional = false;
	}
	$flipped = array_flip($content);
	$key = $flipped[$fileName];
	$file->num = $key + 1;
	$file->previousFile = $key > 0 ? $content[$key - 1] : end($content);
	$file->nextFile = $key < count($content) - 1 ? $content[$key + 1] : reset($content);
	return $file;
}

/**
 * Returns thumbnail name for given file name
 *
 * @param  string $fileName
 * @return string
 */
function getThumbName($fileName)
{
	$lastDotPosition = strrpos($fileName, '.');
	if (false !== $lastDotPosition) {
		$fileName = substr($fileName, 0, $lastDotPosition);
	}
	return THUMB_PREFIX . $fileName . '.jpg';
}

/**
 * Recursively makes directory, returns TRUE if exists or made
 *
 * @param  string  $path The directory path
 * @param  integer $mode
 * @return boolean       TRUE if exists or made or FALSE on failure
 */
function mkdirRecursive($path, $mode = 0777)
{
	$parentPath = dirname($path);
	if (!is_dir($parentPath) && !mkdirRecursive($parentPath, $mode)) {
		return false;
	}
	return is_dir($path) || (@mkdir($path, $mode) && @chmod($path, $mode));
}

/**
 * Generates thumbnail for image
 *Imagemagick
 * @param  string  $filePath
 * @param  string  $thumbPath
 * @return boolean
 * 
 * @todo Save only jpeg with Imagemagick
 * @todo Check, won't it resample small images
 * @todo Add GD functions result checking
 */
function make_thumb($src,$dest,$desired_width)
{

  /* read the source image */
  $source_image = imagecreatefromjpeg($src);
  $width = imagesx($source_image);
  $height = imagesy($source_image);
  
  /* find the "desired height" of this thumbnail, relative to the desired width  */
  $desired_height = floor($height*($desired_width/$width));
  
  /* create a new, "virtual" image */
  $virtual_image = imagecreatetruecolor($desired_width,$desired_height);
  
  /* copy source image at a resized size */
  imagecopyresized($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
  
  /* create the physical thumbnail image to its destination */
  imagejpeg($virtual_image,$dest);

	echo '<a href="#"><img src="../songs/'.$dest.'" alt="'.$src.'"/></a>';
}

/**
 * Recursively un-quotes a quoted variable
 *
 * @param  mixed $var
 * @return mixed
 */
function stripslashes_recursive($var)
{
	if (is_array($var)) {
		$unquoted = array();
		foreach ($var as $key => $value) {
			$unquoted[$key] = stripslashes_recursive($value);
		}
		return $unquoted;
	} elseif (is_scalar($var)) {
		return stripslashes($var);
	} else {
		return $var;
	}
}

/**
 * Makes bounds for list columns (recursive)
 *
 * @param array   $bounds
 * @param integer $total
 * @param integer $numOfColumns
 * @param integer $lastBound
 */
function makeListBounds(&$bounds, $total, $numOfColumns, $lastBound = -1)
{
	if ($numOfColumns > 0) {
		$perColumn = (integer) ceil($total / $numOfColumns);
		$bounds[] = $lastBound + $perColumn;
		makeListBounds(&$bounds, $total - $perColumn, $numOfColumns - 1, $lastBound + $perColumn);
	}
}

if (!ini_get('date.timezone')) {
	ini_set('date.timezone', 'UTC');
}

umask(0000);

error_reporting(0);
error_reporting(E_ALL);

// Disable magic quotes
if (version_compare('5.3.0', phpversion()) > 0) {
	set_magic_quotes_runtime(0);
}
if (get_magic_quotes_gpc()) {
	$_GET = stripslashes_recursive($_GET);
	$_REQUEST = stripslashes_recursive($_REQUEST);
}

// Read content in directory
$content = array();
if ($handle = opendir(DIRNAME)) {
	while (false !== ($file = readdir($handle))) { 
		if (substr($file, 0, 1) != '.'
			&& is_file(DIRNAME . $file)
			&& CURRENT_FILENAME != $file
			&& substr($file, 0, strlen(THUMB_PREFIX)) != THUMB_PREFIX
			&& isAllowedFile($file)
		) { 
			$content[] = $file;
		} 
	}
	closedir($handle); 
}
usort($content, 'sortContent');

// Route the request
if (!empty($_GET)) {
	if (count($_GET) == 1 && '' == reset($_GET) && !isset($_GET['ext'])) {
		$fileName = urldecode($_SERVER['QUERY_STRING']);
		$file = fileFactory($fileName, $content);
		if (!in_array($fileName, $content) || !$file->isDimensional) {
			header('Location: ?');
		}
	} 
}

$songNumber = 0;

if (isset($fileName)):
	/*-- File viewer --*/
	
	$file = fileFactory($fileName, $content);
?>
<div cstyle="width:400px;font-size:13px;float:left;">
<!-- BEGIN HTML -->
		<p class="pagemenu" style="width:400px;font-size:13px;">
			<a href="?#<?php echo $file->name; ?>" title="Back to list">&laquo; back</a>&nbsp;  
			<a href="?<?php echo $file->previousFile; ?>" title="<?php echo $file->previousFile; ?>">&lsaquo; prev</a>	
			<?php echo $file->num; ?>/<?php echo count($content); ?>
			<a href="?<?php echo $file->nextFile; ?>" title="<?php echo $file->nextFile; ?>">next &rsaquo;</a>
		</p>
		<p><a href="?#<?php echo $file->name; ?>"><img src="<?php echo $file->name; ?>" alt="<?php echo $file->name; ?>" title="Back to list"/></a></p>
		<div class="imagetext L">
			<h2>

				<a href="?#<?php echo $file->name; ?>" title="Back to list"><?php echo $file->name; ?></a>

			</h2>
			<?php echo normalizeDate($file->date); ?><br/>
				<?php echo normalizeSize($file->size); ?><br/>
				<?php if ($file->isDimensional): ?>
					<?php echo $file->width; ?> x <?php echo $file->height; ?><br/>
				<?php endif; ?>
		</div>
<?php else:
	/*-- Directory lister --*/
	$files = array();
	$extensions = array();
	
	$selectedExtension = isset($_GET['ext']) && !empty($_GET['ext']) ? $_GET['ext'] : false;

	foreach ($content as $fileName) {
		$file = fileFactory($fileName, $content);
		if (!$selectedExtension || ($selectedExtension && $file->extension == $selectedExtension)) {
			$files[] = $file;
		}
		if ($file->extension) {
			if (!isset($extensions[$file->extension])) {
				$extensions[$file->extension] = 0;
			}
			$extensions[$file->extension]++;
		}
	}
	
	$sorts = array(
		'na' => 'return strcmp(strtolower($file1->name), strtolower($file2->name));',
		'nd' => 'return -strcmp(strtolower($file1->name), strtolower($file2->name));',
		'da' => '$d1 = (integer) $file1->date; $d2 = (integer) $file2->date; return ($d1 == $d2 ? 0 : ($d1 > $d2 ? 1 : -1));',
		'dd' => '$d1 = (integer) $file1->date; $d2 = (integer) $file2->date; return -($d1 == $d2 ? 0 : ($d1 > $d2 ? 1 : -1));',
		'ea' => 'return strcmp(strtolower($file1->extension), strtolower($file2->extension));',
		'ed' => 'return -strcmp(strtolower($file1->extension), strtolower($file2->extension));',
	);
	if (isset($_GET['sort']) && isset($sorts[$_GET['sort']])) {
		usort($files, create_function('$file1,$file2', $sorts[$_GET['sort']]));
	}

	ksort($extensions);
?>
	<form action="" method="get" class="pagemenu" style="width:410px;font-size:13px !important;float:left;margin:0;margin-left:5px">
				
				Filename
				<a href="?sort=na" title="Sort by filename ascending">&#9650;</a>
				<a href="?sort=nd" title="Sort by filename descending">&#9660;</a>
				|
				Date
				<a href="?sort=da" title="Sort by date ascending">&#9650;</a>
				<a href="?sort=dd" title="Sort by date descending">&#9660;</a>
				|
				Extension
				<a href="?sort=ea" title="Sort by entension ascending">&#9650;</a>
				<a href="?sort=ed" title="Sort by entension descending">&#9660;</a>
	</form>

		<?php 
			$total = count($files);
			$i = 0;
			
			makeListBounds($bounds, $total, NUMBER_OF_COLUMNS);
		?>
		<table width="410px" style=";margin-left:5px;position:relative;float:left;" class="none">
			<tr class="none">
				<!-- ><td width="php echo round(100 / NUMBER_OF_COLUMNS) %"> -->
					<td width="410px" style="float:left;" class="none">
					<?php foreach ($files as $file): ?>
						<?php if (0 == $i): ?>
							<table>
						<?php endif; ?>
		
							<tr class="none">
								<td class="thumb" width="100px">
									<a name="<?php echo $file->name; ?>"/>
										
									<?php
									if($file->extension == "jpg" || $file->extension == "png"){
									$src = $file->name;
									$dest = "thumbs/".$src;
									$desired_width = 100;
									make_thumb($src,$dest,$desired_width);
									} else {
									echo "<img src='../songs/thumbs/default.png'/>";
									}
									?>
								
								</td>
					
								<td class="thumbtext none">
									<?php
										$niceName = explode(".",$file->name);
										$niceNamed = rawurlencode($niceName[0]);
									?>
								
									<p>
										<a class="faceboxMore" title="#facebox_<?php echo $songNumber; ?>" href="#facebox_<?php echo $songNumber; ?>">
											<?php echo $niceName[0]; ?>
										</a>
									</p>
									<p>
									<?php echo normalizeDate($file->date); ?><br/>
										<?php echo normalizeSize($file->size); ?><br/>
										<?php if ($file->isDimensional): ?>
											<?php echo $file->width; ?> x <?php echo $file->height; ?>
										<?php endif; ?>
									</p>
								</td>
							</tr>				
						<?php if (in_array($i, $bounds)): ?>
					</table>
				</td>
				<!-- ><td width="php echo round(100 / NUMBER_OF_COLUMNS) %"> -->
				<td width="410px" style="float:left;" class="none">
					<table>
						<?php endif; ?>

						<?php $i++; ?>

						<?php if ($i == $total): ?> 
							</table>
						<?php endif; ?>
				<div style="display:none" class="albumBox" id="facebox_<?php echo $songNumber; ?>"><?php echo $file->name; ?><br/><?php echo $file->name; ?></div>
					<?php $songNumber++; ?>
					<?php endforeach; ?>
				</td>
			</tr>
		</table>
	<?php endif; ?>
</div>
<div style="clear:both;display:block;margin:0;padding:0;height:1px;"></div>
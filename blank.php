<?php
/* built on by code from jelco @ introversion
   http://zuzak.co.uk/compass                 */
error_reporting(E_ALL);

$foo = $_GET["parties"];
$bar = $_GET["size"];

$defaultwidth = 400;
if (isset($_GET["size"])){
	$imgwidth = $_GET["size"];
} else {
	$imgwidth = 400;
}
$multip = 1;
if (isset($_GET["mult"])){
	$multip = $_GET["mult"];
}
$textsize = 8;
if (isset($_GET["rescale"])){
	$textsize *= ($imgwidth/$defaultwidth);
} 
//$imgwidth = 400;
//$textsize = 8(($imgwidth-$defaultwidth)/$defaultwidth);
$dotdiameter = 4;
$font = './font.ttf';

$bounds = array();
$bounds["top"] = -100;
$bounds["bottom"] = 100;
$bounds["left"] = 100;
$bounds["right"] = -100;

$data = array();
$url = "http://users.aber.ac.uk/dog2/whois/api.php";
$rawdata = file_get_contents($url);
$rawdata = json_decode($rawdata);
foreach ($rawdata as $item) {
   if ($item->Economic <> "" && $item->Social <> "") {
      $eco = $item->Economic;
		$soc = $item->Social;
      $nam = $item->Nick;

      $data[$nam] = array($eco,$soc);
		
		if ($eco < $bounds["left"]){
			$bounds["left"] = $eco;
		}
		if ($soc > $bounds["top"]) {
			$bounds["top"] = $soc;
		}
		if ($eco > $bounds["right"]){
			$bounds["right"] = $eco;
		}
		if ($soc < $bounds["bottom"]){
			$bounds["bottom"] = $soc;
		}
   }
}

$offset = array($bounds["left"]*0.5,$bounds["bottom"]*0.5);

foreach($data as $name => $thingy){
	$data2[$name] = array(($thingy[0]-$offset[0])*$multip,($thingy[1]-$offset[1])*$multip);
}
$data["top left"] = array($bounds["left"],$bounds["top"]);
$data["top right"] = array($bounds["right"],$bounds["top"]);
$data["bottom left"] = array($bounds["left"],$bounds["bottom"]);
$data["bottom right"] = array($bounds["right"],$bounds["bottom"]);

$img = imagecreate($imgwidth, $imgwidth);

$colourlefttop = imagecolorallocate($img, 207, 201, 204);
$colourrighttop = imagecolorallocate($img, 207, 201, 204);
$colourleftdown = imagecolorallocate($img, 207, 201, 204);
$colourrightdown = imagecolorallocate($img, 207, 201, 204);
$colourgrid = imagecolorallocate($img, 150, 150, 150);
$colourlines = imagecolorallocate($img, 41, 48, 64);
//$colorhighlight = $colourlines;// imagecolorallocate($img, 222, 222, 222);

imagefilledrectangle($img, 0, 0, ($imgwidth / 2), ($imgwidth / 2), $colourlefttop);
imagefilledrectangle($img, ($imgwidth / 2), 0, $imgwidth, ($imgwidth / 2), $colourrighttop);
imagefilledrectangle($img, 0, ($imgwidth / 2), ($imgwidth / 2), $imgwidth, $colourleftdown);
imagefilledrectangle($img, ($imgwidth / 2), ($imgwidth / 2), $imgwidth, $imgwidth, $colourrightdown);

imageline($img, 0, ($imgwidth / 2), $imgwidth, ($imgwidth / 2), $colourgrid);
imageline($img, ($imgwidth / 2), 0, ($imgwidth / 2), $imgwidth, $colourgrid);

for($pointer = ($imgwidth / 20); $pointer < $imgwidth; $pointer = ($pointer + ($imgwidth / 20)))
{
   if(($pointer % ($imgwidth / 2)) == 0)
      continue 1;
      
   imageline($img, 0, $pointer, $imgwidth, $pointer, $colourgrid);
   imageline($img, $pointer, 0, $pointer, $imgwidth, $colourgrid);
}

foreach($data2 as $key => $value)
{
   $x = (($imgwidth / 2) + round($value[0] * ($imgwidth / 20)));
   $y = (($imgwidth / 2) - round($value[1] * ($imgwidth / 20)));
   
   if (strpos($key,' 20')){
   	$txtsize = $textsize * 1.3;
  // 	$dotcolour = $colourhighlight;
   } else {
   	$txtsize = $textsize;
   }
   imagefilledellipse($img, $x, $y, $dotdiameter, $dotdiameter, $colourlines);
    
   $textx = $x + $dotdiameter;
   $texty = $y + ($dotdiameter / 2);
   
   imagettftext($img, $txtsize, 0, $textx, $texty, $colourlines, $font, $key);
}

header("Content-Type: image/png");

imagepng($img);
?>

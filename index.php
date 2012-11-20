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
$textsize = 8;
if (isset($_GET["rescale"])){
	$textsize *= ($imgwidth/$defaultwidth);
} 
//$imgwidth = 400;
//$textsize = 8(($imgwidth-$defaultwidth)/$defaultwidth);
$dotdiameter = 4;
$font = './font.ttf';

$data = array();
$url = "http://users.aber.ac.uk/dog2/whois/api.php";
$rawdata = file_get_contents($url);
$rawdata = json_decode($rawdata);
foreach ($rawdata as $item) {
   if ($item->Economic <> "" && $item->Social <> "") {
        $eco = $item->Economic;
        $soc = $item->Social;
        $nam = $item->Nick;
//        print $eco.$soc.$nam;
        $data[$nam] = array($eco,$soc);
//      array_push($data,$nam,array($eco,$soc));
   }
}

if (isset($foo)) {
$data["Obama 2012"] = array(6,6);
$data["Romney 2012"] = array(7,6.5);
$data["Labour 2010"] = array(4,8);
$data["Tories 2010"] = array(8,6);
$data["Lib Dems 2010"] = array(4,-1);
$data["Plaid 2010"] = array(-3,1);
$data["Green 2010"] = array(-4,-5);
$data["BNP 2010"] = array(-1.5,9);
}

$img = imagecreate($imgwidth, $imgwidth);

$colourlefttop = imagecolorallocate($img, 255, 117, 117);
$colourrighttop = imagecolorallocate($img, 66, 170, 255);
$colourleftdown = imagecolorallocate($img, 154, 237, 151);
$colourrightdown = imagecolorallocate($img, 192, 154, 234);
$colourgrid = imagecolorallocate($img, 160, 160, 160);
$colourlines = imagecolorallocate($img, 0, 0, 0);
//$colorhighlight = $colourlines;// imagecolorallocate($img, 222, 222, 222);

imagefilledrectangle($img, 0, 0, ($imgwidth / 2), ($imgwidth / 2), $colourlefttop);
imagefilledrectangle($img, ($imgwidth / 2), 0, $imgwidth, ($imgwidth / 2), $colourrighttop);
imagefilledrectangle($img, 0, ($imgwidth / 2), ($imgwidth / 2), $imgwidth, $colourleftdown);
imagefilledrectangle($img, ($imgwidth / 2), ($imgwidth / 2), $imgwidth, $imgwidth, $colourrightdown);

imageline($img, 0, ($imgwidth / 2), $imgwidth, ($imgwidth / 2), $colourlines);
imageline($img, ($imgwidth / 2), 0, ($imgwidth / 2), $imgwidth, $colourlines);

for($pointer = ($imgwidth / 20); $pointer < $imgwidth; $pointer = ($pointer + ($imgwidth / 20)))
{
   if(($pointer % ($imgwidth / 2)) == 0)
      continue 1;
      
   imageline($img, 0, $pointer, $imgwidth, $pointer, $colourgrid);
   imageline($img, $pointer, 0, $pointer, $imgwidth, $colourgrid);
}

foreach($data as $key => $value)
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

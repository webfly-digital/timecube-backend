<?php
$dw = 680;
$dh = 450;
global $mash, $x0, $y0, $im, $line_color, $sq_color;
$mash = array(0.18, 0.19, 0.18);
$dx = 0;

$n = (int)$_GET['n'];
$h = (float)$_GET['h']*630/12;
$w = (float)$_GET['w']*528/11;
$d = (float)$_GET['d']*207/10;
$mash = $mash[$n];


$src_image = imageCreateFromGif($_SERVER['DOCUMENT_ROOT'].'/assets/compare.gif');
$sw = imageSx($src_image)/3;
$sh = imageSy($src_image);

/*$bar_image = imageCreateFromGif($_SERVER['DOCUMENT_ROOT'].'/assets/compare_bar.gif');
$bw = imageSx($src_image);
$bh = imageSy($src_image);*/

$im = @ImageCreate($dw, $dh) or die ("Cannot Initialize new GD image stream");
$background_color = ImageColorAllocate ($im, 255, 255, 255);
imagefill($im, 0, 0, $background_color);
imagecopy($im, $src_image, 0, $dh-$sh, $n*$sw, 0, $sw, $sh);
$line_color = ImageColorAllocate($im, 0xDE, 0xDE, 0xDE);
//$line_color = ImageColorAllocate($im, 0, 0, 0);
$x0 = $sw+$dx;
$y0 = $dh;
$coord = array();
$coord[0] = array(0, 170*$d/207);
$coord[1] = array(0, $coord[0][1] + $h);
$coord[2] = array(110*$d/207, 0);
$coord[3] = array($coord[2][0], $h);
$coord[4] = array($coord[3][0] + 514*$w/528, 185*$h/630 + $h*0.95);
$coord[5] = array($coord[4][0]-$coord[2][0]*1.1, $coord[4][1] + $coord[0][1]*0.9);
$coord[6] = array($coord[4][0], $coord[4][1]-$h*0.95);

$sq_color = ImageColorAllocate($im, 0x8C, 0x8C, 0x8C);
_ImageSq($coord[0], $coord[1], $coord[3], $coord[2]);
$sq_color = ImageColorAllocate($im, 0xA5, 0xA5, 0xA5);
_ImageSq($coord[1], $coord[3], $coord[4], $coord[5]);
$sq_color = ImageColorAllocate($im, 0x7B, 0x7B, 0x7B);
_ImageSq($coord[2], $coord[3], $coord[4], $coord[6]);
//die;

function _ImageSq($c1, $c2, $c3, $c4) {
	global $im, $sq_color;
	$t1 = _ImageLine($c1, $c2);
	_ImageLine($c2, $c3);
	$t2 = _ImageLine($c3, $c4);
	_ImageLine($c4, $c1);

	if($t1[0]<$t2[0])
		$x = $t1[0] + ($t2[0]-$t1[0])/2;
	else
		$x = $t2[0] + ($t1[0]-$t2[0])/2;
	if($t1[1]<$t2[1])
		$y = $t1[1] + ($t2[1]-$t1[1])/2;
	else
		$y = $t2[1] + ($t1[1]-$t2[1])/2;
	//var_dump($x);var_dump($y);die;
	imagefill($im, $x, $y, $sq_color);
}
function _ImageLine($c1, $c2) {
	global $mash, $x0, $y0, $im, $line_color;
	$c1[0] = $c1[0]*$mash + $x0;
	$c1[1] = $y0 - $c1[1]*$mash;
	$c2[0] = $c2[0]*$mash + $x0;
	$c2[1] = $y0 - $c2[1]*$mash;
	//var_dump($line_color);die;
	//var_dump($c1);var_dump($c2);
	ImageLine($im, $c1[0], $c1[1], $c2[0], $c2[1], $line_color);
	return $c1;
}
/*ImageSetThickness($im, 1);
ImageRectangle($im,0,0,XS-1,24,$text_color);
for($j=-2; $j<imagesx($im)/STEP+1; $j++){
    //$cur_points_y[] = -rand(0,STEP);
    //$cur_points_x[] = rand($j*STEP+STEP/1.4,$j*STEP+STEP*1.4);
    $last=0;
    for($i=-2; $i<imagesy($im)/STEP+1; $i++)
    {
        $last = STEP*$i+rand(STEP/1.4,STEP*1.4);
        $cur_points_y[] = $last;
        $cur_points_x[] = rand($j*STEP+STEP/1.4,$j*STEP+STEP*1.4);
    }
    $cur_points_y[] = 25;
    $cur_points_x[] = rand($j*STEP+STEP/1.4,$j*STEP+STEP*1.4);
    for($i=1; $i<5; $i++)
    {
        ImageLine($im,$prev_points_x[$i], $prev_points_y[$i], $cur_points_x[$i], $cur_points_y[$i], $text_color);
        ImageLine($im,$prev_points_x[$i-1], $prev_points_y[$i-1], $cur_points_x[$i], $cur_points_y[$i], $text_color);
        ImageLine($im,$prev_points_x[$i], $prev_points_y[$i], $cur_points_x[$i-1], $cur_points_y[$i-1], $text_color);
    }
    unset($prev_points_x);
    unset($prev_points_y);
    $prev_points_x = $cur_points_x;
    $prev_points_y = $cur_points_y;
    unset($cur_points_x);
    unset($cur_points_y);
}*/


ob_start();
ImageGif($im);
$content=ob_get_contents();
ob_clean();
/*
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// дата в прошлом
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // всегда модифицируется
header("Cache-Control: post-check=0, pre-check=0", false);
*/

//Header("Accept-ranges: bytes");
//Header("Content-length: ".strlen($content));
Header("Content-type: image/gif");
Header("Cache-Control: no-store, no-cache, must-revalidate");
Header("Pragma: no-cache");
echo $content;


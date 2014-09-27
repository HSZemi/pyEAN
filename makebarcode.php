<?php

if(!isset($_GET['code'])){
	echo 'Please provide valid EAN via the ?code=XXXXXXXXXXXXX GET parameter';
	die();
}

function printsvghead($width, $height){
	echo "<?xml version='1.0' encoding='UTF-8' standalone='no'?>

<svg
   xmlns:dc='http://purl.org/dc/elements/1.1/'
   xmlns:cc='http://creativecommons.org/ns#'
   xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'
   xmlns:svg='http://www.w3.org/2000/svg'
   xmlns='http://www.w3.org/2000/svg'
   width='$width'
   height='$height'
   id='svg2'
   version='1.1'>
   ";
}
   
function printsvgtail(){
	echo "</svg>";
}

function printsvgrect($color, $id, $x, $y, $width, $height){
	echo "<rect
       style='color:#000000;fill:$color;fill-opacity:1;stroke:none;stroke-width:0.1;marker:none;visibility:visible;display:inline;overflow:visible;enable-background:accumulate'
       id='rect$id'
       width='$width'
       height='$height'
       x='$x'
       y='$y' />
";
}

function printsvgletter($letter, $x, $y, $id){
	echo "<text
       xml:space='preserve'
       style='font-size:7px;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;line-height:125%;letter-spacing:0px;word-spacing:0px;fill:#000000;fill-opacity:1;stroke:none;font-family:Arial;-inkscape-font-specification:Arial'
       x='$x'
       y='$y'
       id='text$id'>$letter</text>
";
}

function check_ean($inputstring){
	if(strlen($inputstring) != 13){
		return false;
	}
	$val = Array();
	$items = str_split($inputstring);
	for($i = 0; $i < sizeof($items); $i++){
		if(!is_numeric($items[$i])){
			return false;
		} else {
			$val[] = $items[$i];
		}
	}
	$uneven = $val[0]+$val[2]+$val[4]+$val[6]+$val[8]+$val[10]+$val[12];
	$even = $val[1]+$val[3]+$val[5]+$val[7]+$val[9]+$val[11];
	if(($uneven + $even*3)%10 == 0){
		return $val;
	} else {
		return false;
	}
}

function ean2bitstring($inputstring){
	$ean = check_ean($inputstring);
	if(!$ean){
		return false;
	}
	$codings13 = Array();
	$codings13[0] = "UUUUUURRRRRR";
	$codings13[1] = "UUGUGGRRRRRR";
	$codings13[2] = "UUGGUGRRRRRR";
	$codings13[3] = "UUGGGURRRRRR";
	$codings13[4] = "UGUUGGRRRRRR";
	$codings13[5] = "UGGUUGRRRRRR";
	$codings13[6] = "UGGGUURRRRRR";
	$codings13[7] = "UGUGUGRRRRRR";
	$codings13[8] = "UGUGGURRRRRR";
	$codings13[9] = "UGGUGURRRRRR";

	$codings = Array();
	$codings[0] = Array("0001101","0100111","1110010");
	$codings[1] = Array("0011001","0110011","1100110");
	$codings[2] = Array("0010011","0011011","1101100");
	$codings[3] = Array("0111101","0100001","1000010");
	$codings[4] = Array("0100011","0011101","1011100");
	$codings[5] = Array("0110001","0111001","1001110");
	$codings[6] = Array("0101111","0000101","1010000");
	$codings[7] = Array("0111011","0010001","1000100");
	$codings[8] = Array("0110111","0001001","1001000");
	$codings[9] = Array("0001011","0010111","1110100");

	$code = "";

	$indicators = str_split($codings13[$ean[0]]);
	for($i = 0; $i < sizeof($indicators); $i++){
		$c = $indicators[$i];
		$idx = 0;
		if($c === "U"){
			$idx = 0;
		} elseif($c === "G"){
			$idx = 1;
		} elseif($c === "R"){
			$idx = 2;
		}
		$code .= $codings[$ean[$i+1]][$idx];
	}
	$code = "101" . substr($code, 0, 42) . "01010" . substr($code, 42, 42) . "101";
	return $code;
}

### Main


$ean = $_GET['code'];
$code = ean2bitstring($ean);

header("Content-Type: text/xml");
if(isset($_GET['dl']) and ($_GET['dl'] === "1")){
	header("Content-Disposition: attachment; filename=barcode.svg");
}

printsvghead(102, 34);

# draw bars
$x = 6;
$y = 1;
$i = 0;
for($k = 0; $k < 3; $k++){
	$c = substr($code, $k, 1);
	if($c==="1"){
		$color = "#000000";
		printsvgrect($color, $i, $x, $y, 1, 30);
	}
	$i++;
	$x++;
}
for($k = 3; $k < 45; $k++){
	$c = substr($code, $k, 1);
	if($c==="1"){
		if($code[$k-1] === "0"){
			$width = 1;
			while($code[$k+$width] === "1"){
				$width++;
			}
			$color = "#000000";
			printsvgrect($color, $i, $x, $y, $width, 25);
		}
	}
	$i++;
	$x++;
}
for($k = 45; $k < 50; $k++){
	$c = substr($code, $k, 1);
	if($c==="1"){
		$color = "#000000";
		printsvgrect($color, $i, $x, $y, 1, 30);
	}
	$i++;
	$x++;
}
for($k = 50; $k < 92; $k++){
	$c = substr($code, $k, 1);
	if($c==="1"){
		if($code[$k-1] === "0"){
			$width = 1;
			while($code[$k+$width] === "1"){
				$width++;
			}
			$color = "#000000";
			printsvgrect($color, $i, $x, $y, $width, 25);
		}
	}
	$i++;
	$x++;
}
for($k = 92; $k < 95; $k++){
	$c = substr($code, $k, 1);
	if($c==="1"){
		$color = "#000000";
		printsvgrect($color, $i, $x, $y, 1, 30);
	}
	$i++;
	$x++;
}

# write numbers
$x = 1;
$y = 33;
$i = 0;
printsvgletter($ean[0], $x, $y, $i);
$i++;
$x = 11;
for($k = 1; $k < 7; $k++){
	$letter = $ean[$k];
	printsvgletter($letter, $x, $y, $i);
	$i++;
	$x+=7;
}
$x += 4;
for($k = 7; $k < 13; $k++){
	$letter = $ean[$k];
	printsvgletter($letter, $x, $y, $i);
	$i++;
	$x+=7;
}
printsvgtail();
?>
#!/usr/bin/env php
<?php

# TODO:
# * Split algorithm into:
#   - Image manipulation 
#   - Figure recognition
# * Make config files
# * GUI (html5) to upload, rotate and trim, correct wrong guesses
# * Output writer
# * iPhone version

##############################################################################
# CONFIGURATION
##############################################################################

$quickmode = true;
$photomode = true;
$testmode = false;
$debugmode = true;

// $inputfile = "tests/screenshots/shredder.png";
// $rotate = 0.0;
// $x1 = -48;
// $y1 = -58;
// $x2 = -47;
// $y2 = -118;

// $inputfile = "tests/screenshots/wikipedia.png";
// $rotate = 0.0;
// $x1 = -32;
// $y1 = -28;
// $x2 = -26;
// $y2 = -55;

// $inputfile = "tests/photos/iPhone1.png";
// $rotate = 0.4;
// $x1 = -25;
// $y1 = -15;
// $x2 = -25;
// $y2 = -25;

$inputfile = "tests/photos/iPhone2.jpg";
$rotate = 90.0;
$x1 = -330;
$y1 = -630;
$x2 = -435;
$y2 = -535;

$saturation = 100;
if ($photomode)
  $brightness = 200; // Important for photos
else
  $brightness = 125; // Important for screenshots
$contrast1 = 2;
if ($photomode)
  $contrast2 = 40; // Important for photos
else
  $contrast2 = 100; // Important for screenshots
$threshold = 1.0;
$fuzz = 50;

##############################################################################
# DO NOT CHANGE ANYTHING OF THE CONFIGURATION BELOW
##############################################################################

$tempfile = tempnam(sys_get_temp_dir(), "cs_"); unlink($tempfile);
$tempfileext = ".png";
$symbolpath = "symbols/cutted small 72dpi blurred/";
$size = 240;
$tilesize = 30;

$symbols = array(
  array("name" => "Empty (white)",        "char" => "☐", "file" => "e_w",   "points" => 0),
  array("name" => "Empty (black)",        "char" => "☐", "file" => "e_b",   "points" => 0),
  array("name" => "White Pawn (white)",   "char" => "♙", "file" => "p_w_w", "points" => 0),
  array("name" => "Black Pawn (white)",   "char" => "♟", "file" => "p_b_w", "points" => 0),
  array("name" => "White Queen (white)",  "char" => "♕", "file" => "q_w_w", "points" => 0),
  array("name" => "Black Queen (white)",  "char" => "♛", "file" => "q_b_w", "points" => 0),
  array("name" => "White King (white)",   "char" => "♔", "file" => "k_w_w", "points" => 0),
  array("name" => "Black King (white)",   "char" => "♚", "file" => "k_b_w", "points" => 0),
  array("name" => "White Bishop (white)", "char" => "♗", "file" => "b_w_w", "points" => 0),
  array("name" => "Black Bishop (white)", "char" => "♝", "file" => "b_b_w", "points" => 0),
  array("name" => "White Knight (white)", "char" => "♘", "file" => "n_w_w", "points" => 0),
  array("name" => "Black Knight (white)", "char" => "♞", "file" => "n_b_w", "points" => 0),
  array("name" => "White Rook (white)",   "char" => "♖", "file" => "r_w_w", "points" => 0),
  array("name" => "Black Rook (white)",   "char" => "♜", "file" => "r_b_w", "points" => 0),
/*
  array("name" => "White Pawn (black)",   "char" => "♙", "file" => "p_w_b", "points" => 0),
  array("name" => "Black Pawn (black)",   "char" => "♟", "file" => "p_b_b", "points" => 0),
  array("name" => "White Queen (black)",  "char" => "♕", "file" => "q_w_b", "points" => 0),
  array("name" => "Black Queen (black)",  "char" => "♛", "file" => "q_b_b", "points" => 0),
  array("name" => "White King (black)",   "char" => "♔", "file" => "k_w_b", "points" => 0),
  array("name" => "Black King (black)",   "char" => "♚", "file" => "k_b_b", "points" => 0),
  array("name" => "White Bishop (black)", "char" => "♗", "file" => "b_w_b", "points" => 0),
  array("name" => "Black Bishop (black)", "char" => "♝", "file" => "b_b_b", "points" => 0),
  array("name" => "White Knight (black)", "char" => "♘", "file" => "n_w_b", "points" => 0),
  array("name" => "Black Knight (black)", "char" => "♞", "file" => "n_b_b", "points" => 0),
  array("name" => "White Rook (black)",   "char" => "♖", "file" => "r_w_b", "points" => 0),
  array("name" => "Black Rook (black)",   "char" => "♜", "file" => "r_b_b", "points" => 0),
*/
);

##############################################################################
# ALGORITHM
##############################################################################

$command = sprintf("convert \"%s\" \"%s%s\"", 
  $inputfile, $tempfile, $tempfileext);
exec($command);

$command = sprintf("convert \"%s%s\" -rotate %s%% \"%s%s\"", 
  $tempfile, $tempfileext, $rotate, $tempfile, $tempfileext);
exec($command);
$command = sprintf("convert \"%s%s\" -gravity SouthEast -crop %s%s +repage \"%s%s\"", 
  $tempfile, $tempfileext, $x1, $y1, $tempfile, $tempfileext);
exec($command);
$command = sprintf("convert \"%s%s\" -gravity NorthWest -crop %s%s +repage \"%s%s\"", 
  $tempfile, $tempfileext, $x2, $y2, $tempfile, $tempfileext);
exec($command);
$command = sprintf("convert \"%s%s\" -resize %sx%s! \"%s%s\"", 
  $tempfile, $tempfileext, $size, $size, $tempfile, $tempfileext);
exec($command);

$command = sprintf("convert \"%s%s\" -modulate %s,%s \"%s%s\"", 
  $tempfile, $tempfileext, $brightness, $saturation, $tempfile, $tempfileext);
exec($command);
$command = sprintf("convert \"%s%s\" -contrast-stretch %s%%x%s%% \"%s%s\"", 
  $tempfile, $tempfileext, $contrast1, $contrast2, $tempfile, $tempfileext);
exec($command);
if ($testmode || $debugmode)
  exec("open \"$tempfile$tempfileext\"");
if ($testmode)
  exit;

$command = sprintf("convert \"%s%s\" -crop %sx%s +repage \"%s%s\"", 
  $tempfile, $tempfileext, $tilesize, $tilesize, $tempfile, $tempfileext);
exec($command);


# Rules:
# 1. e_w == 0..2 && e_s == 0..2 && b_* == 0..2 => e
# 2. x_s_w && x_s_s => min(x_s_w + x_s_s)
# 3. x_w_w && x_w_s => min(x_w_w + x_w_s)
# ...
# n. e > b > l > s > t > d > k

for ($i=0; $i<=63; $i++) {
  if ($i % 8 == 0)
    print("\n");
  
  $ranking = $symbols;
  
  $j = 0;
  $command = sprintf("compare -metric AE -dissimilarity-threshold %s -fuzz %s%% -compose difference \"%s-%s%s\" \"%s%s.png\" /dev/null 2>&1", 
    $threshold, $fuzz, $tempfile, $i, $tempfileext, $symbolpath, $ranking[$j]["file"]);
  $ranking[$j]["points"] = (int)exec($command, $rcode);
  
  $j = 1;
  $command = sprintf("compare -metric AE -dissimilarity-threshold %s -fuzz %s%% -compose difference \"%s-%s%s\" \"%s%s.png\" /dev/null 2>&1", 
    $threshold, $fuzz, $tempfile, $i, $tempfileext, $symbolpath, $ranking[$j]["file"]);
  $ranking[$j]["points"] = (int)exec($command, $rcode);
  
  $j = 2;
  $command = sprintf("compare -metric AE -dissimilarity-threshold %s -fuzz %s%% -compose difference \"%s-%s%s\" \"%s%s.png\" /dev/null 2>&1", 
    $threshold, $fuzz, $tempfile, $i, $tempfileext, $symbolpath, $ranking[$j]["file"]);
  $ranking[$j]["points"] = (int)exec($command, $rcode);
    
  # Apply rule 1. If OK, then we can already stop here.
  if ($ranking[0]["points"] >= 0 && $ranking[0]["points"] <= 2 && 
      $ranking[1]["points"] >= 0 && $ranking[1]["points"] <= 2 && 
      $ranking[2]["points"] >= 0 && $ranking[2]["points"] <= 2) {
    if ($quickmode) {
      print($ranking[0]["char"]);      
      continue;
    }
  }
    
  for ($j=3; $j<=13; $j++) {
    $command = sprintf("compare -metric AE -dissimilarity-threshold %s -fuzz %s%% -compose difference \"%s-%s%s\" \"%s%s.png\" /dev/null 2>&1", 
      $threshold, $fuzz, $tempfile, $i, $tempfileext, $symbolpath, $ranking[$j]["file"]);
    $ranking[$j]["points"] = (int)exec($command, $rcode);
  }
  
  # Get minimum
  foreach ($ranking as $key => $row) {
      $name[$key]  = $row["name"];
      $code[$key] = $row["file"];
      $points[$key] = $row["points"];
  }
#  print_r($ranking);  
  array_multisort($points, SORT_ASC, $ranking);
#  print_r($ranking);

  if ($quickmode) {
    if ($ranking[0]["file"] == "e_w" || $ranking[0]["file"] == "e_b") {
      $field = array_shift($ranking);
      array_push($ranking, $field);
    }
  }
  if ($ranking[0]["file"] == "e_w" || $ranking[0]["file"] == "e_b") {
    $field = array_shift($ranking);
    array_push($ranking, $field);
  }
  
print($ranking[0]["char"]);
//  foreach ($ranking as $row)
//    print($row["char"].$row["points"]." ");
//  print("\n");
}

unlink("$tempfile$tempfileext");
for ($i=0; $i<=63; $i++)
  unlink("$tempfile-$i$tempfileext");
?>
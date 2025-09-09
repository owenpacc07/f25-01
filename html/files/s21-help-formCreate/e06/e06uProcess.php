<?php
error_reporting(E_ALL ^ E_WARNING);
// define variables and set to empty values
$nameErr = $valueErr = $typeErr = "";
$value = $labelName = $type = $wbody = $line = $block = "";

// READ generic code in file
$rfile = fopen("e06codeParts.txt", "r");
while(!feof($rfile))
  {
    $line = fgets($rfile);
    //echo $line . "<br>";
    $block =$block . $line;
  }
fclose($rfile);

// Decode PARTS of HTML text into PHP index array
$arri = explode(',', $block);



// VALIDATE and INPUT parameters in a form
//-------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["type"])) {
    $typeErr = "Type is required";
  } else {
    $type = test_input($_POST["type"]);
  }
  
  if (empty($_POST["value"])) {
    $valueErr = "Value is required";
  } else {
    $value  = test_input($_POST["value"]);    
  }
}

//CLEAN data in a field
function test_input($data) {
  $data = trim($data);
  //$data = stripslashes($data);
  //$data = htmlspecialchars($data);
  return $data;
}


?>

<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  


<?php

if ( isset( $_POST['submit'] ) ) {

// COMBINE/UPDATE parameters & code in the PHP array
$arri[3]=$value;
$arri[1]=$type;

// Connect all parts in index array arri together into HTML code
$block = $ablock ="";
$arrlength = count($arri);
for($x = 0; $x < $arrlength; $x++) {
  // whole HTML code
  $block = $block . $arri[$x];
  // HTML code into PARTS separated by comma","
  if ($x==0) {  
                $ablock = $ablock . $arri[$x]; 
             }
  else {
           $ablock = $ablock . "," . $arri[$x]; 
       }
}

// WRITE the HTML code to file
$wfile = fopen("e06code.htm", 'w');
  fwrite($wfile, $block);
fclose($wfile);

// WRITE PARTS of HTML code to file
$wfile = fopen("e06codeParts.txt", 'w');
  fwrite($wfile, $ablock);
fclose($wfile);

// TEST: run the HTML code
echo $block;

}

?>

</body>
</html>

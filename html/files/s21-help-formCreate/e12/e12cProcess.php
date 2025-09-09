<?php
error_reporting(E_ALL ^ E_WARNING); 
// define variables and set to empty values
$nameErr = $valueErr = $sizeErr = "";
$labelValue = $labelName = $labelSize = $wbody = $line = $block = "";

// READ generic code in file
$rfile = fopen("e12gen.txt", "r");
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

  if (empty($_POST["id"])) {
    $nameErr = "ID is required";
  } else {
    $id = test_input($_POST["id"]);    
  }
  
  if (empty($_POST["lvalue"])) {
    $valueErr = "Value is required";
  } else {
    $lvalue  = test_input($_POST["lvalue"]);    
  }
  if (empty($_POST["lvalue1"])) {
    $valueErr = "Value is required";
  } else {
    $lvalue1  = test_input($_POST["lvalue1"]);    
  }
  if (empty($_POST["lvalue2"])) {
    $valueErr = "Value is required";
  } else {
    $lvalue2  = test_input($_POST["lvalue2"]);    
  }
  if (empty($_POST["lvalue3"])) {
    $valueErr = "Value is required";
  } else {
    $lvalue3  = test_input($_POST["lvalue3"]);    
  }
  if (empty($_POST["lvalue4"])) {
    $valueErr = "Value is required";
  } else {
    $lvalue4  = test_input($_POST["lvalue4"]);    
  }
  if (empty($_POST["svalue1"])) {
    $valueErr = "Value is required";
  } else {
    $svalue1  = test_input($_POST["svalue1"]);    
  }
  if (empty($_POST["svalue2"])) {
    $valueErr = "Value is required";
  } else {
    $svalue2  = test_input($_POST["svalue2"]);    
  }
  if (empty($_POST["svalue3"])) {
    $valueErr = "Value is required";
  } else {
    $svalue3  = test_input($_POST["svalue3"]);    
  }
  if (empty($_POST["svalue4"])) {
    $valueErr = "Value is required";
  } else {
    $svalue4  = test_input($_POST["svalue4"]);    
  }
  if (empty($_POST["sname"])) {
    $sizeErr = "Name is required";
  } else {
    $sname = test_input($_POST["sname"]);
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
$arri[1]=$id;
$arri[3]=$lvalue;
$arri[5]=$id;
$arri[7]=$sname;
$arri[9]=$svalue1;
$arri[11]=$lvalue1;
$arri[13]=$svalue2;
$arri[15]=$lvalue2;
$arri[17]=$svalue3;
$arri[19]=$lvalue3;
$arri[21]=$svalue4;
$arri[23]=$lvalue4;

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
$wfile = fopen("e12code.htm", 'w');
  fwrite($wfile, $block);
fclose($wfile);

// WRITE PARTS of HTML code to file
$wfile = fopen("../all/code.html", 'a+');
  fwrite($wfile, $block);
fclose($wfile);

// WRITE PARTS of HTML code to file
$wfile = fopen("e12codeParts.txt", 'w');
  fwrite($wfile, $ablock);
fclose($wfile);

// TEST: run the HTML code
echo $block;

}

?>

</body>
</html>

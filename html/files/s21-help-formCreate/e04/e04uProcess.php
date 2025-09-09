<?php
error_reporting(E_ALL ^ E_WARNING);
// define variables and set to empty values
$nameErr = $valueErr = $sizeErr = "";
$cvalue1 = $cvalue2 = $lvalue = $lvalue1 = $lvalue2 = $labelName = $cname = $cvalue1 = $cvalue2 = $r = $wbody = $line = $block = "";

// READ generic code in file
$rfile = fopen("e04gen.txt", "r");
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
  if (empty($_POST["id1"])) {
    $nameErr = "ID is required";
  } else {
    $id1 = test_input($_POST["id1"]);    
  }
  if (empty($_POST["id2"])) {
    $nameErr = "ID is required";
  } else {
    $id2 = test_input($_POST["id2"]);    
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
  if (empty($_POST["cvalue1"])) {
    $valueErr = "Value is required";
  } else {
    $cvalue1  = test_input($_POST["cvalue1"]);    
  }
  if (empty($_POST["cvalue2"])) {
    $valueErr = "Value is required";
  } else {
    $cvalue2  = test_input($_POST["cvalue2"]);    
  }
  
  if (empty($_POST["cname"])) {
    $sizeErr = "Name is required";
  } else {
    $cname = test_input($_POST["cname"]);
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
$arri[5]=$id1;
$arri[7]=$cname;
$arri[9]=$cvalue1;
$arri[11]=$id1;
$arri[13]=$lvalue1;
$arri[15]=$id2;
$arri[17]=$cname;
$arri[19]=$cvalue2;
$arri[21]=$id2;
$arri[23]=$lvalue2;
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
$wfile = fopen("e03code.htm", 'w');
  fwrite($wfile, $block);
fclose($wfile);

// WRITE PARTS of HTML code to file
$wfile = fopen("e03codeParts.txt", 'w');
  fwrite($wfile, $ablock);
fclose($wfile);

// TEST: run the HTML code
echo $block;

}

?>

</body>
</html>

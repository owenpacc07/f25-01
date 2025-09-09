<?php
error_reporting(E_ALL ^ E_WARNING); 
// define variables and set to empty values
$nameErr = $valueErr = $sizeErr = "";
$rvalue1 = $rvalue2 = $rvalue3 = $rvalue4 = $lvalue = $lvalue1 = $lvalue2 = $lvalue3 = $lvalue4 = $labelName = $rname = $r = $wbody = $line = $block = "";

// READ generic code in file
$rfile = fopen("e10gen.txt", "r");
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
  if (empty($_POST["id3"])) {
    $nameErr = "ID is required";
  } else {
    $id3 = test_input($_POST["id3"]);    
  }
  if (empty($_POST["id4"])) {
    $nameErr = "ID is required";
  } else {
    $id4 = test_input($_POST["id4"]);    
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
  if (empty($_POST["rvalue1"])) {
    $valueErr = "Value is required";
  } else {
    $rvalue1  = test_input($_POST["rvalue1"]);    
  }
  if (empty($_POST["rvalue2"])) {
    $valueErr = "Value is required";
  } else {
    $rvalue2  = test_input($_POST["rvalue2"]);    
  }
   if (empty($_POST["rvalue3"])) {
    $valueErr = "Value is required";
  } else {
    $rvalue3  = test_input($_POST["rvalue3"]);    
  }
  if (empty($_POST["rvalue4"])) {
    $valueErr = "Value is required";
  } else {
    $rvalue4  = test_input($_POST["rvalue4"]);    
  }
  
  
  if (empty($_POST["rname"])) {
    $sizeErr = "Name is required";
  } else {
    $rname = test_input($_POST["rname"]);
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
$arri[7]=$rname;
$arri[9]=$rvalue1;
$arri[11]=$id1;
$arri[13]=$lvalue1;
$arri[15]=$id2;
$arri[17]=$rname;
$arri[19]=$rvalue2;
$arri[21]=$id2;
$arri[23]=$lvalue2;
$arri[25]=$id3;
$arri[27]=$rname;
$arri[29]=$rvalue3;
$arri[31]=$id3;
$arri[33]=$lvalue3;
$arri[35]=$id4;
$arri[37]=$rname;
$arri[39]=$rvalue4;
$arri[41]=$id4;
$arri[43]=$lvalue4;

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
$wfile = fopen("e10code.htm", 'w');
  fwrite($wfile, $block);
fclose($wfile);

// WRITE PARTS of HTML code to file
$wfile = fopen("../all/code.html", 'a+');
  fwrite($wfile, $block);
fclose($wfile);

// WRITE PARTS of HTML code to file
$wfile = fopen("e10codeParts.txt", 'w');
  fwrite($wfile, $ablock);
fclose($wfile);

// TEST: run the HTML code
echo $block;

}

?>

</body>
</html>

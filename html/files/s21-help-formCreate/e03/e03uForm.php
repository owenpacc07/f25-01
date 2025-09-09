<?php

// define variables and set to empty values
$nameErr = $valueErr = $sizeErr = "";
$labelValue = $labelName = $labelSize = $wbody = $line = $block = "";

// READ generic code in file
$rfile = fopen("e03codeParts.txt", "r");
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
  
  if (empty($_POST["rname"])) {
    $sizeErr = "Name is required";
  } else {
    $rname = test_input($_POST["rname"]);
  }
    
  if (empty($_POST["rvalue1"])) {
    $sizeErr = "Size is required";
  } else {
    $rvalue1 = test_input($_POST["rvalue1"]);
  }
  if (empty($_POST["rname2"])) {
    $valueErr = "Name is required";
  } else {
    $rname = test_input($_POST["rname2"]);
  }
    if (empty($_POST["rvalue2"])) {
    $valueErr = "Size is required";
  } else {
    $rvalue2 = test_input($_POST["rvalue2"]);
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


<h2>Element # 03</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="e03uProcess.php">  
  Element ID: <input type="text" name="id" value="<?php echo htmlentities($arri[1]); ?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  Label Value: <input type="text" name="lvalue" value="<?php echo htmlentities($arri[3]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
  RadioButton Variable Name: <input type="text" name="rname" value="<?php echo htmlentities($arri[7]); ?>">
  <span class="error">* <?php echo $sizeErr;?></span>
  <br><br>
   Element ID: <input type="text" name="id1" value="<?php echo htmlentities($arri[5]); ?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
   Radio Value1: <input type="text" name="rvalue1" value="<?php echo htmlentities($arri[9]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
  Radio Label 1: <input type="text" name="lvalue1" value="<?php echo htmlentities($arri[13]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
   Element ID: <input type="text" name="id2" value="<?php echo htmlentities($arri[15]); ?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
   Radio Value2: <input type="text" name="rvalue2" value="<?php echo htmlentities($arri[19]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
   Radio Label 2: <input type="text" name="lvalue2" value="<?php echo htmlentities($arri[23]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>

  <input type="submit" name="submit" value="Submit">  
</form>
<p>
<hr>

</body>
</html>
<?php

// define variables and set to empty values
$nameErr = $valueErr = $sizeErr = "";
$labelValue = $labelName = $labelSize = $wbody = $line = $block = "";

// READ generic code in file
$rfile = fopen("e05codeParts.txt", "r");
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


<h2>Element # 05</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="e05uProcess.php">  
  Element ID: <input type="text" name="id" value="<?php echo htmlentities($arri[1]); ?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  Label Value: <input type="text" name="lvalue" value="<?php echo htmlentities($arri[3]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
  SELECT Variable Name: <input type="text" name="sname" value="<?php echo htmlentities($arri[7]); ?>">
  <span class="error">* <?php echo $sizeErr;?></span>
  <br><br>
  SELECT Value 1: <input type="text" name="svalue1" value="<?php echo htmlentities($arri[9]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
  Label Value 1: <input type="text" name="lvalue1" value="<?php echo htmlentities($arri[11]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
  SELECT Value 2: <input type="text" name="svalue2" value="<?php echo htmlentities($arri[13]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
  Label Value 2: <input type="text" name="lvalue2" value="<?php echo htmlentities($arri[15]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>

  <input type="submit" name="submit" value="Submit">  
</form>
<p>
<hr>

</body>
</html>
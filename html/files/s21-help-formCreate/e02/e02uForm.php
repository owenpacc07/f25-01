<?php

// define variables and set to empty values
$nameErr = $valueErr = $sizeErr = "";
$labelValue = $labelName = $labelSize = $wbody = $line = $block = "";

// READ generic code in file
$rfile = fopen("e02codeParts.txt", "r");
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
    $labelValue  = test_input($_POST["lvalue"]);    
  }
    
  if (empty($_POST["lsize"])) {
    $sizeErr = "Size is required";
  } else {
    $labelSize = test_input($_POST["lsize"]);
  }
  if (empty($_POST["tname"])) {
    $sizeErr = "Name is required";
  } else {
    $tname = test_input($_POST["tname"]);
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


<h2>Element # 02</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="e02uProcess.php">  
  Element ID: <input type="text" name="id" value="<?php echo htmlentities($arri[4]); ?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  Label Value: <input type="text" name="lvalue" value="<?php echo htmlentities($arri[6]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br>
  Label Size: <input type="text" name="lsize" value="<?php echo htmlentities($arri[8]); ?>">
  <span class="error">* (enter h1, h2, or h3)<?php echo $sizeErr;?></span>
  <br><br>
  Textbox Variable Name: <input type="text" name="tname" value="<?php echo htmlentities($arri[13]); ?>">
  <span class="error">* <?php echo $sizeErr;?></span>
  <br><br>

  <input type="submit" name="submit" value="Submit">  
</form>
<p>
<hr>

</body>
</html>
<?php

// define variables and set to empty values
$nameErr = $valueErr = $sizeErr = "";
$labelValue = $labelName = $labelSize = $wbody = $line = $block = "";

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
    $sizeErr = "Type is required";
  } else {
    $labelSize = test_input($_POST["type"]);
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


<h2>Element # 06</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="e06uProcess.php">  
  
  Button Type: <input type="text" name="type" value="<?php echo htmlentities($arri[1]); ?>">
  <span class="error">* (enter submit or reset)<?php echo $sizeErr;?></span>
  <br><br>

  Button Value: <input type="text" name="value" value="<?php echo htmlentities($arri[3]); ?>">
  <span class="error">* <?php echo $valueErr;?></span>
  <br><br> 
  <input type="submit" name="submit" value="Submit">  
</form>
<p>
<hr>

</body>
</html>
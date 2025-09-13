<?php
require_once "../config-legacy.php";
global $link;

//changes modes information upon form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {

	//loop through all rows
	foreach($_POST['ModeID'] as $index => $ModeID) {

	//set variables
	$Title = $_POST['Title'][$index];
	$Description = $_POST['Description'][$index];
	$Permissions = $_POST['Permissions'][$index];


	$sql = "UPDATE modes SET Title='$Title', Description='$Description', Permissions='$Permissions' WHERE ModeID='$ModeID'";
	$result = mysqli_query($link, $sql);
	
	if (!$result){
	   echo "Error updating record for ModeID $ModeID: " . mysqli_error($link) . "<br>";
        }

	}

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h2>Modes & Permissions</h2>
	<form id="ModesForm" method="post">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr>
                    <th>ModeID</th>
                    <th>Title</th>
		    <th>Description</th>
	            <th>Permissions</th>
                </tr>
            </thead>
            <tbody>
                
                    <?php

                    $sel_query = "Select ModeID, Title, Description, Permissions from modes;";
                    $result = mysqli_query($link, $sel_query);
                    while ($row = mysqli_fetch_array($result)) { 
			$ModeID = $row['ModeID'];
			$Title = $row['Title'];
			$Description = $row['Description'];
			$Permissions = $row['Permissions'];
?>
			
                        <tr>
                            	<td align="center"><input type="text" name="ModeID[]" value="<?php echo $row['ModeID'] ?>" readonly></td>
                            	<td align="center"><input type="text" name="Title[]" value="<?php echo $row['Title']; ?>"></td>
				<td align="center"><input type="text" name="Description[]" value="<?php echo $row['Description']; ?>"></td>
                            	<td align="center"><input type="text" name="Permissions[]" value="<?php echo $row['Permissions']; ?>"></td>

                      	
			</tr>

                    <?php } ?>                
            	</tbody>
            </table>
 	  <!-- button to save changes -->
          <input type="submit" form="ModesForm" class="btn btn-primary" value="Save">
	  <p style="font-size: 20px;">This form is for viewing the details of each mode, including its title, description, and permissions.</p> 
	</form>
	
    </div>
</body>

</html>
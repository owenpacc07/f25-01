<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="./styles.css">
</head>

<body>
    <?php
    // define variables and set to empty values
    $groupName = $managerName = "";
    $group = $manager = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["GroupName"])) {
            $groupName = "Name is required";
        } else {
            $group = test_input($_POST["GroupName"]);
        }

        if (empty($_POST["ManagerName"])) {
            $managerName = "Name is required";
        } else {
            $manager = test_input($_POST["ManagerName"]);
        }
    }

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>

    <h2>Absolute classes registration</h2>

    <p><span class="error-addGroups">* required field.</span></p>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <table>
            <tr>
                <td>Group name:</td>
                <td><input type="text" name="group">
                    <span class="error">* <?php echo $groupName; ?></span>
                </td>
            </tr>
            <tr>
                <td>Manager Name:</td>
                <td><input type="text" name="manager">
                    <span class="error">* <?php echo $managerName; ?></span>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="submit" name="submit" value="Submit">
                </td>
            </tr>

        </table>
    </form>



</body>

</html>
<?php
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
                <form id="ModesForm">

                    <?php

                    $sel_query = "Select * from modes;";
                    $result = mysqli_query($link, $sel_query);
                    while ($row = mysqli_fetch_array($result)) { ?>
                        <tr>
                            <td align="center"><input type="text" name="modeid" value="<?php echo $row['modeid'] ?>"></td>
                            <td align="center"><input type="text" name="title" value="<?php echo $row['title']; ?>"></td>
                            <td align="center"><input type="text" name="description" value="<?php echo $row['description']; ?>"></td>
                            <td align="center"><input type="text" name="permissions" value="<?php echo $row['permissions']; ?>"></td>
                        </tr>

                    <?php } ?>
                    <tr>

                </form>
            </tbody>
        </table>
        <!-- button to save changes -->
        <input type="submit" form="ModesForm" class="btn btn-primary" value="Save"></button>
    </div>
</body>

</html>
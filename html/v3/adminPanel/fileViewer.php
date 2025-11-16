<?php
session_start();
require_once "../config-legacy.php";
global $link;

$filter_sub = "";
$filter_user = "";
// when server recieves post request 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["Mechanism"]) && $_POST["Mechanism"] != "") {
        // get the value from the mech dropdown
        $filter_sub = $_POST["Mechanism"];
        // keep the value in the session

    }
    if (isset($_POST["filter_user"]) && $_POST["filter_user"] != "") {
        // get the value from the user input
        $filter_user = $_POST["filter_user"];
        // keep the value in the session

    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../fileViewing.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.css">
    </link>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.3/theme/darcula.min.css" integrity="sha512-kqCOYFDdyQF4JM8RddA6rMBi9oaLdR0aEACdB95Xl1EgaBhaXMIe8T4uxmPitfq4qRmHqo+nBU2d1l+M4zUx1g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.js">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.3/mode/clike/clike.js"></script>
    <title>File Viewer</title>
</head>

<body onload="load()">
    <?php require '../navbar.php'; ?>

    <div class="card" style="margin-top: 5rem;">
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <!-- this is the spot where files will be loaded -->
                        <div id="code"></div>
                        <div id="output" style="display: none;"></div>
                        <button class="btn btn-primary" id="saveChanges" style="display: none;" onclick="updateFile()">Save Changes</button>
                    </div>
                    <div class="col">
                        <form method="POST">
                            <select name="Mechanism">
                                <option value="all">All Mechanisms</option>
                                <?php

                                $res = mysqli_query($link, "Select * from mechanisms;");
                                while ($r = mysqli_fetch_array($res)) {
                                    //changed mechanismID to mechanism_id
                                    $mechanismValue = $r['mechanism_id'];
                                    //changed Name to algorithm
                                    $mechanismName = $r['algorithm'];
                                    if (isset($filter_sub)) {
                                        if ($filter_sub == $mechanismValue) {
                                            echo "<option value='$mechanismValue' selected>$mechanismName</option>";
                                        } else {
                                            echo "<option value='$mechanismValue'>$mechanismName</option>";
                                        }
                                        //echo "<option value='$mechanismValue'>$mechanismName</option>";
                                    }
                                }

                                ?>
                            </select>
                            <input type="submit" value="Filter">
                        </form>
                        <br>
                        <form method="POST">
                            <?php
                            if (!isset($filter_user)) {
                                echo "<input type=\"text\" placeholder=\"user id\" name=\"filter_user\">";
                            } else {
                                echo "<input type=\"text\" placeholder=\" $filter_user \" name=\"filter_user\">";
                            }
                            ?>

                            <input type="submit" value="Filter">
                        </form>
                        <hr>
                        <!-- this is where you select files -->
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Owner</th>
                                    <th scope="col">Mechanism</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php
                                $sql = "";
                                // select all the submissions from the database
                                if (!isset($filter_sub) || $filter_sub == "all" || $filter_sub == "") {
                                    $sql = "SELECT * FROM submissions";
                                } else {
                                    $sql = "SELECT * FROM submissions WHERE Mechanism = $filter_sub";
                                }
                                if (!isset($filter_user) || $filter_user == "") {
                                    // do nothing
                                } else {
                                    if (strpos($sql, "WHERE") !== false) {
                                        $sql = $sql . " AND Owner = $filter_user";
                                    } else {
                                        $sql = $sql . " WHERE Owner = $filter_user";
                                    }
                                }

                                $res_r = mysqli_query($link, $sql);
                                // display the res_rs as a vertical table - 1 row per submission
                                if ($res_r) {
                                    while ($row = mysqli_fetch_assoc($res_r)) {
                                        $filePath = "files/" . $row['filePath'];
                                        $id = $row['SubID'];

                                        $owner_result = mysqli_query($link, "select Email from users where UserID = $row[Owner]");
                                        $owner = mysqli_fetch_array($owner_result)[0];

                                        $mechanism_result = mysqli_query($link, "select Name from mechanisms where mechanismID = $row[Mechanism]");
                                        $mechanism = mysqli_fetch_array($mechanism_result)[0];

                                        echo "<tr data-bs-toggle=\"collapse\" data-bs-target=\"#row$id\" class=\"accordion-toggle\">";
                                        echo "<td>" . $row['SubID'] . "</td>";
                                        echo "<td>" . $row['Subtitle'] . "</td>";
                                        echo "<td>" . $owner . "</td>";
                                        echo "<td>" . $mechanism . "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td colspan=\"4\" class=\"hiddenRow\" style=\"padding: 0 !important;\"><div id=\"row$id\" class=\"accordian-body collapse\">";
                                        // this will be options for each file
                                        // first will be a button to open the file
                                        // second will be a button to delete the file
                                        echo "<button class=\"btn btn-primary\" id=\"openButton\" onclick=\"openFile('$filePath', $id)\">Open</button>";
                                        echo "<button class=\"btn btn-primary\" id=\"runButton\" onclick=\"runFile('$filePath', $id)\">Run</button>";
                                        // download file
                                        echo "<button class=\"btn btn-primary\" onclick=\"downloadFile('$filePath')\">Download</button>";
                                        echo "<button class=\"btn btn-primary\" data-bs-dismiss=\"collapse\" onclick=\"openFile('CLOSE', $id)\">Close</button>";

                                        echo "</div></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "Error: " . $sql . "<br>" . mysqli_error($link);
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>


<script>
    let code = "";

    function load() {
        document.querySelectorAll("#runButton").forEach(function(element) {
            element.setAttribute("disabled", "disabled");
        });
    }

    function runFile(file) {
        console.log("run file");
        console.log(file);
        console.log(code);
        document.getElementById("output").style.display = "block";
        let response;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "https://emkc.org/api/v2/piston/execute/", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                response = JSON.parse(this.responseText);
                console.log(response);
                // save the run.output in a string
                var output = response.run.output;
                // display the output in the codeOutput textarea
                console.log(output);
                document.getElementById("output").innerHTML = output;
            }
        };
        xhr.send(JSON.stringify({
            "language": "java",
            "version": "15.0.2",
            "files": [{
                "content": code
            }]
        }));
    }



    function openFile(file, id) {
        //document.getElementById("openButton").setAttribute("disabled", "disabled");
        document.querySelectorAll("#openButton").forEach(function(element) {
            element.setAttribute("disabled", "disabled");
        });

        if (file === "CLOSE") {
            // remove the file from view and close it

            document.getElementById("code").innerHTML = "";
            document.getElementById("saveChanges").style.display = "none";
            // make the open button active again
            document.getElementById("openButton").removeAttribute("disabled");
            document.querySelectorAll("#openButton").forEach(function(element) {
                element.removeAttribute("disabled");
            });

            document.querySelectorAll("#runButton").forEach(function(element) {
                element.setAttribute("disabled", "disabled");
            });
            // close the collapse

            document.getElementById("row" + id).classList.remove("show");
            return;
        }
        // set run button to active when file is opened
        document.querySelectorAll("#runButton").forEach(function(element) {
            element.removeAttribute("disabled");
        });

        fetch("../../" + file)
            .then(response => response.text())
            .then(text => {
                code = text;
                CodeMirror(document.querySelector('#code'), {
                    lineNumbers: true,
                    value: text,
                    mode: "text/x-java",
                    theme: "darcula"
                }).on('change', function(cm) {
                    // update the text of the currently open file
                    code = cm.getValue();

                });

            });
    }



    function downloadFile(file) {
        openFile(file);
        let a = document.createElement("a");
        a.href = "../../" + file;
        a.download = file;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>

</html>
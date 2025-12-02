<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./coding.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</head>

<body>
    <?php include './navbar.php'; ?>


    <!-- Use the pistion lib to send the java code as a string and display response  -->
    <div class="card" id="code-card">
        <div class="card-body">
            <div class="container">
                <h1 class="has-text-centered">Enter code in Code here hit run and see the output in Output here</h1>

                <div class="row">

                    <div class="col">
                        <div class="col-md-auto">
                            <label for="codeInput" class="form-label">Code here</label>
                            <textarea class="form-control" id="codeInput" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="col">
                        <div class="col-md-auto">
                            <label for="codeOutput" class="form-label">Output here</label>
                            <textarea class="form-control" id="codeOutput" rows="3"></textarea>
                        </div>
                    </div>

                </div>
                <hr>
                <div class="row">

                    <div class="col">
                        <div class="col-md-auto">
                            <select class="form-select" id="langSelect" aria-label="Default select example">Language
                                <option value="java">Java</option>
                                <option value="c">C</option>
                                <option value="c++">C++</option>
                                <option value="python">Python</option>
                            </select>

                        </div>
                    </div>

                    <div class="col">
                        <div class="col-md-auto">
                            <!-- button to submit and run runCode() -->
                            <button onclick="runCode()" class="btn btn-primary">Run Code</button>
                        </div>

                    </div>

                    <div class="col">
                        <div class="col-md-auto">

                            <button class="btn btn-primary" href='submission.php'>Submit</button>
                        </div>



                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
<script>
    //let selectedLanguage = "java";


    // function to run the code submitted through api call
    // POST https://emkc.org/api/v2/piston/execute/
    // {
    // "language": "java",
    // "version": "15.0.2",
    // "files":[
    //  {
    //    "content": "class test { public static void main(String[] args) { System.out.println(\"Hello world!\"); } } "
    //  }
    // ]
    // }
    // function gets the code from codeInput and sends it as a json object to the api

    function runCode() {


        // get value of dropwdown and save it in lang
        var lang = document.getElementById("langSelect").value;


        var code = document.getElementById("codeInput").value;
        //console.log(code);
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
                document.getElementById("codeOutput").value = output;
            }
        };
        if (lang == "java") {
            xhr.send(JSON.stringify({
                "language": "java",
                "version": "15.0.2",
                "files": [{
                    "content": code
                }]
            }));
        } else if (lang == "python") {
            xhr.send(JSON.stringify({
                "language": "python",
                "version": "3.10.0",
                "files": [{
                    "content": code
                }]
            }));
        } else if (lang == "c") {
            xhr.send(JSON.stringify({
                "language": "c",
                "version": "10.2.0",
                "files": [{
                    "content": code
                }]
            }));
        } else if (lang == "c++") {
            xhr.send(JSON.stringify({
                "language": "c++",
                "version": "10.2.0",
                "files": [{
                    "content": code
                }]
            }));
        } else {
            console.log("No language selected");
        }



    }
</script>

</html>
<?php

//alerts could be more slick

// connect to mysql database
require_once "config-legacy.php";
global $link;

//It's much simpler to gray out the submit button if passwords don't match, intead of passing both through post
//implementing that next

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  //Use escape string functions to prevent SQL injections
  $email = $link->escape_string($_POST['email']);


  //hashes and salts the password. For login, use password_verify(password, hash)
  $password = $link->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));


  //See if user's email is already in the database
  $result = $link->query("SELECT * FROM users WHERE email='$email'");

  //If the query returns any number of rows, user already exists
  if ($result->num_rows > 0) {
    echo '<script type="text/javascript"> 
            alert("User already exists")
            </script>';
  } else {
    // create random user id that is within INT(50) for sql
    $user_id = rand(1, 99999999);
    $result = $link->query("SELECT * FROM users WHERE UserID='$user_id'");
    while ($result->num_rows > 0) {
      // if the ID already exists, generate a new one
      $user_id = rand(1, 99999999);
      $result = $link->query("SELECT * FROM users WHERE UserID='$user_id'");
    }
    //For now insterting dummy values into rows besides email and pw -- auto increment isnt working (ISSUE RESOLVED)
    $sql = "INSERT INTO users VALUES ('$user_id','$password','$email',0,0,0)";

    if ($link->query($sql)) {
      // $user_name = explode("@", $email); // create user name from email ex: bob12 in bob12@mail.com
      // //upon successful user registration, create unique temporary directory for user I/O editing purposes
      // $user_submissions_path = sys_get_temp_dir() . "/" . session_id() . "/" . $user_name[0];
      // if (!file_exists($user_submissions_path)) {
      //     mkdir($user_submissions_path, 0700, true);
      // }
      //the above sutff doesnt work right now due to permission constraints
      echo ("Success!");
    } else {
      echo ("Failure sending to database: " . $link->error); //display sql error
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   
  <link rel="stylesheet" href="./styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body onload="validEmail()">
  <?php include './navbar.php'; ?>

  <div class="section container p-3 my-3">
    <div class="card">
      <header class="card-header">
        <h3 class="card-header-title">
          Register
        </h3>
      </header>
      <div class="card-body">

        <form method="POST" action="register.php" id="regForm">


          <!-- email -->
          <div class="field">
            <label class="label">Email</label>
            <div class="control has-icons-left has-icons-right">
              <input class="input" type="email" id="emailInput" name="email" placeholder="Email">
              <span class="icon is-small is-left">
                <i class="fas fa-envelope"></i>
              </span>
              <span class="icon is-small is-right">

                <i class="fas fa-check" id="check"></i>
              </span>
            </div>
          </div>
          <script>
            ////
            //// Want to do the same thing for passwords, but with set rules to make passwords "safe"
            ////
            /*function validPass() {
              let pass = document.getElementById("passInput");
              let inputHandler = function(e) {
                if (e.target.value)
              }

            }*/
            // add a change listener to the email field to display a green check if the email is valid
            function validEmail() {
              let ele = document.getElementById("emailInput");
              let inputHandler = function(e) {
                if (validateEmail(e.target.value)) {
                  document.getElementById("check").style.background = "green";
                } else {
                  document.getElementById("check").style.background = "red";
                }
                //console.log(e.target.value);
              }
              // listen for change in the ele value
              ele.addEventListener('input', inputHandler);
              ele.addEventListener('propertychange', inputHandler);
            }

            function validateEmail(email) {
              // send value to the php function to check if it is in the database
              if (email == "") {
                return false;
              }

              return true;


            }
          </script>

          <!-- password -->
          <div class="field">
            <label class="label">Password</label>
            <div class="control has-icons-left has-icons-right">
              <input class="input" type="password" id="password" name="password" placeholder="Password">
              <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
              </span>
            </div>
          </div>

          <!-- confirm password -->
          <div class="field">
            <label class="label">Confirm Password</label>
            <div class="control has-icons-left has-icons-right">
              <input class="input" type="password" name="MyPass2" placeholder="Confirm Password">
              <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
              </span>
            </div>
          </div>



          <div class="field is-grouped">
            <div class="control">
              <input type="submit" form="regForm" class="button is-link" value="Submit"></input>
            </div>
          </div>


        </form>
      </div>
    </div>
  </div>

</body>

</html>
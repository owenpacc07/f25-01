<?php
/*  ----------  PAGE‑REPLACEMENT  submissions.php  ----------  */

session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../../login.php');  exit();
}

require '../../config.php';
if (!isset($_SESSION['log_messages'])) $_SESSION['log_messages'] = [];

/* ---------- 1.  validate experiment id ---------- */
if (!isset($_GET['experiment_id'])) {
    $_SESSION['error_message'] = "No experiment ID provided.";
    header("Location: ../experimentTest.php"); exit();
}
$experiment_id_param = $_GET['experiment_id'];                 //  uID_eID_family
list($user_id, $experiment_id, $family_id) = explode('_', $experiment_id_param);
if (!$user_id || !$experiment_id || !$family_id) {
    $_SESSION['error_message'] = "Invalid experiment ID format.";
    header("Location: ../experimentTest.php"); exit();
}

$user_id      = mysqli_real_escape_string($link, $user_id);
$experiment_id= mysqli_real_escape_string($link, $experiment_id);
$family_id    = mysqli_real_escape_string($link, $family_id);

$base_path = realpath("../../../files/experiments/");
if ($base_path === false) {
    $_SESSION['error_message'] = "Invalid base experiments path.";  exit();
}
$experiment_path = "$base_path/$experiment_id_param";
if (!file_exists($experiment_path)) {
    $_SESSION['error_message'] = "Experiment folder not found.";    exit();
}

/* ---------- 2.  handle form submission ---------- */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitInput'])) {

    /* 🔹 page‑replacement expects: 1st line = frame count, 2nd line = comma list */
    $refsRaw    = trim($_POST['refs'] ?? '');
    $refsArray  = array_filter(array_map('trim', explode(',', $refsRaw)), 'strlen');
    $refsLine   = implode(',', $refsArray);

    $newContents = "$refsLine";

    /* 🔹 write the master input file inside the experiment folder */
    $input_file = "$experiment_path/in-page.dat";
    if (file_put_contents($input_file, $newContents) === false) {
        $_SESSION['error_message'] = "Failed to save input file.";  /* log omitted */
    } else {

        $_SESSION['log_messages'][] = "Wrote to $input_file: $newContents";

        /* 🔹 run all five page‑replacement mechanisms */
        $mechanisms = ['021','022','023','024','025'];               // FIFO, OPT, LRU, LFU, MFU
        foreach ($mechanisms as $mid) {

            /* copy input into mechanism folder so Java finds it */
            $mech_files = realpath("../../../files/core-c/m-$mid");
            if ($mech_files && copy($input_file, "$mech_files/in-$mid.dat")) {
                $_SESSION['log_messages'][] = "Copied input to m$mid";
            }

            $java_path = realpath("../../../cgi-bin/core-e/m-$mid");
            if ($java_path === false) {
                $_SESSION['log_messages'][] = "Java path missing for m$mid";
                continue;
            }

            $java_cmd = "java -classpath " . escapeshellarg($java_path)
                      . " m$mid " . escapeshellarg($experiment_path);
            $java_out = shell_exec("$java_cmd 2>&1");
            if ($java_out) $_SESSION['log_messages'][] = "m$mid: $java_out";

            $output_file = "$experiment_path/out-$mid.dat";
            if (!file_exists($output_file)) {
                $fallback = "Error in output generation";
                file_put_contents($output_file, $fallback);
                $_SESSION['log_messages'][] = "Fallback output for m$mid";
            }
        }

        $_SESSION['success_message'] = "Input processed & simulations launched.";
        header("Location: submissions.php?experiment_id=$experiment_id_param");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Page‑Replacement Submission</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script>
/* 🔹 randomize helper for page references */
function randomizeRefs(){
    const len  = Math.floor(Math.random()*10) + 10;   // 10‑19 refs
    const refs = Array.from({length: len},
                   () => Math.floor(Math.random()*10));

    document.getElementById('refsInput').value = refs.join(', ');
}

</script>
</head>
<body>
<?php include realpath('../../navbar.php'); ?>

<div class="container mt-5">
  <div class="row justify-content-center"><div class="col-md-6">
    <div class="card">
      <div class="card-header"><h3 class="text-center">
          Submit Input for Experiment <?=htmlspecialchars($experiment_id);?>
      </h3></div>
      <div class="card-body">

        <?php if(isset($_SESSION['success_message'])){ ?>
          <div class="alert alert-success"><?=htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']);?></div>
        <?php } if(isset($_SESSION['error_message'])){ ?>
          <div class="alert alert-danger"><?=htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']);?></div>
        <?php } ?>

        <form method="POST" class="text-center">
          <div class="form-group">

            <label for="refsInput" class="mt-2">Page Reference String (comma‑separated):</label>
            <textarea name="refs" id="refsInput" rows="3" class="form-control"
                      required><?=isset($_POST['refs'])?htmlspecialchars($_POST['refs']):'6, 1, 2, 0, 6, 0, 8, 9, 4, 7, 2, 8, 8, 1 ';?></textarea>
          </div>

          <button type="submit" name="submitInput" class="btn btn-purple mr-2">Submit Input</button>
          <button type="button" class="btn btn-purple" onclick="randomizeRefs()">Randomize</button>
        </form>
      </div>
    </div>
  </div></div>
</div>

<style>
.btn-purple{background:#9769D9;color:#fff;border-radius:8px}
.btn-purple:hover{background:#B594E4}
.card-header{background:#9769D9;color:#fff;font-weight:bold}
.card{border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,.1)}
.card:hover{transform:translateY(-5px);box-shadow:0 6px 20px rgba(0,0,0,.15)}
</style>
</body>
</html>

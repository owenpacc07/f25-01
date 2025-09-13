<?php
require(__DIR__ . "/../config.php");

header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (!isset($_POST['mechanism_id'], $_POST['experiment_id'], $_POST['user_id'], $_POST['family_id'])) {
        echo json_encode(["error" => "Missing required parameters"]);
        return;
    }

    $mechanism_id = $_POST['mechanism_id']; // e.g., "031"
    $experiment_id = $_POST['experiment_id']; // e.g., "2_437_4"
    $user_id = $_POST['user_id']; // e.g., "2"
    $family_id = $_POST['family_id']; // e.g., "4"

    // Validate experiment_id format
    $parts = explode('_', $experiment_id);
    if (count($parts) !== 3 || $parts[0] != $user_id || $parts[2] != $family_id) {
        echo json_encode(["error" => "Invalid experiment_id"]);
        return;
    }

    // Fetch algorithm name from comparisons table
    $algorithm_first_word = 'file'; // Default fallback
    $mechanism_query = "SELECT algorithm FROM comparisons WHERE component_id = ? LIMIT 1";
    if ($stmt = $link->prepare($mechanism_query)) {
        $stmt->bind_param("i", $family_id);
        $stmt->execute();
        $mechanism_result = $stmt->get_result();
        if ($row = $mechanism_result->fetch_assoc()) {
            $algorithm_name = $row['algorithm'];
            $algorithm_first_word = strtolower(explode(' ', trim($algorithm_name))[0]);
        } else {
            error_log("No algorithm found for family_id $family_id, using fallback 'file'");
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare query for family_id $family_id: " . $link->error);
    }

    // Base path for experiment submission
    $base_path = ROOT_DIR . "/files/experiments/{$experiment_id}";
    $input_path = "{$base_path}/in-{$algorithm_first_word}.dat"; // e.g., in-file.dat
    $output_path = "{$base_path}/out-{$mechanism_id}.dat"; // e.g., out-032.dat

    if (!file_exists($input_path)) {
        echo json_encode(["error" => "Input file not found: $input_path"]);
        return;
    }
    $file_input = fopen($input_path, "r");
    $input_contents = fread($file_input, filesize($input_path));
    fclose($file_input);

    if (!file_exists($output_path)) {
        echo json_encode(["error" => "Output file not found: $output_path"]);
        return;
    }
    $file_output = fopen($output_path, "r");
    $output_contents = fread($file_output, filesize($output_path));
    fclose($file_output);

    echo json_encode([
        "experiment_id" => $experiment_id,
        "mechanism_id" => $mechanism_id,
        "user_id" => $user_id,
        "family_id" => $family_id,
        "input" => $input_contents,
        "output" => $output_contents,
        "input_file" => basename($input_path), // e.g., in-file.dat
        "output_file" => basename($output_path) // e.g., out-032.dat
    ]);
    return;
}

echo json_encode(["error" => "Only POST is supported"]);
?>
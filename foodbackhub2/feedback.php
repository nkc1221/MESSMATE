<?php
header("Content-Type: application/json");


ini_set("display_errors", 0);
error_reporting(E_ALL);

include "db.php";

$response = ["success" => false];


$required = ["name", "meal_day", "meal_time", "rating", "comment"];
foreach ($required as $f) {
    if (!isset($_POST[$f]) || trim($_POST[$f]) === "") {
        $response["error"] = "Missing required field: $f";
        echo json_encode($response);
        exit;
    }
}

$name       = $conn->real_escape_string($_POST["name"]);
$student_id = $conn->real_escape_string($_POST["student_id"] ?? "");
$day        = $conn->real_escape_string($_POST["meal_day"]);
$time       = $conn->real_escape_string($_POST["meal_time"]);
$rating     = intval($_POST["rating"]);
$comment    = $conn->real_escape_string($_POST["comment"]);

$image_path = "";


if (!empty($_FILES["image"]["name"])) {
    $dir = "uploads/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $file = time() . "_" . basename($_FILES["image"]["name"]);
    $target = $dir . $file;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
        $image_path = $target;
    } else {
        $response["error"] = "Image upload failed";
        echo json_encode($response);
        exit;
    }
}


$sql = "INSERT INTO feedback
(name, student_id, meal_day, meal_time, rating, comment, image_path, status)
VALUES
('$name', '$student_id', '$day', '$time', $rating, '$comment', '$image_path', 'Pending')";

if ($conn->query($sql)) {
    $response["success"] = true;
} else {
    $response["error"] = "Database error: " . $conn->error;
}

echo json_encode($response);
?>

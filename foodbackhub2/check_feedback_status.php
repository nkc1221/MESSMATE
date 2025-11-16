<?php
header("Content-Type: application/json");
include "db.php";

$email = $_GET['email'] ?? '';

if (empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Email required']);
    exit;
}

// Get the most recent feedback for this email
$stmt = $conn->prepare("
    SELECT id, status, meal_day, meal_time, rating, created_at,
           TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes_ago
    FROM feedback 
    WHERE student_id = ? 
    ORDER BY created_at DESC 
    LIMIT 1
");

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $feedback = $result->fetch_assoc();
    
    $statusMessages = [
        'Pending' => sprintf(
            'Your feedback about %s %s is pending review. We typically review feedback within 24 hours.',
            $feedback['meal_day'],
            $feedback['meal_time']
        ),
        'Viewed' => sprintf(
            'Good news! Your feedback about %s %s has been viewed by our management team. They are evaluating your suggestions.',
            $feedback['meal_day'],
            $feedback['meal_time']
        ),
        'Resolved' => sprintf(
            'Great! Your feedback about %s %s has been resolved. Thank you for helping us improve! Your rating: %d/5',
            $feedback['meal_day'],
            $feedback['meal_time'],
            $feedback['rating']
        )
    ];
    
    echo json_encode([
        'success' => true,
        'latest_feedback' => [
            'id' => $feedback['id'],
            'status' => $feedback['status'],
            'meal_day' => $feedback['meal_day'],
            'meal_time' => $feedback['meal_time'],
            'rating' => $feedback['rating'],
            'minutes_ago' => $feedback['minutes_ago']
        ],
        'message' => $statusMessages[$feedback['status']],
        'is_recent' => $feedback['minutes_ago'] < 1440 // Within last 24 hours
    ]);
} else {
    echo json_encode([
        'success' => true,
        'latest_feedback' => null,
        'message' => 'No feedback found. Share your thoughts about our food!'
    ]);
}

$stmt->close();
$conn->close();
?>

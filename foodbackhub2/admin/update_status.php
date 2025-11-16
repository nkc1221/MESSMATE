<?php

require_once 'auth_check.php';

include_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? 'Pending');

    if ($id && in_array($newStatus, ['Pending', 'Viewed', 'Resolved'])) {
        
        
        $stmt = $conn->prepare("SELECT * FROM feedback WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $feedback = $result->fetch_assoc();
        $stmt->close();
        
        
        $stmt = $conn->prepare("UPDATE feedback SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $newStatus, $id);
        $updateSuccess = $stmt->execute();
        $stmt->close();
        
        
        if ($updateSuccess && $feedback && $feedback['status'] !== $newStatus) {
            if (file_exists(__DIR__ . '/../email_helper.php')) {
                include_once __DIR__ . '/../email_helper.php';
                
                $studentEmail = $feedback['student_id'];
                $studentName = $feedback['name'];
                
                if (!empty($studentEmail)) {
                    $feedbackDetails = [
                        'meal_day' => $feedback['meal_day'],
                        'meal_time' => $feedback['meal_time'],
                        'rating' => $feedback['rating'],
                        'comment' => $feedback['comment']
                    ];
                    
                    sendStatusUpdateEmail($studentEmail, $studentName, $newStatus, $feedbackDetails);
                }
            }
        }
    }
}

header('Location: dashboard.php');
exit;
?>

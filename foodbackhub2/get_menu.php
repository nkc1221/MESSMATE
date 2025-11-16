<?php
header("Content-Type: application/json");
include "db.php";

$result = $conn->query("SELECT * FROM menu_items ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), FIELD(meal_time, 'Breakfast', 'Lunch', 'Dinner')");

$menu = [];
while ($row = $result->fetch_assoc()) {
    $day = $row['day_of_week'];
    $mealTime = $row['meal_time'];
    
    if (!isset($menu[$day])) {
        $menu[$day] = [];
    }
    
    $menu[$day][$mealTime] = [
        'items' => array_map('trim', explode(',', $row['food_items'])),
        'special' => (bool)$row['is_special']
    ];
}

echo json_encode($menu);
$conn->close();
?>

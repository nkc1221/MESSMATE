<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

include "db.php";


$sql = "SELECT * FROM feedback ORDER BY id DESC LIMIT 15";
$result = $conn->query($sql);


if (!$result) {
    echo "<div class='muted'>Database error: " . htmlspecialchars($conn->error) . "</div>";
    exit;
}


if ($result->num_rows == 0) {
    echo "<div class='muted'>No feedback submitted yet.</div>";
    exit;
}


while ($row = $result->fetch_assoc()) {
    $status = $row['status'] ?: "Pending"; 
    ?>
    
    <div class="fb">
        <div style="flex:1">
            
            <div class="meta">
                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                <span> • <?php echo htmlspecialchars($row['meal_day']); ?> — <?php echo htmlspecialchars($row['meal_time']); ?></span>
            </div>

            <div class="recent-rating">
                ⭐ Rating: <?php echo intval($row['rating']); ?>/5
            </div>

            <p class="recent-comment">
                <?php echo nl2br(htmlspecialchars($row['comment'])); ?>
            </p>

            <div class="recent-status">
                Status: <b><?php echo htmlspecialchars($status); ?></b>
            </div>

        </div>

        <?php if (!empty($row['image_path'])): ?>
            <img src="./<?php echo htmlspecialchars($row['image_path']); ?>" class="recent-img" alt="Food photo">
        <?php endif; ?>

    </div>

    <?php
}
?>

<?php
// Protect this page with authentication
require_once 'auth_check.php';
include_once __DIR__ . '/../db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        $id = intval($_POST['id']);
        $foodItems = trim($_POST['food_items']);
        $isSpecial = isset($_POST['is_special']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE menu_items SET food_items = ?, is_special = ? WHERE id = ?");
        $stmt->bind_param('sii', $foodItems, $isSpecial, $id);
        
        if ($stmt->execute()) {
            $successMsg = "Menu updated successfully!";
        } else {
            $errorMsg = "Failed to update menu.";
        }
        $stmt->close();
    }
}

// Fetch all menu items
$menuItems = [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$mealTimes = ['Breakfast', 'Lunch', 'Dinner'];

foreach ($days as $day) {
    foreach ($mealTimes as $mealTime) {
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE day_of_week = ? AND meal_time = ?");
        $stmt->bind_param('ss', $day, $mealTime);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $menuItems[$day][$mealTime] = $result->fetch_assoc();
        } else {
            // Create default entry if not exists
            $menuItems[$day][$mealTime] = [
                'id' => null,
                'food_items' => '',
                'is_special' => 0
            ];
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Menu Management — Food-back Hub</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
.menu-management-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.menu-week-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.day-menu-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.day-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: 20px;
    font-weight: 700;
    text-align: center;
}

.meal-editor {
    background: #f9fafb;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 15px;
    border-left: 4px solid #3b82f6;
}

.meal-editor.special {
    border-left-color: #ff6b6b;
    background: #fff5f5;
}

.meal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.meal-title {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
}

.special-badge {
    background: #ff6b6b;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.menu-textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    resize: vertical;
    min-height: 80px;
    transition: all 0.2s ease;
}

.menu-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.special-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 10px 0;
    font-size: 14px;
}

.special-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.update-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
    transition: all 0.2s ease;
}

.update-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.success-message {
    background: #d1fae5;
    color: #065f46;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border-left: 4px solid #10b981;
    animation: slideInDown 0.5s ease;
}

.error-message {
    background: #fee2e2;
    color: #991b1b;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border-left: 4px solid #ef4444;
}

.bulk-actions {
    background: white;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.bulk-actions h3 {
    margin: 0 0 15px 0;
    color: #1f2937;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@media (max-width: 768px) {
    .menu-week-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<div class="admin-wrapper">
    <!-- Header -->
    <header class="admin-header">
        <div class="header-content">
            <div>
                <h1>🍽️ Menu Management</h1>
                <p>Update weekly menu for Simsang Hostel</p>
            </div>
            <div class="header-actions">
                <a href="dashboard.php" class="btn-secondary">
                    <span>📊</span> Dashboard
                </a>
                <a href="../index.html" class="btn-secondary">
                    <span>🏠</span> Main Site
                </a>
                <a href="logout.php" class="btn-logout">
                    <span>🚪</span> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="menu-management-container">
        
        <?php if (isset($successMsg)): ?>
        <div class="success-message">
            ✅ <?= htmlspecialchars($successMsg) ?>
        </div>
        <?php endif; ?>

        <?php if (isset($errorMsg)): ?>
        <div class="error-message">
            ❌ <?= htmlspecialchars($errorMsg) ?>
        </div>
        <?php endif; ?>

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <button class="action-btn btn-primary" onclick="expandAll()">📂 Expand All</button>
                <button class="action-btn btn-secondary" onclick="collapseAll()">📁 Collapse All</button>
                <button class="action-btn btn-success" onclick="if(confirm('This will save all visible changes. Continue?')) { saveAll(); }">💾 Save All Changes</button>
            </div>
        </div>

        <!-- Weekly Menu Grid -->
        <div class="menu-week-grid">
            <?php foreach ($days as $day): ?>
            <div class="day-menu-card">
                <div class="day-header">
                    <?= $day ?>
                </div>

                <?php foreach ($mealTimes as $mealTime): ?>
                    <?php $menu = $menuItems[$day][$mealTime]; ?>
                    <form method="POST" class="meal-editor <?= $menu['is_special'] ? 'special' : '' ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $menu['id'] ?>">
                        
                        <div class="meal-header">
                            <span class="meal-title">
                                <?php
                                $icons = [
                                    'Breakfast' => '🌅',
                                    'Lunch' => '🍛',
                                    'Dinner' => '🌙'
                                ];
                                echo $icons[$mealTime] . ' ' . $mealTime;
                                ?>
                            </span>
                            <?php if ($menu['is_special']): ?>
                            <span class="special-badge">SPECIAL</span>
                            <?php endif; ?>
                        </div>

                        <textarea 
                            name="food_items" 
                            class="menu-textarea" 
                            placeholder="Enter food items separated by commas..."
                            required><?= htmlspecialchars($menu['food_items']) ?></textarea>

                        <label class="special-checkbox">
                            <input 
                                type="checkbox" 
                                name="is_special" 
                                <?= $menu['is_special'] ? 'checked' : '' ?>>
                            <span>Mark as Special Meal</span>
                        </label>

                        <button type="submit" class="update-btn">
                            💾 Update <?= $mealTime ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function expandAll() {
    document.querySelectorAll('.meal-editor').forEach(editor => {
        editor.style.display = 'block';
    });
}

function collapseAll() {
    document.querySelectorAll('.meal-editor').forEach((editor, index) => {
        if (index > 2) editor.style.display = 'none';
    });
}

function saveAll() {
    const forms = document.querySelectorAll('.meal-editor form');
    let saved = 0;
    
    forms.forEach(form => {
        const formData = new FormData(form);
        fetch('manage_menu.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            saved++;
            if (saved === forms.length) {
                alert('✅ All menus updated successfully!');
                location.reload();
            }
        });
    });
}
</script>

</body>
</html>

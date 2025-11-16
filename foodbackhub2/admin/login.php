<?php
session_start();


if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once __DIR__ . '/../db.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        
        $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
           
            if (password_verify($password, $admin['password'])) {
                
                session_regenerate_id(true);
                
                
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['login_time'] = time();
                
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Login — Food-back Hub</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;
}


.bg-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
}

.shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.3;
    animation: float 20s infinite ease-in-out;
}

.shape-1 {
    width: 400px;
    height: 400px;
    background: #ff6b6b;
    top: -200px;
    left: -200px;
}

.shape-2 {
    width: 350px;
    height: 350px;
    background: #4ecdc4;
    bottom: -150px;
    right: -150px;
    animation-delay: 7s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(50px, 50px); }
}


.login-container {
    width: 100%;
    max-width: 450px;
    position: relative;
    z-index: 1;
    animation: slideUp 0.6s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 45px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
}


.login-header {
    text-align: center;
    margin-bottom: 35px;
}

.logo-container {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    margin: 0 auto 20px;
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.login-header h1 {
    font-size: 28px;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 8px;
}

.login-header p {
    color: #6b7280;
    font-size: 14px;
}


.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    opacity: 0.5;
}

.form-group input {
    width: 100%;
    padding: 15px 15px 15px 48px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 15px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus {
    outline: none;
    border-color: #ff6b6b;
    box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.1);
}


.error-msg {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    border: 2px solid #ef4444;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}


.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
}

.remember-me input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.remember-me label {
    font-size: 14px;
    color: #6b7280;
    cursor: pointer;
}


.submit-btn {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    border: none;
    padding: 16px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
    transition: all 0.3s ease;
    margin-top: 10px;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(255, 107, 107, 0.5);
}

.submit-btn:active {
    transform: translateY(0);
}


.back-link {
    text-align: center;
    margin-top: 25px;
}

.back-link a {
    color: #6b7280;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: color 0.2s ease;
}

.back-link a:hover {
    color: #ff6b6b;
}


.security-notice {
    text-align: center;
    margin-top: 20px;
    padding: 15px;
    background: rgba(16, 185, 129, 0.1);
    border-radius: 10px;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.security-notice p {
    font-size: 12px;
    color: #065f46;
    font-weight: 500;
}


@media (max-width: 480px) {
    .login-card {
        padding: 30px 25px;
    }

    .login-header h1 {
        font-size: 24px;
    }
}
</style>
</head>

<body>


<div class="bg-animation">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
</div>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">🔐</div>
            <h1>Admin Login</h1>
            <p>Food-back Hub Management Portal</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="error-msg">
            <span>⚠️</span>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label>👤 Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Enter your username" 
                        required 
                        autocomplete="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>🔒 Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        required 
                        autocomplete="current-password">
                </div>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me on this device</label>
            </div>

            <button type="submit" class="submit-btn">
                <span>Login to Dashboard</span>
                <span>→</span>
            </button>
        </form>

        <div class="back-link">
            <a href="../index.html">← Back to Main Site</a>
        </div>

        <div class="security-notice">
            <p>🔒 Secure connection • All data is encrypted</p>
        </div>
    </div>
</div>

</body>
</html>

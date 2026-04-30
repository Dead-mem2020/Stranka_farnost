<?php
session_start();

// If already logged in, redirect to admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Admin credentials (change these for security)
    $adminUser = 'admin';
    $adminPass = 'farnost2026'; // Change this password!
    
    if ($username === $adminUser && $password === $adminPass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = 'Administrátor';
        header("Location: admin.php");
        exit;
    } else {
        $error = 'Nesprávné uživatelské jméno nebo heslo.';
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Přihlášení - Farnost Přeštice</title>
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .login-container h1 {
            color: #6F7D5C;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #6F7D5C;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background: #fadbd8;
            border-radius: 5px;
        }
        .login-btn {
            width: 100%;
            padding: 0.8rem;
            background: #6F7D5C;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #758268;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #6F7D5C;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Přihlášení</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Uživatelské jméno:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Přihlásit</button>
        </form>
        <a href="index.php" class="back-link">← Zpět na hlavní stránku</a>
    </div>
</body>
</html>
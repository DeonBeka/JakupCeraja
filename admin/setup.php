<?php
/*
 * First-time setup: creates the first admin account.
 * Only works while there is NO admin in the database.
 */
require_once __DIR__ . '/../includes/database.php';
jc_session_start();

$count = (int) $conn->query('SELECT COUNT(*) AS c FROM admins')->fetch_assoc()['c'];
if ($count > 0) {
    header('Location: login.php');
    exit;
}

$error = '';
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if ($fullname === '' || $username === '') {
        $error = 'Plotësoni emrin dhe username.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password duhet të ketë të paktën 8 karaktere.';
    } elseif ($pass !== $pass2) {
        $error = 'Password-et nuk përputhen.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $st = $conn->prepare('INSERT INTO admins (fullname, username, password) VALUES (?, ?, ?)');
        $st->bind_param('sss', $fullname, $username, $hash);
        if ($st->execute()) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $conn->insert_id;
            $_SESSION['admin_fullname'] = $fullname;
            $_SESSION['admin_username'] = $username;
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Llogaria nuk u krijua. Provoni përsëri.';
    }
}
$h = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krijo llogarinë e adminit | Jakup Ceraja</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap">
    <style>
        *{ box-sizing:border-box; margin:0; padding:0; }
        body{ min-height:100vh; display:grid; place-items:center; padding:20px; font-family:Inter, sans-serif;
              background:radial-gradient(900px 400px at 80% 0%, rgba(31,77,58,.10), transparent 60%), #FBF8F3; color:#1d2a24; }
        .card{ width:100%; max-width:430px; background:#fff; border:1px solid rgba(31,77,58,.12); border-radius:16px; padding:34px 28px;
               box-shadow:0 26px 50px -18px rgba(22,58,44,.28); }
        img{ display:block; height:70px; margin:0 auto 14px; }
        h1{ font-family:'Playfair Display', serif; color:#1F4D3A; text-align:center; font-size:1.6rem; }
        p.sub{ text-align:center; color:#5b6660; font-size:.9rem; margin:8px 0 22px; line-height:1.6; }
        label{ display:block; font-size:.85rem; font-weight:600; color:#47514c; margin-bottom:14px; }
        input{ width:100%; margin-top:6px; padding:12px 14px; border:1px solid #d9ded9; border-radius:10px; font:inherit; background:#FBF8F3; }
        input:focus{ outline:0; border-color:#1F4D3A; background:#fff; box-shadow:0 0 0 3px rgba(31,77,58,.12); }
        button{ width:100%; padding:14px; border:0; border-radius:999px; background:#1F4D3A; color:#fff; font:600 1rem Inter, sans-serif; cursor:pointer; margin-top:6px; }
        button:hover{ background:#7A1F2B; }
        .error{ background:#f8e9eb; color:#7A1F2B; padding:12px 14px; border-radius:10px; font-size:.9rem; margin-bottom:16px; }
    </style>
</head>
<body>
    <form class="card" method="post">
        <img src="../assets/images/logo/logo.png" alt="Jakup Ceraja">
        <h1>Mirë se vini!</h1>
        <p class="sub">Krijoni llogarinë e administratorit. Kjo faqe shfaqet vetëm një herë.</p>
        <?php if ($error): ?><div class="error"><?php echo $h($error); ?></div><?php endif; ?>
        <label>Emri i plotë<input name="fullname" required value="<?php echo $h($fullname); ?>"></label>
        <label>Username<input name="username" required autocomplete="username" value="<?php echo $h($username); ?>"></label>
        <label>Password (min. 8 karaktere)<input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
        <label>Përsërit password-in<input type="password" name="password2" required minlength="8" autocomplete="new-password"></label>
        <button type="submit">Krijo llogarinë</button>
    </form>
</body>
</html>

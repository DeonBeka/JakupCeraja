<?php
/* Adds a book to the cart, then goes back to the page the visitor came from. */
require_once 'includes/database.php';
jc_session_start();

$book_id = (int) ($_POST['book_id'] ?? $_GET['id'] ?? 0);
$qty     = max(1, min(20, (int) ($_POST['qty'] ?? 1)));
$go      = $_POST['go'] ?? $_GET['go'] ?? 'back';

$ok = false;
if ($book_id > 0) {
    $st = $conn->prepare('SELECT id, title FROM books WHERE id = ? LIMIT 1');
    $st->bind_param('i', $book_id);
    $st->execute();
    $book = $st->get_result()->fetch_assoc();
    if ($book) {
        jc_cart();
        $current = (int) ($_SESSION['cart'][$book_id] ?? 0);
        $_SESSION['cart'][$book_id] = min(20, $current + $qty);
        $_SESSION['cart_flash'] = '"' . $book['title'] . '" u shtua në shportë.';
        $ok = true;
    }
}

if ($go === 'cart' || !$ok) {
    header('Location: cart.php');
    exit;
}

$back = $_SERVER['HTTP_REFERER'] ?? 'books.php';
$host = parse_url($back, PHP_URL_HOST);
if ($host && $host !== ($_SERVER['HTTP_HOST'] ?? '') && $host !== parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST)) {
    $back = 'books.php';
}
header('Location: ' . $back);
exit;

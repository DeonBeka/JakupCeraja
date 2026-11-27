<?php
/* "Bli librin" – put the book in the cart and go straight to checkout. */
require_once 'includes/database.php';
jc_session_start();

$book_id = (int) ($_GET['id'] ?? 0);

if ($book_id > 0) {
    $st = $conn->prepare('SELECT id FROM books WHERE id = ? LIMIT 1');
    $st->bind_param('i', $book_id);
    $st->execute();
    if ($st->get_result()->fetch_assoc()) {
        jc_cart();
        if (empty($_SESSION['cart'][$book_id])) {
            $_SESSION['cart'][$book_id] = 1;
        }
    }
}

header('Location: cart.php#checkout');
exit;

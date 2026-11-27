<?php
http_response_code(404);
$jc_force_root_base = true;
require_once __DIR__ . '/includes/database.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="cart-page">
    <section class="cart-section" style="padding-top:150px;">
        <div class="container">
            <div class="cart-empty">
                <i class="fa-solid fa-book-open"></i>
                <h2>Faqja nuk u gjet</h2>
                <p>Na vjen keq, faqja që kërkoni nuk ekziston ose është zhvendosur.</p>
                <a href="index.php" class="btn btn-primary">Kthehu në ballinë <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

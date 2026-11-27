<?php
require_once 'includes/database.php';
jc_session_start();
jc_cart();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$errors  = [];
$form    = ['fullname' => '', 'email' => '', 'phone' => '', 'country' => 'Kosovë', 'city' => '', 'address' => '', 'note' => ''];
$success = $_SESSION['order_success'] ?? null;
unset($_SESSION['order_success']);

/* ---------- actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $errors[] = 'Sesioni skadoi. Ju lutem provoni përsëri.';
    } elseif ($action === 'update') {
        foreach ((array) ($_POST['qty'] ?? []) as $id => $q) {
            $id = (int) $id; $q = (int) $q;
            if ($q <= 0) unset($_SESSION['cart'][$id]);
            else $_SESSION['cart'][$id] = min(20, $q);
        }
        header('Location: cart.php'); exit;
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][(int) ($_POST['id'] ?? 0)]);
        header('Location: cart.php'); exit;
    } elseif ($action === 'checkout') {
        foreach ($form as $k => $_) {
            $form[$k] = trim((string) ($_POST[$k] ?? ''));
        }
        if ($form['fullname'] === '') $errors[] = 'Shkruani emrin dhe mbiemrin.';
        if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Shkruani një email të vlefshëm.';
        if ($form['phone'] === '') $errors[] = 'Shkruani numrin e telefonit.';
        if ($form['city'] === '') $errors[] = 'Shkruani qytetin.';
        if ($form['address'] === '') $errors[] = 'Shkruani adresën.';
        if (empty($_SESSION['cart'])) $errors[] = 'Shporta është bosh.';
    }
}

/* ---------- load cart books ---------- */
$items = [];
$subtotal = 0.0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $res = $conn->query(
        "SELECT books.id, books.title, books.price, books.cover, authors.fullname AS author_name
         FROM books LEFT JOIN authors ON authors.id = books.author_id
         WHERE books.id IN ($ids)"
    );
    while ($res && $row = $res->fetch_assoc()) {
        $row['qty'] = (int) $_SESSION['cart'][$row['id']];
        $row['line'] = $row['qty'] * (float) $row['price'];
        $subtotal += $row['line'];
        $items[] = $row;
    }
    // drop books that were deleted meanwhile
    $found = array_column($items, 'id');
    foreach (array_keys($_SESSION['cart']) as $id) {
        if (!in_array($id, $found)) unset($_SESSION['cart'][$id]);
    }
}
$shipping = ($subtotal >= JC_FREE_SHIPPING_FROM || $subtotal == 0) ? 0.0 : JC_SHIPPING_PRICE;
$total    = $subtotal + $shipping;

/* ---------- place order ---------- */
if (($_POST['action'] ?? '') === 'checkout' && !$errors && $items) {
    $code = 'JC-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    $conn->begin_transaction();
    try {
        $st = $conn->prepare(
            'INSERT INTO orders (order_code, fullname, email, phone, country, city, address, note, subtotal, shipping_price, total_price, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "new")'
        );
        $st->bind_param('ssssssssddd', $code, $form['fullname'], $form['email'], $form['phone'],
            $form['country'], $form['city'], $form['address'], $form['note'], $subtotal, $shipping, $total);
        if (!$st->execute()) throw new Exception($st->error);
        $order_id = $conn->insert_id;

        $it = $conn->prepare(
            'INSERT INTO order_items (order_id, book_id, book_title, book_cover, unit_price, quantity) VALUES (?, ?, ?, ?, ?, ?)'
        );
        foreach ($items as $i) {
            $price = (float) $i['price'];
            $it->bind_param('iissdi', $order_id, $i['id'], $i['title'], $i['cover'], $price, $i['qty']);
            if (!$it->execute()) throw new Exception($it->error);
        }
        $conn->commit();

        $_SESSION['cart'] = [];
        $_SESSION['order_success'] = ['code' => $code, 'total' => $total, 'name' => $form['fullname']];
        header('Location: cart.php'); exit;
    } catch (Throwable $e) {
        $conn->rollback();
        $errors[] = 'Porosia nuk u ruajt. Ju lutem provoni përsëri.';
    }
}

$flash = $_SESSION['cart_flash'] ?? '';
unset($_SESSION['cart_flash']);

include 'includes/header.php';
include 'includes/navbar.php';
$h = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>

<main class="cart-page">
    <section class="cart-hero">
        <div class="container">
            <span class="section-tag">Porosia juaj</span>
            <h1>Shporta</h1>
        </div>
    </section>

    <section class="cart-section">
        <div class="container">

        <?php if ($success): ?>
            <div class="cart-success">
                <div class="cart-success-icon"><i class="fa-solid fa-check"></i></div>
                <h2>Faleminderit, <?php echo $h($success['name']); ?>!</h2>
                <p>Porosia juaj u pranua. Numri i porosisë:</p>
                <div class="cart-order-code"><?php echo $h($success['code']); ?></div>
                <p>Totali: <strong><?php echo jc_money($success['total']); ?></strong>. Pagesa bëhet me para në dorë kur ta pranoni librin.
                   Do t'ju kontaktojmë së shpejti për konfirmim.</p>
                <a href="books.php" class="btn btn-primary">Vazhdo me librat <i class="fa-solid fa-arrow-right"></i></a>
            </div>

        <?php elseif (!$items): ?>
            <div class="cart-empty">
                <i class="fa-solid fa-bag-shopping"></i>
                <h2>Shporta është bosh</h2>
                <p>Shfletoni librat tanë dhe shtoni ata që ju pëlqejnë.</p>
                <a href="books.php" class="btn btn-primary">Shfleto librat <i class="fa-solid fa-arrow-right"></i></a>
            </div>

        <?php else: ?>

            <?php if ($flash): ?><div class="cart-alert cart-alert-ok"><i class="fa-solid fa-circle-check"></i> <?php echo $h($flash); ?></div><?php endif; ?>
            <?php if ($errors): ?>
                <div class="cart-alert cart-alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div><?php foreach ($errors as $e) echo '<div>' . $h($e) . '</div>'; ?></div>
                </div>
            <?php endif; ?>

            <div class="cart-layout">

                <div class="cart-items-col">
                    <form method="post" class="cart-items">
                        <input type="hidden" name="csrf" value="<?php echo $h($_SESSION['csrf']); ?>">
                        <input type="hidden" name="action" value="update">
                        <?php foreach ($items as $i): ?>
                            <div class="cart-item">
                                <a href="book-details.php?id=<?php echo (int) $i['id']; ?>" class="cart-item-cover">
                                    <?php if ($i['cover']): ?>
                                        <img src="<?php echo $h($i['cover']); ?>" alt="<?php echo $h($i['title']); ?>">
                                    <?php else: ?>
                                        <i class="fa-solid fa-book"></i>
                                    <?php endif; ?>
                                </a>
                                <div class="cart-item-info">
                                    <h3><a href="book-details.php?id=<?php echo (int) $i['id']; ?>"><?php echo $h($i['title']); ?></a></h3>
                                    <p><?php echo $h($i['author_name'] ?? ''); ?></p>
                                    <span class="cart-item-price"><?php echo jc_money($i['price']); ?></span>
                                </div>
                                <div class="cart-item-qty">
                                    <button type="button" class="qty-btn" data-step="-1" aria-label="Më pak">−</button>
                                    <input type="number" name="qty[<?php echo (int) $i['id']; ?>]" value="<?php echo (int) $i['qty']; ?>" min="0" max="20" inputmode="numeric">
                                    <button type="button" class="qty-btn" data-step="1" aria-label="Më shumë">+</button>
                                </div>
                                <div class="cart-item-total"><?php echo jc_money($i['line']); ?></div>
                                <button type="submit" form="remove-<?php echo (int) $i['id']; ?>" class="cart-item-remove" aria-label="Hiq nga shporta">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                        <div class="cart-items-footer">
                            <a href="books.php" class="cart-continue"><i class="fa-solid fa-arrow-left"></i> Vazhdo blerjen</a>
                            <button type="submit" class="cart-update-btn"><i class="fa-solid fa-rotate"></i> Përditëso shportën</button>
                        </div>
                    </form>
                    <?php foreach ($items as $i): ?>
                        <form method="post" id="remove-<?php echo (int) $i['id']; ?>" hidden>
                            <input type="hidden" name="csrf" value="<?php echo $h($_SESSION['csrf']); ?>">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="id" value="<?php echo (int) $i['id']; ?>">
                        </form>
                    <?php endforeach; ?>

                    <form method="post" class="checkout-form" id="checkout">
                        <input type="hidden" name="csrf" value="<?php echo $h($_SESSION['csrf']); ?>">
                        <input type="hidden" name="action" value="checkout">
                        <h2>Të dhënat për dërgesë</h2>
                        <div class="checkout-grid">
                            <label>Emri dhe mbiemri *<input type="text" name="fullname" required value="<?php echo $h($form['fullname']); ?>" autocomplete="name"></label>
                            <label>Telefoni *<input type="tel" name="phone" required value="<?php echo $h($form['phone']); ?>" autocomplete="tel"></label>
                            <label class="span-2">Email *<input type="email" name="email" required value="<?php echo $h($form['email']); ?>" autocomplete="email"></label>
                            <label>Shteti<select name="country">
                                <?php foreach (['Kosovë', 'Shqipëri', 'Maqedoni e Veriut', 'Mal i Zi', 'Tjetër'] as $c): ?>
                                    <option<?php echo $form['country'] === $c ? ' selected' : ''; ?>><?php echo $c; ?></option>
                                <?php endforeach; ?>
                            </select></label>
                            <label>Qyteti *<input type="text" name="city" required value="<?php echo $h($form['city']); ?>" autocomplete="address-level2"></label>
                            <label class="span-2">Adresa *<input type="text" name="address" required value="<?php echo $h($form['address']); ?>" autocomplete="street-address"></label>
                            <label class="span-2">Shënim (opsional)<textarea name="note" rows="3"><?php echo $h($form['note']); ?></textarea></label>
                        </div>
                        <div class="checkout-pay">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <div><strong>Pagesa në dorëzim</strong><span>Paguani me para në dorë kur ta pranoni porosinë.</span></div>
                        </div>
                        <button type="submit" class="btn btn-primary checkout-submit">Përfundo porosinë · <?php echo jc_money($total); ?></button>
                    </form>
                </div>

                <aside class="cart-summary">
                    <h2>Përmbledhja</h2>
                    <div class="summary-row"><span>Librat (<?php echo array_sum(array_column($items, 'qty')); ?>)</span><span><?php echo jc_money($subtotal); ?></span></div>
                    <div class="summary-row"><span>Dërgesa</span><span><?php echo $shipping > 0 ? jc_money($shipping) : 'Falas'; ?></span></div>
                    <?php if ($shipping > 0): ?>
                        <p class="summary-hint">Dërgesë falas për porosi mbi <?php echo jc_money(JC_FREE_SHIPPING_FROM); ?>.</p>
                    <?php endif; ?>
                    <div class="summary-row summary-total"><span>Totali</span><span><?php echo jc_money($total); ?></span></div>
                    <a href="#checkout" class="btn btn-primary summary-cta">Vazhdo te porosia</a>
                </aside>

            </div>
        <?php endif; ?>

        </div>
    </section>
</main>

<script>
document.querySelectorAll('.qty-btn').forEach(function (b) {
    b.addEventListener('click', function () {
        var input = b.parentNode.querySelector('input');
        var v = Math.max(0, Math.min(20, (parseInt(input.value, 10) || 0) + parseInt(b.dataset.step, 10)));
        input.value = v;
        b.closest('form').submit();
    });
});
</script>

<?php include 'includes/footer.php'; ?>

<?php
/*
 * Database connection + shared helpers.
 *
 * Locally (XAMPP) it uses the defaults below.
 * On Vercel set these Environment Variables in the project settings:
 *   DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME, DB_SSL=1
 *   BLOB_READ_WRITE_TOKEN  (added automatically when a Blob store is connected)
 *   BLOB_BASE_URL          (e.g. https://xxxx.public.blob.vercel-storage.com)
 */

if (defined('JC_BOOTSTRAPPED')) {
    return;
}
define('JC_BOOTSTRAPPED', true);
ob_start();
define('JC_ROOT', dirname(__DIR__));

function jc_env($key, $default = '')
{
    $v = getenv($key);
    return ($v === false || $v === '') ? $default : $v;
}

define('JC_ON_VERCEL', jc_env('VERCEL') !== '');

if (JC_ON_VERCEL) {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
}

$host     = jc_env('DB_HOST', 'localhost');
$port     = (int) jc_env('DB_PORT', '3306');
$username = jc_env('DB_USER', 'root');
$password = jc_env('DB_PASS', '');
$database = jc_env('DB_NAME', 'jakup_ceraja');
$use_ssl  = jc_env('DB_SSL', '0') === '1';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();

$flags = 0;
if ($use_ssl) {
    $ca = null;
    foreach ([
        '/etc/pki/tls/certs/ca-bundle.crt',
        '/etc/ssl/certs/ca-certificates.crt',
        '/etc/ssl/cert.pem',
    ] as $candidate) {
        if (@is_file($candidate)) { $ca = $candidate; break; }
    }
    $conn->ssl_set(null, null, $ca, null, null);
    $flags = MYSQLI_CLIENT_SSL;
    if ($ca === null) {
        $flags |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
    }
}

if (!@$conn->real_connect($host, $username, $password, $database, $port, null, $flags)) {
    http_response_code(500);
    die('Lidhja me databazën dështoi. Ju lutem provoni përsëri më vonë.');
}

$conn->set_charset('utf8mb4');

/* ---------------------------------------------------------------
 * Sessions stored in the database (Vercel servers don't share files)
 * ------------------------------------------------------------- */

class JcDbSessionHandler implements SessionHandlerInterface
{
    private $db;
    public function __construct($db) { $this->db = $db; }
    public function open($path, $name): bool { return true; }
    public function close(): bool { return true; }
    public function read($id): string|false
    {
        $st = $this->db->prepare('SELECT data FROM php_sessions WHERE id = ?');
        $st->bind_param('s', $id);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        return $row ? $row['data'] : '';
    }
    public function write($id, $data): bool
    {
        $now = time();
        $st = $this->db->prepare(
            'REPLACE INTO php_sessions (id, data, updated_at) VALUES (?, ?, ?)'
        );
        $st->bind_param('ssi', $id, $data, $now);
        return $st->execute();
    }
    public function destroy($id): bool
    {
        $st = $this->db->prepare('DELETE FROM php_sessions WHERE id = ?');
        $st->bind_param('s', $id);
        return $st->execute();
    }
    public function gc($max): int|false
    {
        $limit = time() - $max;
        $st = $this->db->prepare('DELETE FROM php_sessions WHERE updated_at < ?');
        $st->bind_param('i', $limit);
        $st->execute();
        return $st->affected_rows;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_save_handler(new JcDbSessionHandler($conn), true);
    ini_set('session.gc_maxlifetime', '86400');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => JC_ON_VERCEL,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function jc_session_start()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/* ---------------------------------------------------------------
 * File uploads: local disk with XAMPP, Vercel Blob on Vercel
 * ------------------------------------------------------------- */

/** Turn a path like "../assets/uploads/x.jpg" into "assets/uploads/x.jpg". */
function jc_relative_path($target)
{
    $abs = $target;
    if (!preg_match('~^([a-zA-Z]:)?[/\\\\]~', $target)) {
        $abs = getcwd() . '/' . $target;
    }
    $parts = [];
    foreach (preg_split('~[/\\\\]+~', $abs) as $p) {
        if ($p === '' || $p === '.') continue;
        if ($p === '..') { array_pop($parts); continue; }
        $parts[] = $p;
    }
    $abs  = implode('/', $parts);
    $root = trim(str_replace('\\', '/', JC_ROOT), '/');
    if (stripos($abs, $root . '/') === 0) {
        return substr($abs, strlen($root) + 1);
    }
    return ltrim($target, './');
}

function jc_blob_enabled()
{
    return jc_env('BLOB_READ_WRITE_TOKEN') !== '';
}

function jc_move_uploaded_file($tmp, $target)
{
    if (!jc_blob_enabled()) {
        $dir = dirname($target);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return move_uploaded_file($tmp, $target);
    }

    if (!is_uploaded_file($tmp)) {
        return false;
    }

    $path = jc_relative_path($target);
    $mime = function_exists('mime_content_type') ? (mime_content_type($tmp) ?: 'application/octet-stream') : 'application/octet-stream';

    $ch = curl_init('https://blob.vercel-storage.com/' . str_replace('%2F', '/', rawurlencode($path)));
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_POSTFIELDS     => file_get_contents($tmp),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . jc_env('BLOB_READ_WRITE_TOKEN'),
            'x-api-version: 7',
            'x-add-random-suffix: 0',
            'x-allow-overwrite: 1',
            'x-content-type: ' . $mime,
        ],
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $code >= 200 && $code < 300;
}

function jc_unlink($target)
{
    if (!jc_blob_enabled()) {
        return @unlink($target);
    }
    $base = rtrim(jc_env('BLOB_BASE_URL'), '/');
    if ($base === '') {
        return false;
    }
    $url = $base . '/' . jc_relative_path($target);
    $ch = curl_init('https://blob.vercel-storage.com/delete');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['urls' => [$url]]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . jc_env('BLOB_READ_WRITE_TOKEN'),
            'x-api-version: 7',
            'Content-Type: application/json',
        ],
    ]);
    curl_exec($ch);
    curl_close($ch);
    return true;
}

/* ---------------------------------------------------------------
 * Shopping cart (stored in the session)
 * ------------------------------------------------------------- */

define('JC_SHIPPING_PRICE', 2.00);       // € per order
define('JC_FREE_SHIPPING_FROM', 30.00);  // free shipping from this subtotal

function jc_cart()
{
    jc_session_start();
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function jc_cart_count()
{
    return array_sum(jc_cart());
}

function jc_money($v)
{
    return number_format((float) $v, 2, ',', '.') . ' €';
}

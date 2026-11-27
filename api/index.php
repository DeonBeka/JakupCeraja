<?php
/*
 * Vercel entry point: every .php request is routed here (see vercel.json)
 * and this file runs the matching page from the project root.
 */

$jc_root = dirname(__DIR__);

$jc_path = $_GET['__path'] ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
unset($_GET['__path']);
$_SERVER['QUERY_STRING'] = http_build_query($_GET);
$jc_path = '/' . ltrim(rawurldecode((string) $jc_path), '/');

// Uploaded images live in Vercel Blob: send the browser there.
if (preg_match('~^/assets/(uploads|images/books)/~', $jc_path)) {
    $base = rtrim((string) getenv('BLOB_BASE_URL'), '/');
    if ($base !== '') {
        header('Cache-Control: public, max-age=3600');
        header('Location: ' . $base . $jc_path, true, 302);
        exit;
    }
    http_response_code(404);
    exit('Not found');
}

if ($jc_path === '/' || $jc_path === '') {
    $jc_path = '/index.php';
} elseif ($jc_path === '/admin' || $jc_path === '/admin/') {
    $jc_path = '/admin/dashboard.php';
} elseif (substr($jc_path, -1) === '/') {
    $jc_path .= 'index.php';
}

$jc_file = realpath($jc_root . $jc_path);
$jc_rel  = $jc_file ? str_replace('\\', '/', substr($jc_file, strlen($jc_root))) : '';

$jc_blocked = '~^/(api|includes|database|vendor|\.)~';

if (
    !$jc_file ||
    strpos($jc_file, $jc_root . DIRECTORY_SEPARATOR) !== 0 ||
    substr($jc_file, -4) !== '.php' ||
    preg_match($jc_blocked, $jc_rel) ||
    !is_file($jc_file)
) {
    http_response_code(404);
    $jc_404 = $jc_root . '/404.php';
    if (is_file($jc_404)) {
        chdir($jc_root);
        $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = '/404.php';
        require $jc_404;
    } else {
        echo 'Faqja nuk u gjet.';
    }
    exit;
}

chdir(dirname($jc_file));
$_SERVER['SCRIPT_NAME']     = $jc_rel;
$_SERVER['PHP_SELF']        = $jc_rel;
$_SERVER['SCRIPT_FILENAME'] = $jc_file;
$_SERVER['DOCUMENT_ROOT']   = $jc_root;
unset($jc_root, $jc_path, $jc_rel, $jc_blocked);

require $jc_file;

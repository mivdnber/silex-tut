<?php
if (!file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    error_log('file not found, rewriting! ' . $_SERVER['REQUEST_URI'], 4);
    include __DIR__ . '/index.php';
    return true;
}
return false;

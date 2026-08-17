<?php
define('ROOT', __DIR__);
require_once ROOT . '/config.php';
session_destroy();
header('Location: ' . BASE_URL . '/index.php');
exit;

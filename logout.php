<?php
// logout.php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSecureSession();
session_destroy();
header('Location: ' . SITE_URL . '/index.php');
exit;

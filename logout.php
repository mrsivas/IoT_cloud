<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (isLoggedIn()) {

    logoutUser();

}

session_start();

setFlash(
    'success',
    'You have been logged out successfully.'
);

redirect('login.php');
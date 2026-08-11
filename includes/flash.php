<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| MCEIoT Flash Messages
|--------------------------------------------------------------------------
*/

if (!defined('APP_NAME')) {
    exit('Direct access is not allowed.');
}

/*
|--------------------------------------------------------------------------
| Set Flash Message
|--------------------------------------------------------------------------
*/

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/*
|--------------------------------------------------------------------------
| Success Message
|--------------------------------------------------------------------------
*/

function success(string $message): void
{
    setFlash('success', $message);
}

/*
|--------------------------------------------------------------------------
| Error Message
|--------------------------------------------------------------------------
*/

function error(string $message): void
{
    setFlash('danger', $message);
}

/*
|--------------------------------------------------------------------------
| Warning Message
|--------------------------------------------------------------------------
*/

function warning(string $message): void
{
    setFlash('warning', $message);
}

/*
|--------------------------------------------------------------------------
| Info Message
|--------------------------------------------------------------------------
*/

function info(string $message): void
{
    setFlash('info', $message);
}

/*
|--------------------------------------------------------------------------
| Get Flash Message
|--------------------------------------------------------------------------
*/

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];

    unset($_SESSION['flash']);

    return $flash;
}

/*
|--------------------------------------------------------------------------
| Display Flash Message
|--------------------------------------------------------------------------
*/

function displayFlash(): void
{
    $flash = getFlash();

    if ($flash === null) {
        return;
    }

    ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button"
                class="btn-close"
                data-coreui-dismiss="alert"
                aria-label="Close">
        </button>
    </div>
    <?php
}
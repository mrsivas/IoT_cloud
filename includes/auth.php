<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Authentication Helpers
|--------------------------------------------------------------------------
*/

function isLoggedIn(): bool
{
    return isset($_SESSION['user'])
        && !empty($_SESSION['user']['id']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function currentUserId(): int
{
    return (int)($_SESSION['user']['id'] ?? 0);
}

function currentUserRole(): int
{
    return (int)($_SESSION['user']['role'] ?? 0);
}

function currentUserName(): string
{
    return (string)($_SESSION['user']['full_name'] ?? '');
}

/*
|--------------------------------------------------------------------------
| Login Required
|--------------------------------------------------------------------------
*/

function requireLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    $_SESSION['flash'] = [
        'type'    => 'warning',
        'message' => 'Please login to continue.'
    ];

    header('Location: /login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Role Checking
|--------------------------------------------------------------------------
*/

function hasRole(array $roles): bool
{
    return in_array(
        currentUserRole(),
        $roles,
        true
    );
}

function requireRole(array $roles): void
{
    requireLogin();

    if (hasRole($roles)) {
        return;
    }

    http_response_code(403);

    exit('Access denied.');
}
/*
|--------------------------------------------------------------------------
| Role Helper Functions
|--------------------------------------------------------------------------
*/

function isSuperAdmin(): bool
{
    return currentUserRole() === ROLE_SUPER_ADMIN;
}

function isAdmin(): bool
{
    return currentUserRole() === ROLE_ADMIN;
}

function isDeviceOwner(): bool
{
    return currentUserRole() === ROLE_DEVICE_OWNER;
}

function isReadOnly(): bool
{
    return currentUserRole() === ROLE_READ_ONLY;
}

function canManageUsers(): bool
{
    return hasRole([
        ROLE_SUPER_ADMIN,
        ROLE_ADMIN
    ]);
}

function canManageDevices(): bool
{
    return hasRole([
        ROLE_SUPER_ADMIN,
        ROLE_ADMIN,
        ROLE_DEVICE_OWNER
    ]);
}

function canViewDevices(): bool
{
    return hasRole([
        ROLE_SUPER_ADMIN,
        ROLE_ADMIN,
        ROLE_DEVICE_OWNER,
        ROLE_READ_ONLY
    ]);
}

/*
|--------------------------------------------------------------------------
| Login User
|--------------------------------------------------------------------------
*/

function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['last_activity'] = time();

    $_SESSION['user'] = [
        'id'        => (int)$user['id'],
        'full_name' => $user['full_name'],
        'email'     => $user['email'],
        'role'      => (int)$user['role']
    ];

    $_SESSION['last_activity'] = time();
}

/*
|--------------------------------------------------------------------------
| Logout User
|--------------------------------------------------------------------------
*/

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool)$params['secure'],
            (bool)$params['httponly']
        );

    }

    session_destroy();
}
/*
|--------------------------------------------------------------------------
| Password Verification
|--------------------------------------------------------------------------
*/

function verifyUserCredentials(
    PDO $pdo,
    string $email,
    string $password
): ?array {

    $stmt = $pdo->prepare("
        SELECT
            id,
            full_name,
            email,
            password,
            role,
            status,
            failed_attempts,
            locked_until
        FROM users
        WHERE
            email = ?
            AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {

        return null;

    }

    if ((int)$user['status'] !== STATUS_ACTIVE) {

        return null;

    }

    if (
        !empty($user['locked_until'])
        &&
        strtotime($user['locked_until']) > time()
    ) {

        return null;

    }

    if (!password_verify($password, $user['password'])) {

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                failed_attempts = failed_attempts + 1,
                updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $user['id']
        ]);

        return null;

    }

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            failed_attempts = 0,
            locked_until = NULL,
            last_login = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $user['id']
    ]);

    unset($user['password']);

    return $user;

}
/*
|--------------------------------------------------------------------------
| Change Password
|--------------------------------------------------------------------------
*/

function changeUserPassword(
    PDO $pdo,
    int $userId,
    string $currentPassword,
    string $newPassword
): bool {

    $stmt = $pdo->prepare("
        SELECT password
        FROM users
        WHERE
            id = ?
            AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([$userId]);

    $hash = $stmt->fetchColumn();

    if (!$hash) {

        return false;

    }

    if (!password_verify($currentPassword, (string)$hash)) {

        return false;

    }

    $newHash = password_hash(
        $newPassword,
        PASSWORD_DEFAULT
    );

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            password = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    return $stmt->execute([
        $newHash,
        $userId
    ]);

}

/*
|--------------------------------------------------------------------------
| Session Refresh
|--------------------------------------------------------------------------
*/

function refreshCurrentUser(
    PDO $pdo
): void {

    if (!isLoggedIn()) {

        return;

    }

    $stmt = $pdo->prepare("
        SELECT
            id,
            full_name,
            email,
            role
        FROM users
        WHERE
            id = ?
            AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([
        currentUserId()
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {

        logoutUser();

        header('Location: /login.php');

        exit;

    }

    loginUser($user);

}
/*
|--------------------------------------------------------------------------
| Account Lock Helpers
|--------------------------------------------------------------------------
*/

function incrementFailedLogin(
    PDO $pdo,
    int $userId,
    int $maxAttempts = 5,
    int $lockMinutes = 15
): void {

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            failed_attempts = failed_attempts + 1,
            locked_until =
                CASE
                    WHEN failed_attempts + 1 >= ?
                    THEN DATE_ADD(NOW(), INTERVAL ? MINUTE)
                    ELSE locked_until
                END,
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $maxAttempts,
        $lockMinutes,
        $userId
    ]);

}

function unlockUserAccount(
    PDO $pdo,
    int $userId
): void {

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            failed_attempts = 0,
            locked_until = NULL,
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $userId
    ]);

}

/*
|--------------------------------------------------------------------------
| Permission Helpers
|--------------------------------------------------------------------------
*/

function canEditUser(int $userId): bool
{
    if (isSuperAdmin()) {
        return true;
    }

    if (isAdmin()) {
        return $userId !== currentUserId();
    }

    return false;
}

function canEditDevice(int $ownerId): bool
{
    if (isSuperAdmin() || isAdmin()) {
        return true;
    }

    if (isDeviceOwner()) {
        return currentUserId() === $ownerId;
    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Authorization Helpers
|--------------------------------------------------------------------------
*/

function canDeleteUser(int $userId): bool
{
    if (!canManageUsers()) {
        return false;
    }

    return $userId !== currentUserId();
}

function canDeleteDevice(int $ownerId): bool
{
    return canEditDevice($ownerId);
}

function ensurePermission(bool $allowed): void
{
    if ($allowed) {
        return;
    }

    http_response_code(403);

    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'You do not have permission to perform this action.'
    ];

    header('Location: /index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| End of File
|--------------------------------------------------------------------------
*/
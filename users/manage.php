<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

requireLogin();
requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

$action = clean((string)get('action', post('action', '')));

switch ($action) {

    /*
    |--------------------------------------------------------------------------
    | Save User
    |--------------------------------------------------------------------------
    */

    case 'save':

        clearErrors();

        $id        = toInt(post('id'));
        $fullName  = clean((string)post('full_name'));
        $email     = strtolower(clean((string)post('email')));
        $password  = (string)post('password');
        $role      = toInt(post('role'));
        $status    = toInt(post('status'));

        required(
            'full_name',
            $fullName,
            'Full Name'
        );

        required(
            'email',
            $email,
            'Email'
        );

        email(
            'email',
            $email,
            'Email'
        );

        if ($id === 0) {

            required(
                'password',
                $password,
                'Password'
            );

            minLength(
                'password',
                $password,
                PASSWORD_MIN_LENGTH,
                'Password'
            );

        } elseif ($password !== '') {

            minLength(
                'password',
                $password,
                PASSWORD_MIN_LENGTH,
                'Password'
            );

        }

        $allowedRoles = [

            ROLE_ADMIN,
            ROLE_DEVICE_OWNER,
            ROLE_READ_ONLY

        ];


        if (isSuperAdmin()) {

            $allowedRoles[] = ROLE_SUPER_ADMIN;

        }


        inArray(
            'role',
            $role,
            $allowedRoles,
            'Role'
        );

        inArray(
            'status',
            $status,
            [
                STATUS_ACTIVE,
                STATUS_INACTIVE
            ],
            'Status'
        );

        if ($id === 0) {

            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM users
                WHERE
                    email = ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([$email]);

        } else {

            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM users
                WHERE
                    email = ?
                    AND id <> ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([
                $email,
                $id
            ]);

        }

        if ((int)$stmt->fetchColumn() > 0) {

            addError(
                'email',
                'Email address already exists.'
            );

        }

        if (hasErrors()) {

            $_SESSION['errors'] = getErrors();

            redirect(
                $id > 0
                    ? 'index.php?id=' . $id
                    : 'index.php'
            );

        }

        $pdo->beginTransaction();

        try {
                        if ($id === 0) {

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare("
                    INSERT INTO users
                    (
                        full_name,
                        email,
                        password,
                        role,
                        status,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        NOW(),
                        NOW()
                    )
                ");

                $stmt->execute([
                    $fullName,
                    $email,
                    $passwordHash,
                    $role,
                    $status
                ]);

                setFlash(
                    'success',
                    'User created successfully.'
                );

            } else {

                if ($password !== '') {

                    $passwordHash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $stmt = $pdo->prepare("
                        UPDATE users
                        SET
                            full_name = ?,
                            email = ?,
                            password = ?,
                            role = ?,
                            status = ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ");

                    $stmt->execute([
                        $fullName,
                        $email,
                        $passwordHash,
                        $role,
                        $status,
                        $id
                    ]);

                } else {

                    $stmt = $pdo->prepare("
                        UPDATE users
                        SET
                            full_name = ?,
                            email = ?,
                            role = ?,
                            status = ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ");

                    $stmt->execute([
                        $fullName,
                        $email,
                        $role,
                        $status,
                        $id
                    ]);

                }

                setFlash(
                    'success',
                    'User updated successfully.'
                );

            }

            $pdo->commit();

        } catch (Throwable $e) {

            $pdo->rollBack();

            setFlash(
                'danger',
                'Unable to save user.'
            );

        }

        redirect('index.php');

        break;

    /*
    |--------------------------------------------------------------------------
    | Delete User
    |--------------------------------------------------------------------------
    */

    case 'delete':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid user selected.'
            );

            redirect('index.php');

        }

        $stmt = $pdo->prepare("
            SELECT role
            FROM users
            WHERE id = ?
        ");

        $stmt->execute([
            $id
        ]);

        $targetRole = (int)$stmt->fetchColumn();


        if (
            $targetRole === ROLE_SUPER_ADMIN
            &&
            !isSuperAdmin()
        ) {

            setFlash(
                'danger',
                'Only Super Admin can delete Super Admin accounts.'
            );

            redirect('index.php');

        }


        if ($id === currentUserId()) {

            setFlash(
                'warning',
                'You cannot delete your own account.'
            );

            redirect('index.php');

        }

        $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE
                id = ?
                AND deleted_at IS NULL
        ");

        $stmt->execute([$id]);

        if (!$stmt->fetch()) {

            setFlash(
                'danger',
                'User not found.'
            );

            redirect('index.php');

        }
                $pdo->beginTransaction();

        try {

            $stmt = $pdo->prepare("
                UPDATE users
                SET
                    deleted_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([$id]);

            $pdo->commit();

            setFlash(
                'success',
                'User deleted successfully.'
            );

        } catch (Throwable $e) {

            $pdo->rollBack();

            setFlash(
                'danger',
                'Unable to delete user.'
            );

        }

        redirect('index.php');

        break;

    /*
    |--------------------------------------------------------------------------
    | Activate User
    |--------------------------------------------------------------------------
    */

    case 'activate':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid user selected.'
            );

            redirect('index.php');

        }

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                status = ?,
                updated_at = NOW()
            WHERE
                id = ?
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            STATUS_ACTIVE,
            $id
        ]);

        setFlash(
            'success',
            'User activated successfully.'
        );

        redirect('index.php');

        break;

    /*
    |--------------------------------------------------------------------------
    | Deactivate User
    |--------------------------------------------------------------------------
    */

    case 'deactivate':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid user selected.'
            );

            redirect('index.php');

        }

        if ($id === (int)$_SESSION['user']['id']) {

            setFlash(
                'warning',
                'You cannot deactivate your own account.'
            );

            redirect('index.php');

        }

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                status = ?,
                updated_at = NOW()
            WHERE
                id = ?
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            STATUS_INACTIVE,
            $id
        ]);

        setFlash(
            'success',
            'User deactivated successfully.'
        );

        redirect('index.php');

        break;

            /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    case 'reset-password':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid user selected.'
            );

            redirect('index.php');

        }

        $temporaryPassword = generateRandomString(10);

        $passwordHash = password_hash(
            $temporaryPassword,
            PASSWORD_DEFAULT
        );

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                password = ?,
                updated_at = NOW()
            WHERE
                id = ?
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            $passwordHash,
            $id
        ]);

        $_SESSION['temporary_password'] = [
            'user_id'  => $id,
            'password' => $temporaryPassword
        ];

        setFlash(
            'success',
            'Password has been reset successfully.'
        );

        redirect('index.php');

        break;

    /*
    |--------------------------------------------------------------------------
    | Unlock User
    |--------------------------------------------------------------------------
    */

    case 'unlock':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid user selected.'
            );

            redirect('index.php');

        }

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                failed_attempts = 0,
                locked_until = NULL,
                updated_at = NOW()
            WHERE
                id = ?
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            $id
        ]);

        setFlash(
            'success',
            'User account unlocked successfully.'
        );

        redirect('index.php');

        break;

    /*
    |--------------------------------------------------------------------------
    | Default
    |--------------------------------------------------------------------------
    */

    default:

        setFlash(
            'danger',
            'Invalid request.'
        );

        redirect('index.php');

        break;

}
/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
|
| These helper functions are local to this controller.
| If your project already defines them globally,
| you can safely omit this section.
|
*/

if (!function_exists('generateRandomString')) {

    /**
     * Generate a cryptographically secure random string.
     */
    function generateRandomString(int $length = 10): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $maxIndex   = strlen($characters) - 1;

        $result = '';

        for ($i = 0; $i < $length; $i++) {

            $result .= $characters[random_int(0, $maxIndex)];

        }

        return $result;
    }
}

if (!function_exists('setFlash')) {

    function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }
}

if (!function_exists('redirect')) {

    function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
/*
|--------------------------------------------------------------------------
| Notes
|--------------------------------------------------------------------------
|
| Expected table columns used by this controller:
|
| users
| -----
| id
| full_name
| email
| password
| role
| status
| failed_attempts
| locked_until
| last_login
| created_at
| updated_at
| deleted_at
|
*/

/*
|--------------------------------------------------------------------------
| End of File
|--------------------------------------------------------------------------
*/
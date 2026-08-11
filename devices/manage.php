<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

requireLogin();

requireRole([
    ROLE_SUPER_ADMIN,
    ROLE_ADMIN,
    ROLE_DEVICE_OWNER
]);

$action = clean((string)get('action', post('action', '')));

switch ($action) {

    /*
    |--------------------------------------------------------------------------
    | Save Device
    |--------------------------------------------------------------------------
    */

    case 'save':

        clearErrors();

        $id          = toInt(post('id'));
        $deviceName  = clean((string)post('device_name'));
        $deviceType  = clean((string)post('device_type'));
        $description = clean((string)post('description'));
        $ownerId     = toInt(post('owner_id'));
        $status      = toInt(post('status'));

        if (isDeviceOwner()) {

            $ownerId = currentUserId();

        }

        required(
            'device_name',
            $deviceName,
            'Device Name'
        );

        required(
            'device_type',
            $deviceType,
            'Device Type'
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

        if (!hasErrors()) {

            if ($id === 0) {

                do {

                    $deviceUuid = uuidv4();

                    $stmt = $pdo->prepare("
                        SELECT COUNT(*)
                        FROM devices
                        WHERE device_uuid = ?
                    ");

                    $stmt->execute([
                        $deviceUuid
                    ]);

                } while ((int)$stmt->fetchColumn() > 0);

            } else {

                $stmt = $pdo->prepare("
                    SELECT device_uuid
                    FROM devices
                    WHERE id = ?
                ");

                $stmt->execute([
                    $id
                ]);

                $deviceUuid = (string)$stmt->fetchColumn();

            }

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

                $stmt = $pdo->prepare("
                    INSERT INTO devices
                    (
                        device_uuid,
                        device_name,
                        device_type,
                        description,
                        owner_id,
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
                        ?,
                        NOW(),
                        NOW()
                    )
                ");

                $stmt->execute([
                    $deviceUuid,
                    $deviceName,
                    $deviceType,
                    $description,
                    $ownerId,
                    $status
                ]);
                                setFlash(
                    'success',
                    'Device created successfully.'
                );

            } else {

                if (isDeviceOwner()) {

                    $stmt = $pdo->prepare("
                        UPDATE devices
                        SET
                            device_name = ?,
                            device_type = ?,
                            description = ?,
                            status = ?,
                            updated_at = NOW()
                        WHERE
                            id = ?
                            AND owner_id = ?
                            AND deleted_at IS NULL
                    ");

                    $stmt->execute([
                        $deviceName,
                        $deviceType,
                        $description,
                        $status,
                        $id,
                        currentUserId()
                    ]);

                } else {

                    $stmt = $pdo->prepare("
                        UPDATE devices
                        SET
                            device_name = ?,
                            device_type = ?,
                            description = ?,
                            owner_id = ?,
                            status = ?,
                            updated_at = NOW()
                        WHERE
                            id = ?
                            AND deleted_at IS NULL
                    ");

                    $stmt->execute([
                        $deviceName,
                        $deviceType,
                        $description,
                        $ownerId,
                        $status,
                        $id
                    ]);

                }

                setFlash(
                    'success',
                    'Device updated successfully.'
                );

            }

            $pdo->commit();

        } catch (Throwable $e) {

            $pdo->rollBack();

            setFlash(
                'danger',
                'Unable to save device.'
            );

        }

        redirect('index.php');

        break;

    /*
    |--------------------------------------------------------------------------
    | Delete Device
    |--------------------------------------------------------------------------
    */

    case 'delete':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid device selected.'
            );

            redirect('index.php');

        }

        if (isDeviceOwner()) {

            $stmt = $pdo->prepare("
                UPDATE devices
                SET
                    deleted_at = NOW(),
                    updated_at = NOW()
                WHERE
                    id = ?
                    AND owner_id = ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([
                $id,
                currentUserId()
            ]);

        } else {

            $stmt = $pdo->prepare("
                UPDATE devices
                SET
                    deleted_at = NOW(),
                    updated_at = NOW()
                WHERE
                    id = ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([
                $id
            ]);

        }

        setFlash(
            'success',
            'Device deleted successfully.'
        );

        redirect('index.php');

        break;
            /*
    |--------------------------------------------------------------------------
    | Activate Device
    |--------------------------------------------------------------------------
    */

    case 'activate':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid device selected.'
            );

            redirect('index.php');

        }

        if (isDeviceOwner()) {

            $stmt = $pdo->prepare("
                UPDATE devices
                SET
                    status = ?,
                    updated_at = NOW()
                WHERE
                    id = ?
                    AND owner_id = ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([
                STATUS_ACTIVE,
                $id,
                currentUserId()
            ]);

        } else {

            $stmt = $pdo->prepare("
                UPDATE devices
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

        }

        setFlash(
            'success',
            'Device activated successfully.'
        );

        redirect('index.php');

        break;

    /*
    |--------------------------------------------------------------------------
    | Deactivate Device
    |--------------------------------------------------------------------------
    */

    case 'deactivate':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid device selected.'
            );

            redirect('index.php');

        }

        if (isDeviceOwner()) {

            $stmt = $pdo->prepare("
                UPDATE devices
                SET
                    status = ?,
                    updated_at = NOW()
                WHERE
                    id = ?
                    AND owner_id = ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([
                STATUS_INACTIVE,
                $id,
                currentUserId()
            ]);

        } else {

            $stmt = $pdo->prepare("
                UPDATE devices
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

        }

        setFlash(
            'success',
            'Device deactivated successfully.'
        );

        redirect('index.php');

        break;
            /*
    |--------------------------------------------------------------------------
    | Regenerate Device UUID
    |--------------------------------------------------------------------------
    */

    case 'regenerate-uuid':

        $id = toInt(get('id'));

        if ($id <= 0) {

            setFlash(
                'danger',
                'Invalid device selected.'
            );

            redirect('index.php');

        }

        do {

            $newUuid = uuidv4();

            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM devices
                WHERE device_uuid = ?
            ");

            $stmt->execute([
                $newUuid
            ]);

        } while ((int)$stmt->fetchColumn() > 0);

        if (isDeviceOwner()) {

            $stmt = $pdo->prepare("
                UPDATE devices
                SET
                    device_uuid = ?,
                    updated_at = NOW()
                WHERE
                    id = ?
                    AND owner_id = ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([
                $newUuid,
                $id,
                currentUserId()
            ]);

        } else {

            $stmt = $pdo->prepare("
                UPDATE devices
                SET
                    device_uuid = ?,
                    updated_at = NOW()
                WHERE
                    id = ?
                    AND deleted_at IS NULL
            ");

            $stmt->execute([
                $newUuid,
                $id
            ]);

        }

        setFlash(
            'success',
            'Device UUID regenerated successfully.'
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
| These helper functions are used by this controller.
| Remove them if your project already provides global implementations.
|
*/

if (!function_exists('redirect')) {

    function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('setFlash')) {

    function setFlash(
        string $type,
        string $message
    ): void {

        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];

    }
}

/*
|--------------------------------------------------------------------------
| Expected Database Columns
|--------------------------------------------------------------------------
|
| devices
| -------
| id
| device_uuid
| device_name
| device_type
| description
| owner_id
| status
| last_value
| last_updated
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
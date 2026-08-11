<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

requireLogin();

$pageTitle = 'Devices';

$page = max(1, toInt(get('page', 1)));
$search = trim((string)get('search', ''));
$editId = toInt(get('id'));

$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = "WHERE d.deleted_at IS NULL";
$params = [];

if ($search !== '') {

    $where .= "
        AND
        (
            d.device_name LIKE ?
            OR d.device_uuid LIKE ?
            OR d.device_type LIKE ?
            OR d.description LIKE ?
        )
    ";

    $keyword = '%' . $search . '%';

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
}

if (isDeviceOwner()) {

    $where .= " AND d.owner_id = ?";

    $params[] = currentUserId();
}

$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM devices d
    {$where}
");

$countStmt->execute($params);

$totalRecords = (int)$countStmt->fetchColumn();

$totalPages = max(
    1,
    (int)ceil($totalRecords / $perPage)
);

$listStmt = $pdo->prepare("
    SELECT
        d.id,
        d.device_uuid,
        d.device_name,
        d.device_type,
        d.description,
        d.status,
        d.`last_value`,
        d.last_seen,
        d.created_at,
        u.full_name AS owner_name
    FROM devices d
    LEFT JOIN users u
        ON u.id = d.owner_id
    {$where}
    ORDER BY d.id DESC
    LIMIT {$offset}, {$perPage}
");

$listStmt->execute($params);

$devices = $listStmt->fetchAll(PDO::FETCH_ASSOC);

$device = [
    'id' => '',
    'device_uuid' => '',
    'device_name' => '',
    'device_type' => '',
    'description' => '',
    'owner_id' => currentUserId(),
    'status' => STATUS_ACTIVE
];

$editMode = false;

if ($editId > 0) {

    $stmt = $pdo->prepare("
        SELECT
            id,
            device_uuid,
            device_name,
            device_type,
            description,
            owner_id,
            status,
            created_at,
            last_seen,
            `last_value`
        FROM devices
        WHERE
            id = ?
            AND deleted_at IS NULL
    ");
    
    

    $stmt->execute([$editId]);

    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {

        $device = $record;

        $editMode = true;

    }

}

$owners = [];

if (!isDeviceOwner()) {

    $stmt = $pdo->query("
        SELECT
            id,
            full_name
        FROM users
        WHERE
            status = " . STATUS_ACTIVE . "
            AND deleted_at IS NULL
        ORDER BY full_name
    ");

    $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

include INCLUDE_PATH . '/header.php';
?>
<div class="container-xl" style="margin-top: 42px;">

    <div class="row">

        <div class="col-lg-8">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <strong>Device List</strong>

                    <form
                        method="get"
                        class="d-flex">

                        <input
                            type="text"
                            name="search"
                            class="form-control form-control-sm me-2"
                            placeholder="Search..."
                            value="<?= e($search) ?>">

                        <button
                            class="btn btn-sm btn-primary"
                            type="submit">

                            <i class="cil-search">Find</i>

                        </button>

                    </form>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover table-striped mb-0">

                            <thead>

                                <tr>

                                    <th>ID</th>

                                    <th>Device</th>

                                    <th>Type</th>

                                    <th>Owner</th>

                                    <th>Last Value</th>

                                    <th>Status</th>

                                    <th width="200">Action</th>

                                </tr>

                            </thead>

                            <tbody>

<?php if (empty($devices)): ?>

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-4">

                                        No devices found.

                                    </td>

                                </tr>

<?php else: ?>

<?php foreach ($devices as $row): ?>

                                <tr>

                                    <td>

                                        <?= e($row['id']) ?>

                                    </td>

                                    <td>

                                        <strong>

                                            <?= e($row['device_name']) ?>

                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            <?= e($row['device_uuid']) ?>

                                        </small>

                                    </td>

                                    <td>

                                        <?= e($row['device_type']) ?>

                                    </td>

                                    <td>

                                        <?= e($row['owner_name']) ?>

                                    </td>

                                    <td>

                                         <?= e($row['last_value'] ?? '-') ?>

                                    </td>

                                    <td>

<?php if ((int)$row['status'] === STATUS_ACTIVE): ?>

                                        <span class="badge bg-success">

                                            Active

                                        </span>

<?php else: ?>

                                        <span class="badge bg-danger">

                                            Inactive

                                        </span>

<?php endif; ?>

                                    </td>

                                    <td>
                                        <a
                                            href="view.php?id=<?= e($row['id']) ?>"
                                            class="btn btn-sm btn-info" title="View Graph">
                                            
                                            <i class="ti ti-chart-bar"></i>
                                            
                                            View
                                            
                                            </a>
                                                                                
                                        <?php if (canManageDevices()): ?>
                                            <a
                                            href="?id=<?= e($row['id']) ?>"
                                            class="btn btn-sm btn-primary" title="Edit">

                                            <i class="ti ti-pencil-star"></i>

                                        </a>
                                        <?php endif; ?>

                                        <?php if (canManageDevices()): ?>

<?php if ((int)$row['status'] === STATUS_ACTIVE): ?>

                                        <a
                                            href="manage.php?action=deactivate&id=<?= e($row['id']) ?>"
                                            class="btn btn-sm btn-warning"
                                            onclick="return confirm('Deactivate this device?')" title="De-Activate">

                                            <i class="ti ti-assembly-off"></i>

                                        </a>

<?php else: ?>

                                        <a
                                            href="manage.php?action=activate&id=<?= e($row['id']) ?>"
                                            class="btn btn-sm btn-success"
                                            onclick="return confirm('Activate this device?')" title="Activate">

                                            <i class="ti ti-assembly"></i>

                                        </a>

<?php endif; ?>

                                        <a
                                            href="manage.php?action=delete&id=<?= e($row['id']) ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this device?')" title="Delete">

                                            <i class="ti ti-trash-x"></i>

                                        </a>

                                        <?php endif; ?>
                                    </td>

                                </tr>

<?php endforeach; ?>

<?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

<?php if ($totalPages > 1): ?>

                <div class="card-footer">

                    <nav>

                        <ul class="pagination pagination-sm mb-0">

<?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">

                                <a
                                    class="page-link"
                                    href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">

                                    <?= $i ?>

                                </a>

                            </li>

<?php endfor; ?>

                        </ul>

                    </nav>

                </div>

<?php endif; ?>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card">

                <div class="card-header">

                    <strong>

                        <?= $editMode ? 'Edit Device' : 'Add Device' ?>

                    </strong>

                </div>

                <div class="card-body">

                    <form
                        method="post"
                        action="manage.php?action=save">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= e($device['id']) ?>">
                                                    <div class="mb-3">

                            <label class="form-label">

                                Device Name

                            </label>

                            <input
                                type="text"
                                name="device_name"
                                class="form-control"
                                value="<?= e($device['device_name']) ?>"
                                required>

                            <?php if (getError('device_name')): ?>

                                <div class="text-danger mt-1">

                                    <?= e(getError('device_name')) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Device Type

                            </label>

<select
    name="device_type"
    class="form-select"
    required>

    <option value="">
        Select Data Type
    </option>


    <option value="BOOLEAN"
        <?= ($device['device_type'] ?? '') === 'BOOLEAN' ? 'selected' : '' ?>>
        Boolean (0 / 1)
    </option>


    <option value="INTEGER"
        <?= ($device['device_type'] ?? '') === 'INTEGER' ? 'selected' : '' ?>>
        Integer
    </option>


    <option value="DECIMAL"
        <?= ($device['device_type'] ?? '') === 'DECIMAL' ? 'selected' : '' ?>>
        Decimal
    </option>


    <option value="TEXT"
        <?= ($device['device_type'] ?? '') === 'TEXT' ? 'selected' : '' ?>>
        Text
    </option>


</select>

                            <?php if (getError('device_type')): ?>

                                <div class="text-danger mt-1">

                                    <?= e(getError('device_type')) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Description

                            </label>

                            <textarea
                                name="description"
                                class="form-control"
                                rows="3"><?= e($device['description']) ?></textarea>

                            <?php if (getError('description')): ?>

                                <div class="text-danger mt-1">

                                    <?= e(getError('description')) ?>

                                </div>

                            <?php endif; ?>

                        </div>

<?php if (!isDeviceOwner()): ?>

                        <div class="mb-3">

                            <label class="form-label">

                                Device Owner

                            </label>

                            <select
                                name="owner_id"
                                class="form-select"
                                required>

<?php foreach ($owners as $owner): ?>

                                <option
                                    value="<?= e($owner['id']) ?>"
                                    <?= (int)$owner['id'] === (int)$device['owner_id'] ? 'selected' : '' ?>>

                                    <?= e($owner['full_name']) ?>

                                </option>

<?php endforeach; ?>

                            </select>

                        </div>

<?php else: ?>

                        <input
                            type="hidden"
                            name="owner_id"
                            value="<?= e(currentUserId()) ?>">

<?php endif; ?>
                        <div class="mb-3">

                            <label class="form-label">

                                Status

                            </label>

                            <select
                                name="status"
                                class="form-select"
                                required>

                                <option
                                    value="<?= STATUS_ACTIVE ?>"
                                    <?= (int)$device['status'] === STATUS_ACTIVE ? 'selected' : '' ?>>

                                    Active

                                </option>

                                <option
                                    value="<?= STATUS_INACTIVE ?>"
                                    <?= (int)$device['status'] === STATUS_INACTIVE ? 'selected' : '' ?>>

                                    Inactive

                                </option>

                            </select>

                            <?php if (getError('status')): ?>

                                <div class="text-danger mt-1">

                                    <?= e(getError('status')) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                        <?php if ($editMode): ?>

                        <div class="mb-3">

                            <label class="form-label">

                                Device UUID

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= e($device['device_uuid']) ?>"
                                readonly>

                            <small class="text-muted">

                                This UUID is generated automatically and is used by the device API.

                            </small>

                        </div>

                        <?php endif; ?>

                        <div class="d-grid gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="cil-save"></i>

                                <?= $editMode ? 'Update Device' : 'Save Device' ?>

                            </button>

<?php if ($editMode): ?>

                            <a
                                href="index.php"
                                class="btn btn-secondary">

                                <i class="cil-x"></i>

                                Cancel

                            </a>

<?php endif; ?>

                        </div>

                    </form>

                </div>

            </div>

<?php if ($editMode): ?>

            <div class="card mt-3">

                <div class="card-header">

                    <strong>

                        Device Information

                    </strong>

                </div>

                <div class="card-body">

                    <table class="table table-sm mb-0">

                        <tr>

                            <th width="140">

                                Device UUID

                            </th>

                            <td>

                                <code><?= e($device['device_uuid']) ?></code>

                            </td>

                        </tr>
                                                <tr>

                            <th>

                                Last Value

                            </th>

                            <td>

                                <?= e($device['last_value'] ?? '-') ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Last Updated

                            </th>

                            <td>

                                <?= !empty($device['last_seen'])
                                    ? e(formatDateTime($device['last_seen']))
                                    : 'Never' ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Created

                            </th>

                            <td>

                                <?= !empty($device['created_at'])
                                    ? e(formatDateTime($device['created_at']))
                                    : '-' ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Status

                            </th>

                            <td>

<?php if ((int)$device['status'] === STATUS_ACTIVE): ?>

                                <span class="badge bg-success">

                                    Active

                                </span>

<?php else: ?>

                                <span class="badge bg-danger">

                                    Inactive

                                </span>

<?php endif; ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

<?php endif; ?>

        </div>

    </div>

</div>
<?php
/*
|--------------------------------------------------------------------------
| Temporary Password / Flash Display
|--------------------------------------------------------------------------
|
| Display any temporary device information or flash messages.
|
*/
?>

<?php if (isset($_SESSION['flash'])): ?>

<div class="toast-container position-fixed top-0 end-0 p-3">

    <div
        class="toast show text-bg-<?= e($_SESSION['flash']['type']) ?>"
        role="alert">

        <div class="d-flex">

            <div class="toast-body">

                <?= e($_SESSION['flash']['message']) ?>

            </div>

            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"></button>

        </div>

    </div>

</div>

<?php unset($_SESSION['flash']); ?>

<?php endif; ?>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const toastElements = document.querySelectorAll('.toast');

    toastElements.forEach(function (toastElement) {

        if (typeof bootstrap !== 'undefined') {

            const toast = new bootstrap.Toast(toastElement, {
                delay: 3000
            });

            toast.show();

        }

    });

});

</script>
<?php
/*
|--------------------------------------------------------------------------
| Page Cleanup
|--------------------------------------------------------------------------
|
| Clear validation errors after rendering the page so they are shown
| only once after a redirect.
|
*/

clearErrors();

?>

<?php include INCLUDE_PATH . '/footer.php'; ?>






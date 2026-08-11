<?php
declare(strict_types=1);

$pageTitle = 'User Management';

require_once __DIR__ . '/../includes/bootstrap.php';

requireLogin();
requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

$search = clean((string)get('search'));
$page   = currentPage();

$where  = "WHERE deleted_at IS NULL";
$params = [];

if ($search !== '') {

    $where .= "
        AND
        (
            full_name LIKE ?
            OR email LIKE ?
        )
    ";

    $keyword = "%{$search}%";

    $params[] = $keyword;
    $params[] = $keyword;
}

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM users
    {$where}
");

$stmt->execute($params);

$totalRecords = (int)$stmt->fetchColumn();

$totalPages = totalPages($totalRecords);

$offset = pageOffset($page);

$sql = "
    SELECT
        id,
        full_name,
        email,
        role,
        status,
        created_at,
        last_login
    FROM users
    {$where}
    ORDER BY full_name
    LIMIT ?
    OFFSET ?
";

$stmt = $pdo->prepare($sql);

$index = 1;

foreach ($params as $value) {

    $stmt->bindValue($index++, $value);

}

$stmt->bindValue($index++, RECORDS_PER_PAGE, PDO::PARAM_INT);
$stmt->bindValue($index, $offset, PDO::PARAM_INT);

$stmt->execute();

$users = $stmt->fetchAll();

$editMode = false;

$user = [
    'id'        => '',
    'full_name' => '',
    'email'     => '',
    'role'      => ROLE_DEVICE_OWNER,
    'status'    => STATUS_ACTIVE
];

if (get('id') !== null) {

    $editId = toInt(get('id'));

    $stmt = $pdo->prepare("
    SELECT
        id,
        full_name,
        email,
        role,
        status,
        created_at,
        last_login
        FROM users
        WHERE
            id = ?
            AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([$editId]);

    $record = $stmt->fetch();

    if ($record) {


        if (
            !isSuperAdmin()
            &&
            (int)$record['role'] === ROLE_SUPER_ADMIN
        ) {

            setFlash(
                'danger',
                'You cannot edit Super Admin users.'
            );

            redirect('index.php');

        }


        $user = $record;

        $editMode = true;

    }

}

include INCLUDE_PATH . '/header.php';
//include INCLUDE_PATH . '/navbar.php';
//include INCLUDE_PATH . '/sidebar.php';
?>

<div class="container-xl" style="margin-top: 42px;">

    <?php displayFlash(); ?>

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">

                User Management

            </h4>

            <a
                href="index.php"
                class="btn btn-primary">

                <i class="cil-user-plus"></i>

                New User

            </a>

        </div>

        <div class="card-body">

            <form
                method="get"
                class="row g-3 mb-4">

                <div class="col-md-5">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by Name or Email"
                        value="<?= e($search) ?>">

                </div>

                <div class="col-auto">

                    <button
                        class="btn btn-primary"
                        type="submit">

                        Search

                    </button>

                    <a
                        href="index.php"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </form>

            <div class="row">

                <div class="col-lg-8">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead>

                                <tr>

                                    <th width="70">ID</th>

                                    <th>Name</th>

                                    <th>Email</th>

                                    <th width="130">Role</th>

                                    <th width="90">Status</th>

                                    <th width="170">Actions</th>

                                </tr>

                            </thead>

                            <tbody>
  <?php if (empty($users)): ?>

    <tr>

        <td
            colspan="6"
            class="text-center text-muted">

            No users found.

        </td>

    </tr>

<?php else: ?>

    <?php foreach ($users as $row): ?>

        <tr>

            <td>

                <?= e($row['id']) ?>

            </td>

            <td>

                <?= e($row['full_name']) ?>

            </td>

            <td>

                <?= e($row['email']) ?>

            </td>

            <td>

                <?= e(roleName((int)$row['role'])) ?>

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
                    href="index.php?id=<?= (int)$row['id'] ?>"
                    class="btn btn-sm btn-warning">

                    <i class="cil-pencil"></i>

                    Edit

                </a>

                <?php if ((int)$row['id'] !== currentUserId()): ?>

                    <a
                        href="manage.php?action=delete&id=<?= (int)$row['id'] ?>"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this user?');">

                        <i class="cil-trash"></i>

                        Delete

                    </a>

                <?php endif; ?>

            </td>

        </tr>

    <?php endforeach; ?>

<?php endif; ?>

                            </tbody>

                        </table>

                    </div>

<?php if ($totalPages > 1): ?>

<nav>

    <ul class="pagination">

        <?php if ($page > 1): ?>

            <li class="page-item">

                <a
                    class="page-link"
                    href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">

                    Previous

                </a>

            </li>

        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

            <li class="page-item <?= $page == $i ? 'active' : '' ?>">

                <a
                    class="page-link"
                    href="?search=<?= urlencode($search) ?>&page=<?= $i ?>">

                    <?= $i ?>

                </a>

            </li>

        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>

            <li class="page-item">

                <a
                    class="page-link"
                    href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">

                    Next

                </a>

            </li>

        <?php endif; ?>

    </ul>

</nav>

<?php endif; ?>

                </div>

                <div class="col-lg-4">

                    <div class="card border">

                        <div class="card-header">

                            <strong>

                                <?= $editMode ? 'Edit User' : 'Add User' ?>

                            </strong>

                        </div>

                        <div class="card-body">

                            <form
                                method="post"
                                action="manage.php?action=save">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= e($user['id']) ?>">
                                                                    <div class="mb-3">

                                    <label class="form-label">

                                        Full Name

                                    </label>

                                    <input
                                        type="text"
                                        name="full_name"
                                        class="form-control"
                                        value="<?= e($user['full_name']) ?>"
                                        required>

                                    <?php if (getError('full_name')): ?>

                                        <div class="text-danger mt-1">

                                            <?= e(getError('full_name')) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">

                                        Email Address

                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        value="<?= e($user['email']) ?>"
                                        required>

                                    <?php if (getError('email')): ?>

                                        <div class="text-danger mt-1">

                                            <?= e(getError('email')) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">

                                        Password

                                        <?php if ($editMode): ?>

                                            <small class="text-muted">

                                                (Leave blank to keep existing password)

                                            </small>

                                        <?php endif; ?>

                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        <?= $editMode ? '' : 'required' ?>>

                                    <?php if (getError('password')): ?>

                                        <div class="text-danger mt-1">

                                            <?= e(getError('password')) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">

                                        Role

                                    </label>

                                    <select
                                        name="role"
                                        class="form-select"
                                        required>

                                        <?php if (isSuperAdmin()): ?>

                                        <option
                                            value="<?= ROLE_SUPER_ADMIN ?>"
                                            <?= (int)$user['role'] === ROLE_SUPER_ADMIN ? 'selected' : '' ?>>

                                            Super Admin

                                        </option>

                                        <?php endif; ?>

                                        <option
                                            value="<?= ROLE_ADMIN ?>"
                                            <?= (int)$user['role'] === ROLE_ADMIN ? 'selected' : '' ?>>

                                            Admin

                                        </option>

                                        <option
                                            value="<?= ROLE_DEVICE_OWNER ?>"
                                            <?= (int)$user['role'] === ROLE_DEVICE_OWNER ? 'selected' : '' ?>>

                                            Device Owner

                                        </option>

                                        <option
                                            value="<?= ROLE_READ_ONLY ?>"
                                            <?= (int)$user['role'] === ROLE_READ_ONLY ? 'selected' : '' ?>>

                                            Read Only

                                        </option>

                                    </select>

                                    <?php if (getError('role')): ?>

                                        <div class="text-danger mt-1">

                                            <?= e(getError('role')) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>
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
                                            <?= (int)$user['status'] === STATUS_ACTIVE ? 'selected' : '' ?>>

                                            Active

                                        </option>

                                        <option
                                            value="<?= STATUS_INACTIVE ?>"
                                            <?= (int)$user['status'] === STATUS_INACTIVE ? 'selected' : '' ?>>

                                            Inactive

                                        </option>

                                    </select>

                                    <?php if (getError('status')): ?>

                                        <div class="text-danger mt-1">

                                            <?= e(getError('status')) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div class="d-grid gap-2">

                                    <button
                                        type="submit"
                                        class="btn btn-primary">

                                        <i class="cil-save"></i>

                                        <?= $editMode ? 'Update User' : 'Save User' ?>

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

                    <div class="card border mt-3">

                        <div class="card-header">

                            <strong>

                                User Information

                            </strong>

                        </div>

                        <div class="card-body">

                            <table class="table table-sm mb-0">

                                <tr>

                                    <th width="120">

                                        User ID

                                    </th>

                                    <td>

                                        <?= e($user['id']) ?>

                                    </td>

                                </tr>

                                <tr>

                                    <th>

                                        Role

                                    </th>

                                    <td>

                                        <?= e(roleName((int)$user['role'])) ?>

                                    </td>

                                </tr>

                                <tr>

                                    <th>

                                        Status

                                    </th>

                                    <td>

                                        <?= (int)$user['status'] === STATUS_ACTIVE ? 'Active' : 'Inactive' ?>

                                    </td>

                                </tr>
                                                                <tr>

                                    <th>

                                        Created

                                    </th>

                                    <td>

                                        <?php
                                        $created = '';

                                        foreach ($users as $rowData) {

                                            if ((int)$rowData['id'] === (int)$user['id']) {

                                                $created = $rowData['created_at'];
                                                break;

                                            }

                                        }

                                        echo $created !== ''
                                            ? e(formatDateTime($created))
                                            : '-';
                                        ?>

                                    </td>

                                </tr>

                                <tr>

                                    <th>

                                        Last Login

                                    </th>

                                    <td>

                                        <?php
                                        $lastLogin = null;

                                        foreach ($users as $rowData) {

                                            if ((int)$rowData['id'] === (int)$user['id']) {

                                                $lastLogin = $rowData['last_login'];
                                                break;

                                            }

                                        }

                                        echo !empty($lastLogin)
                                            ? e(formatDateTime($lastLogin))
                                            : 'Never';
                                        ?>

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

<?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

                                <tr>

                                    <th>

                                        Created

                                    </th>

                                    <td>

                                        <?= !empty($user['created_at'])
                                            ? e(formatDateTime($user['created_at']))
                                            : '-' ?>

                                    </td>

                                </tr>

                                <tr>

                                    <th>

                                        Last Login

                                    </th>

                                    <td>

                                        <?= !empty($user['last_login'])
                                            ? e(formatDateTime($user['last_login']))
                                            : 'Never' ?>

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

<?php //endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include INCLUDE_PATH . '/footer.php'; ?>
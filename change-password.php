<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

requireLogin();

$pageTitle = 'Change Password';

if (isPost()) {

    clearErrors();

    $currentPassword = (string)post('current_password');
    $newPassword     = (string)post('new_password');
    $confirmPassword = (string)post('confirm_password');

    required(
        'current_password',
        $currentPassword,
        'Current Password'
    );

    required(
        'new_password',
        $newPassword,
        'New Password'
    );

    required(
        'confirm_password',
        $confirmPassword,
        'Confirm Password'
    );

    minLength(
        'new_password',
        $newPassword,
        PASSWORD_MIN_LENGTH,
        'New Password'
    );

    if ($newPassword !== $confirmPassword) {

        addError(
            'confirm_password',
            'Passwords do not match.'
        );

    }

    if (!hasErrors()) {

        if (
            changeUserPassword(
                $pdo,
                currentUserId(),
                $currentPassword,
                $newPassword
            )
        ) {

            setFlash(
                'success',
                'Password changed successfully.'
            );

            redirect('index.php');

        }

        addError(
            'current_password',
            'Current password is incorrect.'
        );

    }

}

include INCLUDE_PATH . '/header.php';
?>
<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card">

                <div class="card-header">

                    <strong>

                        Change Password

                    </strong>

                </div>

                <div class="card-body">

                    <form
                        method="post">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($csrfToken) ?>">

                        <div class="mb-3">

                            <label class="form-label">

                                Current Password

                            </label>

                            <input
                                type="password"
                                name="current_password"
                                class="form-control"
                                required>

                            <?php if (getError('current_password')): ?>

                                <div class="text-danger mt-1">

                                    <?= e(getError('current_password')) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                New Password

                            </label>

                            <input
                                type="password"
                                name="new_password"
                                class="form-control"
                                minlength="<?= PASSWORD_MIN_LENGTH ?>"
                                required>

                            <?php if (getError('new_password')): ?>

                                <div class="text-danger mt-1">

                                    <?= e(getError('new_password')) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Confirm New Password

                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                minlength="<?= PASSWORD_MIN_LENGTH ?>"
                                required>

                            <?php if (getError('confirm_password')): ?>

                                <div class="text-danger mt-1">

                                    <?= e(getError('confirm_password')) ?>

                                </div>

                            <?php endif; ?>

                        </div>
                                                <div class="d-grid gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="cil-lock-locked"></i>

                                Update Password

                            </button>

                            <a
                                href="index.php"
                                class="btn btn-secondary">

                                <i class="cil-arrow-left"></i>

                                Cancel

                            </a>

                        </div>

                    </form>

                </div>

                <div class="card-footer">

                    <small class="text-muted">

                        Password Requirements:

                    </small>

                    <ul class="small mb-0 mt-2">

                        <li>

                            Minimum <?= PASSWORD_MIN_LENGTH ?> characters

                        </li>

                        <li>

                            Use a strong, unique password

                        </li>

                        <li>

                            Avoid using personal information

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>
<script>

document.addEventListener('DOMContentLoaded', function () {

    const newPassword = document.querySelector(
        'input[name="new_password"]'
    );

    const confirmPassword = document.querySelector(
        'input[name="confirm_password"]'
    );

    function validatePasswordMatch() {

        if (
            confirmPassword.value !== ''
            &&
            newPassword.value !== confirmPassword.value
        ) {

            confirmPassword.setCustomValidity(
                'Passwords do not match.'
            );

        } else {

            confirmPassword.setCustomValidity('');

        }

    }

    newPassword.addEventListener(
        'input',
        validatePasswordMatch
    );

    confirmPassword.addEventListener(
        'input',
        validatePasswordMatch
    );

});

</script>
<?php

clearErrors();

include INCLUDE_PATH . '/footer.php';

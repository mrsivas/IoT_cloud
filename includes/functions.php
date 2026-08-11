<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Request Helpers
|--------------------------------------------------------------------------
*/

function get(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

function post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

function request(string $key, mixed $default = null): mixed
{
    return $_REQUEST[$key] ?? $default;
}

function currentPage(): string
{
    return basename(
        $_SERVER['PHP_SELF'] ?? ''
    );
}

function pageOffset(string $path = ''): string
{
    return basename(
        $path !== ''
            ? $path
            : ($_SERVER['PHP_SELF'] ?? '')
    );
}


if (!function_exists('displayFlash')) {

    function displayFlash(): string
    {

        $flash = $_SESSION['flash'] ?? null;


        if (!$flash) {

            return '';

        }


        unset($_SESSION['flash']);


        $type = htmlspecialchars(
            $flash['type'] ?? 'info',
            ENT_QUOTES,
            'UTF-8'
        );


        $message = htmlspecialchars(
            $flash['message'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        );


        return '
        <div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">

            ' . $message . '

            <button type="button"
                class="btn-close"
                data-coreui-dismiss="alert">
            </button>

        </div>';

    }

}


/*
|--------------------------------------------------------------------------
| Type Helpers
|--------------------------------------------------------------------------
*/

function clean(?string $value): string
{
    return trim((string)$value);
}

function e(mixed $value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
function toInt(mixed $value): int
{
    return (int)$value;
}

function toFloat(mixed $value): float
{
    return (float)$value;
}

function toBool(mixed $value): bool
{
    return filter_var(
        $value,
        FILTER_VALIDATE_BOOLEAN
    );
}

/*
|--------------------------------------------------------------------------
| Redirect Helper
|--------------------------------------------------------------------------
*/

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}
/*
|--------------------------------------------------------------------------
| Flash Message Helpers
|--------------------------------------------------------------------------
*/

function setFlash(
    string $type,
    string $message
): void {

    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];

}

function flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];

    unset($_SESSION['flash']);

    return $flash;
}

/*
|--------------------------------------------------------------------------
| Validation Error Helpers
|--------------------------------------------------------------------------
*/

function addError(
    string $field,
    string $message
): void {

    $_SESSION['errors'][$field] = $message;

}

function getError(
    string $field
): ?string {

    return $_SESSION['errors'][$field] ?? null;

}

function getErrors(): array
{
    return $_SESSION['errors'] ?? [];
}

function hasErrors(): bool
{
    return !empty($_SESSION['errors']);
}

function clearErrors(): void
{
    $_SESSION['errors'] = [];
}

/*
|--------------------------------------------------------------------------
| Old Input Helpers
|--------------------------------------------------------------------------
*/

function old(
    string $key,
    mixed $default = ''
): mixed {

    return $_SESSION['old'][$key] ?? $default;

}

function rememberInput(array $input): void
{
    $_SESSION['old'] = $input;
}

function forgetInput(): void
{
    unset($_SESSION['old']);
}
/*
|--------------------------------------------------------------------------
| Validation Helpers
|--------------------------------------------------------------------------
*/

function required(
    string $field,
    ?string $value,
    string $label
): void {

    if (trim((string)$value) === '') {

        addError(
            $field,
            $label . ' is required.'
        );

    }

}

function email(
    string $field,
    ?string $value,
    string $label
): void {

    if (
        trim((string)$value) !== ''
        &&
        !filter_var($value, FILTER_VALIDATE_EMAIL)
    ) {

        addError(
            $field,
            $label . ' must be a valid email address.'
        );

    }

}

function minLength(
    string $field,
    ?string $value,
    int $length,
    string $label
): void {

    if (
        trim((string)$value) !== ''
        &&
        mb_strlen((string)$value) < $length
    ) {

        addError(
            $field,
            $label . " must be at least {$length} characters."
        );

    }

}

function maxLength(
    string $field,
    ?string $value,
    int $length,
    string $label
): void {

    if (
        mb_strlen((string)$value) > $length
    ) {

        addError(
            $field,
            $label . " must not exceed {$length} characters."
        );

    }

}

function inArray(
    string $field,
    mixed $value,
    array $allowed,
    string $label
): void {

    if (!in_array($value, $allowed, true)) {

        addError(
            $field,
            $label . ' is invalid.'
        );

    }

}
/*
|--------------------------------------------------------------------------
| Date & Time Helpers
|--------------------------------------------------------------------------
*/

function formatDateTime(
    ?string $dateTime,
    string $format = 'd-m-Y h:i A'
): string {

    if (empty($dateTime)) {
        return '-';
    }

    try {

        return (new DateTime($dateTime))
            ->format($format);

    } catch (Throwable $e) {

        return '-';

    }

}

function formatDate(
    ?string $date,
    string $format = 'd-m-Y'
): string {

    return formatDateTime(
        $date,
        $format
    );

}

/*
|--------------------------------------------------------------------------
| UUID Helper
|--------------------------------------------------------------------------
*/

function uuidv4(): string
{
    $data = random_bytes(16);

    $data[6] = chr(
        (ord($data[6]) & 0x0f) | 0x40
    );

    $data[8] = chr(
        (ord($data[8]) & 0x3f) | 0x80
    );

    return vsprintf(
        '%s%s-%s-%s-%s-%s%s%s',
        str_split(
            bin2hex($data),
            4
        )
    );
}

/*
|--------------------------------------------------------------------------
| Random String Helper
|--------------------------------------------------------------------------
*/

function randomString(
    int $length = 16
): string {

    $characters =
        'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';

    $max = strlen($characters) - 1;

    $value = '';

    for ($i = 0; $i < $length; $i++) {

        $value .= $characters[
            random_int(0, $max)
        ];

    }

    return $value;
}
/*
|--------------------------------------------------------------------------
| Pagination Helpers
|--------------------------------------------------------------------------
*/

function paginate(
    int $page,
    int $perPage
): array {

    $page = max(1, $page);

    $perPage = max(1, $perPage);

    return [
        'page' => $page,
        'perPage' => $perPage,
        'offset' => ($page - 1) * $perPage
    ];

}

function totalPages(
    int $totalRecords,
    int $perPage = DEFAULT_PER_PAGE
): int {

    if ($perPage <= 0) {
        return 1;
    }

    return max(
        1,
        (int)ceil($totalRecords / $perPage)
    );

}

/*
|--------------------------------------------------------------------------
| Role Helpers
|--------------------------------------------------------------------------
*/

function roleName(int $role): string
{
    return match ($role) {

        ROLE_SUPER_ADMIN => 'Super Admin',

        ROLE_ADMIN => 'Admin',

        ROLE_DEVICE_OWNER => 'Device Owner',

        ROLE_READ_ONLY => 'Read Only',

        default => 'Unknown'

    };
}

function statusName(int $status): string
{
    return match ($status) {

        STATUS_ACTIVE => 'Active',

        STATUS_INACTIVE => 'Inactive',

        default => 'Unknown'

    };
}

/*
|--------------------------------------------------------------------------
| Response Helpers
|--------------------------------------------------------------------------
*/

function jsonResponse(
    array $data,
    int $statusCode = 200
): never {

    http_response_code($statusCode);

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    );

    exit;
}
/*
|--------------------------------------------------------------------------
| Array Helpers
|--------------------------------------------------------------------------
*/

function arrayGet(
    array $array,
    string|int $key,
    mixed $default = null
): mixed {

    return $array[$key] ?? $default;

}

function arrayHas(
    array $array,
    string|int $key
): bool {

    return array_key_exists(
        $key,
        $array
    );

}

/*
|--------------------------------------------------------------------------
| String Helpers
|--------------------------------------------------------------------------
*/

function startsWith(
    string $haystack,
    string $needle
): bool {

    return str_starts_with(
        $haystack,
        $needle
    );

}

function endsWith(
    string $haystack,
    string $needle
): bool {

    return str_ends_with(
        $haystack,
        $needle
    );

}

function contains(
    string $haystack,
    string $needle
): bool {

    return str_contains(
        $haystack,
        $needle
    );

}

/*
|--------------------------------------------------------------------------
| Device Helpers
|--------------------------------------------------------------------------
*/

function generateDeviceUuid(): string
{
    return uuidv4();
}

function isValidUuid(
    string $uuid
): bool {

    return (bool)preg_match(
        '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
        $uuid
    );

}
/*
|--------------------------------------------------------------------------
| Database Helpers
|--------------------------------------------------------------------------
*/

function dbValue(
    PDOStatement $statement
): mixed {

    return $statement->fetchColumn();

}

function dbRow(
    PDOStatement $statement
): array|false {

    return $statement->fetch(PDO::FETCH_ASSOC);

}

function dbRows(
    PDOStatement $statement
): array {

    return $statement->fetchAll(PDO::FETCH_ASSOC);

}

/*
|--------------------------------------------------------------------------
| Logging Helpers
|--------------------------------------------------------------------------
*/

function appLog(
    string $message,
    string $level = 'INFO'
): void {

    error_log(
        sprintf(
            '[%s] %s',
            strtoupper($level),
            $message
        )
    );

}

/*
|--------------------------------------------------------------------------
| File Upload Helpers
|--------------------------------------------------------------------------
*/

function uploadedFileExists(
    string $field
): bool {

    return isset($_FILES[$field])
        &&
        $_FILES[$field]['error'] === UPLOAD_ERR_OK;

}

function uploadedFileName(
    string $field
): string {

    return basename(
        $_FILES[$field]['name'] ?? ''
    );

}

function uploadedFileExtension(
    string $field
): string {

    return strtolower(
        pathinfo(
            uploadedFileName($field),
            PATHINFO_EXTENSION
        )
    );

}
/*
|--------------------------------------------------------------------------
| File Helpers
|--------------------------------------------------------------------------
*/

function ensureDirectory(string $path): bool
{
    if (is_dir($path)) {
        return true;
    }

    return mkdir($path, 0755, true);
}

function safeFileName(string $fileName): string
{
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

    return uniqid('file_', true)
        . ($extension ? '.' . strtolower($extension) : '');
}

/*
|--------------------------------------------------------------------------
| Miscellaneous Helpers
|--------------------------------------------------------------------------
*/

function now(): string
{
    return date('Y-m-d H:i:s');
}

function clientIp(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function isPost(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function isGet(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
}

/*
|--------------------------------------------------------------------------
| End of File
|--------------------------------------------------------------------------
*/

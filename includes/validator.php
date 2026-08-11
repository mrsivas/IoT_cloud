<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| MCEIoT Validator
|--------------------------------------------------------------------------
*/

if (!defined('APP_NAME')) {
    exit('Direct access is not allowed.');
}

/*
|--------------------------------------------------------------------------
| Validation Error Storage
|--------------------------------------------------------------------------
*/

function validationErrors(?array $errors = null): array
{
    static $validationErrors = [];

    if ($errors !== null) {
        $validationErrors = $errors;
    }

    return $validationErrors;
}

/*
|--------------------------------------------------------------------------
| Add Validation Error
|--------------------------------------------------------------------------
*/

function addError(string $field, string $message): void
{
    $errors = validationErrors();

    $errors[$field] = $message;

    validationErrors($errors);
}

/*
|--------------------------------------------------------------------------
| Clear Errors
|--------------------------------------------------------------------------
*/

function clearErrors(): void
{
    validationErrors([]);
}

/*
|--------------------------------------------------------------------------
| Has Errors
|--------------------------------------------------------------------------
*/

function hasErrors(): bool
{
    return !empty(validationErrors());
}

/*
|--------------------------------------------------------------------------
| Get Error
|--------------------------------------------------------------------------
*/

function getError(string $field): string
{
    $errors = validationErrors();

    return $errors[$field] ?? '';
}

/*
|--------------------------------------------------------------------------
| Get All Errors
|--------------------------------------------------------------------------
*/

function getErrors(): array
{
    return validationErrors();
}

/*
|--------------------------------------------------------------------------
| Required
|--------------------------------------------------------------------------
*/

function required(string $field, mixed $value, string $label): bool
{
    if (trim((string)$value) === '') {

        addError(
            $field,
            $label . ' is required.'
        );

        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| Email
|--------------------------------------------------------------------------
*/

function email(string $field, string $value, string $label): bool
{
    if (
        trim($value) !== '' &&
        !filter_var($value, FILTER_VALIDATE_EMAIL)
    ) {

        addError(
            $field,
            $label . ' is invalid.'
        );

        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| Integer
|--------------------------------------------------------------------------
*/

function integer(string $field, mixed $value, string $label): bool
{
    if (
        $value !== '' &&
        filter_var($value, FILTER_VALIDATE_INT) === false
    ) {

        addError(
            $field,
            $label . ' must be a valid integer.'
        );

        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| Decimal / Numeric
|--------------------------------------------------------------------------
*/

function decimal(string $field, mixed $value, string $label): bool
{
    if (
        $value !== '' &&
        !is_numeric($value)
    ) {

        addError(
            $field,
            $label . ' must be numeric.'
        );

        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| Minimum Length
|--------------------------------------------------------------------------
*/

function minLength(
    string $field,
    string $value,
    int $length,
    string $label
): bool {

    if (mb_strlen($value) < $length) {

        addError(
            $field,
            sprintf(
                '%s must be at least %d characters.',
                $label,
                $length
            )
        );

        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| Maximum Length
|--------------------------------------------------------------------------
*/

function maxLength(
    string $field,
    string $value,
    int $length,
    string $label
): bool {

    if (mb_strlen($value) > $length) {

        addError(
            $field,
            sprintf(
                '%s cannot exceed %d characters.',
                $label,
                $length
            )
        );

        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| Match Fields
|--------------------------------------------------------------------------
*/

function same(
    string $field,
    mixed $value1,
    mixed $value2,
    string $message
): bool {

    if ($value1 !== $value2) {

        addError(
            $field,
            $message
        );

        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| In Array
|--------------------------------------------------------------------------
*/

function inArray(
    string $field,
    mixed $value,
    array $allowed,
    string $label
): bool {

    if (!in_array($value, $allowed, true)) {

        addError(
            $field,
            $label . ' is invalid.'
        );

        return false;
    }

    return true;
}
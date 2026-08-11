/*
|--------------------------------------------------------------------------
| MCEIoT Application JavaScript
|--------------------------------------------------------------------------
|
| Common frontend functions used across the application.
|
*/

'use strict';


/*
|--------------------------------------------------------------------------
| Auto Hide Alerts
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const alerts =
            document.querySelectorAll(
                '.alert'
            );


        alerts.forEach(
            function (alert) {

                setTimeout(
                    function () {

                        if (
                            typeof coreui !== 'undefined'
                        ) {

                            const instance =
                                coreui.Alert
                                .getOrCreateInstance(alert);

                            instance.close();

                        } else {

                            alert.style.display = 'none';

                        }

                    },
                    5000
                );

            }
        );

    }
);


/*
|--------------------------------------------------------------------------
| Delete Confirmation
|--------------------------------------------------------------------------
*/

function confirmDelete(
    message = 'Are you sure you want to delete this item?'
) {

    return confirm(message);

}


/*
|--------------------------------------------------------------------------
| Device Value Formatting
|--------------------------------------------------------------------------
*/

function formatDeviceValue(
    value
) {

    if (
        value === null ||
        value === undefined ||
        value === ''
    ) {

        return '-';

    }

    return value;

}


/*
|--------------------------------------------------------------------------
| Password Toggle
|--------------------------------------------------------------------------
*/

function togglePassword(
    inputId
) {

    const input =
        document.getElementById(
            inputId
        );


    if (!input) {

        return;

    }


    if (
        input.type === 'password'
    ) {

        input.type = 'text';

    } else {

        input.type = 'password';

    }

}
/*
|--------------------------------------------------------------------------
| Form Validation Helpers
|--------------------------------------------------------------------------
*/

function validateRequiredFields(
    form
) {

    let valid = true;


    const fields =
        form.querySelectorAll(
            '[required]'
        );


    fields.forEach(
        function (field) {

            if (
                field.value.trim() === ''
            ) {

                field.classList.add(
                    'is-invalid'
                );

                valid = false;

            } else {

                field.classList.remove(
                    'is-invalid'
                );

            }

        }
    );


    return valid;

}


/*
|--------------------------------------------------------------------------
| Confirm Form Submit
|--------------------------------------------------------------------------
*/

function confirmSubmit(
    message = 'Continue with this action?'
) {

    return window.confirm(
        message
    );

}


/*
|--------------------------------------------------------------------------
| Copy To Clipboard
|--------------------------------------------------------------------------
*/

function copyText(
    text
) {

    if (
        navigator.clipboard
    ) {

        navigator.clipboard.writeText(
            text
        );

        return true;

    }


    return false;

}


/*
|--------------------------------------------------------------------------
| AJAX Helper (Reserved)
|--------------------------------------------------------------------------
|
| Currently MCEIoT does not use AJAX.
| This helper is kept for future expansion.
|
*/

async function apiRequest(
    url,
    options = {}
) {

    const response =
        await fetch(
            url,
            options
        );


    return await response.json();

}


/*
|--------------------------------------------------------------------------
| Number Formatting
|--------------------------------------------------------------------------
*/

function formatNumber(
    number
) {

    return Number(number)
        .toLocaleString();

}
/*
|--------------------------------------------------------------------------
| Date Formatting
|--------------------------------------------------------------------------
*/

function formatDate(
    dateString
) {

    if (!dateString) {

        return '-';

    }


    const date =
        new Date(
            dateString
        );


    if (isNaN(date)) {

        return '-';

    }


    return date.toLocaleString();

}


/*
|--------------------------------------------------------------------------
| Disable Submit Button
|--------------------------------------------------------------------------
*/

function disableSubmitButton(
    button
) {

    if (!button) {

        return;

    }


    button.disabled = true;

    button.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> Processing...';

}


/*
|--------------------------------------------------------------------------
| Initialize Application
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const forms =
            document.querySelectorAll(
                'form'
            );


        forms.forEach(
            function (form) {

                form.addEventListener(
                    'submit',
                    function () {

                        const button =
                            form.querySelector(
                                'button[type="submit"]'
                            );


                        if (button) {

                            disableSubmitButton(
                                button
                            );

                        }

                    }
                );

            }
        );


    }
);

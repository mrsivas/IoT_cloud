<?php
declare(strict_types=1);


require_once __DIR__ . '/../includes/bootstrap.php';


requireLogin();


$id = (int)($_GET['id'] ?? 0);


if ($id <= 0) {

    setFlash(
        'danger',
        'Invalid device.'
    );

    redirect(
        'index.php'
    );

}



$stmt = $pdo->prepare("

    SELECT

        d.*,

        u.full_name AS owner_name

    FROM devices d

    LEFT JOIN users u

        ON u.id = d.owner_id

    WHERE

        d.id = ?

        AND d.deleted_at IS NULL

    LIMIT 1

");


$stmt->execute([
    $id
]);


$device = $stmt->fetch();



if (!$device) {

    setFlash(
        'danger',
        'Device not found.'
    );

    redirect(
        'index.php'
    );

}




/*
|--------------------------------------------------------------------------
| Recent Logs
|--------------------------------------------------------------------------
*/


$stmt = $pdo->prepare("

    SELECT

        value,

        created_at

    FROM device_logs

    WHERE device_id = ?

    ORDER BY id DESC

    LIMIT 20

");


$stmt->execute([
    $id
]);


$logs = $stmt->fetchAll();



$pageTitle =
    $device['device_name'];



include INCLUDE_PATH . '/header.php';

?>








<div class="container-xl" style="margin-top: 42px;">


<div class="row g-3">


    <!-- Device Status Card -->


    <div class="col-md-3">


        <div class="card text-white bg-primary">


            <div class="card-body">


                <div class="d-flex justify-content-between">


                    <div>


                        <h6 class="text-uppercase">

                            Device

                        </h6>


                        <h5>

                            <?= e($device['device_name']) ?>

                        </h5>


                    </div>


                    <i class="cil-devices display-5"></i>


                </div>


            </div>


        </div>


    </div>




    <!-- Current Value -->


    <div class="col-md-3">


        <div class="card text-white bg-success">


            <div class="card-body">


                <div class="d-flex justify-content-between">


                    <div>


                        <h6 class="text-uppercase">

                            Current Value

                        </h6>


                        <h5>

                            <?= e($device['last_value'] ?? '-') ?>

                        </h5>


                    </div>


                    <i class="cil-speedometer display-5"></i>


                </div>


            </div>


        </div>


    </div>




    <!-- Status -->


    <div class="col-md-3">


        <div class="card text-white <?= $device['status'] ? 'bg-success' : 'bg-danger' ?>">


            <div class="card-body">


                <div class="d-flex justify-content-between">


                    <div>


                        <h6 class="text-uppercase">

                            Status

                        </h6>


                        <h5>


<?php if ((int)$device['status'] === 1): ?>


                            Online


<?php else: ?>


                            Offline


<?php endif; ?>


                        </h5>


                    </div>


                    <i class="cil-check-circle display-5"></i>


                </div>


            </div>


        </div>


    </div>





    <!-- Last Seen -->


    <div class="col-md-3">


        <div class="card text-white bg-secondary">


            <div class="card-body">


                <div class="d-flex justify-content-between">


                    <div>


                        <h6 class="text-uppercase">

                            Last Seen

                        </h6>


                        <h6>

                            <?= e(
                                $device['last_seen'] ?? '-'
                            ) ?>

                        </h6>


                    </div>


                    <i class="cil-clock display-5"></i>


                </div>


            </div>


        </div>


    </div>



</div>

<div class="row mt-4">


    <!-- Device Information -->


    <div class="col-lg-4">


        <div class="card">


            <div class="card-header">


                <strong>

                    Device Information

                </strong>


            </div>


            <div class="card-body">


                <table class="table table-sm mb-0">


                    <tr>

                        <th>

                            UUID

                        </th>


                        <td>

                            <?= e($device['device_uuid']) ?>

                        </td>

                    </tr>



                    <tr>

                        <th>

                            Type

                        </th>


                        <td>

                            <?= e($device['device_type']) ?>

                        </td>

                    </tr>



                    <tr>

                        <th>

                            Owner

                        </th>


                        <td>

                            <?= e($device['owner_name'] ?? '-') ?>

                        </td>

                    </tr>



                    <tr>

                        <th>

                            Created

                        </th>


                        <td>

                            <?= e($device['created_at']) ?>

                        </td>

                    </tr>



                </table>


            </div>


        </div>


    </div>





    <!-- Chart -->


    <div class="col-lg-8">


        <div class="card">



<div class="card-header">

    <div class="d-flex justify-content-between align-items-center">

        <strong>
            Sensor History
        </strong>


        <select
            id="chartRange"
            class="form-select form-select-sm"
            style="width:150px;">


            <option value="1h">
                Last 1 Hour
            </option>


            <option value="6h">
                Last 6 Hours
            </option>


            <option value="24h">
                Last 24 Hours
            </option>


            <option value="today">
                Today
            </option>


            <option value="7d">
                Last 7 Days
            </option>

            <option value="All" selected>
                All
            </option>

        </select>
        
        <div class="form-check form-switch ms-3">

    <input
        class="form-check-input"
        type="checkbox"
        id="autoRefresh">

    <label
        class="form-check-label"
        for="autoRefresh">

        Auto Refresh

    </label>

</div>


    </div>

</div>


            <div class="card-body">


               <?php if($device['device_type']==='BOOLEAN'): ?>

<div id="booleanStatus"
class="text-center p-4">

</div>

<?php else: ?>

<canvas id="deviceChart"></canvas>

<?php endif; ?>


            </div>


        </div>


    </div>



</div>

<div class="row mt-4">


    <!-- Recent Data Logs -->


    <div class="col-12">


        <div class="card">


            <div class="card-header">


                <strong>

                    Recent Data

                </strong>


            </div>


            <div class="card-body p-0">


                <div class="table-responsive">


                    <table class="table table-striped mb-0">


                        <thead>


                            <tr>


                                <th>

                                    Value

                                </th>


                                <th>

                                    Received Time

                                </th>


                            </tr>


                        </thead>



                        <tbody>


<?php if (empty($logs)): ?>


                            <tr>


                                <td
                                    colspan="2"
                                    class="text-center text-muted">


                                    No data available.


                                </td>


                            </tr>


<?php else: ?>


<?php foreach ($logs as $log): ?>


                            <tr>


                                <td>


                                    <?= e($log['value']) ?>


                                </td>


                                <td>


                                    <?= e($log['created_at']) ?>


                                </td>


                            </tr>


<?php endforeach; ?>


<?php endif; ?>


                        </tbody>


                    </table>


                </div>


            </div>


        </div>


    </div>



</div>


</div>


<script>

let deviceChart = null;

let refreshTimer = null;


document
.getElementById('autoRefresh')
.addEventListener(
    'change',
    function(){

        if(this.checked){

            refreshTimer = setInterval(
                function(){

                    let range =
                    document.getElementById('chartRange').value;

                    loadChart(range);
                    //alert( loadChart(range));

                },
                10000
            );

        }
        else{

            clearInterval(refreshTimer);

        }

    }
);

function loadChart(range = 'All') {


    fetch(
        '<?= BASE_URL ?>/api/device-history.php?device=<?= e($device['device_uuid']) ?>&range=' + range
    )


    .then(response => response.json())


.then(result => {


    if (!result.success) {

        return;

    }


    const deviceType =
        result.device_type;


    const labels =
        result.data.map(
            item => item.created_at
        );


    const values =
        result.data.map(
            item => item.value
        );


    let chartValues =   values;



    if (deviceType === 'BOOLEAN') {


        chartValues =
            values.map(
                value => Number(value)
            );


    }



    const ctx =
        document.getElementById(
            'deviceChart'
        );



    if (deviceChart) {

        deviceChart.destroy();

    }


if(deviceType === 'BOOLEAN'){


let latest =
result.data[result.data.length-1];


document.getElementById(
'booleanStatus'
).innerHTML =

latest.value == 1

?

'<h1>🟢 ON</h1>'

:

'<h1>🔴 OFF</h1>';

return;


}

    deviceChart = new Chart(
        ctx,
        {

            type: 'line',

            data: {

                labels: labels,

                datasets: [

                    {

                        label: 'Value',

                        data: chartValues,

                        tension: 0.3

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false

            }

        }

    );


});


}



document.addEventListener(
    'DOMContentLoaded',
    function () {


        loadChart('All');



        document
        .getElementById('chartRange')
        .addEventListener(
            'change',
            function () {


                loadChart(
                    this.value
                );


            }
        );


    }
);




</script>



<?php

include INCLUDE_PATH . '/footer.php';

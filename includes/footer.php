<?php
declare(strict_types=1);
?>


       <!--  BEGIN FOOTER  -->
        <footer class="footer footer-transparent d-print-none">
          <div class="container-xl">
            <div class="row text-center align-items-center flex-row-reverse">
              <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                  <li class="list-inline-item"><a href="https://docs.tabler.io" target="_blank" class="link-secondary" rel="noopener">Mookambigai College of Engineering | All Rights Reserved</a></li>
                  
                </ul>
              </div>
              <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                  <li class="list-inline-item">
                    Copyright © <?= date('Y') ?>
                    <a href="." class="link-secondary"><?= e(APP_NAME) ?></a>.
                  </li>
                  <li class="list-inline-item">
                    <a href="#" class="link-secondary" rel="noopener"> v<?= e(APP_VERSION) ?> </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </footer>
        <!--  END FOOTER  -->
      </div>




</div>




<!-- CoreUI JS -->

<script
src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/js/coreui.bundle.min.js">
</script>



<!-- Application JS -->

<script
src="/assets/js/app.js">
</script>

<?php if (!empty($pageScripts)): ?>


<?php foreach ($pageScripts as $script): ?>


<script src="<?= e($script) ?>"></script>


<?php endforeach; ?>


<?php endif; ?>




<script>


document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | Auto Close Alerts
        |--------------------------------------------------------------------------
        */


        const alerts =
            document.querySelectorAll(
                '.alert'
            );


        alerts.forEach(
            function (alertElement) {


                setTimeout(
                    function () {


                        if (
                            typeof coreui !== 'undefined'
                        ) {


                            const alert =
                                coreui.Alert
                                .getOrCreateInstance(
                                    alertElement
                                );


                            alert.close();


                        }


                    },
                    5000
                );


            }
        );




        /*
        |--------------------------------------------------------------------------
        | Delete Confirmation
        |--------------------------------------------------------------------------
        */


        const confirmButtons =
            document.querySelectorAll(
                '[data-confirm]'
            );


        confirmButtons.forEach(
            function(button){


                button.addEventListener(
                    'click',
                    function(event){


                        if (
                            !confirm(
                                button.dataset.confirm
                            )
                        ){

                            event.preventDefault();

                        }


                    }
                );


            }
        );


    }
);


</script>


  <script
    src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>


</body>

</html>


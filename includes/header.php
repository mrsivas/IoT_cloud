<?php
declare(strict_types=1);

if (!isset($pageTitle)) {
    $pageTitle = APP_NAME;
}

$flash = flash();


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
    <?= e($pageTitle) ?> | <?= e(APP_NAME) ?>
    </title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
    <script
    src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js">
    </script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    
    
    
<!-- BEGIN PAGE LEVEL STYLES -->
  <link href="https://preview.tabler.io/dist/libs/jsvectormap/dist/jsvectormap.css" rel="stylesheet" />
  <!-- END PAGE LEVEL STYLES -->
  <!-- BEGIN GLOBAL MANDATORY STYLES -->
  <link href="/assets/css/tabler.min.css" rel="stylesheet"/>
  <!-- END GLOBAL MANDATORY STYLES -->
  <!-- BEGIN PLUGINS STYLES -->

  <link href="/assets/css/tabler-themes.min.css" rel="stylesheet"/>
  <!-- END PLUGINS STYLES -->
  <!-- BEGIN DEMO STYLES -->
  <link href="/assets/css/demo.min.css" rel="stylesheet"/>
  <!-- END DEMO STYLES -->
  <!-- BEGIN CUSTOM FONT -->
  <style>
    @import url("https://rsms.me/inter/inter.css");
  </style>
  <!-- END CUSTOM FONT -->  

</head>

<body>

 <script src="/assets/js/tabler-theme.min.js" integrity="sha384-seGicidLI2dtSkO/7bmCkGNGdmTXMlIaoSz+W+lmvL4ZoQRPmJNh0wb2XSsbNYBv"></script> 



<div class="page">





      <div class="page-wrapper">


<!-- old --->


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




<?php include INCLUDE_PATH . '/sidebar.php'; ?>



<?php include INCLUDE_PATH . '/navbar.php'; ?>
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
    <?= e($flash['message']) ?>
    </div>
<?php endif; ?>



<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title><?php echo $title; ?></title>
    <meta name="Description" content="" />
    <meta name="Author" content="Łukasz Jakubowski - SMP" />
    <meta name="Author" content="Michał Sztefanica - SMP" />
    <meta name="Keywords" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/png" href="img/icon.png">
    <link rel="stylesheet" type="text/css" href="<?php echo $mainDir?>css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $mainDir?>css/fa.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $mainDir?>css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $mainDir?>css/bootstrap-grid.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $mainDir?>css/bootstrap-reboot.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $mainDir; ?>css/bootstrap-toggle.min.css"/>

    

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">

    <script src="<?php echo $mainDir?>js/bootstrap.min.js"></script>
    <script src="<?php echo $mainDir?>js/popper.min.js"></script>
    <script src="<?php echo $mainDir?>js/jquery-3.3.1.slim.min.js"></script>
    <script src="<?php echo $mainDir?>js/vue.js"></script>
</head>

<body>
    <?php if($_SESSION['signedIn']){ ?>
    <div class="user_menu">
        <?php include('userMenu.html.php'); ?>
    </div>
    <?php } ?>
    <div class="content_box">
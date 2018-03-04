<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8" />
  <title>Digicel</title>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="css/animate.css" type="text/css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="css/icon.css" type="text/css" />
  <link rel="stylesheet" href="css/font.css" type="text/css" />
  <link rel="stylesheet" href="css/app.css" type="text/css" />
  <link rel="stylesheet" href="js/chosen/chosen.css" type="text/css" />
  <link rel="stylesheet" href="js/datepicker/datepicker.css" type="text/css" />
  <link rel="stylesheet" href="js/datatables/datatables.css" type="text/css" />
  <link rel="stylesheet" href="js/datatables/buttons.dataTables.min.css" type="text/css" />

  <!-- <link rel="stylesheet" href="js/datatables/datatables.css" type="text/css"/> -->

  <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
  <style type="text/css">
    .chosen-container{
      width: 100% !important;
    }
  </style>
</head>
<body class="" >
  <section class="vbox">
    <header class="bg-white header header-md navbar navbar-fixed-top-xs box-shadow">
      <div class="navbar-header aside-md dk">
        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html">
          <i class="fa fa-bars"></i>
        </a>
        <a href="home.php" class="navbar-brand">
          <img src="logo/1.png" class="m-r-sm" alt="scale">
          <span class="hidden-nav-xs">Digicel</span>
        </a>
        <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".user">
          <i class="fa fa-cog"></i>
        </a>
      </div>
      <?php /* Bloqueo de pagina de carga */ ?>
      <style media="screen">
      .loader {
          position: fixed;
          left: 0px;
          top: 0px;
          width: 100%;
          height: 100%;
          z-index: 9999;
          background-color: #000;
          opacity: .2;
        }
      </style>
    </header>
    <?php /* Bloqueo de pagina de carga */ ?>
    <div style="display:none;" class="loader" id="llamado" >
      <div style="width: 200px; margin: 0 auto; margin-top:23%; opacity:1; z-index: 99999;">
        <img style="opacity:1; z-index: 999999;" src="images/1.png" alt="">
      </div>
    </div>
    <section>
      <section class="hbox stretch">
      <?php
        if(isset($_SESSION['USER_ID']))
          include("left-nav.php"); ?>

<?php
  include("constant.php");
?>
<!DOCTYPE html>
<html lang="en" class=" ">
<head>  
  <meta charset="utf-8" />
  <title>Scale | Web Application</title>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
  <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="css/animate.css" type="text/css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="css/icon.css" type="text/css" />
  <link rel="stylesheet" href="css/font.css" type="text/css" />
  <link rel="stylesheet" href="css/app.css" type="text/css" />  
    <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
</head>
<body class="" >
  <section id="content" class="m-t-lg wrapper-md animated fadeInUp">    
    <div class="container aside-xl">
      <section class="m-b-lg">
        <header class="wrapper text-center">
          <strong><?php print C_LOGIN?></strong>
        </header>
        <form action="index.html">
          <div class="list-group">
            <div class="list-group-item">
              <input type="email" placeholder="Email" class="form-control no-border">
            </div>
            <div class="list-group-item">
               <input type="password" placeholder="Password" class="form-control no-border">
            </div>
          </div>
          <button type="submit" class="btn btn-lg btn-primary btn-block">Sign in</button>
          <div class="text-center m-t m-b"><a href="#"><small>Forgot password?</small></a></div>
          
        </form>
      </section>
    </div>
  </section>
  
  <script src="js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="js/bootstrap.js"></script>
  <!-- App -->
  <script src="js/app.js"></script>  
  <script src="js/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="js/app.plugin.js"></script>
</body>
</html>
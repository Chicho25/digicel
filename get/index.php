<?php
    ob_start();
    session_start();
    include("include/config.php");
    include("include/defs.php");

    // it will never let you open index(login) page if session is set
     if(isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] !="")
     {
          header("Location: home.php");
          exit;
     }


    $errMSG="";


     if( isset($_POST['btn-login']) ) {

        $username = $_POST['username'];
        $password = encryptIt($_POST['password']);
        $username = strip_tags(trim($username));
        $password = strip_tags(trim($password));


        if(RecCount("users", "user = '".$username."' and password = '".$password."' and stat = 1"))
        {

            $row = GetRecord("users", "user = '".$username."' and password = '".$password."' and stat = 1");
            $getRole = GetRecord("type_user", "id_user = ".$row['id_roll_user']." and stat = 1");

            $_SESSION['USER_ID'] = $row['id'];
            $_SESSION['USER_NAME'] = $row['user'];
            $_SESSION['USER_ROLE'] = $getRole['name_type_user'];

            /* Log de Actividad */
            if(isset($_POST['ingreso'])){
              $mensaje = "El Usuario: ".$_SESSION['USER_NAME']." ha ingresado al sistema ";
              log_actividad(1, 6, $_SESSION['USER_ID'], $mensaje);
            }
            /* Fin de log de actividad */

            header("Location: home.php");

        }
        else
          $errMSG = '<div class="alert alert-danger"><a href="#" class="close" style="color:#000;" data-dismiss="alert">&times;</a><strong>Invalid Email or Password, Try again...!</strong></div>';
     }
?>
<!DOCTYPE html>
<html lang="en" class=" ">
<head>
  <meta charset="utf-8" />
  <title>dCHAIN</title>
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
      <a class="navbar-brand block">Login</a>
      <section class="m-b-lg">
        <header class="wrapper text-center">
          <strong>dChain Get</strong>
        </header>
        <form name="frmForm" method="post" action="index.php">
          <div class="list-group">
            <div class="list-group-item">
              <input type="text" placeholder="Username" name="username" class="form-control no-border">
            </div>
            <div class="list-group-item">
               <input type="password" placeholder="Password" name="password" class="form-control no-border">
            </div>
          </div>
          <input type="hidden" name="ingreso" value="1">
          <button type="submit" name="btn-login" class="btn btn-lg btn-primary btn-block">Sign in</button>
          <div class="text-center m-t m-b"><a href="#"><small>Forgot password?</small></a></div>

        </form>
      </section>
    </div>
  </section>
  <!-- footer -->

  <!-- / footer -->
  <script src="js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="js/bootstrap.js"></script>
  <!-- App -->
  <script src="js/app.js"></script>
  <script src="js/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="js/app.plugin.js"></script>
</body>
</html>

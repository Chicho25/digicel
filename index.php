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
        $password = $_POST['password'];
        $password = encryptIt($_POST['password']);
        $username = strip_tags(trim($username));
        $password = strip_tags(trim($password));

        if(RecCount("users", "user_name = '".$username."' and pass = '".$password."' and stat = 1") > 0)
        {
          echo "paso";
            $row = GetRecord("users", "user_name = '".$username."' and pass = '".$password."' and stat = 1");
            $_SESSION['USER_ID'] = $row['id'];
            $_SESSION['USER_NAME'] = $row['user_name'];
            $_SESSION['TYPE_USER'] = $row['type_user'];
            header("Location: home.php");
        }
        else
          $errMSG = '<div class="alert alert-danger"><a href="" class="close" style="color:#000;" data-dismiss="alert">&times;</a><strong><b>Email o password Incorrectos, intenta de nuevo...!</b></strong></div>';
     }
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8">
        <title>Facturas Digicel</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- CSS -->
        <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
        <link rel="stylesheet" href="css/animate.css" type="text/css" />
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=PT+Sans:400,700'>
        <link rel="stylesheet" href="assets/css/reset.css">
        <link rel="stylesheet" href="assets/css/supersized.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
      <?php echo $errMSG; ?>
        <div class="page-container">
            <h1>Digicel</h1>
            <form action="" method="post">
                <input type="text" name="username" class="username" placeholder="Usuario">
                <input type="password" name="password" class="password" placeholder="Contraseña">
                <button type="submit" class="btn btn-primary" name="btn-login">Ingresar</button>
                <div class="error"><span>+</span></div>
            </form>
        </div>
        <!-- Javascript -->
        <script src="js/bootstrap.js"></script>
        <script src="assets/js/jquery-1.8.2.min.js"></script>
        <script src="assets/js/supersized.3.2.7.min.js"></script>
        <script src="assets/js/supersized-init.js"></script>
        <script src="assets/js/scripts.js"></script>
    </body>
</html>

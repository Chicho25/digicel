<?php
    ob_start();
    $userclass="class='active'";
    $registerclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']) && $loggdUType != "Admin" && $loggdUType != "Business Unit Manager")
     {
          header("Location: index.php");
          exit;
     }
     $message="";

    if(isset($_POST['submitUser']))
     {
        $USERNAME = $_POST['username'];
        $FIRSTNAME = $_POST['name'];
        $LASTNAME = $_POST['lastname'];
        $password = encryptIt($_POST['password']);
        $usertype = $_POST['usertype'];
        $email = $_POST['email'];
        $ifUserExist = RecCount("users", "user = '".$USERNAME."' or Email = '".$email."'");
        if($ifUserExist > 0)
        {
          $message = '<div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Username or email already exist!</strong>
            </div>';
        }
        else
        {
            $arrVal = array(
                          "user" => $USERNAME,
                          "Name" => $FIRSTNAME,
                          "Last_name" => $LASTNAME,
                          "password" => $password,
                          "Email" => $email,
                          "id_roll_user" => $usertype,
                          "create_date" => date("Y-m-d H:i:s"),
                          "stat" => 0
                         );

          $nId = InsertRec("users", $arrVal);


          foreach ($_POST['category_business'] as $key => $value) {

          $arrVal = array(
                        "id_category" => $value,
                        "id_user" => $nId,
                        "stat" => 1
                       );

          $nId_category = InsertRec("acces_category", $arrVal);

        }


          /* Log de Actividad */
          $mensaje = "Se ha registrado un usuario. usuario: '".$USERNAME."' Nombre: '".$FIRSTNAME."' '".$LASTNAME."' ";
          log_actividad(3, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {
              if(isset($_POST['country']) && $_POST['country'] != '')
              {
                $arrLoc = array(
                                "id_user" => $nId,
                                "id_roll_user" => $usertype,
                                "id_region" => $_POST['region'],
                                "id_territory" => $_POST['territory']
                              );
                InsertRec("location_user", $arrLoc);
              }
              MySQLQuery("Insert into master_notes (id) values (0)");
              $mstId = mysql_insert_id();
              $verificationCode = md5(uniqid("yourrandomstringyouwanttoaddhere", true));
              UpdateRec("users", "id = ".$nId, array("id_ref_master_note" => $mstId, "activationkey" => $verificationCode));

              if($mstId > 0)
              {
                $notemsg = "Nuevo Usuario ".$USERNAME." Registrado por ".$_SESSION['USER_NAME'];
                $subj = "User ( ".$USERNAME." )";
                create_log($mstId, $subj, $notemsg);
              }

              if(isset($_FILES['photo']) && $_FILES['photo']['tmp_name'] != "")
              {
                  $target_dir = "photos/";
                  $target_file = $target_dir . basename($_FILES["photo"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["photo"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

                      UpdateRec("users", "id = ".$nId, array("Image" => $filenameThumb));
                  }
              }

              /* Email */

              $actual_link = "http://".$_SERVER['HTTP_HOST']."/active.php?key=" . $verificationCode;

              /*$admin_email = "smccape@gmail.com";*/
              $admin_email = "dev@dchain.com";
              $subject = "dChain GET: Cuenta Creada";
              $headers = "MIME-Version: 1.0\\r\ ";
              $headers .= "Content-type: text/html; charset=iso-8859-1\\r\ ";
              $headers .= "From: ".$admin_email;
              $comment = " Hola ".$USERNAME.",\n\n tu cuenta fue creada. Tu contraseña es ".$_POST['password'].". Has click en el siguiente enlase. " . $actual_link . " ";
              /*$send = mail($email, "$subject", $comment, $headers);*/

              include("mailjet/src/Mailjet/php-mailjet-v3-simple.class.php");

              $apiKey = '16ecb7873995588027a5cef50f59b719';
              $secretKey = '06e6276f1fe3249498c103b601869f58';

              $mj = new Mailjet($apiKey, $secretKey);
              if (isset($_POST['submitUser'])) {

                  function sendEmail($email, $subject, $comment, $admin_email) {
                      // Create a new Object
                      $mj = new Mailjet();
                      $params = array(
                          "method" => "POST",
                          "from" => "{$admin_email}",
                          "to" => "{$email}",
                          "subject" => "{$subject}",
                          "text" => "{$comment}"
                      );
                      $result = $mj->sendEmail($params);
                      if ($mj->_response_code == 200) {
                          //echo "success - email sent";
                          print '<script type="text/javascript">';
                          print 'alert("email successfully sent!")';
                          print '</script>';
                      } elseif ($mj->_response_code == 400) {
                          //echo "error - " . $mj->_response_code;
                          print '<script type="text/javascript">';
                          print 'alert("Bad Request! One or more arguments are missing or maybe mispelling.")';
                          print '</script>';
                      } elseif ($mj->_response_code == 401) {
                          //echo "error - " . $mj->_response_code;
                          print '<script type="text/javascript">';
                          print 'alert("Unauthorized! You have specified an incorrect ApiKey or username/password couple.")';
                          print '</script>';
                      } elseif ($mj->_response_code == 404) {
                          //echo "error - " . $mj->_response_code;
                          print '<script type="text/javascript">';
                          print 'alert("Not Found! The method your are trying to reach don\'t exists.")';
                          print '</script>';
                      } elseif ($mj->_response_code == 405) {
                          //echo "error - " . $mj->_response_code;
                          print '<script type="text/javascript">';
                          print 'alert("Method Not Allowed! You made a POST request instead of GET, or the reverse.")';
                          print '</script>';
                      } else {
                          print '<script type="text/javascript">';
                          print 'alert(" Internal Server Error! Status returned when an unknow error occurs")';
                          print '</script>';
                      }

                      return $result;
                  }

                  sendEmail($email, $subject, $comment, $admin_email);
              }

              $message = '<div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>User created successfully</strong>
                          </div>';
              }
          }
     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Registro de usuario</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de usuario</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre de usuario" name="username" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Apellido</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Apellido" name="lastname" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Password</label>
                            <div class="col-lg-4">
                              <input type="password" class="form-control" placeholder="Password" name="password" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Email</label>
                            <div class="col-lg-4">
                              <input type="email" class="form-control" placeholder="Email" name="email" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Imagen</label>
                            <div class="col-lg-4">
                                <div style="width:204px;
                                            height:154px;
                                            background-color: #cccccc;
                                            border: solid 2px gray;
                                            margin: 5px;">
                                    <img id="img" src="#" style='width:200px; height:150px;display: none;' alt="your image" />
                                </div>
                                <label class="btn yellow btn-default">
                                  Cargar Foto <input type="file" name="photo" style="display: none;" onchange="readURL(this);">
                                </label>
                            </div>
                          </div>
                          <script type="text/javascript">
                            function ocultar(){
                            var x = document.getElementById("select").value;
                            if(x == 1){
                            document.getElementById("no_mostrar").style.display="none";
                            }else{
                            document.getElementById("no_mostrar").style.display="block";
                              }
                            }
                          </script>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label font-bold">Roll de usuario</label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" id="select" name="usertype" required="required" onChange="ocultar()">
                                    <option value="">Seleccionar</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from type_user where stat = 1");
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id_user'];
                                      $kinDesc = $value['name_type_user'];
                                    ?>
                                    <option value="<?php echo $kinId?>"><?php echo $kinDesc?></option>
                                    <?php } ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required" id="no_mostrar">
                              <label class="col-lg-4 text-right control-label font-bold">Categorias</label>
                              <div class="col-lg-4">
                                <select style="width:260px" multiple class="chosen-select" name="category_business[]" <?php if($_SESSION['USER_ROLE']=="Business Unit Manager"){ ?> disabled <?php } ?>>
                                  <?php $arrcat = GetRecords("Select * from category_business where stat = 1"); ?>
                                  <?php foreach ($arrcat as $key => $value) { ?>
                                  <option value="<?php echo $value['id']; ?>" <?php if($_SESSION['USER_ROLE']=="Business Unit Manager"){ echo optener_category_roll($_SESSION['USER_ID'], $value['id']); } ?> ><?php echo $value['description']; ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>País</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="country" required="required"  onChange="getOptionsData(this.value, 'regionbycountry', 'region');">
                                    <option value="">------------</option>
                                    <?PHP
                                        $arrKindMeetings = GetRecords("Select * from country where stat = 1");
                                        foreach ($arrKindMeetings as $key => $value) {
                                          $kinId = $value['id'];
                                          $kinDesc = $value['name'];
                                          $selRoll = (isset($_GET['cid']) && $_GET['cid'] == $kinId) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $kinId?>" <?php echo $selRoll;?>><?php echo $kinDesc?></option>
                                        <?php
                                        }
                                        ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>Region</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="region" id="region" required="required"  onChange="getOptionsData(this.value, 'territorybyregion', 'territory');">
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>Territorio</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="territory" id="territory" required="required"  >
                                  </select>
                              </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Registrar</button>
                        </footer>
                      </section>
                    </form>
                  </div>
              </div>
            </section>
        </section>
    </section>
    <script type="text/javascript">
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img').show().attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
  </script>
<?php
	include("footer.php");
?>

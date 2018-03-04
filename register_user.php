<?php
    ob_start();
    session_start();
    $userclass="class='active'";
    $registerclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: logout.php");
          exit;
     }

     include("include/config.php");
     include("include/defs.php");
     include("header.php");
     $message="";

    if(isset($_POST['submitUser']))
     {
        $USERNAME = $_POST['username'];
        $FIRSTNAME = $_POST['name'];
        $LASTNAME = $_POST['lastname'];
        $password = encryptIt($_POST['password']);
        $usertype = $_POST['usertype'];
        $email = $_POST['email'];
        $ifUserExist = RecCount("users", "user_name = '".$USERNAME."'");
        if($ifUserExist > 0)
        {
          $message = '<div class="alert alert-danger">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>El nombre de usuario ya existe!</strong>
                      </div>';
        }
        else
        {
            $arrVal = array(
                          "user_name" => $USERNAME,
                          "Name" => $FIRSTNAME,
                          "last_name" => $LASTNAME,
                          "pass" => $password,
                          "Email" => $email,
                          "type_user" => $usertype,
                          "stat" => 1,
                          "user_register" => $_SESSION['USER_ID']
                         );

          $nId = InsertRec("users", $arrVal);

          if($nId > 0)
          {
              if(isset($_FILES['photo']) && $_FILES['photo']['tmp_name'] != "")
              {
                  $target_dir = "photos/";
                  $target_file = $target_dir . basename($_FILES["photo"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["photo"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 300, 300);

                      UpdateRec("users", "id = ".$nId, array("photo" => $filenameThumb));
                  }
              }

              $message = '<div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Usuario Creado Con Exito!</strong>
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
                	<form class="form-horizontal" data-parsley-validate="" method="post" enctype="multipart/form-data">
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
                              <input type="text" class="form-control" placeholder="Nombre de usuario" name="username" data-required="true" required>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre" name="name" data-required="true" required>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Apellido</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Apellido" name="lastname" data-required="true" required>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Password</label>
                            <div class="col-lg-4">
                              <input type="password" class="form-control" placeholder="Password" name="password" data-required="true" required>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Email</label>
                            <div class="col-lg-4">
                              <input type="email" class="form-control" placeholder="Email" name="email" data-required="true" required>
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
                                  Cargar Foto <input type="file" name="photo" style="display: none;" onchange="readURL(this);" required>
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
                                  <select class="chosen-select form-control" id="select" name="usertype" required="">
                                    <option value="">Seleccionar</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from type_user where stat = 1");
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id'];
                                      $kinDesc = $value['descriptions'];
                                    ?>
                                    <option value="<?php echo $kinId?>"><?php echo $kinDesc?></option>
                                    <?php } ?>
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

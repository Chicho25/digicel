<?php

    ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']) || $loggdUType != "Admin")
     {
          header("Location: index.php");
          exit;
     }
     $message="";
    if(isset($_POST['submitUser']) && $_REQUEST['id'] > 0)
     {
        $USERNAME = $_POST['username'];
        $FIRSTNAME = $_POST['name'];
        $LASTNAME = $_POST['lastname'];
        $password = encryptIt($_POST['password']);
        $usertype = $_POST['usertype'];
        $email = $_POST['email'];
        $stval = (isset($_POST['status'])) ? 1 : 0;
        $ifUserExist = RecCount("users", "(user = '".$USERNAME."' or Email = '".$email."') and id <> ".$_REQUEST['id']);
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
                           "stat" => $stval
                         );

          UpdateRec("users", "id=".$_REQUEST['id'], $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha actualizado un usuario. usuario: '".$USERNAME."' Nombre: '".$FIRSTNAME."' '".$LASTNAME."' Fecha:".date("Y-m-d H:i:s");
            log_actividad(3, 2, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          $nId=$_REQUEST['id'];
          if($nId > 0)
          {
              $arrLoc = array(
                                "id_roll_user" => $usertype,
                                "id_region" => $_POST['region'],
                                "id_territory" => $_POST['territory']
                              );
              UpdateRec("location_user", "id_user = ".$nId, $arrLoc);
              $getrefid = GetRecord("users", "id = ".$_REQUEST['id']);
              $mstId = $getrefid['id_ref_master_note'];
              if($mstId > 0)
              {
                $notemsg = "User ( ".$USERNAME." )  updated by ".$_SESSION['USER_NAME'];
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

              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Usuario actualizado</strong>
                    </div>';

              header("location:user-view.php?id=".$nId);
          }
        }




     }

     $arrUser = GetRecord("users", "id = ".$_REQUEST['id']);
     $arrLocUser = GetRecord("location_user", "id_user = ".$_REQUEST['id']);
     $arrReg = GetRecord("region", "id = ".$arrLocUser['id_region']);
     $status = ($arrUser['stat'] == 1) ? 'checked' : '';

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data">
                      <input type="hidden" value="<?php echo $arrUser['id']?>" name="id">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Editar Usuario</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de usuario</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['user']?>" placeholder="User Name" name="username" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name" value="<?php echo $arrUser['Name']?>" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Apellido</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Last Name" value="<?php echo $arrUser['Last_name']?>" name="lastname" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Password</label>
                            <div class="col-lg-4">
                              <input type="password" class="form-control" placeholder="Password" value="<?php echo decryptIt($arrUser['password'])?>" name="password" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Email</label>
                            <div class="col-lg-4">
                              <input type="email" class="form-control" placeholder="Email" value="<?php echo $arrUser['Email']?>" name="email" data-required="true">
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
                                    <img id="img" src="<?php echo $arrUser['Image']?>" style='width:200px; height:150px;' " alt="your image" />
                                </div>
                                <label class="btn yellow btn-default">
                                  Cargar Foto <input type="file" name="photo" style="display: none;" onchange="readURL(this);">
                                </label>
                            </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label font-bold">Roll del usuario</label>
                              <div class="col-lg-4">
                                  <select class="form-control" name="usertype" required="required" onChange="mostrar(this.value);">
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from type_user");
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id_user'];
                                      $kinDesc = $value['name_type_user'];

                                      $selRoll = (isset($arrUser['id_roll_user']) && $arrUser['id_roll_user'] == $kinId) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $kinId?>" <?php echo  $selRoll?>><?php echo $kinDesc?></option>
                                    <?php
                                }
                                    ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>País</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select  form-control" name="country" required="required"  onChange="getOptionsData(this.value, 'regionbycountry', 'region');">
                                    <option value="">------------</option>
                                    <?PHP
                                        $arrKindMeetings = GetRecords("Select * from country where stat = 1");
                                        foreach ($arrKindMeetings as $key => $value) {
                                          $kinId = $value['id'];
                                          $kinDesc = $value['name'];
                                          $selRoll = (isset($arrReg['id_country']) && $arrReg['id_country'] == $kinId) ? 'selected' : '';
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
                                  <select class="chosen-select  form-control" name="region" id="region" required="required"  onChange="getOptionsData(this.value, 'territorybyregion', 'territory');">
                                    <option value="">------------</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from region where stat = 1 and id_country = ".$arrReg['id_country']);
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id'];
                                      $kinDesc = $value['name'];
                                      $selRoll = (isset($arrReg['id']) && $arrReg['id'] == $kinId) ? 'selected' : '';
                                    ?>
                                      <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                    <?php
                                     }
                                    ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>Territorio</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="territory" id="territory" required="required"  >
                                    <option value="">------------</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from territory where stat = 1 and id_region = ".$arrReg['id']);
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id'];
                                      $kinDesc = $value['name'];
                                      $selRoll = (isset($arrLocUser['id_territory']) && $arrLocUser['id_territory'] == $kinId) ? 'selected' : '';
                                    ?>
                                      <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                    <?php
                                     }
                                    ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 font-bold control-label">Activo/Deactivo</label>
                            <div class="col-lg-4">
                              <label class="switch">
                                <input type="checkbox" name="status" <?php echo $status?>>
                                <span></span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Actualizar</button>
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

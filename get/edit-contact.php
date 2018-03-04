<?php

    ob_start();
    $businessclass="class='active'";
    $editContclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
     $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }
     $message="";
    if(isset($_POST['submitUser']) && $_REQUEST['id'] > 0)
     {
        $name = $_POST['name'];
        $busid = $_POST['busid'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $description = $_POST['description'];
        $stval = (isset($_POST['status'])) ? 1 : 0;
        $arrVal = array(
                      "Name" => $name,
                      "id_business" => $busid,
                      "Phone" => $phone,
                      "Email" => $email,
                      "position" => $position,
                      "Description" => $description,
                      "stat" => $stval
                     );

          UpdateRec("contact", "id=".$_REQUEST['id'], $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha actualizado un contacto. Contacto: '".$name."' Fecha:".date("Y-m-d H:i:s");
            log_actividad(5, 2, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          $nId=$_REQUEST['id'];
          if($nId > 0)
          {
              $getrefid = GetRecord("contact", "id = ".$_REQUEST['id']);
              $mstId = $getrefid['id_ref_master_note'];
              if($mstId > 0)
              {
                $notemsg = "Contact ( ".$name." )  updated by ".$_SESSION['USER_NAME'];
                $subj = "Contact ( ".$name." )";
                create_log($mstId, $subj, $notemsg);
              }
              if(isset($_FILES['logo']) && $_FILES['logo']['tmp_name'] != "")
              {
                  $target_dir = "contactimage/";
                  $target_file = $target_dir . basename($_FILES["logo"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["logo"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

                      UpdateRec("contact", "id = ".$nId, array("Image" => $filenameThumb));
                  }
              }

              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Contact updated successfully</strong>
                    </div>';

              header("location:contact-view.php?id=".$_REQUEST['id']);
          }




     }

     $arrUser = GetRecord("contact", "id = ".$_REQUEST['id']);
     $status = ($arrUser['stat'] == 1) ? 'checked' : '';

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data" novalidate>
                      <input type="hidden" value="<?php echo $arrUser['id']?>" name="id">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Editar Contacto</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Seleccionar Empresa</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control"  name="busid" required="required">
                                <option value="">----------------</option>
                                <?PHP
                                $where = "";
                                 if($loggdUType != "Admin")
                                 {
                                   $where.=" and id_user_register=". $_SESSION['USER_ID'];
                                 }
                                $arrKindMeetings = GetRecords("Select * from business where stat = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name'];
                                  $selRoll = (isset($arrUser['id_business']) && $arrUser['id_business'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre </label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['Name']?>" placeholder="Nombre" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Logo</label>
                            <div class="col-lg-4">
                                <div style="width:200px;
                                            height:200px;
                                            background-color: #cccccc;
                                            border: solid 2px gray;
                                            margin: 5px;">
                                    <img id="img" src="<?php echo $arrUser['Image']?>" style='width:200px; height:200px;' alt="your image" />
                                </div>
                                <label class="btn yellow btn-default">
                                  Cargar Foto <input type="file" name="logo" style="display: none;" onchange="readURL(this);">
                                </label>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Teléfono</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Teléfono" value="<?php echo $arrUser['Phone']?>" name="phone" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Email</label>
                            <div class="col-lg-4">
                              <input type="email" class="form-control" placeholder="Email" value="<?php echo $arrUser['Email']?>" name="email" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="description" placeholder="Descripción"><?php echo $arrUser['Description']?></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Puesto en la empresa</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['position']?>" placeholder="Puesto en la empresa" name="position">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 font-bold control-label">Activo/Inactivo</label>
                            <div class="col-lg-4">
                              <label class="switch">
                                <input type="checkbox" name="status" <?php echo $status?>>
                                <span></span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Guardar</button>
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

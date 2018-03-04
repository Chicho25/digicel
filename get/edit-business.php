<?php

    ob_start();
    $businessclass="class='active'";
    $editBusnclass="class='active'";

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
        $rut = $_POST['rut'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $backgroundcompany = $_POST['backgroundcompany'];
        $url = $_POST['url'];
        $address = $_POST['address'];
        $stval = (isset($_POST['status'])) ? 1 : 0;
        $arrVal = array(
                      "Name" => $name,
                      "Rut" => $rut,
                      "Phone" => $phone,
                      "Email" => $email,
                      "Url" => $url,
                      "Address" => $address,
                      "ComanyBackground" => $backgroundcompany,
                      "Description" => $description,
                      "stat" => $stval
                     );

          UpdateRec("business", "id=".$_REQUEST['id'], $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha actulizado una empresa. Empresa: '".$name."' Fecha:".date("Y-m-d H:i:s");
            log_actividad(5, 2, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          $nId=$_REQUEST['id'];
          if($nId > 0)
          {
              $getrefid = GetRecord("business", "id = ".$_REQUEST['id']);
              $mstId = $getrefid['id_ref_master_note'];
              if($mstId > 0)
              {
                $notemsg = "Business ( ".$name." )  updated by ".$_SESSION['USER_NAME'];
                $subj = "Business ( ".$name." )";
                create_log($mstId, $subj, $notemsg);
              }
              if(isset($_FILES['logo']) && $_FILES['logo']['tmp_name'] != "")
              {
                  $target_dir = "logo/";
                  $target_file = $target_dir . basename($_FILES["logo"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["logo"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

                      UpdateRec("business", "id = ".$nId, array("Logo" => $filenameThumb));
                  }
              }

              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>La empresa fue actualizada!</strong>
                    </div>';
              header("location:business-view.php?id=".$_REQUEST['id']);
          }




     }

     $arrUser = GetRecord("business", "id = ".$_REQUEST['id']);
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
                          <span class="h4">Editar Empresa</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de la empresa</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['Name']?>" placeholder="Nombre de la empresa" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Ruc</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Ruc" value="<?php echo $arrUser['Rut']?>" name="rut">
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
                                    <img id="img" src="<?php echo $arrUser['Logo']?>" style='width:200px; height:150px;' alt="your image" />
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
                            <label class="col-lg-4 text-right control-label font-bold">Url de la empresa</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['Url']?>" placeholder="Url de la empresa" name="url">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Dirección</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="address" placeholder="Dirección"><?php echo $arrUser['Address']?></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Background de la empresa</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="backgroundcompany" placeholder="Background de la empresa"><?php echo $arrUser['ComanyBackground']?></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 font-bold control-label">Activa/Desactiva</label>
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

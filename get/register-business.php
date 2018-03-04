<?php
    ob_start();
    session_start();
    $businessclass="class='active'";
    $registerBusnclass="class='active'";

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

    if(isset($_POST['submitUser']))
     {
        $name = $_POST['name'];
        $rut = $_POST['rut'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $backgroundcompany = $_POST['backgroundcompany'];
        $url = $_POST['url'];
        $address = $_POST['address'];

        $arrVal = array(
                      "Name" => $name,
                      "Rut" => $rut,
                      "Phone" => $phone,
                      "Email" => $email,
                      "Url" => $url,
                      "Address" => $address,
                      "ComanyBackground" => $backgroundcompany,
                      "Description" => $description,
                      "create_date" => date("Y-m-d H:i:s"),
                      "id_user_register" => $_SESSION['USER_ID'],
                      "stat" => 1
                     );

          $nId = InsertRec("business", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado una empresa. Empresa: '".$name."'";
            log_actividad(5, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {

              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("business", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New business ".$name." registered by ".$_SESSION['USER_NAME'];
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
                      <strong>Empresa registrada correctamente</strong>
                    </div>';
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
                          <span class="h4">Registrar Empresa</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de la Empresa</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre de la Empresa" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">RUC</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="RUC" name="rut">
                            </div>
                          </div>
                          <div class="form-group ">
                              <label class="col-lg-4 text-right control-label font-bold">Logo</label>
                              <div class="col-lg-4">
                                  <input type="file" name="logo" class="form-control">
                                </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Teléfono</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Teléfono" name="phone" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Email</label>
                            <div class="col-lg-4">
                              <input type="email" class="form-control" placeholder="Email" name="email">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="description" placeholder="Descripción"></textarea>
                            </div>
                          </div>
                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">Url de la Empresa</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Url de la Empresa" name="url">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Dirección</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="address" placeholder="Dirección"></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Background de la empresa</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="backgroundcompany" placeholder="Background de la empresa<"></textarea>
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

<?php
	include("footer.php");
?>

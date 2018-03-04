<?php
    set_time_limit(0);
    ob_start();
    $productclass="class='active'";
    $registerProdclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']) || $loggdUType != "Admin" && $loggdUType != "Business Unit Manager")
     {
          header("Location: index.php");
          exit;
     }
     $message="";

    if(isset($_POST['submitUser']))
     {
        $cname = $_POST['name'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $code = $_POST['code'];
        $training = (isset($_POST['training'])) ? 1 : 0;
            $arrVal = array(
                          "name" => $cname,
                          "description" => $description,
                          "id_category" => $category,
                          "code"  => $code,
                          "training"  => $training,
                          "stat" => 1
                         );

          $nId = InsertRec("product", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado un producto. Producto: '".$cname."'";
            log_actividad(6, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {
              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("product", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New Product ( ".$cname." ) registered by ".$_SESSION['USER_NAME'];
                $subj = "Product ( ".$cname." )";
                create_log($mstId, $subj, $notemsg);
              }

              // if(isset($_FILES['image']) && $_FILES['image']['tmp_name'] != "")
              // {
              //     $target_dir = "product/image/";
              //     $target_file = $target_dir . basename($_FILES["image"]["name"]);
              //     $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
              //     $filename = $target_dir . $nId.".".$imageFileType;
              //     $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
              //     if (move_uploaded_file($_FILES["image"]["tmp_name"], $filename))
              //     {
              //         makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

              //         UpdateRec("product", "id = ".$nId, array("image" => $filenameThumb));
              //     }
              // }

              // if(isset($_FILES['attachment']) && $_FILES['attachment']['tmp_name'] != "")
              // {
              //     $target_dir = "product/attachment/";
              //     $target_file = $target_dir . basename($_FILES["attachment"]["name"]);
              //     $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
              //     $filename = $target_dir . $nId.".".$imageFileType;
              //     $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
              //     if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $filename))
              //     {
              //         makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

              //         UpdateRec("product", "id = ".$nId, array("attachment" => $filenameThumb));
              //     }
              // }
              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>El producto fue registrado</strong>
                    </div>';
          }
          else
          {


            $message = '<div class="alert alert-danger">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>El producto no fue registrado</strong>
                  </div>';
          }



     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post" enctype="multipart/form-data">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Registrar Producto</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Seleccionar Categoria</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="category" data-required="true" >
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from category where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];

                                  $selRoll = (isset($_GET['cid']) && $_GET['cid'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre del Producto</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre del Producto" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <textarea cols="40" class="form-control" rows="5" name="description" data-required="true" placeholder="Descripción"></textarea>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Código</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Código" name="code" data-required="true">
                            </div>
                          </div>
                          <!-- <div class="form-group required">
                              <label class="col-lg-4 text-right control-label font-bold">Image</label>
                              <div class="col-lg-4">
                                  <input type="file" data-required="true" name="image" class="form-control">
                                </div>
                          </div> -->
                          <!-- <div class="form-group required">
                              <label class="col-lg-4 text-right control-label font-bold">Attached Document</label>
                              <div class="col-lg-4">
                                  <input type="file" data-required="true" name="attachment" class="form-control">
                                </div>
                          </div> -->
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Requiere Entrenamiento ?</label>
                            <div class="col-lg-4">
                              <label class="checkbox-inline i-checks">
                                <input type="checkbox" name="training" value="option1"><i></i>
                              </label>
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

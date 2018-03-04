<?php

    ob_start();
    $productclass="class='active'";
    $editProdclass="class='active'";

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
        $cname = $_POST['name'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $code = $_POST['code'];
        $training = (isset($_POST['training'])) ? 1 : 0;
        $stval = (isset($_POST['status'])) ? 1 : 0;
        $arrVal = array(
                      "name" => $cname,
                      "description" => $description,
                      "id_category" => $category,
                      "code"  => $code,
                      "training"  => $training,
                      "stat" => $stval
                     );

          UpdateRec("product", "id=".$_REQUEST['id'], $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha actualizado un producto. Producto: '".$cname."' Fecha:".date("Y-m-d H:i:s");
            log_actividad(6, 2, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          $nId=$_REQUEST['id'];
          if($nId > 0)
          {
              $getrefid = GetRecord("product", "id = ".$_REQUEST['id']);
              $mstId = $getrefid['id_ref_master_note'];
              if($mstId > 0)
              {
                $notemsg = "Product ( ".$name." )  updated by ".$_SESSION['USER_NAME'];
                $subj = "Product ( ".$name." )";
                create_log($mstId, $subj, $notemsg);
              }
              if(isset($_FILES['image']) && $_FILES['image']['tmp_name'] != "")
              {
                  $target_dir = "product/image/";
                  $target_file = $target_dir . basename($_FILES["image"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["image"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

                      UpdateRec("product", "id = ".$nId, array("image" => $filenameThumb));
                  }
              }

              if(isset($_FILES['attachment']) && $_FILES['attachment']['tmp_name'] != "")
              {
                  $target_dir = "product/attachment/";
                  $target_file = $target_dir . basename($_FILES["attachment"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

                      UpdateRec("product", "id = ".$nId, array("attachment" => $filenameThumb));
                  }
              }

              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Producto Actualizado</strong>
                    </div>';

              header("location:product-view.php?id=".$_REQUEST['id']);
          }




     }

     $arrUser = GetRecord("product", "id = ".$_REQUEST['id']);
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
                          <span class="h4">Editar Producto</span>
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

                                  $selRoll = (isset($arrUser['id_category']) && $arrUser['id_category'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de el producto</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['name']?>" placeholder="Nombre de el producto" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <textarea cols="40" class="form-control" rows="5" name="description" data-required="true" placeholder="Descripción"><?php echo $arrUser['description']?></textarea>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Código</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['code']?>" placeholder="Código" name="code" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Imagen</label>
                            <div class="col-lg-4">
                                <div style="width:204px;
                                            height:154px;
                                            background-color: #cccccc;
                                            border: solid 2px gray;
                                            margin: 5px;">
                                    <img id="img" src="<?php echo $arrUser['image']?>" style='width:200px; height:150px;' " alt="your image" />
                                </div>
                                <label class="btn yellow btn-default">
                                  Cargar Foto <input type="file" name="image" style="display: none;" onchange="readURL(this, 'img');">
                                </label>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Adjunto</label>
                            <div class="col-lg-4">
                                <div style="width:204px;
                                            height:154px;
                                            background-color: #cccccc;
                                            border: solid 2px gray;
                                            margin: 5px;">
                                    <img id="att" src="<?php echo $arrUser['attachment']?>" style='width:200px; height:150px;' " alt="your image" />
                                </div>
                                <label class="btn yellow btn-default">
                                  Cargar Foto <input type="file" name="attachment" style="display: none;" onchange="readURL(this, 'att');">
                                </label>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Requiere Entrenamiento ?</label>
                            <div class="col-lg-4">
                              <label class="checkbox-inline i-checks">
                                <input type="checkbox" name="training" <?php echo $tr = ($arrUser['training'] == 1) ? 'checked' : ''?> value="option1"><i></i>
                              </label>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 font-bold control-label">Activo/Desactivo</label>
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
    function readURL(input, id) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#'+id).show().attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
  </script>
<?php
	include("footer.php");
?>

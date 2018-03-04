<?php

    ob_start();
    $productclass="class='active'";
    $editCateclass="class='active'";

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
        $cname = $_POST['cname'];
        $description = $_POST['description'];
        $stval = (isset($_POST['status'])) ? 1 : 0;
        $arrVal = array(
                      "name" => $cname,
                      "description" => $description,
                      "stat" => $stval
                     );

        UpdateRec("category", "id=".$_REQUEST['id'], $arrVal);

        /* Log de Actividad */
          $mensaje = "Se ha actualizado una categoria. Categoria: '".$cname."' Fecha:".date("Y-m-d H:i:s");
          log_actividad(6, 2, $_SESSION['USER_ID'], $mensaje);
        /* Fin de log de actividad */

        $nId=$_REQUEST['id'];

        $getRefId = GetRecord("category", "id = ".$_REQUEST['id']);
        $mstId = $getRefId['id_ref_master_note'];
        if($mstId > 0)
        {
          $notemsg = "Category ( ".$cname." ) updated by ".$_SESSION['USER_NAME'];
          $subj = "Category ( ".$cname." )";
          create_log($mstId, $subj, $notemsg);

        }

        $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>La categoria fue actualizada</strong>
                    </div>';

        header("location:category-view.php?id=".$_REQUEST['id']);
     }

     $arrUser = GetRecord("category", "id = ".$_REQUEST['id']);
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
                          <span class="h4">Editar Categoria</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de la Categoria</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php echo $arrUser['name']?>" placeholder="Nombre de la Categoria" name="cname" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <textarea cols="40" class="form-control" rows="5" name="description" data-required="true" placeholder="Descripción"><?php echo $arrUser['description']?></textarea>
                            </div>
                          </div>
                          <div class="form-group required">
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

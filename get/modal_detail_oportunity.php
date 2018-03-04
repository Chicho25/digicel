<?php
    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    $arrUser = GetRecords("select * from campaign where id='".$_GET['id_campaign']."' and stat = 1");

    $name = $arrUser[0]['name'];
    $date_start = $arrUser[0]['date_start'];
    $date_end = $arrUser[0]['date_end'];
    $category = $arrUser[0]['category'];
    $content_campaign = $arrUser[0]['content_campaign'];

?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form" method="post" action="#" enctype="multipart/form-data">

	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Detalle de la campaña</h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
            <section id="content">
                    <section class="vbox">
                    <header class="panel-heading">
                    </header>
                    <div class="panel-body">
                      <div class="form-group required">
                        <label class="col-lg-4 text-right control-label font-bold">Nombre</label>
                        <div class="col-lg-7">
                          <input type="text" readonly class="form-control" value="<?php echo $name; ?>">
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-lg-4 text-right control-label font-bold">Categoria</label>
                        <div class="col-lg-7">
                          <select disabled class="chosen-select form-control" name="busid" required="required" <?php if(isset($id_empre)){ echo 'readonly';} ?>>
                            <option value="">----------------</option>
                            <?PHP

                            $arrKindMeetings = GetRecords("Select * from category where stat = 1 $where");
                            foreach ($arrKindMeetings as $key => $value) {
                              $kinId = $value['id'];
                              $kinDesc = $value['name'];
                              $selRoll = (isset($category) && $category == $kinId) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                            <?php
                        }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group ">
                          <label class="col-lg-4 text-right control-label font-bold">Fecha Inicio</label>
                          <div class="col-lg-7">
                              <input type="text" name="logo" class="form-control" readonly value="<?php echo $date_start; ?>">
                          </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-lg-4 text-right control-label font-bold">Fecha Final</label>
                        <div class="col-lg-7">
                          <input type="text" class="form-control" readonly value="<?php echo $date_end; ?>">
                        </div>
                      </div>
                      <div class="form-group ">
                        <label class="col-lg-4 text-right control-label font-bold">Contenido</label>
                        <div class="col-lg-7">
                          <a href="download.php?file_campaign=<?php echo $content_campaign?>" title="Descargar Archivo">Descargar Contenido</a>
                        </div>
                      </div>
                    </div>
              </section>
			  </div>

		  </div>
	    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>
	      <!--<button type="submit"   name="submitContac" class="btn btn-primary">Ok</button>-->
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

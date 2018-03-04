<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

?>

<div class="modal-dialog">
  <div class="modal-content">

    <form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">

	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Aprobar Propuesta</h4>
	    </div>
	    <div class="modal-body">
        <div class="row">
		      <div class="form form-horizontal">
            <?php
            $sql_oportunidad = GetRecords("SELECT name, id_contact FROM opportunity WHERE id =".$_GET['id_oportunidad']);
                        foreach ($sql_oportunidad as $key => $l) {
                              $nombre_oportunidad=$l['name'];
                        }
             ?>
            <input type="hidden" name="id_propuesta" value="<?php echo $_GET['id']; ?>">
            <input type="hidden" name="email_cliente" value="<?php echo $_GET['email_cliente']; ?>">
            <input type="hidden" name="email_copia" value="<?php echo $_GET['email_copia']; ?>">
            <input type="hidden" name="nombre_oportunidad" value="<?php echo $nombre_oportunidad; ?>">
            <input type="hidden" name="file" value="<?php echo $_GET['file']?>">
            <input type="hidden" name="mensaje" value="<?php echo str_replace("_", " ", $_GET['menssajj']); ?>">
            <section id="content">
                <section class="vbox">
                <!--<header class="panel-heading">
                </header>-->
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-lg-10 text-center control-label font-bold"><h4>La propuesta será enviada al cliente, desea enviarla?</h4></label>
                  </div>
                  <div class="form-group">
                    <div class="checkbox i-checks">
                      <label class="col-lg-4 text-center control-label font-bold">
                        <input type="checkbox" name="send_customer" value="1"><i></i> Enviar al Cliente
                      </label>
                    </div>
                  </div>
                </div>

              </section>
            </section>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
            <button type="submit" name="aprobada" class="btn btn-primary">Enviar</button>
          </div>
        </div>
        </div>
        </form>
  </div>
</div>

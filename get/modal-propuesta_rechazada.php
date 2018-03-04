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
	      <h4 class="modal-title">Agregar un Mensaje</h4>
	    </div>
	    <div class="modal-body">
        <div class="row">
		      <div class="form form-horizontal">
            <input type="hidden" name="id_propuesta" value="<?php echo $_GET['id']; ?>">
            <section id="content">
                <section class="vbox">
                <header class="panel-heading">
                </header>
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-lg-2 text-right control-label font-bold">Mensaje</label>
                    <div class="col-lg-10">
                      <textarea rows="3" class="form-control" cols="44" name="messaje" placeholder="Mensaje"></textarea>
                    </div>
                  </div>
                </div>
              </section>
            </section>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
            <button type="submit"   name="empresa_modal" class="btn btn-primary">Crear</button>
          </div>
        </div>
        </div>
        </form>
  </div>
</div>

<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    //$loggdUType = current_user_type();

    /*if(isset($_GET['optid']) && $_GET['optid'] > 0)
    {
    	$getNote = GetRecord("opportunity_option", "id=".$_GET['optid']);
    	$optname = $getNote['optionName'];
    	$optdesc = $getNote['description'];
    	$optid = $_GET['optid'];
    	$htext = "Edit";
    }
    else
    {
    	$optname = "";
    	$optdesc = "";
    	$optid = "";
    	$htext = "Add";
    }*/
?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">

	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Agregar una Empresa</h4>
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
                  <label class="col-lg-4 text-right control-label font-bold">Nombre de la Empresa</label>
                  <div class="col-lg-7">
                    <input type="text" class="form-control" placeholder="Nombre de la Empresa" name="name" data-required="true">
                  </div>
                </div>
                <div class="form-group ">
                  <label class="col-lg-4 text-right control-label font-bold">RUC</label>
                  <div class="col-lg-7">
                    <input type="text" class="form-control" placeholder="RUC" name="rut">
                  </div>
                </div>
                <div class="form-group ">
                    <label class="col-lg-4 text-right control-label font-bold">Logo</label>
                    <div class="col-lg-7">
                        <input type="file" name="logo" class="form-control">
                      </div>
                </div>
                <div class="form-group required">
                  <label class="col-lg-4 text-right control-label font-bold">Teléfono</label>
                  <div class="col-lg-7">
                    <input type="text" class="form-control" placeholder="Teléfono" name="phone" data-required="true">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-4 text-right control-label font-bold">Email</label>
                  <div class="col-lg-7">
                    <input type="email" class="form-control" placeholder="Email" name="email">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                  <div class="col-lg-7">
                    <textarea rows="3" class="form-control" cols="44" name="description" placeholder="Descripción"></textarea>
                  </div>
                </div>
                <div class="form-group ">
                  <label class="col-lg-4 text-right control-label font-bold">Url de la Empresa</label>
                  <div class="col-lg-7">
                    <input type="text" class="form-control" placeholder="Url de la Empresa" name="url">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-4 text-right control-label font-bold">Dirección</label>
                  <div class="col-lg-7">
                    <textarea rows="3" class="form-control" cols="44" name="address" placeholder="Dirección"></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-4 text-right control-label font-bold">Background de la empresa</label>
                  <div class="col-lg-7">
                    <textarea rows="3" class="form-control" cols="44" name="backgroundcompany" placeholder="Background de la empresa<"></textarea>
                  </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
            <button type="submit"   name="empresa_modal" class="btn btn-primary">Crear</button>
          </div>
        </form>
        </section>

      </section>
    <div>

  </div>

</div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

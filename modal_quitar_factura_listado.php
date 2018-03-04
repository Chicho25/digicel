<?php

    include("include/config.php");
    include("include/defs.php");

    if(isset($_GET['N_PEDIDO'])){

    $arrUser = GetRecords("select * from FACTURAS where N_PEDIDO = '".$_GET['N_PEDIDO']."'");

       }
?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Eliminar de la lista </h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
                <h3 style="color:red;">Esta segur@ que quiere eliminar de la lista el pedido <?php echo $arrUser[0]['N_PEDIDO'];?></h3>
			    </div>
			  </div>
    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
        <button type="submit" class="btn btn-primary" name="eliminar_lista">Ok</button>
        <input type="hidden" name="n_pedido_list" value="<?php echo $arrUser[0]['N_PEDIDO']; ?>">
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

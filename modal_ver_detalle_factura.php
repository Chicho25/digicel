<?php

    include("include/config.php");
    include("include/defs.php");

    if(isset($_GET['N_PEDIDO'])){

    $arrUser = GetRecords("select * from DETALLE_FACTURA where N_PEDIDO = '".$_GET['N_PEDIDO']."'");

       }
?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action="">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Detalle de los Productos </h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
            <div class="table-responsive">
                <table class="table table-striped b-t b-light" data-ride="datatables">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Numero de Pedido</th>
                      <th>SKU</th>
                      <th>Cantidad</th>
                      <th>Precio Unitario</th>
                      <th>Descripcion</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?PHP
                    foreach ($arrUser as $key => $value) {
                    ?>
                  <tr>
                      <td class="tbdata"> <?php echo $value['ID']?>
                        <input type="hidden" value="<?php echo $value['ID']?>" name="id_detail[]">
                      </td>
                      <td class="tbdata"> <?php echo $value['N_PEDIDO']?>
                        <input type="hidden" value="<?php echo $value['N_PEDIDO']?>" name="n_pedido"></td>
                      <td class="tbdata"> <?php echo $value['SKU']?> </td>
                      <td class="tbdata"> <?php echo $value['CANTIDAD']?> </td>
                      <td class="tbdata"> <input class="form-control" value="<?php echo $value['PRECIO_UNITARIO']?>" name="precio_unitario[]"> </td>
                      <td class="tbdata"> <?php echo $value['DESCRIPCION']?> </td>
                  </tr>
                  <?php
                    }
                  ?>
                  </tbody>
                </table>
            </div>
			    </div>
			  </div>
    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
        <button type="submit" name="cambiar_precio" class="btn btn-primary">Cambiar Precio</button>
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

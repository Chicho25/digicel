<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $terrid = current_user_territory();
    $loggdUType = current_user_type();

    /*include("header.php");*/

      $arrKindMeetings = GetRecords("SELECT * FROM proposal WHERE id ='".$_GET['id']."'");
      foreach ($arrKindMeetings as $key => $value) {

?>

<div class="modal-dialog" id="myModal">

  <div class="modal-content">
  	<form class="form-horizontal" data-validate="parsley" method="post" id="frmProduct" enctype="multipart/form-data">
      <input type="hidden" name="id_propuesta" value="<?PHP echo $value['id']?>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Propuesta</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="form form-horizontal">
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Estado</label>
              <div class="col-lg-7">
                <span class="btn btn-<?php if($value['status']==1){ echo 'warning';}
                                                    elseif($value['status']==2){ echo 'success';}
                                                      elseif($value['status']==3){ echo 'danger';}?> btn-rounded font-bold"><?php if($value['status']==1){ echo 'Pendiente';}
                                                                          				              																		elseif($value['status']==2){ echo 'Aprobada';}
                                                                          				              																			elseif($value['status']==3){ echo 'Verificar';}?></span>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Correo</label>
              <div class="col-lg-7">
                <input type="text" class="form-control" name="email" id="qty" value="<?php echo $value['email']; ?>" data-required="true" placeholder="Correo" readonly>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Adjunto</label>
              <div class="col-lg-7">
                <a href="<?php echo $value['attached']; ?>">Adjunto</a>
              </div>
            </div>
            <?php /* ?>
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Categoría</label>
              <div class="col-lg-7">
                <?php

                        $categoria = GetRecords("SELECT
                                                  category.name as category,
                                                  product.name as product
                                                 FROM product inner join category on product.id_category = category.id
                                                 WHERE
                                                 product.id ='".$value['id_product']."'");
                        foreach ($categoria as $key => $cate) {

                                $categoria_1 = $cate['category'];
                                $product_1 = $cate['product'];

                        }

                 ?>
                <input type="text" class="form-control" name="email" id="qty" value="<?php echo $categoria_1; ?>" data-required="true" placeholder="Correo" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Producto</label>
              <div class="col-lg-7">
                <input type="text" class="form-control" name="email" id="qty" value="<?php echo $product_1; ?>" data-required="true" placeholder="Correo" readonly>
              </div>
            </div>*/ ?>
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Monto de la propuesta</label>
              <div class="col-lg-7">
                <input type="text" readonly class="form-control" value="<?php echo $value['amount'];?>" name="monto_propuesta" id="saleprice"  data-required="true" placeholder="Precio de Venta">
              </div>
            </div> <?php /*
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Cantidad</label>
              <div class="col-lg-7">
                <input type="text" readonly class="form-control" name="cantidad" id="qty" value="<?php echo $value['quantity']; ?>" data-required="true" placeholder="Cantidad">
              </div>
            </div>
            <?php */ ?>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Mensaje</label>
              <div class="col-lg-7">
                <textarea rows="7" readonly cols="40" class="form-control" name="mensaje" id="description" data-required="true" placeholder="Descripción"><?php echo $value['message']; ?></textarea>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Nota</label>
              <div class="col-lg-7">
                <textarea rows="7" readonly cols="40" class="form-control" name="nota" id="description"  data-required="true" placeholder="Nota"><?php echo $value['note']; ?></textarea>
              </div>
            </div>

            <?php if($value['rejection_message']!=""){ ?>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Comentarios</label>
              <div class="col-lg-7">
                <textarea rows="7" readonly cols="40" class="form-control" name="nota" id="description"  data-required="true" placeholder="Nota"><?php echo $value['rejection_message']; ?></textarea>
              </div>
            </div>
          <?php } ?>
        </div>

      </div>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
        <button type="button" onclick="validatProduct()" class="btn btn-primary">Enviar</button>
      </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<?php } ?>

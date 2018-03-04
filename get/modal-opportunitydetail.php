<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $terrid = current_user_territory();
    $loggdUType = current_user_type();

    /*include("header.php");*/

?>

<div class="modal-dialog" id="myModal">

  <div class="modal-content">
  	<form class="form-horizontal" data-validate="parsley" method="post" id="" enctype="multipart/form-data">
      <input type="hidden" name="optionid" value="<?PHP echo $_GET['oppid']?>">
      <input type="hidden" name="contactid" value="<?PHP echo $_GET['id_contact ']?>">
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
                <span class="btn btn-warning btn-rounded font-bold">Pendiente</span>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Correo</label>
              <div class="col-lg-7">
                <input type="text" class="form-control" name="email" id="" value="<?php echo $_GET['correo_contacto']; ?>" data-required="true" placeholder="Correo" readonly>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Adjunto</label>
              <div class="col-lg-7">
                <input type="file" class="form-control" name="attached" id=""  data-required="true" placeholder="Adjunto">
              </div>
            </div>
            <?php /* ?>
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Categoría</label>
              <div class="col-lg-7">
                <select class="form-control" name="category" id="category" data-required="true" onChange="getOptionsData(this.value, 'productbycategory', 'product');">
                          <option value="">------------------</option>
                          <?PHP
                          $where = "";
                           if($loggdUType != "Admin")
                           {
                             $catQuery = "Select category.* from category
                                          inner join product on product.id_category = category.id
                                          inner join product_by_territory on product_by_territory.id_product = product.id
                                          WHERE category.stat = 1 and product.stat = 1 and product_by_territory.id_territory = ".$terrid;

                             $readonlyPrice = "readonly='readonly'";
                           }
                           else
                           {
                              $catQuery = "Select * from category WHERE stat = 1 ";
                              $readonlyPrice = "";
                           }

                          $arrKindMeetings = GetRecords($catQuery);
                          foreach ($arrKindMeetings as $key => $value) {
                            $kinId = $value['id'];
                            $kinDesc = $value['name'];
                          ?>
                          <option value="<?php echo $kinId?>"><?php echo $kinDesc?></option>
                          <?php
                        }
                          ?>
                    </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Producto</label>
              <div class="col-lg-7">
                <select class="form-control" name="producto" onChange="getOptionsData(this.value, 'pricebyproductterritory', 'saleprice');" id="product" data-required="true" >
                </select>
              </div>
            </div>
            */ ?>
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Monto Propuesta</label>
              <div class="col-lg-7">
                <input type="number" class="form-control" name="monto_propuesta" id="saleprice" data-required="true" placeholder="Monto de la Propuesta">
              </div>
            </div> <?php /*
            <div class="form-group">
              <label class="col-lg-3 text-right control-label">Cantidad</label>
              <div class="col-lg-7">
                <input type="text" class="form-control" name="cantidad" id="qty"  data-required="true" placeholder="Cantidad">
              </div>
            </div>
            <?php */ ?>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Mensaje</label>
              <div class="col-lg-7">
                <textarea rows="7" cols="40" class="form-control" name="mensaje" id=""  data-required="true" placeholder="Descripción"></textarea>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Nota</label>
              <div class="col-lg-7">
                <textarea rows="7" cols="40" class="form-control" name="nota" id=""  data-required="true" placeholder="Nota"></textarea>
              </div>
            </div>
        </div>

      </div>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
        <button type="submit" class="btn btn-primary">Enviar</button>
      </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

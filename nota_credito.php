<?php
    ob_start();
    session_start();
    $fpiclass="class='active'";
    $ncclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: logout.php");
          exit;
     }

     include("include/config.php");
     include("include/defs.php");
     include("header.php");

     // Metodo Factura fiscal
     if(isset($_POST['n_pedido'])){

       $factura = GetRecords("SELECT * FROM FACTURAS WHERE N_PEDIDO = '".$_POST['n_pedido']."'");
       $factura_detalle = GetRecords("SELECT * FROM DETALLE_FACTURA WHERE N_PEDIDO = '".$_POST['n_pedido']."'");

     }

     if(isset($_POST['editar_nota'])){

        if($_POST['cantidad']){

            $factura = GetRecords("SELECT * FROM FACTURAS WHERE N_PEDIDO = '".$_POST['n_pedido']."'");

            foreach ($factura as $key => $value) {

                $arrFac = array(
                              "N_PEDIDO" => $value['N_PEDIDO'].'-NC',
                              "DEALER_NAME" => $value['DEALER_NAME'],
                              "RUC" => $value['RUC'],
                              "METODO_PAGO" => $value['METODO_PAGO'],
                              "STAT" => 6,
                              "SECUENCIA_FISCAL" => 0,
                              "TIPO_DOCUMENTO" => 1,
                              "MONTO_PAGO" => $value['MONTO_PAGO'],
                              "FECHA_REGISTRO" => date("m-d-Y H:i:s"),
                              "ID_USER_REGISTER" => $_SESSION['USER_ID'],
                              "FACTURA_REFERENCIA"=>$value['FACTURA_REFERENCIA']
                             );
                $nIdFacturaEd = InsertRec("FACTURAS", $arrFac);
            }

            $factura_detalle = GetRecords("SELECT * FROM DETALLE_FACTURA WHERE N_PEDIDO = '".$_POST['n_pedido']."'");

            foreach ($factura_detalle as $key => $value) {

                $arrFacDet = array(
                              "N_PEDIDO" => $value['N_PEDIDO'].'-NC',
                              "SKU" => $value['SKU'],
                              "CANTIDAD" => $_POST['cantidad'][$key],
                              "PRECIO_UNITARIO" => $value['PRECIO_UNITARIO'],
                              "STAT" => $value['STAT'],
                              "DESCRIPCION" => $value['DESCRIPCION']
                             );
                $nIdFacturaDetEd = InsertRec("DETALLE_FACTURA", $arrFacDet);
            }

            if ($nIdFacturaEd != '' && $nIdFacturaDetEd != '') {

                $message = '<div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                              <strong>Nota de credito Registrada Exito!</strong>
                            </div>';

                /* ################################### */
                include("print.php");
                $metodo = "printDocumentoFiscal";
                imprimir(1,$_POST['n_pedido'].'-NC',$metodo);
            }

        }else{
          $editar = 1;
        }
     }

     if(isset($_POST['print_direct'])){

       $factura = GetRecords("SELECT * FROM FACTURAS WHERE N_PEDIDO = '".$_POST['n_pedido']."'");

       foreach ($factura as $key => $value) {

           $arrFac = array(
                         "N_PEDIDO" => $value['N_PEDIDO'].'-NC',
                         "DEALER_NAME" => $value['DEALER_NAME'],
                         "RUC" => $value['RUC'],
                         "METODO_PAGO" => $value['METODO_PAGO'],
                         "STAT" => 6,
                         "SECUENCIA_FISCAL" => 0,
                         "TIPO_DOCUMENTO" => 1,
                         "MONTO_PAGO" => $value['MONTO_PAGO'],
                         "FECHA_REGISTRO" => date("m-d-Y H:i:s"),
                         "ID_USER_REGISTER" => $_SESSION['USER_ID'],
                         "FACTURA_REFERENCIA"=>$value['FACTURA_REFERENCIA']
                        );
           $nIdFactura = InsertRec("FACTURAS", $arrFac);
       }

       $factura_detalle = GetRecords("SELECT * FROM DETALLE_FACTURA WHERE N_PEDIDO = '".$_POST['n_pedido']."'");

       foreach ($factura_detalle as $key => $value) {

           $arrFacDet = array(
                         "N_PEDIDO" => $value['N_PEDIDO'].'-NC',
                         "SKU" => $value['SKU'],
                         "CANTIDAD" => $value['CANTIDAD'],
                         "PRECIO_UNITARIO" => $value['PRECIO_UNITARIO'],
                         "STAT" => $value['STAT'],
                         "DESCRIPCION" => $value['DESCRIPCION']
                        );
           $nIdFacturaDet = InsertRec("DETALLE_FACTURA", $arrFacDet);
       }

     }

     if ($nIdFactura != '' && $nIdFacturaDet != '') {

         $message = '<div class="alert alert-success">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>Nota de credito Registrada Exito!</strong>
                     </div>';

         /* ################################### */
         include("print.php");
         $metodo = "printDocumentoFiscal";
         imprimir(1,$_POST['n_pedido'].'-NC',$metodo);
     }

      /*$message = '<div class="alert alert-success">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Usuario Creado Con Exito!</strong>
                  </div>';*/ ?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <div class="row">
                <div class="col-sm-12">
                  <?php
                        if($message !="")
                            echo $message;
                  ?>
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Nota de Credito</span>
                        </header>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-lg-4 text-right control-label font-bold">Numero de Pedido</label>
                                <div class="col-lg-4">
                                  <form action="" method="post">
                                    <input type="text" name="n_pedido" value="" autocomplete="off">
                                    <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Buscar</button>
                                  </form>
                                </div>
                            </div>
                            <?php if (isset($_POST['n_pedido'])): ?>
                              <br>
                              <h3>Facturas</h3>
                                <table class="table table-striped b-t b-light">
                                  <thead>
                                    <tr>
                                      <th>ID</th>
                                      <th># Pedido</th>
                                      <th>Razon Social</th>
                                      <th>RUC</th>
                                      <th>Metodo de Pago</th>
                                      <th>Monto Pago</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?PHP
                                    foreach ($factura as $key => $value) {
                                    ?>
                                  <tr>
                                      <td class="tbdata"> <?php echo $value['ID']?> </td>
                                      <td class="tbdata"> <?php echo $value['N_PEDIDO']?> </td>
                                      <td class="tbdata"> <?php echo utf8_encode($value['DEALER_NAME'])?> </td>
                                      <td class="tbdata"> <?php echo $value['RUC']?> </td>
                                      <td class="tbdata"> <?php echo $value['METODO_PAGO']?> </td>
                                      <td class="tbdata"> <?php echo $value['MONTO_PAGO']?> </td>
                                  </tr>
                                  <?php } ?>
                                  </tbody>
                                </table>
                            <div class="table-responsive">
                              <h3>Productos</h3>
                              <form action="" method="post"  style="display:inline;">
                                <table class="table table-striped b-t b-light">
                                  <thead>
                                    <tr>
                                      <th>ID</th>
                                      <th># Pedido</th>
                                      <th>SKU</th>
                                      <th>Cantidad</th>
                                      <th>Precio Unitario</th>
                                      <th>Descripcion</th>
                                      <th>ISC ?</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?PHP
                                    foreach ($factura_detalle as $key => $value) {
                                    ?>
                                  <tr>
                                      <td class="tbdata"> <?php echo $value['ID']?> </td>
                                      <td class="tbdata"> <?php echo $value['N_PEDIDO']?> </td>
                                      <td class="tbdata"> <?php echo $value['SKU']?> </td>
                                      <td class="tbdata"> <?php if(isset($editar)){ ?> <input type="number" name="cantidad[]" value="<?php echo $value['CANTIDAD']; ?>"> <?php }else{ echo $value['CANTIDAD'];}?> </td>
                                      <td class="tbdata"> <?php echo $value['PRECIO_UNITARIO']?> </td>
                                      <td class="tbdata"> <?php echo utf8_encode($value['DESCRIPCION'])?> </td>
                                      <td class="tbdata"> <?php if($value['STAT'] == 5){ echo "Si";}else{ echo "No";}?></td>
                                  </tr>
                                  <?php
                                    }
                                  ?>
                                  </tbody>
                                </table>
                            </div>

                        </div>
                        <footer class="panel-footer text-right bg-light lter" >

                            <button type="submit" name="editar_nota" class="btn btn-primary btn-s-xs"><?php if(isset($editar)){ echo 'Guardar / Imprimir';}else{ echo 'Editar';} ?></button>
                            <input type="hidden" name="n_pedido" value="<?php echo $_POST['n_pedido'];?>" class="font-control">
                          </form>
                          <form action="" method="post"  style="display:inline;">
                            <button type="submit" name="print_direct" class="btn btn-primary btn-s-xs">Imprimir</button>
                            <input type="hidden" name="n_pedido" value="<?php echo $_POST['n_pedido'];?>" class="font-control">
                          </form>
                        </footer>
                          <?php endif; ?>
                      </section>
                  </div>
              </div>
            </section>
        </section>
    </section>
<?php
	include("footer.php");
?>

<?php
    ob_start();
    session_start();
    $reportclass="class='active'";
    $reportlistclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

    include("include/config.php");
    include("include/defs.php");

    include("header.php");
    $where = "where (1=1)";

     if(isset($_POST['n_pedido']) && $_POST['n_pedido'] != "")
     {
        $where.=" and  FACTURAS.N_PEDIDO LIKE '%".$_POST['n_pedido']."%'";
        $name = $_POST['n_pedido'];
     }
     if(isset($_POST['nombre_comerciante']) && $_POST['nombre_comerciante'] != "")
     {
        $where.=" and FACTURAS.DEALER_NAME LIKE '%".$_POST['nombre_comerciante']."%'";
        $lname = $_POST['nombre_comerciante'];
     }
     if(isset($_POST['ruc']) && $_POST['ruc'] != "")
     {
        $where.=" and FACTURAS.RUC LIKE '%".$_POST['ruc']."%'";
        $user = $_POST['ruc'];
     }

      $arrfactu = GetRecords("select
                              facturas.FACTURA_REFERENCIA,
                              facturas.SECUENCIA_FISCAL,
                              facturas.N_PEDIDO,
                              facturas.FECHA_REGISTRO,
                              facturas.DEALER_NAME,
                              facturas.RUC,
                              facturas.DESCRIPCION_PAGO,
                              facturas.METODO_PAGO,
                              sum(detalle_factura.cantidad) as cantidad,
                              sum(detalle_factura.precio_unitario) as precio_unitario
                               from facturas inner join detalle_factura on facturas.n_pedido = detalle_factura.n_pedido
                               $where
                               group by
                              facturas.N_PEDIDO,
                              facturas.FECHA_REGISTRO,
                              facturas.DEALER_NAME,
                              facturas.RUC,
                              facturas.DESCRIPCION_PAGO,
                              facturas.METODO_PAGO,
                              facturas.FACTURA_REFERENCIA,
                              facturas.SECUENCIA_FISCAL
                              "); ?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <?php
                      if($message !="")
                          echo $message;
                ?>
                <header class="panel-heading">
                    <span class="h4">Lista de Facturas por imprimir</span>
                </header>
                <div class="panel-body">
                  <?php /* ?>
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($name)){ echo $name;}?>" name="n_pedido" placeholder="Fecha Desde">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($lname)){ echo $lname;}?>" name="nombre_comerciante" placeholder="Fecha Hasta">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($user)){ echo $user;}?>" name="ruc" placeholder="Factura">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <?php */ ?>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th># Pedido</th>
                              <th>Fecha</th>
                              <th>Hora</th>
                              <th>Factura</th>
                              <th>Razon Social</th>
                              <th>Ruc</th>
                              <th>QTY</th>
                              <th>Descripcion</th>
                              <th>Unit Prec.</th>
                              <th>Net Prec.</th>
                              <th>Itbms</th>
                              <th>isc</th>
                              <th>Sub total</th>
                              <th>Total Pago</th>
                              <th>Metodo Pago</th>
                              <th>sec</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            foreach ($arrfactu as $key => $value) {

                            ?>
                          <tr>
                            <th><?php echo $value['N_PEDIDO']; ?></th>
                            <th><?php $date = date_create($value['FECHA_REGISTRO']);
                                      $fecha = date_format($date, 'd-m-Y');
                                      echo $fecha;?></th>
                            <th><?php $fecha = date_format($date, 'H:i:s');
                                      echo $fecha;?></th>
                            <th><?php echo $value['FACTURA_REFERENCIA']; ?></th>
                            <th><?php echo utf8_decode($value['DEALER_NAME']); ?></th>
                            <th><?php echo $value['RUC']; ?></th>
                            <th><?php echo $value['cantidad']; ?></th>
                            <th><?php echo $value['DESCRIPCION_PAGO']; ?></th>
                            <th><?php echo $value['precio_unitario']; ?></th>

                            <?php
                              $arrImpuesto = GetRecords("select cantidad * precio_unitario as monto,
                                                    		 ((cantidad * precio_unitario)*7/100) as itbms,
                                                         case
                                                    			when stat = 5
                                                    				then ((cantidad * precio_unitario)*5/100)
                                                    			else 0
                                                    			end as isc
                                                    		 from
                                                    		 detalle_factura
                                                         where
                                                         n_pedido = '".$value['N_PEDIDO']."'");

                             $monto=0;
                             $itbms=0;
                             $isc=0;
                            foreach ($arrImpuesto as $key => $valueIn):
                              $monto += $valueIn['monto'];
                              $itbms += $valueIn['itbms'];
                              $isc += $valueIn['isc'];
                            endforeach; ?>

                            <th><?php echo $monto; ?></th>
                            <th><?php echo $itbms; ?></th>
                            <th><?php echo $isc; ?></th>
                            <th><?php echo $monto + $itbms + $isc; ?></th>
                            <th><?php echo $monto + $itbms + $isc; ?></th>
                            <th><?php echo $value['METODO_PAGO']; ?></th>
                            <th><?php echo $value['SECUENCIA_FISCAL']; ?></th>
                          </tr>
                          <?php } ?>
                          </tbody>
                        </table>
                    </div>
                </div>
              </section>
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

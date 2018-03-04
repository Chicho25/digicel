<?php
    ob_start();
    session_start();
    $fpiclass="class='active'";
    $fpilistclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

     include("include/config.php");
     include("include/defs.php");

     if (isset($_POST['cambiar_precio'])) {

       $array_factura = array("PRECIO_UNITARIO"=>$_POST['precio_unitario']);

       UpdateRec("DETALLE_FACTURA", "N_PEDIDO = '".$_POST['n_pedido']."'", $array_factura);

       $message = '<div class="alert alert-danger">
                   <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                     <strong>Precio Unitario Modificado</strong>
                   </div>';
     }

     if (isset($_POST['eliminar_lista'])) {

       $array_factura = array("STAT" => 999,
                              "FECHA_REGISTRO"=>date("m-d-Y H:i:s"));

       UpdateRec("FACTURAS", "N_PEDIDO = '".$_POST['n_pedido_list']."'", $array_factura);

       $message = '<div class="alert alert-danger">
                   <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                     <strong>Pedido Quitado de la lista</strong>
                   </div>';
     }
     // Metodo Factura fiscal
     if(isset($_POST['n_factura'])){
       include("print.php");
       if ($_POST['n_factura'] != "") {
           $metodo = "printDocumentoFiscal";
       }
       $n_factura = imprimir("0", $_POST['n_factura'], $metodo);

       // imprimir nota de credito
       if(RecCount("DETALLE_FACTURA", "N_PEDIDO = '".$_POST['n_factura']."' and STAT = 5") > 0)
       {

          $factura_n_credito = $_POST['n_factura'].'-NC';

           $detalle_fact = GetRecordS("SELECT
                                          ID,
                                          N_PEDIDO,
                                          SKU,
                                          CANTIDAD,
                                          (precio_unitario * 5 / 100) AS ISC,
                                          STAT,
                                          DESCRIPCION
                                          FROM
                                          DETALLE_FACTURA
                                          WHERE
                                          N_PEDIDO = '".$_POST['n_factura']."'
                                          AND
                                          STAT = 5");

            foreach ($detalle_fact as $key => $valueDF) {
              $arrayNota_credito_detalle =  array("N_PEDIDO"=>$factura_n_credito,
                                          "SKU"=>$valueDF['SKU'],
                                          "CANTIDAD"=>$valueDF['CANTIDAD'],
                                          "precio_unitario"=>$valueDF['ISC'],
                                          "STAT"=>5,
                                          "DESCRIPCION"=>$valueDF['DESCRIPCION']);

                                          InsertRec("DETALLE_FACTURA", $arrayNota_credito_detalle);
            }

           $facturas = GetRecordS("SELECT * FROM FACTURAS WHERE N_PEDIDO = '".$_POST['n_factura']."' AND STAT = 3");

           foreach ($facturas as $key => $valueF) {
              $arrayNota_credito =  array("N_PEDIDO"=>$factura_n_credito,
                                          "DEALER_NAME"=>$valueF['DEALER_NAME'],
                                          "RUC"=>$valueF['RUC'],
                                          "METODO_PAGO"=>$valueF['METODO_PAGO'],
                                          "STAT"=>6,
                                          "TIPO_DOCUMENTO"=>1,
                                          "FECHA_REGISTRO"=>date("m-d-Y H:i:s"),
                                          "ID_USER_REGISTER"=>$_SESSION['USER_ID'],
                                          "FACTURA_REFERENCIA"=>$valueF['FACTURA_REFERENCIA']);

                                           InsertRec("FACTURAS", $arrayNota_credito);
           }

           imprimir("1", $factura_n_credito, $metodo);

          /* $isc = 0;
           foreach ($row as $key => $value) {
                    $isc += $value["ISC"];
           }*/
           /**/
       }

       $array_factura = array("STAT" => 4,
                              "SECUENCIA_FISCAL" => $n_factura,
                              "FECHA_REGISTRO"=>date("m-d-Y H:i:s"),
                              "FACTURA_REFERENCIA"=>$n_factura);

       UpdateRec("FACTURAS", "N_PEDIDO = '".$_POST['n_factura']."'", $array_factura);
       //echo 'despues del return'.$n_factura;
     }

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

      $arrUser = GetRecords("Select
                            	FACTURAS.ID,
                            	FACTURAS.N_PEDIDO,
                            	FACTURAS.DEALER_NAME,
                            	FACTURAS.RUC,
                            	FACTURAS.METODO_PAGO,
                            	FACTURAS.STAT,
                            	(SELECT SUM(PRECIO_UNITARIO) AS SUMA_PRECIO FROM DETALLE_FACTURA WHERE DETALLE_FACTURA.N_PEDIDO = FACTURAS.N_PEDIDO) AS SUMA_PRECIO,
                            	(SELECT SUM(CANTIDAD) AS CANTIDAD FROM DETALLE_FACTURA WHERE DETALLE_FACTURA.N_PEDIDO = FACTURAS.N_PEDIDO) AS SUMA_CANTIDAD
                            	from FACTURAS
                               $where
                               AND
                               FACTURAS.STAT = 3"); ?>
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
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($name)){ echo $name;}?>" name="n_pedido" placeholder="Numero de Pedido">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($lname)){ echo $lname;}?>" name="nombre_comerciante" placeholder="Nombre de Comerciante">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($user)){ echo $user;}?>" name="ruc" placeholder="Ruc">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th># Pedido</th>
                              <th>Razon Social</th>
                              <th>RUC</th>
                              <th>Metodo de Pago</th>
                              <th>Cantidad</th>
                              <th>Precio Unitario</th>
                              <th>Ver Detalle</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            foreach ($arrUser as $key => $value) {
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['ID']?> </td>
                              <td class="tbdata"> <?php echo $value['N_PEDIDO']?> </td>
                              <td class="tbdata"> <?php echo utf8_encode($value['DEALER_NAME'])?> </td>
                              <td class="tbdata"> <?php echo $value['RUC']?> </td>
                              <td class="tbdata"> <?php echo $value['METODO_PAGO']?> </td>
                              <td class="tbdata"> <?php echo $value['SUMA_CANTIDAD']?> </td>
                              <td class="tbdata"> <?php echo $value['SUMA_PRECIO']?> </td>
                              <td class="tbdata" style="width:150px;">
                                <form class="" action="" method="post">
                                  <a href="modal_ver_detalle_factura.php?N_PEDIDO=<?php echo $value['N_PEDIDO']?>" title="Detalle de Productos" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-primary"><i class="glyphicon glyphicon-eye-open"></i></a>
                                  <button type="submit" title="Imprimir" class="btn btn-sm btn-icon btn-primary"><i class="glyphicon glyphicon-print"></i></button>
                                  <a href="modal_quitar_factura_listado.php?N_PEDIDO=<?php echo $value['N_PEDIDO']?>" title="Eliminar pedido" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                                  <input type="hidden" name="n_factura" value="<?php echo $value['N_PEDIDO']; ?>">
                                </form>
                              </td>
                          </tr>
                          <?php
                            }
                          ?>
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

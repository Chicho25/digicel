<?php
ob_start();
session_start();

if(!isset($_SESSION['USER_ID']))
 {
      header("Location: index.php");
      exit;
 }

 include("include/config.php");
 include("include/defs.php");

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
                          where
                          FACTURAS.ID=".$_GET['N_PEDIDO']);

$html = '<h2>Pedido</h2>
        <table border="1">
          <thead>
            <tr>
              <th>ID</th>
              <th># Pedido</th>
              <th>Razon Social</th>
              <th>RUC</th>
              <th>Metodo de Pago</th>
              <th>Cantidad</th>
              <th>Precio Unitario</th>
            </tr>
          </thead>
          <tbody>';

            foreach ($arrUser as $key => $value) {

          $html.='<tr>
                    <td class="tbdata">'.$value['ID'].'</td>
                    <td class="tbdata">'.$value['N_PEDIDO'].'</td>
                    <td class="tbdata">'.utf8_encode($value['DEALER_NAME']).'</td>
                    <td class="tbdata">'.$value['RUC'].'</td>
                    <td class="tbdata">'.$value['METODO_PAGO'].'</td>
                    <td class="tbdata">'.$value['SUMA_CANTIDAD'].'</td>
                    <td class="tbdata">'.$value['SUMA_PRECIO'].'</td>
                  </tr>';
            }
          $html.='</tbody>
        </table>
        <h2>Productos</h2>';

        $arrUser2 = GetRecords("select * from DETALLE_FACTURA where N_PEDIDO = '".$arrUser[0]['N_PEDIDO']."'");

        $html.='<table border="1">
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
          <tbody>';

            foreach ($arrUser2 as $key => $value) {

          $html.='<tr>
              <td class="tbdata">'.$value['ID'].'</td>
              <td class="tbdata">'.$value['N_PEDIDO'].'</td>
              <td class="tbdata">'.$value['SKU'].'</td>
              <td class="tbdata">'.$value['CANTIDAD'].'</td>
              <td class="tbdata">'.$value['PRECIO_UNITARIO'].'</td>
              <td class="tbdata">'.$value['DESCRIPCION'].'</td>
          </tr>';
            }
          $html.='</tbody>
        </table>';


require_once('mpdf/mpdf.php');
$mpdf = new Mpdf(['mode' => 'c']);
$mpdf->WriteHTML($html);
$mpdf->Output();

 ?>

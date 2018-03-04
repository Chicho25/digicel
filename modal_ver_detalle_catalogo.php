<?php

    include("include/config.php");
    include("include/defs.php");

    if(isset($_GET['sku'])){

    $arrUser = GetRecords("select * from [PRODUCT_CATALOGUE].[dbo].[vw_Sku_Catalogue] where SKU = '".$_GET['sku']."'");

    $SKU = $arrUser[0]['SKU'];
    $Color = $arrUser[0]['Color'];
    $ID_Color = $arrUser[0]['ID_Color'];
    $Master_SKU = $arrUser[0]['Master_SKU'];
    $ID_Master_Sku = $arrUser[0]['ID_Master_Sku'];
    $ID_Model = $arrUser[0]['ID_Model'];
    $Model = $arrUser[0]['Model'];
    $ID_Brands = $arrUser[0]['ID_Brands'];
    $Brand = $arrUser[0]['Brand'];
    $Description = $arrUser[0]['Description'];
    $Category_Sku = $arrUser[0]['Category_Sku'];
    $ID_Cat_Sku = $arrUser[0]['ID_Cat_Sku'];
    $Origin_Sku = $arrUser[0]['Origin_Sku'];
    $Dummy_Sku = $arrUser[0]['Dummy_Sku'];
    $Tier = $arrUser[0]['Tier'];
    $Message = $arrUser[0]['Message'];
    $First_Cost = $arrUser[0]['First_Cost'];
    $Last_Cost = $arrUser[0]['Last_Cost'];
    $Price_Pre = $arrUser[0]['Price_Pre'];
    $Denominacion = $arrUser[0]['Denominacion'];
    $ID_Denominacion = $arrUser[0]['ID_Denominacion'];
    $Ventas_Minimas = $arrUser[0]['Ventas_Minimas'];
    $Status_Name = $arrUser[0]['Status_Name'];
    $ID_Status_Sku = $arrUser[0]['ID_Status_Sku'];
    $Corporativo = $arrUser[0]['Corporativo'];
       }
?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">

	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Detalle del Producto del catalogo </h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
			      <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">SKU</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $SKU; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Color</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Color; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Id Color</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $ID_Color; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Master SKU</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Master_SKU; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">ID Master SKU</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $ID_Master_Sku; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">ID modelo</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $ID_Model; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Modelo</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Model; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">ID Marca</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $ID_Brands; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Marca</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Brand; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Descripcion</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Description; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Categoria Sku</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Category_Sku; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">ID Categoria Sku</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $ID_Cat_Sku; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Origen Sku</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Origin_Sku; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Dummy SKU</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Dummy_Sku; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Nivel</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Tier; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Mensaje</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo utf8_encode($Message); ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Primer costo</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $First_Cost; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Ultimo Costo</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Last_Cost; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Precio</label>
              <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Price_Pre; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Denominacion</label>
               <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Denominacion; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">ID Denominacion</label>
               <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $ID_Denominacion; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Ventas Minimas</label>
               <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Ventas_Minimas; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Estatus Nombre</label>
               <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Status_Name; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">ID status SKU</label>
               <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $ID_Status_Sku; ?>
              </div>
            </div>
            <div class="form-group  ">
              <label class="col-lg-5 text-right control-label">Corporativo</label>
               <div class="col-lg-5 text-left control-label" style="text-align:left;">
                <?php echo $Corporativo; ?>
              </div>
            </div>
			      </div>
			  </div>
    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
	      
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

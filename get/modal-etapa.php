<?php

    //ob_start();
    /*$userclass="class='active'";
    $userlistclass="class='active'";*/

    include("include/config.php");
    include("include/defs.php");
    //$loggdUType = current_user_type();

    /*include("header.php");*/

?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">

	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Seleccionar Etapa </h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">

			      <div class="form-group required">
			        <label class="col-lg-3 text-right control-label">Etapa</label>
			        <div class="col-lg-7">
                <?php $source = $_GET['source']; ?>
                <input type="hidden" name="id_opportunity" value="<?php echo $_GET['etapa']; ?>">

                <select class="form-control" name="etapaNote" required="required">
	                        <?PHP
	                        $arrKindMeetings = GetRecords("select * from stage where status = 1");
	                        foreach ($arrKindMeetings as $key => $value) {
	                          $kinId = $value['id'];
	                          $kinDesc = $value['Name_stage'];
	                          $selType = (!empty($source) && $source == $kinId) ? 'selected' : '';
	                        ?>
	                        <option value="<?php echo $kinId?>" <?php echo $selType?>><?php echo utf8_encode($kinDesc)?></option>
	                        <?php
	                    	 }
	                        ?>
	               </select>

			          </div>
			      	</div>
			      </div>
			  </div>
    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
	      <button type="submit" class="btn btn-primary">Ok</button>
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<?php

    //ob_start();
    include("include/config.php");
    include("include/defs.php");

?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action=""   enctype="multipart/form-data">
  		<input type="hidden" value="<?php echo $_GET['id_suspect']?>" name="id_suspect">
      <input type="hidden" value="<?php echo $_GET['id_campaign']?>" name="id_campaign">
      <input type="hidden" value="<?php echo $_GET['master_note']?>" name="master_note">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Aprobar Oportunidad</h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
  		    <div class="form form-horizontal">
  			      <span style="margin-left:10px;">Precione "Ok" Para aprobar la oportunidad</span>
  			  </div>
  		  </div>
	    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
	      <button type="submit" name="submitcheckoportunity" class="btn btn-primary">Ok</button>
	    </div>
    </form>
    <script src="js/datepicker/bootstrap-datepicker.js"></script>
    <script src="js/app.plugin.js"></script>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<?php

    //ob_start();
    include("include/config.php");
    include("include/defs.php");

?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action=""   enctype="multipart/form-data">
  		<input type="hidden" value="<?php echo $_GET['id_suspect']?>" name="id_suspect">
      <input type="hidden" value="<?php echo $_GET['campaign']?>" name="id_campaign">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Verificar Sospechoso</h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
            <?php if(isset($_GET['verify']) && $_GET['verify'] == 0){ ?>
            <span style="color:red; margin-left:10px;">Faltan campos por llenar!</span>
              <?php }else{ ?>
			      <span style="margin-left:10px;">Presione "Ok" Para verificar al sospechoso</span>
            <?php } ?>
			  </div>
		  </div>
	    </div>
      <input type="hidden" name="note_master" value="<?php echo $_GET['id_master_note']; ?>" >
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
        <?php if(isset($_GET['verify']) && $_GET['verify'] == 0){ ?>
        <?php }else{ ?>
	      <button type="submit" name="submitcheck" class="btn btn-primary">Ok</button>
        <?php } ?>
      </div>
    </form>
    <script src="js/datepicker/bootstrap-datepicker.js"></script>
    <script src="js/app.plugin.js"></script>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

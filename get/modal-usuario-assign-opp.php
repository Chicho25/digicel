<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

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
	      <h4 class="modal-title">Seleccionar Business Manager </h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
            <input type="hidden" value="<?php echo $_GET['id_suspect'];?>" name="id_suspect">
            <input type="hidden" value="<?php echo $_GET['id_campaign']?>" name="id_campaign">
            <input type="hidden" value="<?php echo $_GET['master_note']?>" name="master_note">
			      <div class="form-group required">
			        <label class="col-lg-3 text-right control-label">Business Manager</label>
			        <div class="col-lg-7">
			          <select class="form-control" name="id_usuario" required="required" >
	                        <option value="">------------------</option>
	                        <?PHP
	                        $arrKindMeetings = GetRecords("Select * from users WHERE stat = 1 and id_roll_user = 3");
	                        foreach ($arrKindMeetings as $key => $value) {
	                          $kinId = $value['id'];
	                          $kinDesc = $value['Name']." ".$value['Last_name'];
	                        ?>
	                        <option value="<?php echo $kinId?>"><?php echo utf8_encode($kinDesc)?></option>
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
	      <button type="submit" name="submitUsuarioOpp" class="btn btn-primary">Ok</button>
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

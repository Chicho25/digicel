<?php

    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");

?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">
  		<input type="hidden" value="<?php echo $_GET['result']?>" name="id">
      <?php if(isset($_GET['id_result'])){ ?>
      <?php $notType = $_GET['id_result']; ?>
      <?php } ?>
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Resultado</h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
            <div class="form-group">
              <label class="col-lg-3 text-right control-label"></label>
              <div class="col-lg-7">
                <span style="color:red;"><?php if($result_check==0){ echo "No se ha llenado todos los campos";} ?></span>
              </div>
            </div>
			      <div class="form-group required">
              <label class="col-lg-3 text-right control-label">Resultado</label>
			        <div class="col-lg-7">
			          <select class="form-control" name="result" required="required" >
                      <option value="">------------------</option>
                      <?PHP
                      $arrKindMeetings = GetRecords("Select * from result WHERE stat = 1");
                      foreach ($arrKindMeetings as $key => $value) {
                        if($_GET['result_check']==0 && $value['id'] ==1){
                          continue;
                        }
                        $kinId = $value['id'];
                        $kinDesc = $value['name'];
                        $selType = (!empty($notType) && $notType == $kinId) ? 'selected' : '';
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
      <input type="hidden" name="nextid" value="<?php echo $_GET['id_master_note']; ?>" >
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
	      <button type="submit" name="submitResult" class="btn btn-primary">Ok</button>
	    </div>
    </form>
    <script src="js/datepicker/bootstrap-datepicker.js"></script>
    <script src="js/app.plugin.js"></script>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

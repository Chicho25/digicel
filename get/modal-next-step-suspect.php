<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    //$loggdUType = current_user_type();

    /*include("header.php");*/

    $iscall = (isset($_GET['call']) && $_GET['call'] == 1) ? true : false;
    if(isset($_GET['id']) && $_GET['id'] > 0)
    {
    	$getNote = GetRecord("note_detail", "id=".$_GET['id']);
    	$notType = $getNote['note_type'];
    	$note_subject = $getNote['note_subject'];
    	$note = $getNote['note'];
    	$attached = $getNote['attached'];
    	$noteid = $_GET['id'];
    	$htext = "Edit";
    }
    else
    {
    	$notType = "";
    	$note_subject = "";
    	$note = "";
    	$attached = "";
    	$noteid = "";
    	$htext = "Add";
    }
?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action=""   enctype="multipart/form-data">
  		<input type="hidden" value="<?php echo $_GET['next_step']?>" name="id">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Proximo Paso</h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
              <?php $notType=$_GET['next_step_id']; ?>
		      	  <?php if(!$iscall) : ?>
			      <div class="form-group required">
			        <label class="col-lg-3 text-right control-label">Proximo Paso</label>
			        <div class="col-lg-7">
			          <select class="form-control" name="nextstep" required="required" >
	                        <option value="">Seleccionar</option>
	                        <?PHP

	                        $arrKindMeetings = GetRecords("Select * from next_step_suspect WHERE stat = 1");
	                        foreach ($arrKindMeetings as $key => $value) {
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
			      <?php else: ?>
			      	<input type="hidden" value="5" name="notetype">
			      <?php endif; ?>
			      <div class="form-group required">
			        <label class="col-lg-3 text-right control-label">Fecha Proximo Paso</label>
			        <div class="col-lg-7">
			          <input class="form-control" type="date" id="datepicker" name="nextstepdate" size="16" data-date-format="yyyy-mm-dd" value="<?php echo $_GET['fecha']; ?>" >
			        </div>
			      </div>
            <div class="form-group required">
			        <label class="col-lg-3 text-right control-label">Hora Proximo Paso</label>
			        <div class="col-lg-7">
                <select class="form-control" name="hour" required="required" >
	                        <option value="">Seleccionar</option>
                          <?php $cero = 0; ?>
                          <?php $minu_0 = "00"; ?>
                          <?php $minu_3 = 30; ?>
                          <?PHP for($i=0;$i<=23;$i++){
                                if($i==0 || $i==1 || $i==2 || $i==3 || $i==4){
                                  continue;
                                }
                                $hora_select = $cero.$i.":".$minu_0;
                                $hora_select_3 = $cero.$i.":".$minu_3; ?>
	                        <option value="<?php if(strlen($i) == 1){ echo "0".$i.":00"; }else{ echo $i.":00"; } ?>" <?php if($_GET['hora']==$hora_select){ echo "selected";} ?> ><?php if(strlen($i) == 1){ echo "0".$i.":00"; }else{ echo $i.":00"; } ?></option>
                          <option value="<?php if(strlen($i) == 1){ echo "0".$i.":30"; }else{ echo $i.":30"; } ?>" <?php if($_GET['hora']==$hora_select_3){ echo "selected";} ?> ><?php if(strlen($i) == 1){ echo "0".$i.":30"; }else{ echo $i.":30"; } ?></option>
	                        <?php } ?>
	                </select>
			        </div>
			      </div>
			  </div>
		  </div>
	    </div>
      <input type="hidden" name="nextid" value="<?php echo $_GET['id_master_note']; ?>" >

	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
	      <button type="submit" name="submitNextstep" class="btn btn-primary">Ok</button>
	    </div>
    </form>

    <script src="js/datepicker/bootstrap-datepicker.js"></script>
    <script src="js/app.plugin.js"></script>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

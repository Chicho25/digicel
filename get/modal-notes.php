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
  		<input type="hidden" value="<?php echo $noteid?>" name="noteid">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">
          <?php if($iscall){ ?>
          <h4 class="modal-title">Agregar Llamada</h4>
          <?php }else{ ?>
          <h4 class="modal-title">Notas &amp; Adjuntos</h4>
          <?php } ?>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">

		      	  <?php if(!$iscall) : ?>
			      <div class="form-group required">
			        <label class="col-lg-3 text-right control-label">Tipo de Nota</label>
			        <div class="col-lg-7">
			          <select class="form-control" name="notetype" required="required" >
	                       <option value="">------------------</option>
	                        <?PHP
	                        $arrKindMeetings = GetRecords("Select * from type_note WHERE stat = 1 and Name_type_note <> 'Log'");
	                        foreach ($arrKindMeetings as $key => $value) {
                            if($value['id']==6 || $value['id']==5){ continue; }
	                          $kinId = $value['id'];
	                          $kinDesc = $value['Name_type_note'];
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
			        <label class="col-lg-3 text-right control-label">Asunto</label>
			        <div class="col-lg-7">
			          <input type="text" class="form-control" name="subject" value="<?php echo utf8_encode($note_subject)?>"  required="required" placeholder="Asunto">
			        </div>
			      </div>
			      <?php if(!$iscall) : ?>
			      <div class="form-group">
			          <label class="col-lg-3 control-label">Adjunto</label>
			          <div class="col-lg-7">
		                <input type="file" name="attached" class="form-control">
		              </div>
			      </div>
			      <?php endif; ?>
			      <div class="form-group required">
			        <label class="col-lg-3 text-right control-label">Nota</label>
			        <div class="col-lg-7">
			          <textarea rows="7" cols="40" class="form-control" name="note" required="required" placeholder="Nota"><?php echo utf8_encode($note)?></textarea>

                  <input type="hidden" name="master_note" value="<?php if(isset($_GET['master_note'])){ echo $_GET['master_note']; } ?>">
              </div>
			      </div>
			  </div>

		  </div>
	    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
	      <button type="submit" name="submitNote" class="btn btn-primary">Ok</button>
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    //$loggdUType = current_user_type();

    /*include("header.php");*/
    if(isset($_GET['optid']) && $_GET['optid'] > 0)
    {
    	$getNote = GetRecord("opportunity_option", "id=".$_GET['optid']);
    	$optname = $getNote['optionName'];
    	$optdesc = $getNote['description'];
    	$optid = $_GET['optid'];
    	$htext = "Edit";
    }
    else
    {
    	$optname = "";
    	$optdesc = "";
    	$optid = "";
    	$htext = "Add";
    }
?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form" method="post" action="" enctype="multipart/form-data">

      <input type="hidden" value="<?php echo $optid?>" name="optid">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title"><?php echo $htext?> Option</h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
			      <div class="form-group required">
	                <label class="col-lg-4 text-right control-label font-bold">Name Option</label>
	                <div class="col-lg-4">
	                  <input type="text" class="form-control" placeholder="Name Option" value="<?php echo $optname?>" name="optname" data-required="true">
	                </div>
	              </div>
	              <div class="form-group required">
	                <label class="col-lg-4 text-right control-label font-bold">Description</label>
	                <div class="col-lg-4">
	                  <textarea rows="3" class="form-control" cols="44" name="description" data-required="true" placeholder="Description"><?php echo $optdesc?></textarea>
	                </div>
	              </div>
			  </div>

		  </div>
	    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
	      <button type="submit"   name="submitOption" class="btn btn-primary">Ok</button>
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

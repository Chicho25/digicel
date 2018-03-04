<?php
	ob_start();
?>
<section class="panel panel-default">
    <header class="panel-heading">
      <span class="h4">Notes &amp; Attached</span>
      <span><a href="modal-notes.php" data-toggle="ajaxModal" class="btn btn-sm btn-primary">Add</a></span>
    </header>
    <div class="panel-body">
<?php
	$message="";
	
	if(isset($_POST['submitNote']))
	{
		$notetype = $_POST['notetype'];
        $note = $_POST['note'];
        $noteid = $_POST['noteid'];

            
        if($noteid == "")
        {
        	$arrVal = array(
                          "note_type" => $notetype,
                          "note" => $note,
                          "note_subject" => $subject,
                          "note_ref_id" => $userrefid,
                          "create_date" => date("Y-m-d H:i:s")
                         );
        	$nId = InsertRec("note_detail", $arrVal);  
        }
        else
        {
        	$nId = $noteid;
        	$arrVal = array(
                          "note_type" => $notetype,
                          "note" => $note,
                          "note_subject" => $subject
                         );

        	UpdateRec("note_detail", "id = ".$nId, $arrVal);
        }
        
        if($nId == 0)
        {
        	$message = 'Note not created!';
        }
        else
        {
        	if(isset($_FILES['attached']) && $_FILES['attached']['tmp_name'] != "")
	          {
	              $target_dir = "notes/";
	              $target_file = $target_dir . basename($_FILES["attached"]["name"]);
	              $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	              $filename = $target_dir . $nId.".".$imageFileType;
	              $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
	              if (move_uploaded_file($_FILES["attached"]["tmp_name"], $filename)) 
	              {
	                  makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);
	                  
	                  UpdateRec("note_detail", "id = ".$nId, array("attached" => $filenameThumb));
	              }
	          }
        	$message = 'Note created successfully!';
        }

        //echo '<script>window.location="'.$_SERVER['REQUEST_URI'].'"</script>';
	}
?>
		<ul class="list-group list-group-lg no-bg auto">                          
	          <?PHP
	            $arrKindMeetings = GetRecords("Select note_detail.*, type_note.Name_type_note from note_detail inner join type_note on type_note.id = note_detail.note_type where note_ref_id = ".$userrefid." order by note_detail.id desc");
	            foreach ($arrKindMeetings as $key => $value) {
	              //$kinId = $value['id_user'];
	              //$kinDesc = $value['name_type_user'];
	            ?>
		          <div class="col-sm-12 clearfix list-group-item clearfix">
			          <div class="col-sm-6">
				          <a href="modal-notes.php?id=<?php echo $value['id']?>" data-toggle="ajaxModal" >
				            <span class="clear">
				              <span class="i i-circle text-info-dk"></span>
				              <span><?php echo $value['note']?></span>
				              <small class="padder  text-muted clear text-ellipsis p"><?php echo date("m/d/Y", strtotime($value['create_date']))?></small>
				            </span>
				          </a>
				      </div>
				      <div class="col-sm-6">
				      	<?php
				      	if($value['attached'] != "") { ?>
				      		<a href="download.php?file=<?php echo $value['attached']?>" class="btn btn-sm btn-primary">Download</a>
				      	<?php } ?>
				      </div>    
				  </div>
		        <?PHP
		        }
		        ?>  
	     </ul>
    </div>
</section>  
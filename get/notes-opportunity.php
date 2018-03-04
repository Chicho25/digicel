<?php
	ob_start();
?>
<section class="panel panel-default">
    <header class="panel-heading">
      <span class="h4">Notas &amp; Proximo
				 Paso</span>
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
                          "create_date" => date("Y-m-d H:i:s"),
													"id_user" => $_SESSION['USER_ID']
                         );
        	$nId = InsertRec("note_detail", $arrVal);
        }
        else
        {
        	$nId = $noteid;
        	$arrVal = array(
                          "note_type" => $notetype,
                          "note" => $note,
                          "note_subject" => $subject,
													"id_user" => $_SESSION['USER_ID']
                         );

        	UpdateRec("note_detail", "id = ".$nId, $arrVal);
        }

        if($nId == 0)
        {
        	$message = 'La nota no fue creada!';
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

	                  UpdateRec("note_detail", "id = ".$nId, array("attached" => $filenameThumb, "filename" => basename($_FILES["attached"]["name"])));
	              }
	          }
        	$message = 'Nota creada con éxito!';
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
				              <span><?php echo utf8_encode($value['note'])?></span>
				              <?php if($value['create_date'] != "") : ?>
				              <small class="padder text-muted clear text-ellipsis p">
				              	<?php echo $value['create_date']; ?>

				              </small>
				          	  <?php endif;?>
				            </span>
				          </a>
				      </div>
				      <div class="col-sm-6">
				      	<?php
				      	if(isset($value['attached']) && $value['attached'] != "") {
				      		?>
				      		<a href="download.php?file=<?php echo $value['attached']?>" class="text-info"><?php echo $value['filename']?></a>
				      	<?php } ?>
				      </div>
				  </div>
		        <?PHP
		        }
		        ?>
	     </ul>
    </div>
</section>

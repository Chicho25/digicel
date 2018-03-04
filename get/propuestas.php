<?php
	ob_start();
?>
<section class="panel panel-default">
    <header class="panel-heading">
      <span class="h4">Estatus de las Propuestas</span>
    </header>
    <div class="panel-body">

		<ul class="list-group list-group-lg no-bg auto">
	          <?PHP
	            $arrKindMeetings = GetRecords("SELECT * FROM proposal WHERE id_oppottunity ='".$id_oportunidad."' ORDER BY id desc ");
	            foreach ($arrKindMeetings as $key => $value) { ?>
		          <div class="col-sm-12 clearfix list-group-item clearfix">
			          <div class="col-sm-6">
				          <a href="modal-propuesta.php?id=<?php echo $value['id']?>" data-toggle="ajaxModal" >
				            <span class="clear">
				              <span class="i i-circle text-<?php if($value['status']==1){ echo 'warning';}
				              																		elseif($value['status']==2){ echo 'success';}
				              																			elseif($value['status']==3){ echo 'danger';}?>">
				              </span>
				              <span><?php echo $value['note']?></span>
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
				      		<a href="download.php?file=<?php if($value['attached']==""){ }else{ echo $value['attached']; }?>" class="text-info">Adjunto</a>
				      	<?php } ?>
				      </div>
				  </div>
		        <?PHP
		        }
		        ?>
	     </ul>
    </div>
</section>

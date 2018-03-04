	<ul class="pagination">
<?php
	for ($i=1; $i<=$nTotalRecords; $i++) {  
?>
		<li><a href="<?php echo $_SERVER['PHP_SELF'];?>"><?php echo $i?></a></li>
<?php
	} 
?>
	</ul>
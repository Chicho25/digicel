<?php
	include("config.php");
  include("defs.php");

	$terrid = current_user_territory();
	$loggdUType = current_user_type();
	$reqType = $_POST['reqtype'];
	switch($reqType)
	{
		case "regionbycountry" :
			$countryId = $_POST['id'];
			if($countryId > 0)
			{
				$arrUser = GetRecords("SELECT * from region where id_country = ".$countryId." and stat = 1
                             order by name");
				$html="<option value=''>----------------</option>";
				foreach ($arrUser as $key => $value) {
					$html.="<option value='".$value['id']."'>".$value['name']."</option>";
				}
				echo $html;
			}
		break;

		case "territorybyregion" :
			$regId = $_POST['id'];
			if($regId > 0)
			{
				$arrUser = GetRecords("SELECT * from territory where id_region = ".$regId." and stat = 1
                             order by name");
				$html="<option value=''>----------------</option>";
				foreach ($arrUser as $key => $value) {
					$html.="<option value='".$value['id']."'>".$value['name']."</option>";
				}
				echo $html;
			}
		break;

		case "productbycategory" :
			$regId = $_POST['id'];

			if($regId > 0)
			{
				if($loggdUType != "Admin")
                {
					$arrUser = GetRecords("SELECT product.* from product
									   inner join product_by_territory on product_by_territory.id_product = product.id
									   where product.id_category = ".$regId." and product.stat = 1 and product_by_territory.id_territory = ".$terrid."
                             order by product.name");
				}
				else
				{
					$arrUser = GetRecords("SELECT product.* from product
									   where product.id_category = ".$regId." and product.stat = 1
                             order by name");
				}
				$html="<option value=''>----------------</option>";
				foreach ($arrUser as $key => $value) {
					$html.="<option value='".$value['id']."'>".$value['code']." / ".$value['name']."</option>";
				}
				echo $html;
			}
		break;

		case "contactbybusiness" :
			$regId = $_POST['id'];
			if($regId > 0)
			{
				$arrUser = GetRecords("SELECT * from contact where id_business = ".$regId." and stat = 1
                             order by Name");
				$html="<option value=''>----------------</option>";
				foreach ($arrUser as $key => $value) {
					$html.="<option value='".$value['id']."'>".$value['Name']."</option>";
				}
				echo $html;
			}
		break;

		case "pricebyproduct" :
			$regId = $_POST['id'];
			if($regId > 0)
			{
				$arrUser = GetRecords("SELECT * from product where id = ".$regId." ");
				echo $arrUser[0]['price'];

			}
		break;

		case "pricebyproductterritory" :
			$regId = $_POST['id'];
			if($regId > 0)
			{
				$arrUser = GetRecords("SELECT * from product_by_territory where id_product = ".$regId." ");
				echo $arrUser[0]['price'];

			}
		break;

	}
?>

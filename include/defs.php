<?php
//	error_reporting(E_ERROR  |E_WARNING | E_PARSE);
	error_reporting(E_ERROR | E_PARSE);

	set_time_limit(0);
	if(!empty($_GET)) extract($_GET);
	if(!empty($_POST)) extract($_POST);
	if(!empty($_SERVER)) extract($_SERVER);
	// open a connection with MsSQL server
	// display an error message if connection
	// was not properly openned


	Function MsSQLConnect()
	{
		// $success = odbc_pconnect("Pubs", "sa", "zid6sh2ls9", "SQL_CUR_USE_ODBC");
		//$success = odbc_pconnect("pubs", "sa", "Zid6sh2ls9", "SQL_CUR_USE_ODBC");
		/*$success = odbc_connect("DSN=test; Driver={SQL Server Native Client 11.0};Server=".$GLOBALS["DB_Server"].";Database=".$GLOBALS["DB_DBName"].";", $GLOBALS["DB_Username"], $GLOBALS["DB_Password"]);*/
		$servidor2= "Driver={SQL Server};Server=172.27.71.245;Database=FacturacionB2B;Integrated Security=SSPI;Persist Security Info=False;";
		$success = odbc_connect( $servidor2, 'facturacionb2b', 'facturacionb2b2017');


		if (!$success)
			echo odbc_error() . ": " . odbc_errormsg() . "<BR>";
		else
		{
			//echo "<br>in else";
			return $success;
		}
	}
	// send a query to MsSQL server.
	// display an error message if there
	// was some error in the query
	function MsSQLQuery($query)
	{

		//echo "<br>".$query;
		$con = MsSQLConnect();
		$success= odbc_do($con, $query);

		if(!$success)
		{
			$strError =  odbc_error();

			//$rstRow =odbc_procedurecolumns ($success , '', '', 'hierarchy_balance' );

			if(error_reporting() != E_NONE)
				if($strError != 23000)
				{
					echo $strError .": ".odbc_errormsg()."<BR>";
					echo "<hr>";
					echo $query;
					echo "<hr>";
					echo error_reporting();
				}
				else
					//echo $strError .": ".odbc_errormsg()."<BR>"."<br>You are deleting an Item which is being used in some other table. Record not Deleted<br>";
					echo "<BR>"."<br><b>Primary Key must be Unique. Record not Inserted</b><br>";
		}

		return $success;
	}


	//send aresult set
	//function return noumber of rows in result set atr
	//in case of delete indert or update
	// number of row s affacted by result
	Function ResultRowCount($result)
	{
		return  odbc_num_rows($result);

	}


	/*	the function remove single quote from the string
		and replace it with two single quotes

		strString:		string to be fixed
		returns:		fixed string
	*/
	function FixString($strString)
	{
		$strString = str_replace("'", "''", $strString);
		$strString = str_replace("\'", "'", $strString);

		return $strString;
	}

	/*	the function returns true if strString contains
		strFindWhat within itself otherwise it returns
		false

		strString:		string to be searched in
		strFindWhat:	string to be searched
		returns:		true if found, flase otherwise
	*/
	function HasString($strString, $strFindWhat)
	{
		$nPos = strpos($strString, $strFindWhat);

		if (!is_integer($nPos))
			return false;
		else
			return true;
	}

	// find the number of records in a table
	//
	// strTable:		name of table to count records in.
	// strCriteria:		select criteria,
	//					if this is not passed, returns the number of all
	//					rows in the table
	// returns:			number of rows in the table
	//
	function RecCount($strTable, $strCriteria = "")
	{
		if(empty($strCriteria))
			$strQuery = "select count(*) as cnt from $strTable";
		else
			$strQuery = "select count(*) as cnt from $strTable where $strCriteria";
		//echo $strQuery ;
		$nResult = MsSQLQuery($strQuery);
		$rstRow = odbc_fetch_array($nResult);
		return $rstRow["cnt"];
	}

	/*	the function returns an associative array containing
		the field names and their type

		strTable:		table name to be described
		returns:		associative array, for instance:
							"user_id" => "int(11)"
							"user_name" => "varchar(32)"
	*/
	function DescTable($strTable)
	{
		$strQuery = "desc $strTable";
		$nResult = MsSQLQuery($strQuery);

		$arrArray = array();

		while($rstRow = odbc_fetch_array($nResult))
		{
			$arrArray[$rstRow["Field"]] = $rstRow["Type"];
		}

		return $arrArray;
	}

	/* the function updates the given table.

		strTable:		table name to be updates.
		strWhere:		where clause for record selection.
		arrValue:		an associated array with key-value of fields
						to be updated.
	*/
	function UpdateRecCheckBox($strTable, $strWhere, $arrValue)
	{
		$strQuery = "	update $strTable set ";

		reset($arrValue);

		while (list ($strKey, $strVal) = each ($arrValue))
		{
			if(FixString($strVal) == 'on')
				$nBit = 1;
			else
				$nBit = 0;
			$strQuery .= $strKey . "='" . $nBit . "',";
		}

		// remove last comma
		$strQuery = substr($strQuery, 0, strlen($strQuery) - 1);

		$strQuery .= " where $strWhere ";

		// execute query
		MsSQLQuery($strQuery);

	}

	/* the function updates the given table.

		strTable:		table name to be updates.
		strWhere:		where clause for record selection.
		arrValue:		an associated array with key-value of fields
						to be updated.
	*/
	function UpdateRec($strTable, $strWhere, $arrValue)
	{
		$strQuery = "	update $strTable set ";

		reset($arrValue);

		while (list ($strKey, $strVal) = each ($arrValue))
		{
			$strQuery .= $strKey . "='" . FixString($strVal) . "',";
		}

		// remove last comma
		$strQuery = substr($strQuery, 0, strlen($strQuery) - 1);

		$strQuery .= " where $strWhere";
		// echo "<br>". $strQuery;
		// execute query
		MsSQLQuery($strQuery);
		return $strQuery;
	}


	/*	the function insert a record in strTable with
		the values given by the associated array

		strTable:		table name where record will be inserted
		arrValue:		assoicated array with key-val pairs
		returns:		ID of the record inserted
	*/
	function InsertRec($strTable, $arrValue)
	{
		$strQuery = "	insert into $strTable (";

		reset($arrValue);
		while(list ($strKey, $strVal) = each($arrValue))
		{
			$strQuery .= $strKey . ",";
		}

		// remove last comma
		$strQuery = substr($strQuery, 0, strlen($strQuery) - 1);

		$strQuery .= ") values (";

		reset($arrValue);
		while(list ($strKey, $strVal) = each($arrValue))
		{
			$strQuery .= "'" . FixString($strVal) . "',";
		}

		// remove last comma
		$strQuery = substr($strQuery, 0, strlen($strQuery) - 1);
		$strQuery .= ")";

		// execute query
		//echo $strQuery;
		MsSQLQuery($strQuery);
		$nResult = MsSQLQuery("SELECT @@IDENTITY AS 'Identity' ");
		$rstRow = odbc_fetch_array($nResult);
		// return id of last insert record
		return $rstRow['Identity'];
	}
	function GetRecords($sql){
		$check=MsSQLQuery($sql);
		$return=array();
		$i=0;
		while ($row = odbc_fetch_array($check)) {
			$return[$i]=$row;
			//echo "<br/> Addinng row <br/>";
			$i++;
		}
		return $return;
	}


	function GetSingleRow($sql){
	//	echo "<br/> sql inside is $sql <br/>";
		$check=MsSQLQuery($sql);
		$numrows= odbc_num_rows($check);
		if ($numrows=0){
			return array();
		}
		$row=odbc_fetch_array($check);
		return $row;
	}
function GetColumnPairRows($sql,$keyfield,$valuefield){
	$check=MsSQLQuery($sql);
	$return=array();
	$i=0;
	//echo "<br/> Running sql $sql <br/>";
	while ($row = odbc_fetch_array($check)) {
		$key=$row[$keyfield];
		$value=$row[$valuefield];
		//printr($row,"row at $i ");
		//echo "<br/> $key,$value, $keyfield,$valuefield <br/>";
		$return[$key]=$value;
		//echo "<br/> Addinng row <br/>";
		$i++;
	}
	if (!$check){
		QueryError($sql);
	}
	return $return;
}

	function GetDefaultValue($sql){
		$SingleRow=MsSQLQuery($sql);
		$SingleRow=odbc_fetch_array($SingleRow);
		if (!$SingleRow){
			return false;
		}else{
			//printr($SingleRow,"Single Row");
			$Keys=array_keys($SingleRow);
			$FirstKey=$Keys[0];
			return $SingleRow[$FirstKey];
		}
	}
	function printr ( $object , $name = '' ) {
	   echo "<hr/>";
	   if ($name<>'') print ( 'printr of \'' . $name . '\' : ' ) ;
			print ( '<pre>' )  ;
	   if ( is_array ( $object ) ) {
		   print_r ( $object ) ;

	   } else {
		   var_dump ( $object ) ;
	   }
		print ( '</pre>' ) ;
		echo "<hr/>";
	}



	// the function returns the assocatied array containing
	// the field name and field value pair for record.
	//
	// strTable:		table name.
	// strCriteria:		where criteria
	//
	function GetRecord($strTable, $strCriteria)
	{
		$strQuery = "select * from $strTable ";

		if(!empty($strCriteria))
			$strQuery .= "where $strCriteria";

		$nResult = MsSQLQuery($strQuery);
		//echo "$strQuery";
		return odbc_fetch_array($nResult);
	}


	/*	the function deletes the record from the
		given table.

		strTable:		table name.
		strCriteria:	where criteria
	*/
	function DeleteRec($strTable, $strCriteria)
	{
		$strQuery = "delete from $strTable where $strCriteria";
		MsSQLQuery($strQuery);
	}

	function Br($nTime)
	{
		$n=1;
		do
		{
			echo"<br>";
			$n++;
		}
		while($n<$nTime);
	}
	// the displays a text field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// bPassword:			1 if to be displayed as password, 0 as text
	//
	function TextField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $bPassword , $callBack = '', $strReadonly='')
	{
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		if($bPassword)
			echo "		<input type=password $strReadonly name=$strField value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>";
		else
			echo "		<input type=text $strReadonly name=$strField value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>";

		echo "	</td>";
		echo "</tr>";
	}

	function TextFieldSimple($strLabel, $strField, $strValue, $nSize, $nMaxLength, $bPassword , $callBack = '', $strReadonly='',$style='')
	{
		if($strValue == "1900-01-01")
			$strValue = "";
		echo "	<td>";
		echo		$strLabel;

		echo "		<input type=text $strReadonly name=$strField value='$strValue' size=$nSize maxlength=$nMaxLength $callBack style='$style'>";

		echo "	</td>";

	}
	// the displays 2 text fields in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField1:			Text field1 name in form.
	// strField2:			Text field2 name in form.
	// strValue1:			Value to be shown in text field 1.
	// strValue1:			Value to be shown in text field 2.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// bPassword:			1 if to be displayed as password, 0 as text
	//
	function TextField2($strLabel, $strField1, $strField2, $strValue1, $strValue2, $nSize, $nMaxLength, $bPassword, $nZero = 1 )
		{
			echo "</tr>";
			echo "	<td>";
			echo		$strLabel;
			echo "	</td>";
			echo "	<td align=center>";
			if($nZero == 1)
			{
				if ($strValue1 == 0)
					$strValue1 = "0000";
				if ($strValue2 == 0)
					$strValue2 = "0000";
			}

			if($bPassword)
				echo "		<input type=password name=$strField1 value='$strValue1' size=$nSize maxlength=$nMaxLength>";
			else
				echo "		<input type=text name=$strField1 value='$strValue1' size=$nSize maxlength=$nMaxLength>";

			echo "	</td>";
			echo "	<td align=center>";
			if($bPassword)
				echo "		<input type=password name=$strField2 value='$strValue2' size=$nSize maxlength=$nMaxLength>";
			else
				echo "		<input type=text name=$strField2 value='$strValue2' size=$nSize maxlength=$nMaxLength>";

			echo "	</td>";
			echo "</tr>";

		}
	// the displays a read only text field for as field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// strFormName:			Name of HTML form
	//




	function TextLookupFieldReadOnlyForEdit($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument,$nid ,$callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack readonly>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					window.open('/include/strcommon/$strLookupDocument?strField=$strField&nId=$nid' , 'CalPop', 'scrollbars=yes, toolbar=0,width=600,height=600');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}




	function TextLookupFieldReadOnly($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack readonly>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					window.open('/include/strcommon/$strLookupDocument?strField=$strField' , 'CalPop', 'scrollbars=yes, toolbar=0,width=600,height=600');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}
	function TextLookupFieldStr($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					window.open('/include/strcommon/$strLookupDocument?strField=$strField' , 'CalPop', 'scrollbars=yes, toolbar=0,width=500,height=500');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}
	// the displays a text field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// bPassword:			1 if to be displayed as password, 0 as text
	//
	// difference from textfield  =   aligned center textbox's
	function TextField3($strLabel, $strField, $strValue, $nSize, $nMaxLength, $bPassword , $callBack = '')
	{
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td align=center>";

		if($bPassword)
			echo "		<input type=password name=$strField value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>";
		else
			echo "		<input type=text name=$strField value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>";

		echo "	</td>";
		echo "</tr>";
	}
	// the displays a text field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// bPassword:			1 if to be displayed as password, 0 as text
	//
	function ReadOnlyField($strLabel, $strField, $strValue, $nSize, $nMaxLength)
	{
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo $strValue;
		echo "	</td>";
		echo "</tr>";
	}



	/*	the function displays OK and Cancel buttons in the form

	*/
	function OKCancelButtons($okValue=' Ok ',$cancelValue='Cancel',$onClick='')
	{ 	global $oldPage;
		$svrName=$_SERVER['SERVER_ADDR'];
		$oldPage="http://$svrName".$oldPage;
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		//echo "<input type=submit value='  $okValue  '> <input type=submit value='$cancelValue' onClick='history.back(); return false;'>";
		echo "<input type=submit value='  $okValue  ' onClick='$onClick;'> <input type=button value='$cancelValue' onClick=\"window.open('$oldPage','_self')\">";
		echo "	</td>";
		echo "</tr>";
	}


	function OKCancelButtonsWithNames($okValue=' Ok ',$cancelValue='Cancel',$onClick='',$name1)
	{ 	global $oldPage;
		$svrName=$_SERVER['SERVER_ADDR'];
		$oldPage="http://$svrName".$oldPage;
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		//echo "<input type=submit value='  $okValue  '> <input type=submit value='$cancelValue' onClick='history.back(); return false;'>";
		echo "<input type=submit name = '$name1' value='  $okValue  ' onClick='$onClick;'> <input type=button value='$cancelValue' onClick=\"window.open('$oldPage','_self')\">";
		echo "	</td>";
		echo "</tr>";
	}

	function OKCloseButtons()
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=submit value='   OK   '> <input type=button value='Close' onClick='window.close();'>";
		echo "	</td>";
		echo "</tr>";
	}

	function NextBackButtons()
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=submit value='<< Back' onClick='history.go(-1); return false;'> <input type=submit value='   Next >>   '> ";
		echo "	</td>";
		echo "</tr>";
	}

	function OKButton($OnClick)
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=button value='     OK      ' onClick='".$OnClick."();'>";
		echo "	</td>";
		echo "</tr>";
	}
	function BackButton($OnClick ="")
	{
		global $oldPage;
		$svrName=$_SERVER['SERVER_ADDR'];
		$oldPage="http://$svrName".$oldPage;
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		 <input type=button value='  Back  ' onClick=\"window.open('$oldPage','_self')\">";
		echo "	</td>";
		echo "</tr>";
	}

	function OKButtonCancelOnClick($OnClick)
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=button value='     OK      ' onClick='".$OnClick."'><input type=submit value='Cancel' onClick='history.go(-1); return false;'>";
		echo "	</td>";
		echo "</tr>";
	}

	/*	the function displays OK and Cancel buttons in the form

	*/
	function CloseButtons()
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=button value='Close' onClick='window.close();' >";
		echo "	</td>";
		echo "</tr>";
	}

	/*	the function creates an hidden field

		strName:		name of hidden field
		strValue:		value to be passed in hidden field
	*/
	function HiddenField($strName, $strValue)
	{
		echo "<input type=hidden name='$strName' id='$strName' value='$strValue'>\r\n";
	}

	/*	the function creates a text area

		strLabel:			Label in left column.
		strField:			Text field name in form.
		strValue:			Value to be shown in text field.
		nRows:				number of rows in text area
		nCols:				number of columsn in text area
	*/
	function TextArea($strLabel, $strField, $strValue, $nRows, $nCols, $class='')
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo "		<textarea name=$strField rows=$nRows class=$class cols=$nCols>$strValue</textarea>";

		echo "	</td>";
		echo "</tr>";
	}

	/*
		the function creates a file upload widget on form.

		strLabel:			Label in left column
		strFileName:		File name
	*/
	function FileUpload($strLabel, $strFileName)
	{
		echo "<tr>";
		echo "	<td valign=top width=100%>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td width=100%>";
		//echo " <input type='hidden' name='MAX_FILE_SIZE' value=2M />";
		echo "<input type='file' name='$strFileName'>";
		echo "	</td>";
		echo "</tr>";
	}


	/*
		the function displays combox box

		nSelectedVal:		index of selected value
		arr:				array containig items to be displayed
		bIndexValue:		true: use array index as item value e.g: 0, 1, 2, ...
							false: use array value as item value e.g: 2003, 2004, 2005, ...
	*/
	function ComboBox($nSelectedVal, $arr, $bIndexValue)
	{
		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;

			if($i == $nSelectedVal)
				if($bIndexValue == true)
					echo "<option value=$j selected>" . $arr[$i] . "\r\n";
				else
					echo "<option selected>" . $arr[$i] . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$j>" . $arr[$i] . "\r\n";
				else
					echo "<option>" . $arr[$i] . "\r\n";
		}
	}

	/*
		the function displays combox box

		nSelectedVal:		selected value
		arr:				array containig items to be displayed
		bIndexValue:		true: use array index as item value e.g: 0, 1, 2, ...
							false: use array value as item value e.g: 2003, 2004, 2005, ...
	*/
	function ComboBoxValueSelected($nSelectedVal, $arr, $bIndexValue)
	{
		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;

			if($arr[$i] == $nSelectedVal)
				if($bIndexValue == true)
					echo "<option value=$j selected>" . $arr[$i] . "\r\n";
				else
					echo "<option selected>" . $arr[$i] . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$j>" . $arr[$i] . "\r\n";
				else
					echo "<option>" . $arr[$i] . "\r\n";
		}
	}
	/*
		the function displays Multi Selection Combo

		nSelectedVal:		selected value
		arr:				array containig items to be displayed
		bIndexValue:		true: use array index as item value e.g: 0, 1, 2, ...
							false: use array value as item value e.g: 2003, 2004, 2005, ...
	*/
	function MultiComboBox($strLable, $strName, $nSelectedVal, $arr, $bIndexValue)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "<select name=$strName multiple >";

		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;

			if($j == $nSelectedVal)
				if($bIndexValue == true)
					echo "<option value=$j selected >" . $arr[$i] . "\r\n";
				else
					echo "<option selected >" . $arr[$i] . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$j  >" . $arr[$i] . "\r\n";
				else
					echo "<option  >" . $arr[$i] . "\r\n";
		}
		echo "		</select>";
		echo "	</td>";
		echo "</tr>";

	}


	/*
		the function displays Multi Selection Combo

		nSelectedVal:		selected value
		arr:				array containig items to be displayed
		bIndexValue:		true: use array index as item value e.g: 0, 1, 2, ...
							false: use array value as item value e.g: 2003, 2004, 2005, ...
	*/
	function MultiComboBoxForMachineCriteria($strLable, $strName, $nSelectedVal, $arr, $bIndexValue)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "<select name=$strName multiple >";

		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i;

			if($j == $nSelectedVal)
				if($bIndexValue == true)
					echo "<option value=$j selected >" . $arr[$i] . "\r\n";
				else
					echo "<option selected >" . $arr[$i] . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$j  >" . $arr[$i] . "\r\n";
				else
					echo "<option  >" . $arr[$i] . "\r\n";
		}
		echo "		</select>";
		echo "	</td>";
		echo "</tr>";

	}

	/*
		the function displays Multi Selection Combo

		nSelectedVal:		selected value
		arr:				array containig items to be displayed along with the its index too  not its auto gererated index
		bIndexValue:		true: use array index as item value e.g: 0, 1, 2, ...
							false: use array value as item value e.g: 2003, 2004, 2005, ...
	*/
	function MultiComboBoxIndex($strLable, $strName, $nSelectedVal, $arr, $bIndexValue, $CallBack="")
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "<select name=$strName multiple $CallBack>";
		$ArrIndex = array_keys($arr);
		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;
			if($j == $nSelectedVal)
				if($bIndexValue == true)
					echo "<option value=".$ArrIndex[$i]." selected >" . $arr[$ArrIndex[$i]] . "\r\n";
				else
					echo "<option selected >" . $arr[$ArrIndex[$i]] . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=".$ArrIndex[$i].">" . $arr[$ArrIndex[$i]] . "\r\n";
				else
					echo "<option  >" . $arr[$ArrIndex[$i]] . "\r\n";
		}
		echo "		</select>";
		echo "	</td>";
		echo "</tr>";

	}

	/*
		the function draws combo box fitted in table row by
		using the function ComboBox();
	*/
	function ArrayComboBox($strLable, $strName, $nSelectedVal, $arr, $bIndexValue, $callBack ='', $strDisabled="")
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "		<select name=$strName $callBack $strDisabled>";
		ComboBox($nSelectedVal, $arr, $bIndexValue);
		echo "		</select>";
		echo "	</td>";
		echo "</tr>";
	}

	/*
		the function displays Multi Selection Combo Key Value

		nSelectedVal:		selected value
		arr:				array containig items to be displayed
		bIndexValue:		true: use array index as item value e.g: 0, 1, 2, ...
							false: use array value as item value e.g: 2003, 2004, 2005, ...
	*/
	function MultiComboBoxKeyValue($strLable, $strName, $nSelectedVal, $arr, $bIndexValue, $nAllUnDef = -1, $nSize = 5)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "<select name=$strName multiple size=$nSize >";
		reset($arr);
		if($nAllUnDef == 0)
			echo "<option value=0000>ALL \r\n";
		if($nAllUnDef == 1)
			echo "<option value=0000>--------------- \r\n";
		while (list($key, $val) = each($arr))
		{
			if(trim($key) == trim($nSelectedVal))
				if($bIndexValue == true)
					echo "<option value=$key selected>" . $val . "\r\n";
				else
					echo "<option selected>" . $val . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$key>" . $val . "\r\n";
				else
					echo "<option>" . $val . "\r\n";

		}
		echo "		</select>";
		echo "	</td>";
		echo "</tr>";

	}
	/*
		the function draws combo box fitted in table row by
		using the function ComboBox();
	*/
	function ArrayComboBoxTd($strLable, $strName, $nSelectedVal, $arr, $bIndexValue, $callBack ='')
	{
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "		<select name=$strName $callBack>";
		ComboBox($nSelectedVal, $arr, $bIndexValue);
		echo "		</select>";
		echo "	</td>";

	}



	/*
		the function draws combo box fitted in table row by
		using the function ComboBox();
	*/
	function ArrayComboBoxStrKeyValue($strLable, $strName, $nSelectedVal, $arr, $bIndexValue, $callBack ='')
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "		<select name=$strName $callBack>";

		reset($arr);
		while (list($key, $val) = each($arr))
		{
			if(trim($key) == trim($nSelectedVal))
				if($bIndexValue == true)
					echo "<option value=$key selected>" . $val . "\r\n";
				else
					echo "<option selected>" . $val . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$key>" . $val . "\r\n";
				else
					echo "<option>" . $val . "\r\n";

		}
		echo "		</select>";
		echo "	</td>";
		echo "</tr>";
	}

	/*
		the function draws combo box fitted in table row by
		using the function ComboBox();
	*/
	function ArrayComboBoxStrKeyValueTd($strLable, $strName, $nSelectedVal, $arr, $bIndexValue, $callBack ='')
	{
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "		<select name=$strName $callBack>";

		reset($arr);
		while (list($key, $val) = each($arr))
		{
			if(trim($key) == trim($nSelectedVal))
				if($bIndexValue == true)
					echo "<option value=$key selected>" . $val . "\r\n";
				else
					echo "<option selected>" . $val . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$key>" . $val . "\r\n";
				else
					echo "<option>" . $val . "\r\n";

		}
		echo "		</select>";
		echo "	</td>";

	}
	/*
	function Returns Rota Rest Days
	$strStartDate           End Date
	$strEndDate          Start Date
	$nEmpGrpId			 emp Rota Group Id
	$arrRGroupSW 		Array of Rota Groups
	*/

	function GetRotaSWRestDates($rstRow, $strStartDate, $strEndDate, $nDayCount, $arrRGroupSW)
	{
	// Attendace for rota Group
		// Check Rota
		//  Single Char i.e A, B, C Index is Used to Store Single Off Day
		//	 Double Char i.e AA, BB, CC  Index is Used to Store Double Off Day
		$arrLeavTypeSetting = empLeaveTypeSetting($rstRow);

		$strGrpId = "rota_".$arrRGroupSW[$rstRow['emp_shift']];

		$Query = "SELECT ". $strGrpId .", rota_date
					FROM tblRotaSW
							WHERE ( rota_date BETWEEN '". $strStartDate ."' AND '". $strEndDate ."')";
		$nResultRota = MSSQLQuery( $Query );

		$strSRestId = "S_".$rstRow['emp_shift'];
		$strGRestId = "G_".$rstRow['emp_shift'];


		$nIndexS = 1;
		$nIndexG = 1;
		$nDCnt = 1;

		while($rstRowRota = odbc_fetch_array($nResultRota))
		{

			if(trim($rstRowRota[$strGrpId]) == 'R')
			{
				$arrRotaRest[$strSRestId][$nIndexS]= $rstRowRota['rota_date'];
				$nIndexS++;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexG)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							$arrRotaRest[$strGRestId][$nIndexG]= $rstRowRota['rota_date'];
							$nIndexG++;
						}
					}
					if($nDCnt > $nIndexG && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							$arrRotaRest[$strGRestId][$nIndexG]= $rstRowRota['rota_date'];
							$nIndexG++;
						}

					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							$arrRotaRest[$strGRestId][$nIndexG]= $rstRowRota['rota_date'];
							$nIndexG++;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			$nDCnt++;
		}//end while
		return $arrRotaRest;
	}

	/*
	function Returns Rota Rest Days
	$strStartDate           End Date
	$strEndDate          Start Date
	$nEmpGrpId			 emp Rota Group Id
	$arrRGroupTW 		Array of Rota Groups
	*/

	function GetRotaTWRestDates($rstRow, $strStartDate, $strEndDate, $nDayCount, $arrRGroupTW)
	{
	// Attendace for rota Group
		// Check Rota
		//  Single Char i.e A, B, C Index is Used to Store Single Off Day
		//	 Double Char i.e AA, BB, CC  Index is Used to Store Double Off Day
		$arrLeavTypeSetting = empLeaveTypeSetting($rstRow);

		$strGrpId = "rota_".$arrRGroupTW[$rstRow['emp_shift']];

		$Query = "SELECT ". $strGrpId .", rota_date
					FROM tblRotaTW
							WHERE ( rota_date BETWEEN '". $strStartDate ."' AND '". $strEndDate ."')";
		$nResultRota = MSSQLQuery( $Query );

		$strSRestId = "S_".$rstRow['emp_shift'];
		$strGRestId = "G_".$rstRow['emp_shift'];


		$nIndexS = 1;
		$nIndexG = 1;
		$nDCnt = 1;

		while($rstRowRota = odbc_fetch_array($nResultRota))
		{

			if(trim($rstRowRota[$strGrpId]) == 'R')
			{
				$arrRotaRest[$strSRestId][$nIndexS]= $rstRowRota['rota_date'];
				$nIndexS++;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexG)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							$arrRotaRest[$strGRestId][$nIndexG]= $rstRowRota['rota_date'];
							$nIndexG++;
						}
					}
					if($nDCnt > $nIndexG && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							$arrRotaRest[$strGRestId][$nIndexG]= $rstRowRota['rota_date'];
							$nIndexG++;
						}

					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							$arrRotaRest[$strGRestId][$nIndexG]= $rstRowRota['rota_date'];
							$nIndexG++;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			$nDCnt++;
		}//end while
		return $arrRotaRest;
	}
	/*
	function Returns Rota Rest Days
	$strStartDate           End Date
	$strEndDate          Start Date
	*/
	function GetRotaRestDates($rstRow, $strStartDate, $strEndDate, $nDayCount)
	{
		// Check Rota
		//  Single Char i.e A, B, C Index is Used to Store Single Off Day
		//	 Double Char i.e AA, BB, CC  Index is Used to Store Double Off Day
		//Get Leave type Setting
		$arrLeavTypeSetting = empLeaveTypeSetting($rstRow);

		$Query = "SELECT *
					FROM tblRota
							WHERE ( rota_date BETWEEN '". $strStartDate ."' AND '". $strEndDate ."')";
		$nResultRota = MSSQLQuery( $Query );
		$nIndexA = 1;
		$nIndexB = 1;
		$nIndexC = 1;
		$nIndexD = 1;
		$nIndexGA = 1;
		$nIndexGB = 1;
		$nIndexGC = 1;
		$nIndexGD = 1;
		$nDCnt = 1;
		while($rstRowRota = odbc_fetch_array($nResultRota))
		{
			//Rota rest and Gazzeted holidays for shift A
			if(trim ($rstRowRota['rota_rest'] )== "A")
			{
				$arrRotaRest['AA'][$nIndexA]= $rstRowRota['rota_date'];
				$nIndexA++;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGA)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							$arrRotaRest['GAZTAA'][$nIndexGA]= $rstRowRota['rota_date'];
							$nIndexGA++;
						}
					}
					if($nDCnt > $nIndexGA && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							$arrRotaRest['GAZTAA'][$nIndexGA]= $rstRowRota['rota_date'];
							$nIndexGA++;
						}

					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							$arrRotaRest['GAZTAA'][$nIndexGA]= $rstRowRota['rota_date'];
							$nIndexGA++;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			//Rota rest and Gazzeted holidays for shift B
			if(trim ($rstRowRota['rota_rest'] )== "B")
			{
				$arrRotaRest['BB'][$nIndexB]= $rstRowRota['rota_date'];
				$nIndexB++;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGB)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							$arrRotaRest['GAZTBB'][$nIndexGB]= $rstRowRota['rota_date'];
							$nIndexGB++;
						}
					}
					if($nDCnt > $nIndexGB && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							$arrRotaRest['GAZTBB'][$nIndexGB]= $rstRowRota['rota_date'];
							$nIndexGB++;
						}

					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							$arrRotaRest['GAZTBB'][$nIndexGB]= $rstRowRota['rota_date'];
							$nIndexGB++;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			//Rota rest and Gazzeted holidays for shift C
			if(trim ($rstRowRota['rota_rest'] )== "C")
			{
				$arrRotaRest['CC'][$nIndexC]= $rstRowRota['rota_date'];
				$nIndexC++;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGC)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							$arrRotaRest['GAZTCC'][$nIndexGC]= $rstRowRota['rota_date'];
							$nIndexGC++;
						}
					}
					if($nDCnt > $nIndexGC && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							$arrRotaRest['GAZTCC'][$nIndexGC]= $rstRowRota['rota_date'];
							$nIndexGC++;
						}

					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							$arrRotaRest['GAZTCC'][$nIndexGC]= $rstRowRota['rota_date'];
							$nIndexGC++;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			//Rota rest and Gazzeted holidays for shift D
			if(trim ($rstRowRota['rota_rest'] )== "D")
			{
				$arrRotaRest['DD'][$nIndexD]= $rstRowRota['rota_date'];
				$nIndexD++;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGD)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							$arrRotaRest['GAZTDD'][$nIndexGD]= $rstRowRota['rota_date'];
							$nIndexGD++;
						}
					}
					if($nDCnt > $nIndexGD && $nDCnt < $nDayCount)
					{

						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							$arrRotaRest['GAZTDD'][$nIndexGD]= $rstRowRota['rota_date'];
							$nIndexGD++;
						}

					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							$arrRotaRest['GAZTDD'][$nIndexGD]= $rstRowRota['rota_date'];
							$nIndexGD++;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			$nDCnt++;

		}//end while
		return $arrRotaRest;
	}
	/*
	function Returns General Group Employees Rest Days
	$strStartDate           End Date
	$strEndDate          Start Date
	*/
	function GetGenEmpRestDates($arrRowEmp, $strStartDate, $strEndDate, $nLeavPeriod=0)
	{
		//Get Leave type Setting
		if($nLeavPeriod==0)
		{
			$rstRowPrd = odbc_fetch_array(MsSQLQuery("SELECT (DATEDIFF(dd, '".$strStartDate."', '".$strEndDate."') + 1) AS day"));
			$nLeavPeriod = $rstRowPrd['day'];
		}

		$arrLeavTypeSetting = empLeaveTypeSetting($arrRowEmp);
		//$arrGenEmpRest['DATE'] = 0;
		$arrSDate = explode('-', $strStartDate);

		$nDays = substr($arrSDate[2], 0, 2);
		$nOffIndex = 1;
		$nGztIndex = 1;
		for($nCounter = 1; $nCounter <= $nLeavPeriod; $nCounter++)//Count No. of Off Days for an Employee
		{
			$strCDate = date('m/d/Y', mktime(0, 0, 0, $arrSDate[1], $nDays, $arrSDate[0]));
			$nWeekDay = (date('w', mktime(0, 0, 0, $arrSDate[1], $nDays, $arrSDate[0]))) + 1;
			if( $nWeekDay == $arrRowEmp['emp_offDay1'] || $nWeekDay == $arrRowEmp['emp_offDay2'] )
			{
				$arrGenEmpRest["OFFDAY"][$nOffIndex] = $strCDate;
				$nOffIndex++;
			}
			else
			{
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $strCDate ."')
																OR  ( hol_end = '". $strCDate. "' )
																OR  ( hol_start < '". $strCDate. "' AND hol_end  > '". $strCDate. "' )");

				if($nGazzetedHoliday > 0)
				{
					if($nCounter == 1 || $nCounter == $nGztIndex)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							$arrGenEmpRest["GZTDAY"][$nGztIndex] = $strCDate;
							$nGztIndex++;
						}
					}
					else if($nCounter > $nGztIndex && $nCounter < $nLeavPeriod)
					{

						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							$arrGenEmpRest["GZTDAY"][$nGztIndex] = $strCDate;
							$nGztIndex++;
						}

					}
					else if($nCounter == $nLeavPeriod)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							$arrGenEmpRest["GZTDAY"][$nGztIndex] = $strCDate;
							$nGztIndex++;
						}
					}
				}// end nGazzetedHoliday
			}
			$nDays++;
		}//end for

		return $arrGenEmpRest;
	}

	/*
	function Post Rota Group Leaves
	$strStartDate           End Date
	$strEndDate          Start Date
	*/
	function PostRotaSWEmpLeaves($rstRow, $strStartDate, $strEndDate, $nDayCount, $nLeaveType, $arrRGroupSW, $bPastLeaves=0)
	{

		$arrLeavTypeSetting = empLeaveTypeSetting($rstRow);
		// Check Rota
		$strGrpId = "rota_".$arrRGroupSW[$rstRow['emp_shift']];

		$Query = "SELECT ". $strGrpId .", rota_date
					FROM tblRotaSW
							WHERE ( rota_date BETWEEN '". $strStartDate ."' AND '". $strEndDate ."')";
		$nResultRota = MSSQLQuery( $Query );

		$nDCnt = 1;
		$bLastDay= 1;
		$nIndexG = 1;

		if($nLeaveType == 99)
		{
			$strLeavCategory = "COMPENSATORY";
			$nLeavTypId = 0;
			$atmStatus = 99;
		}
		else if($nLeaveType == "MISCELLANEOUS")
		{
			$strLeavCategory = "UNPAID";
			$nLeavTypId = 0;
			$atmStatus = 3;
		}
		else
		{
			$nResultRow = GetRecord("tblLeaveType","lea_id = $nLeaveType");
			$strLeavCategory = $nResultRow['lea_category'];
			$nLeavTypId = $nLeaveType;
			$atmStatus = 7;
		}

		$arrCStartDate = explode('-', $strStartDate);
		$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCStartDate[1], $arrCStartDate[2], $arrCStartDate[0]));
		//$strTempStartDate = $strStartDate;

		while($rstRowRota = odbc_fetch_array($nResultRota))
		{
			//Rota rest and Gazzeted holidays for shift A
			$arrCDate = explode('-', $rstRowRota['rota_date']);
			$nDays = substr($arrCDate[2], 0, 2);
			$strCDate = date('Y-m-d', mktime(0, 0, 0, $arrCDate[1], $nDays, $arrCDate[0]));
			$nEmpAttCheck = RecCount("tblAttendance"," att_emp_id = " . $rstRow['emp_id'] . " AND att_duty_date = '".$strCDate."'");
			if(trim($rstRowRota[$strGrpId] )== "R"  || ($nEmpAttCheck > 0 && $bPastLeaves == 1))
			{
				$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));

				$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
				$rstRowLD = odbc_fetch_array($nResultLD);
				if($rstRowLD['nLDay'] >= 0)
				{
					$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
						//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
						if($bPastLeaves==1)
						{
							$pasteToArray = explode("/", $strTempEndDate);
							$fromArray = explode("/",  $strTempStartDate);
							$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
							$nCounterAtm = 0;
						//echo $nOverWrite;
							do
							{
								$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
								UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
								$nCounterAtm++;
							}
							while($nCounterAtm <= $nDaysAtm);
						}


				}
				$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
				if($nDCnt == $nDayCount)
					$bLastDay= 0;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");


				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexG)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexG++;
						}
					}
					if($nDCnt > $nIndexG && $nDCnt < $nDayCount)
					{

						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));

								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}
								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexG++;
						}
					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$bLastDay= 0;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			$nDCnt++;

		}//end while
		//post Last Day
		if($bLastDay== 1)
		{

			//Post Normal Leave
			$nId = InsertRec("tblfullLeave", array(
										"efl_emp_id"=>$rstRow['emp_id'],
										"efl_emp_by"=>$_SESSION['USER_ID'],
										"efl_Category"=>$strLeavCategory,
										"efl_leaType_id"=>$nLeavTypId,
										"efl_startDate"=>$strTempStartDate,
										"efl_endDate"=>$strEndDate,
										"efl_leavePost"=> date("m/d/Y H:i:s")));
			if($bPastLeaves==1)
			{
				$pasteToArray = explode("-", $strEndDate);
				$fromArray = explode("/",  $strTempStartDate);
				$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[1]  , $pasteToArray[2], $pasteToArray[0]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
				$nCounterAtm = 0;
			//echo $nOverWrite;
				do
				{
					$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
					UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
					$nCounterAtm++;
				}
				while($nCounterAtm <= $nDaysAtm);
			}

			//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

		}
		echo "Leave Successfully Saved.";

	}

	/*
	function Post Rota Group Leaves
	$strStartDate           End Date
	$strEndDate          Start Date
	*/
	function PostRotaTWEmpLeaves($rstRow, $strStartDate, $strEndDate, $nDayCount, $nLeaveType, $arrRGroupTW, $bPastLeaves=0)
	{
		$arrLeavTypeSetting = empLeaveTypeSetting($rstRow);
		// Check Rota
		$strGrpId = "rota_".$arrRGroupTW[$rstRow['emp_shift']];

		$Query = "SELECT ". $strGrpId .", rota_date
					FROM tblRotaTW
							WHERE ( rota_date BETWEEN '". $strStartDate ."' AND '". $strEndDate ."')";
		$nResultRota = MSSQLQuery( $Query );

		$nDCnt = 1;
		$bLastDay= 1;
		$nIndexG = 1;

		if($nLeaveType == 99)
		{
			$strLeavCategory = "COMPENSATORY";
			$nLeavTypId = 0;
			$atmStatus = 99;
		}
		else if($nLeaveType == "MISCELLANEOUS")
		{
			$strLeavCategory = "UNPAID";
			$nLeavTypId = 0;
			$atmStatus = 3;
		}
		else
		{
			$nResultRow = GetRecord("tblLeaveType","lea_id = $nLeaveType");
			$strLeavCategory = $nResultRow['lea_category'];
			$nLeavTypId = $nLeaveType;
			$atmStatus = 7;
		}

		$arrCStartDate = explode('-', $strStartDate);
		$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCStartDate[1], $arrCStartDate[2], $arrCStartDate[0]));

		while($rstRowRota = odbc_fetch_array($nResultRota))
		{
			//Rota rest and Gazzeted holidays for shift A
			$arrCDate = explode('-', $rstRowRota['rota_date']);
			$nDays = substr($arrCDate[2], 0, 2);
			$strCDate = date('Y-m-d', mktime(0, 0, 0, $arrCDate[1], $nDays, $arrCDate[0]));
			$nEmpAttCheck = RecCount("tblAttendance"," att_emp_id = " . $rstRow['emp_id'] . " AND att_duty_date = '".$strCDate."'");
			if(trim($rstRowRota[$strGrpId] )== "R" || ($nEmpAttCheck > 0 && $bPastLeaves == 1))
			{
				$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
				$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
				$rstRowLD = odbc_fetch_array($nResultLD);

				if($rstRowLD['nLDay'] >= 0)
				{
					$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
						//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
						if($bPastLeaves==1)
						{
							$pasteToArray = explode("/", $strTempEndDate);
							$fromArray = explode("/",  $strTempStartDate);
							$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
							$nCounterAtm = 0;

						//echo $nOverWrite;
							do
							{
								$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
								UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
								$nCounterAtm++;
							}
							while($nCounterAtm <= $nDaysAtm);
						}

				}
				$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
				if($nDCnt == $nDayCount)
					$bLastDay= 0;
			}
			else
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexG)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexG++;
						}
					}
					if($nDCnt > $nIndexG && $nDCnt < $nDayCount)
					{

						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));

								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}
								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexG++;
						}
					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$bLastDay= 0;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			$nDCnt++;

		}//end while
		//post Last Day
		if($bLastDay== 1)
		{

			//Post Normal Leave
			$nId = InsertRec("tblfullLeave", array(
										"efl_emp_id"=>$rstRow['emp_id'],
										"efl_emp_by"=>$_SESSION['USER_ID'],
										"efl_Category"=>$strLeavCategory,
										"efl_leaType_id"=>$nLeavTypId,
										"efl_startDate"=>$strTempStartDate,
										"efl_endDate"=>$strEndDate,
										"efl_leavePost"=> date("m/d/Y H:i:s")));
			if($bPastLeaves==1)
			{
				$pasteToArray = explode("-", $strEndDate);

				$fromArray = explode("/",  $strTempStartDate);

				$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[1]  , $pasteToArray[2], $pasteToArray[0]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
				$nCounterAtm = 0;
			//echo $nOverWrite;
				do
				{
					$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
					UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
					$nCounterAtm++;
				}
				while($nCounterAtm <= $nDaysAtm);
			}

			//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

		}
		echo "Leave Successfully Saved.";

	}

	/*
	function Post Rota Group Leaves
	$strStartDate           End Date
	$strEndDate          Start Date
	*/
	function PostRotaEmpLeaves($rstRow, $strStartDate, $strEndDate, $nDayCount, $nLeaveType, $bPastLeaves=0)
	{

		// Check Rota
		//  Single Char i.e A, B, C Index is Used to Store Single Off Day
		//	 Double Char i.e AA, BB, CC  Index is Used to Store Double Off Day
		//Get Leave type Setting
		$arrLeavTypeSetting = empLeaveTypeSetting($rstRow);
		$Query = "SELECT *
					FROM tblRota
							WHERE ( rota_date BETWEEN '". $strStartDate ."' AND '". $strEndDate ."')";
		$nResultRota = MSSQLQuery( $Query );
		$nDCnt = 1;
		$bLastDay= 1;
		$nIndexGA = 1;
		$nIndexGB = 1;
		$nIndexGC = 1;
		$nIndexGD = 1;

		if($nLeaveType == 99)
		{
			$strLeavCategory = "COMPENSATORY";
			$nLeavTypId = 0;
			$atmStatus = 99;
		}
		else if($nLeaveType == "MISCELLANEOUS")
		{
			$strLeavCategory = "UNPAID";
			$nLeavTypId = 0;
			$atmStatus = 3;
		}
		else
		{
			$nResultRow = GetRecord("tblLeaveType","lea_id = $nLeaveType");
			$strLeavCategory = $nResultRow['lea_category'];
			$nLeavTypId = $nLeaveType;
			$atmStatus = 7;
		}

		$arrCStartDate = explode('-', $strStartDate);
		$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCStartDate[1], $arrCStartDate[2], $arrCStartDate[0]));
		//$strTempStartDate = $strStartDate;

		while($rstRowRota = odbc_fetch_array($nResultRota))
		{
			//Rota rest and Gazzeted holidays for shift A
			$arrCDate = explode('-', $rstRowRota['rota_date']);
			$nDays = substr($arrCDate[2], 0, 2);
			$strCDate = date('Y-m-d', mktime(0, 0, 0, $arrCDate[1], $nDays, $arrCDate[0]));
			$nEmpAttCheck = RecCount("tblAttendance"," att_emp_id = " . $rstRow['emp_id'] . " AND att_duty_date = '".$strCDate."'");
			if((trim ($rstRowRota['rota_rest'] )== "A" && $rstRow['emp_shift'] == '1') || ($nEmpAttCheck > 0 && $bPastLeaves == 1))
			{
				$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
				$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
				$rstRowLD = odbc_fetch_array($nResultLD);
				if($rstRowLD['nLDay'] >= 0)
				{
					$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
						//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
						if($bPastLeaves==1)
						{
							$pasteToArray = explode("/", $strTempEndDate);
							$fromArray = explode("/",  $strTempStartDate);
							$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
							$nCounterAtm = 0;
						//echo $nOverWrite;
							do
							{
								$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
								UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
								$nCounterAtm++;
							}
							while($nCounterAtm <= $nDaysAtm);
						}


				}
				$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
				if($nDCnt == $nDayCount)
					$bLastDay= 0;
			}
			else if ($rstRow['emp_shift'] == '1')
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGA)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGA++;
						}
					}
					if($nDCnt > $nIndexGA && $nDCnt < $nDayCount)
					{

						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));

								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}
								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGA++;
						}
					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$bLastDay= 0;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			//Rota rest and Gazzeted holidays for shift B
			if((trim ($rstRowRota['rota_rest'] )== "B" && $rstRow['emp_shift'] == '2') || ($nEmpAttCheck > 0 && $bPastLeaves == 1))
			{
				$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
				$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
				$rstRowLD = odbc_fetch_array($nResultLD);
				if($rstRowLD['nLDay'] >= 0)
				{
					$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
						if($bPastLeaves==1)
						{
							$pasteToArray = explode("/", $strTempEndDate);
							$fromArray = explode("/",  $strTempStartDate);
							$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
							$nCounterAtm = 0;
						//echo $nOverWrite;
							do
							{
								$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
								UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
								$nCounterAtm++;
							}
							while($nCounterAtm <= $nDaysAtm);
						}

						//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

				}
				$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
				if($nDCnt == $nDayCount)
					$bLastDay= 0;
			}
			else  if ($rstRow['emp_shift'] == '2')
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGB)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGB++;
						}
					}
					if($nDCnt > $nIndexGB && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGB++;
						}
					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$bLastDay= 0;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			//Rota rest and Gazzeted holidays for shift C
			if((trim ($rstRowRota['rota_rest'] )== "C" && $rstRow['emp_shift'] == '3') || ($nEmpAttCheck > 0 && $bPastLeaves == 1))
			{
				$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
				$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
				$rstRowLD = odbc_fetch_array($nResultLD);
				if($rstRowLD['nLDay'] >= 0)
				{
					$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
						if($bPastLeaves==1)
						{
							$pasteToArray = explode("/", $strTempEndDate);
							$fromArray = explode("/",  $strTempStartDate);
							$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
							$nCounterAtm = 0;
						//echo $nOverWrite;
							do
							{
								$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
								UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
								$nCounterAtm++;
							}
							while($nCounterAtm <= $nDaysAtm);
						}

						//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

				}
				$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
				if($nDCnt == $nDayCount)
					$bLastDay= 0;
			}
			else  if ($rstRow['emp_shift'] == '3')
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGC)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGC++;
						}
					}
					if($nDCnt > $nIndexGC && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGC++;
						}
					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$bLastDay= 0;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			//Rota rest and Gazzeted holidays for shift D
			if((trim ($rstRowRota['rota_rest'] )== "D" && $rstRow['emp_shift'] == '4') || ($nEmpAttCheck > 0 && $bPastLeaves == 1))
			{
				$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
				$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
				$rstRowLD = odbc_fetch_array($nResultLD);
				if($rstRowLD['nLDay'] >= 0)
				{
					$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
						if($bPastLeaves==1)
						{
							$pasteToArray = explode("/", $strTempEndDate);
							$fromArray = explode("/",  $strTempStartDate);
							$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
							$nCounterAtm = 0;
						//echo $nOverWrite;
							do
							{
								$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
								UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
								$nCounterAtm++;
							}
							while($nCounterAtm <= $nDaysAtm);
						}

						//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

				}
				$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
				if($nDCnt == $nDayCount)
					$bLastDay= 0;
			}
			else  if ($rstRow['emp_shift'] == '4')
			{
				//Gazetted Holiday
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $rstRowRota['rota_date'] ."')
																OR  ( hol_end = '". $rstRowRota['rota_date']. "' )
																OR  ( hol_start < '". $rstRowRota['rota_date']. "' AND hol_end  > '".$rstRowRota['rota_date']. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nDCnt == 1 || $nDCnt == $nIndexGD)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGD++;
						}
					}
					if($nDCnt > $nIndexGD && $nDCnt < $nDayCount)
					{
						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays+1, $arrCDate[0]));
							$nIndexGD++;
						}
					}
					if($nDCnt == $nDayCount)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('m/d/Y', mktime(0, 0, 0, $arrCDate[1], $nDays-1, $arrCDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$rstRow['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("/", $strTempEndDate);
									$fromArray = explode("/",  $strTempStartDate);
									$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[0]  , $pasteToArray[1], $pasteToArray[2]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$rstRow['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$rstRowRota['rota_date'],
																	"gzl_todate"=>$rstRowRota['rota_date'],
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$bLastDay= 0;
						}
					}
				}// end nGazzetedHoliday
			} // end else
			$nDCnt++;

		}//end while
		//post Last Day
		if($bLastDay== 1)
		{

			//Post Normal Leave
			$nId = InsertRec("tblfullLeave", array(
										"efl_emp_id"=>$rstRow['emp_id'],
										"efl_emp_by"=>$_SESSION['USER_ID'],
										"efl_Category"=>$strLeavCategory,
										"efl_leaType_id"=>$nLeavTypId,
										"efl_startDate"=>$strTempStartDate,
										"efl_endDate"=>$strEndDate,
										"efl_leavePost"=> date("m/d/Y H:i:s")));
			if($bPastLeaves==1)
			{
				$pasteToArray = explode("-", $strEndDate);
				$fromArray = explode("/",  $strTempStartDate);
				$nDaysAtm = (mktime(0, 0, 0, $pasteToArray[1]  , $pasteToArray[2], $pasteToArray[0]) - mktime(0, 0, 0, $fromArray[0]  , $fromArray[1], $fromArray[2])) / 86400 ;
				$nCounterAtm = 0;
			//echo $nOverWrite;
				do
				{
					$strDateMaster = date("m-d-Y", mktime(0, 0, 0, $fromArray[0], $fromArray[1] + $nCounterAtm, $fromArray[2]));
					UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $rstRow['emp_id'], array("atm_status"=>$atmStatus) );
					$nCounterAtm++;
				}
				while($nCounterAtm <= $nDaysAtm);
			}

			//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

		}
		echo "Leave Successfully Saved.";

	}
	/*
	function Returns General Group Employees Rest Days
	$strStartDate           End Date
	$strEndDate          Start Date
	*/
	function PostGenEmpLeaves($arrRowEmp, $strStartDate, $strEndDate, $nLeavPeriod, $nLeaveType, $bPastLeaves=0)
	{
		//Get Leave type Setting
		$arrLeavTypeSetting = empLeaveTypeSetting($arrRowEmp);
		//$arrGenEmpRest['DATE'] = 0;
		$arrSDate = explode('-', $strStartDate);
		$strTempStartDate = $strStartDate;
		$nDays = substr($arrSDate[2], 0, 2);
		$bLastDay= 1;
		$nGztIndex= 1;

		if($nLeaveType == 99)
		{
			$strLeavCategory = "COMPENSATORY";
			$nLeavTypId = 0;
			$atmStatus = 99;
		}
		else if($nLeaveType == "MISCELLANEOUS")
		{
			$strLeavCategory = "UNPAID";
			$nLeavTypId = 0;
			$atmStatus = 3;
		}
		else
		{
			$nResultRow = GetRecord("tblLeaveType","lea_id = $nLeaveType");
			$strLeavCategory = $nResultRow['lea_category'];
			$nLeavTypId = $nLeaveType;
			$atmStatus = 7;
		}
		for($nCounter = 1; $nCounter <= $nLeavPeriod; $nCounter++)//Count No. of Off Days for an Employee
		{
			$strCDate = date('Y-m-d', mktime(0, 0, 0, $arrSDate[1], $nDays, $arrSDate[0]));
			$nWeekDay = (date('w', mktime(0, 0, 0, $arrSDate[1], $nDays, $arrSDate[0]))+1);

			$nEmpAttCheck = RecCount("tblAttendance"," att_emp_id = " . $arrRowEmp['emp_id'] . " AND att_duty_date = '".$strCDate."'");


			//echo $nWeekDay ."==". $arrRowEmp['emp_offDay1']."------".$strTempStartDate."-------".$nDays."<br>";
			if($nWeekDay == $arrRowEmp['emp_offDay1'] || $nWeekDay == $arrRowEmp['emp_offDay2'] || ($nEmpAttCheck > 0 && $bPastLeaves == 1))
			{
				$strTempEndDate = date('Y-m-d', mktime(0, 0, 0, $arrSDate[1], $nDays-1, $arrSDate[0]));
				$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
				$rstRowLD = odbc_fetch_array($nResultLD);

				if($rstRowLD['nLDay'] >= 0)
				{
						$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$arrRowEmp['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));

						//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

						if($bPastLeaves==1)
						{
							$pasteToArray = explode("-", $strTempEndDate);
							$fromArray = explode("-",  $strTempStartDate);
							$nDaysAtm = ((mktime(0, 0, 0, $pasteToArray[1]  , $pasteToArray[2], $pasteToArray[0]) - mktime(0, 0, 0, $fromArray[1]  , $fromArray[2], $fromArray[0])) / 86400) ;
							$nCounterAtm = 0;
							//echo $nOverWrite;
							do
							{
								$strDateMaster = date("Y-m-d", mktime(0, 0, 0, $fromArray[1], $fromArray[2] + $nCounterAtm, $fromArray[0]));
								UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $arrRowEmp['emp_id'], array("atm_status"=>$atmStatus) );
								$nCounterAtm++;
							}
							while($nCounterAtm <= $nDaysAtm);
						}

						//Mark as Last day
				}
				$strTempStartDate = date('Y-m-d', mktime(0, 0, 0, $arrSDate[1], $nDays+1, $arrSDate[0]));
				if($nCounter == $nLeavPeriod)
					$bLastDay= 0;
			}
			else
			{
				$nGazzetedHoliday = RecCount("tblHoliday", "( hol_start = '". $strCDate ."')
																OR  ( hol_end = '". $strCDate. "' )
																OR  ( hol_start < '". $strCDate. "' AND hol_end  > '". $strCDate. "' )");
				if($nGazzetedHoliday > 0)
				{
					if($nCounter == 1 || $nCounter == $nGztIndex)
					{
						if($arrLeavTypeSetting['les_gfst'] == "ALOWD")
						{
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$arrRowEmp['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$strCDate,
																	"gzl_todate"=>$strCDate,
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('Y-m-d', mktime(0, 0, 0, $arrSDate[1], $nDays+1, $arrSDate[0]));
							$nGztIndex++;
						}
					}
					else if($nCounter > $nGztIndex && $nCounter < $nLeavPeriod)
					{

						if($arrLeavTypeSetting['les_gsndwich'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('Y-m-d', mktime(0, 0, 0, $arrSDate[1], $nDays-1, $arrSDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$arrRowEmp['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("-", $strTempEndDate);
									$fromArray = explode("-",  $strTempStartDate);
									$nDaysAtm = ((mktime(0, 0, 0, $pasteToArray[1]  , $pasteToArray[2], $pasteToArray[0]) - mktime(0, 0, 0, $fromArray[1]  , $fromArray[2], $fromArray[0])) / 86400) ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("Y-m-d", mktime(0, 0, 0, $fromArray[1], $fromArray[2] + $nCounterAtm, $fromArray[0]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $arrRowEmp['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$arrRowEmp['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$strCDate,
																	"gzl_todate"=>$strCDate,
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));
							$strTempStartDate = date('Y-m-d', mktime(0, 0, 0, $arrSDate[1], $nDays+1, $arrSDate[0]));
							$nGztIndex++;

						}
					}
					else if($nCounter == $nLeavPeriod)
					{
						if($arrLeavTypeSetting['les_glst'] == "ALOWD")
						{
							//Post Normal Leave
							$strTempEndDate = date('Y-m-d', mktime(0, 0, 0, $arrSDate[1], $nDays-1, $arrSDate[0]));
							$nResultLD = MSSQLQuery("SELECT  DATEDIFF(d, '".$strTempStartDate."', '".$strTempEndDate."') as nLDay");
							$rstRowLD = odbc_fetch_array($nResultLD);
							if($rstRowLD['nLDay'] >= 0)
							{
								$nId = InsertRec("tblfullLeave", array(
															"efl_emp_id"=>$arrRowEmp['emp_id'],
															"efl_emp_by"=>$_SESSION['USER_ID'],
															"efl_Category"=>$strLeavCategory,
															"efl_leaType_id"=>$nLeavTypId,
															"efl_startDate"=>$strTempStartDate,
															"efl_endDate"=>$strTempEndDate,
															"efl_leavePost"=> date("m/d/Y H:i:s")));
								if($bPastLeaves==1)
								{
									$pasteToArray = explode("-", $strTempEndDate);
									$fromArray = explode("-",  $strTempStartDate);
									$nDaysAtm = ((mktime(0, 0, 0, $pasteToArray[1]  , $pasteToArray[2], $pasteToArray[0]) - mktime(0, 0, 0, $fromArray[1]  , $fromArray[2], $fromArray[0])) / 86400) ;
									$nCounterAtm = 0;
								//echo $nOverWrite;
									do
									{
										$strDateMaster = date("Y-m-d", mktime(0, 0, 0, $fromArray[1], $fromArray[2] + $nCounterAtm, $fromArray[0]));
										UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $arrRowEmp['emp_id'], array("atm_status"=>$atmStatus) );
										$nCounterAtm++;
									}
									while($nCounterAtm <= $nDaysAtm);
								}

								//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));
							}
							//Post Gazetted Holiday Leave
							$nId = InsertRec("tblGazLeaves", array(
																	"gzl_emp_id"=>$arrRowEmp['emp_id'],
																	"gzl_post_by"=>$_SESSION['USER_ID'],
																	"gzl_fromdate"=>$strCDate,
																	"gzl_todate"=>$strCDate,
																	"gzl_leavepost"=>date("m/d/Y H:i:s")
																	));


							$nGztIndex++;
							$bLastDay= 0;
						}
					}
				}// end nGazzetedHoliday
			}
			$nDays++;
		}//end for
		//post Last Day
		if($bLastDay== 1)
		{

			//Post Normal Leave
			$nId = InsertRec("tblfullLeave", array(
										"efl_emp_id"=>$arrRowEmp['emp_id'],
										"efl_emp_by"=>$_SESSION['USER_ID'],
										"efl_Category"=>$strLeavCategory,
										"efl_leaType_id"=>$nLeavTypId,
										"efl_startDate"=>$strTempStartDate,
										"efl_endDate"=>$strEndDate,
										"efl_leavePost"=> date("m/d/Y H:i:s")));

			if($bPastLeaves==1)
			{
				$pasteToArray = explode("-", $strTempEndDate);
				$fromArray = explode("-",  $strTempStartDate);
				$nDaysAtm = ((mktime(0, 0, 0, $pasteToArray[1]  , $pasteToArray[2], $pasteToArray[0]) - mktime(0, 0, 0, $fromArray[1]  , $fromArray[2], $fromArray[0])) / 86400) ;
				$nCounterAtm = 0;
			//echo $nOverWrite;
				do
				{
					$strDateMaster = date("Y-m-d", mktime(0, 0, 0, $fromArray[1], $fromArray[2] + $nCounterAtm, $fromArray[0]));
					UpdateRec("tblAttendanceMaster", " atm_date = '" . $strDateMaster . "' AND atm_emp_id = " . $arrRowEmp['emp_id'], array("atm_status"=>$atmStatus) );
					$nCounterAtm++;
				}
				while($nCounterAtm <= $nDaysAtm);
			}
			//updateRec("tblTempLeave" , "etfl_id = " . $tempId , array("etfl_status"=>1));

		}
		echo "Leave Successfully Saved.";
	}
	/*
	function Returns Employee Service Period in Months
	$nEmpID              Employee db-id
	*/
	function GetServiceLengthFL($nEmpID)
	{
		$Query = "SELECT  DATEDIFF(mm, emp_joiningDate, GETDATE()) as mn FROM tblEmployee WHERE emp_id = $nEmpID";
		$nResult = MSSQLQuery($Query );
		$rstRowMn = odbc_fetch_array($nResult);
		return $rstRowMn['mn'];
	}
	//function
	function empLeaveTypeSetting($rstRowEmp)
	{
		$strSetting = "SELF";
		$bPLeavSetup= true;
		$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = ".$rstRowEmp['emp_id']." AND les_level = 4 ");

//		echo $rstRowEmp['emp_id'] ;


		if(!$rstRowPLS)
		{
			$strSetting = "SECTION";
		}
		else
		{
			switch($rstRowPLS['les_settingSource'])
			{
				case "DENIED":
					$bPLeavSetup= false;
					break;
				case "SECTION_SETTING":
					$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = ".$rstRowEmp['emp_section_id']." AND les_level = 3 ");
					switch($rstRowPLS['les_settingSource'])
					{
					case "DENIED":
						$bPLeavSetup= false;
						break;
					case "DEPARTMENT_SETTING":
						$rstRowDept = GetRecord("tblSection , tblDepartment"," sec_id = ".$rstRowEmp['emp_section_id']." AND dept_id = sec_dept_id");
						$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = ".$rstRowDept['dept_id']." AND les_level = 2 ");

						if($rstRowPLS['les_settingSource'] == "DENIED")
						{
							$bPLeavSetup= false;
						}
						elseif($rstRowPLS['les_settingSource'] == "ORGANIZATIONAL_SETTING")
						{
							$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");
							if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
							{
								$bPLeavSetup= false;
							}
						}
						break;
					case "ORGANIZATIONAL_SETTING":
						$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");
						if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
						{
							$bPLeavSetup= false;
						}
						break;
					}//end section switch
					break;
				case "DEPARTMENT_SETTING":
					$rstRowDept = GetRecord("tblSection , tblDepartment"," sec_id = ".$rstRowEmp['emp_section_id']." AND dept_id = sec_dept_id");
					$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = ".$rstRowDept['dept_id']." AND les_level = 2 ");

					if($rstRowPLS['les_settingSource'] == "DENIED")
					{
						$bPLeavSetup= false;
					}
					elseif($rstRowPLS['les_settingSource'] == "ORGANIZATIONAL_SETTING")
					{
						$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");
						if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
						{
							$bPLeavSetup= false;
						}
					}
					break;
				case "ORGANIZATIONAL_SETTING":
					$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");
					if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
					{
						$bPLeavSetup= false;
					}
					break;
			}// end employee Switch
		}// end else
		//Get Section Setting if not have own
		if($strSetting == "SECTION")
		{

			$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = ".$rstRowEmp['emp_section_id']." AND les_level = 3 ");

			//echo $rstRowEmp['emp_section_id']  ;

			if(!$rstRowPLS)
			{
				$strSetting = "DEPARTMENT";
			}
			else
			{
				switch($rstRowPLS['les_settingSource'])
					{
					case "DENIED":
						$bPLeavSetup= false;
						break;
					case "DEPARTMENT_SETTING":
						$rstRowDept = GetRecord("tblSection , tblDepartment"," sec_id = ".$rstRowEmp['emp_section_id']." AND dept_id = sec_dept_id");
						$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = ".$rstRowDept['dept_id']." AND les_level = 2 ");

						if($rstRowPLS['les_settingSource'] == "DENIED")
						{
							$bPLeavSetup= false;
						}
						elseif($rstRowPLS['les_settingSource'] == "ORGANIZATIONAL_SETTING")
						{
							$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");
							if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
							{
								$bPLeavSetup= false;
							}
						}
						break;
					case "ORGANIZATIONAL_SETTING":
						$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");

						if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
						{
							$bPLeavSetup= false;
						}
						break;
					}//end switch
				}//else end
			}//end section if
			// IF SECTION IS ALSO NOT SETTED
			if($strSetting == "DEPARTMENT")
			{
				$rstRowDept = GetRecord("tblSection , tblDepartment"," sec_id = ".$rstRowEmp['emp_section_id']." AND dept_id = sec_dept_id");
				$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = ". $rstRowDept['dept_id'] ." AND les_level = 2 ");
				if(!$rstRowPLS)
				{
					$strSetting = "ORGANIZATION";
				}
				else
				{
					if($rstRowPLS['les_settingSource'] == "DENIED")
					{
						$bPLeavSetup= false;
					 }
					 elseif($rstRowPLS['les_settingSource'] == "ORGANIZATIONAL_SETTING")
					 {
						$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");

						if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
						{
							$bPLeavSetup= false;
						}
					  }
				  }//else end
			}//end if
			// IF DEPARTMENT IS ALSO NOT SETTED
			if($strSetting == "ORGANIZATION")
			{
				$rstRowPLS = GetRecord("tblLeavetypeSetting", " les_deptSec_id = 0 AND les_level = 1 ");
				if(!$rstRowPLS || $rstRowPLS['les_settingSource'] == "DENIED")
				{
					$bPLeavSetup= false;
				}
			}//END IF
			return $rstRowPLS;
		}//end function
	/*
		the function displays combox box

		nSelectedVal:		index of selected value
		arr:				array containig items to be displayed
		bIndexValue:		true: use array index as item value e.g: 0, 1, 2, ...
							false: use array value as item value e.g: 2003, 2004, 2005, ...

	function ComboBoxPD($nSelectedVal, $arr, $bIndexValue)
	{
		for($i=0; $i < sizeof($arr); $i++)
		{
			//$j = $i+1;

			if($i == $nSelectedVal)
				if($bIndexValue == true)
					echo "<option value=$i selected>" . $arr[$i] . "\r\n";
				else
					echo "<option selected>" . $arr[$i] . "\r\n";
			else
				if($bIndexValue == true)
					echo "<option value=$i>" . $arr[$i] . "\r\n";
				else
					echo "<option>" . $arr[$i] . "\r\n";
		}
	}

		the function draws combo box fitted in table row by
		using the function ComboBox();

	function ArrayComboBoxPD($strLable, $strName, $nSelectedVal, $arr, $bIndexValue, $callBack ='')
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLable;
		echo "	</td>";
		echo "	<td>";
		echo "		<select name=$strName $callBack>";
		ComboBoxPD($nSelectedVal, $arr, $bIndexValue);
		echo "		</select>";
		echo "	</td>";
		echo "</tr>";
	}
	*/
	/*
		the function shows the date combo box
	*/
	function DateCombo($strLabel, $strField, $strDate)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		$strDate = strtok($strDate, " ");

		if(empty($strDate))
			$strDate = date("Y-m-d");

		$strYr = strtok($strDate, "-");
		$strMn = strtok("-");
		$strDy = strtok("-");

		$arrYr = array();
		$nDisplayMaxYear = date("Y") -18;
		for($i = 1900; $i <= $nDisplayMaxYear; $i++)
			array_push($arrYr, $i);

		$arrDay = array();
		for($i = 1; $i <= 31; $i++)
			array_push($arrDay, $i);

		$strTemp = $strField . "Year";
		echo "<select name=$strTemp>";
		ComboBoxValueSelected($strYr, $arrYr, false);
		echo "</select>";

		$strTemp = $strField . "Month";
		echo "<select name=$strTemp>";
		$arr = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		ComboBox($strMn-1, $arr, true);
		echo "</select>";

		$strTemp = $strField . "Date";
		echo "<select name=$strTemp>";
		ComboBox($strDy-1, $arrDay, true);
		echo "</select>";

		echo "	</td>";
		echo "</tr>";
	}

	function MonthYearCombo($strLabel, $strField, $strDate)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		$strDate = strtok($strDate, " ");

		if(empty($strDate))
			$strDate = date("Y-m-d");

		$strYr = strtok($strDate, "-");
		$strMn = strtok("-");
		$strDy = strtok("-");

		$arrYr = array();

		for($i = $strYr-1; $i <= ($strYr+5); $i++)
			array_push($arrYr, $i);

		$arrDay = array();
		for($i = 1; $i <= 31; $i++)
			array_push($arrDay, $i);

		$strTemp = $strField . "Year";
		echo "<select name=$strTemp>";
		ComboBox(1, $arrYr, false);
		echo "</select>";

		$strTemp = $strField . "Month";
		echo "<select name=$strTemp>";
		$arr = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		ComboBox($strMn-1, $arr, true);
		echo "</select>";
		echo "	</td>";
		echo "</tr>";
	}


	function MonthYearComboForCanteenSummary($strLabel, $strField, $strDate)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		$strDate = strtok($strDate, " ");

		if(empty($strDate))
			$strDate = date("Y-m-d");

		$strYr = strtok($strDate, "-");
		$strMn = strtok("-");
		$strDy = strtok("-");

		$arrYr = array();

		for($i = $strYr-5; $i <= ($strYr+5); $i++)
			array_push($arrYr, $i);

		$arrDay = array();
		for($i = 1; $i <= 31; $i++)
			array_push($arrDay, $i);

		$strTemp = $strField . "Year";
		echo "<select name=$strTemp>";
		ComboBox(5, $arrYr, false);
		echo "</select>";

		$strTemp = $strField . "Month";
		echo "<select name=$strTemp>";
		$arr = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		ComboBox($strMn-1, $arr, true);
		echo "</select>";
		echo "	</td>";
		echo "</tr>";
	}

	//Combo Show Previous Month
	function MonthYearCombopr($strLabel, $strField, $strDate)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		$strDate = strtok($strDate, " ");

		if(empty($strDate))
			$strDate = date("Y-m-d");

		$strYr = strtok($strDate, "-");
		$strMn = strtok("-");
		$strDy = strtok("-");

		$arrYr = array();

		for($i = $strYr-1; $i <= ($strYr+5); $i++)
			array_push($arrYr, $i);

		$arrDay = array();
		for($i = 1; $i <= 31; $i++)
			array_push($arrDay, $i);

		$strTemp = $strField . "Year";
		echo "<select name=$strTemp>";
		ComboBox(1, $arrYr, false);
		echo "</select>";

		$strTemp = $strField . "Month";
		echo "<select name=$strTemp>";
		$arr = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		ComboBox($strMn-2, $arr, true);
		echo "</select>";
		echo "	</td>";
		echo "</tr>";
	}
	/*
		the function shows time combox with first combo of hours and
		second combo of minutes.

		strTime:		time to show in combo
						Format: hh:mm[:ss]
	*/
	function TimeCombo($strLabel, $strField, $strTime)
	{
		$arrTime = explode(':', $strTime);

		$nHr = $arrTime[0];
		$nMn = $arrTime[1];
		$arrHr = array();

		for($i = 0; $i <= 23; $i++)
			array_push($arrHr, $i);

		$strHr = $strField . "Hr";
		$strMn = $strField . "Mn";
		$arrMn = array();
		$nCounter = 0;
		for($i = 0; $i <= 59; $i += 5)
		{
			array_push($arrMn, $i);
			if($i ==$nMn)
				$nShowMn = $nCounter;
			$nCounter++;
		}
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo "<table><tr><td>";
		echo "<select name=$strHr>";
		ComboBox($nHr, $arrHr, true);
		echo "</select></td>
			<td>h</td>";
		echo "<td><select name=$strMn>";
		ComboBox($nShowMn, $arrMn, true);
		echo "</select></td>
			<td>m</td></tr></table>";
		echo "	</td>";
		echo "</tr>";

	}

	/*
		the function shows time combox with first combo of hours and
		second combo of minutes.

		strTime:		time to show in combo
						Format: hh:mm[:ss]
	*/
	function TimeComboOvertime($strLabel, $strField, $strTime)
	{
		$arrTime = explode(':', $strTime);

		$nHr = $arrTime[0];
		$nMn = $arrTime[1];
		$arrHr = array();

		for($i = 0; $i <= 23; $i++)
			array_push($arrHr, $i);

		$strHr = $strField . "Hr";
		$strMn = $strField . "Mn";
		$arrMn = array();
		$nCounter = 0;
		for($i = 0; $i <= 59; $i++)
		{
			array_push($arrMn, $i);
			if($i ==$nMn)
				$nShowMn = $nCounter;
			$nCounter++;
		}
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo "<table><tr><td>";
		echo "<select name=$strHr>";
		ComboBox($nHr, $arrHr, true);
		echo "</select></td>
			<td>h</td>";
		echo "<td><select name=$strMn>";
		ComboBox($nShowMn, $arrMn, true);
		echo "</select></td>
			<td>m</td></tr></table>";
		echo "	</td>";
		echo "</tr>";

	}

//////////

function TimeCombo1($strLabel, $strField, $strTime)
	{
		$arrTime = explode(':', $strTime);

		$nHr = $arrTime[0];
		$nMn = $arrTime[1];
		$arrHr = array();
		$arrHr[0] = "---";

		for($i = 0; $i <= 23; $i++)
			array_push($arrHr, $i);

		$strHr = $strField . "Hr";
		$strMn = $strField . "Mn";
		$arrMn = array();
		$nCounter = 0;
		$arrMn[0]=  "---";
		for($i = 0; $i <= 59; $i += 5)
		{
			array_push($arrMn, $i);
			if($i ==$nMn)
				$nShowMn = $nCounter;
			$nCounter++;
		}
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo "<select name=$strHr>";
		ComboBox($nHr, $arrHr, true);
		echo "</select>";
		echo "<select name=$strMn>";
		ComboBox($nShowMn, $arrMn, true);
		echo "</select>";
		echo "	</td>";
		echo "</tr>";

	}



	/*
		the function shows a combo box with values from a table

		strTable:			table name
		strDispField:		field name to show
		strIDField:			id field name
		strCriteria:		select criteria for where clause
		strName:			combo name
		nSelId:				id of selected record
		$nAllUnDef          optional Add additinal Text fields (ALL/Unknown) at 0 or 1 index
	*/
	function TableCombo($strLabel, $strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
			$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";

		$nResult = MsSQLQuery($strQuery);
		echo "<tr>
				<td>".
					$strLabel;
		echo    "</td>
				<td>";
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0000>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000>--------------- \r\n";
					if($nAllUnDef == 2)
						echo "<option value=1> Stock \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strIDField];

						if($nID == $nSelId)
							echo "<option value=$nID selected>"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}
				}
				else
				{
					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strDispField];

						if($nID == $nSelId)
							echo "<option value=$nID  selected >"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}




				}
		echo "</select></td></tr>";
	}

	function TableComboForLeaveType($strLabel, $strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
			$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";


		$nResult = MsSQLQuery($strQuery);
		echo "<tr>
				<td>".
					$strLabel;
		echo    "</td>
				<td>";
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=''>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000>--------------- \r\n";
					if($nAllUnDef == 2)
						echo "<option value=1> Stock \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strIDField];

						if($nID == $nSelId)
							echo "<option value=$nID selected>"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}
					echo "<option value='0'>Unpaid Leaves\n\r";
					echo "<option value='compensatoryleave'>Compensatory Leaves\n\r";
				}
				else
				{
					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=''>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strDispField];

						if($nID == $nSelId)
							echo "<option value=$nID  selected >"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}
					echo "<option value='UN_PAID'>Unpaid Leaves\n\r";
					echo "<option value='compensatoryleave'>Compensatory Leaves\n\r";


				}
		echo "</select></td></tr>";
	}

	/*
		the function shows a combo box with values from a table

		strTable:			table name
		strDispField:		field name to show
		strIDField:			id field name
		strCriteria:		select criteria for where clause
		strName:			combo name
		nSelId:				id of selected record
		$nAllUnDef          optional Add additinal Text fields (ALL/Unknown) at 0 or 1 index
	*/
	function TableComboQry($strLabel, $strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MsSQLQuery($strQuery);
		echo "<tr>
				<td>".
					$strLabel;
		echo    "</td>
				<td>";
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0000>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strIDField];

						if($nID == $nSelId)
							echo "<option value='$nID' selected>"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value='$nID'>" . $rstRow[$strDispField] . "\r\n";
					}
				}
				else
				{
					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strDispField];

						if(trim($nID) == trim($nSelId))
							echo "<option value='$nID'  selected >"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value='$nID'>" . $rstRow[$strDispField] . "\r\n";
					}




				}
		echo "</select></td></tr>";
	}


	function TableComboQry1($strLabel, $strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MsSQLQuery($strQuery);
		echo "
			<td>".
					$strLabel;
		echo    "</td>
				<td>";
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0000>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strIDField];

						if($nID == $nSelId)
							echo "<option value=$nID selected>"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}
				}
				else
				{
					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strDispField];

						if(trim($nID) == trim($nSelId))
							echo "<option value=$nID  selected >"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}




				}
		echo "</select></td>";
	}




	/*
		the function shows a combo box Multi with values from a table

		strTable:			table name
		strDispField:		field name to show
		strIDField:			id field name
		strCriteria:		select criteria for where clause
		strName:			combo name
		nSelId:				id of selected record
		$nAllUnDef          optional Add additinal Text fields (ALL/Unknown) at 0 or 1 index
	*/
	function TableComboMulti($strLabel, $strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
			$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";

		$nResult = MsSQLQuery($strQuery);
		echo "<tr>
				<td>".
					$strLabel;
		echo    "</td>
				<td>";
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback multiple><br>";
					if($nAllUnDef == 0)
						echo "<option value=0000>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strIDField];

						if($nID == $nSelId)
							echo "<option value=$nID selected>"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}
				}
				else
				{
					echo "<select name=$strName $callback multiple><br>";
					if($nAllUnDef == 0)
						echo "<option value=0>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0>--------------- \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strDispField];

						if($nID == $nSelId)
							echo "<option value=$nID  selected >"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}




				}
		echo "</select> </td></tr>";
	}
	/*
		function return the Minimum Salary Grade id of given Section
		nSec_id : Section id to find grade id
	*/

	function GetMinSectionSalGrade($nSec_id)
	{
		$strQuery = "SELECT sg_id
						FROM tblSalGrade
							WHERE sg_secDept_id = ".$nSec_id." AND sg_salary =(SELECT	MIN(sg_salary)
										FROM tblSalGrade
											WHERE sg_secDept_id = ".$nSec_id.")";
		$nResult = MsSQLQuery($strQuery);
		$rstRow = odbc_fetch_array($nResult);
		if (!$rstRow)
			return 0 ;
		else
		 	return $rstRow['sg_id'];

	}
	/*
	function Returns Employee Service Period in Months
	$nEmpID              Employee db-id
	*/
	function GetServiceLength($nEmpID)
	{
		$Query = "SELECT tblEmployee.*, DATEDIFF(mm, emp_joiningDate, GETDATE()) as mn FROM tblEmployee WHERE emp_id = $nEmpID";
		$nResult = MSSQLQuery($Query );
		$rstRowMn = odbc_fetch_array($nResult);
		return $rstRowMn['mn'] + $rstRowMn['emp_adjustService'] - $rstRowMn['emp_GateLock_fine'] -  $rstRowMn['emp_other_fine'];
	}

	/*
		Function return the DeptSection ID and Level of Source of SalGrade Setting
		nDeptSecID : Section id to find Source DeptSecttion ID
		nLevel : Section Level to find Source DeptSecttion Level
	*/
	function getSourceDeptSecID( &$nDeptSecID , &$nLevel )
	{
		$rstRow = GetRecord("tblSection , tblDepartment", "sec_id = ".$nDeptSecID."AND dept_id = sec_dept_id ");

		if($rstRow['sec_salGradeSource'] == "ORGANIZATIONAL_SETTING" )
		{
			$nLevel = 1;
			$nDeptSecID = 0;
		}
		elseif( $rstRow['sec_salGradeSource'] == "DEPARTMENT_SETTING" )
		{
			$nLevel = 2;
			$nDeptSecID = $rstRow['sec_dept_id'];
			if( $rstRow['dept_salGradeSource'] == "ORGANIZATIONAL_SETTING" )
			{
				$nLevel = 1;
				$nDeptSecID = 0;
			}
		}

	}

	/*
		function displays combo box of District/tehsils
		$strLabel = label shown in html table
		strName : Name of HTML object
		nSelId : Id of selected cost center. Default value is -1
		blnAll : if 1 then any value can be selected, if 0 then only lowest level values can be selected
		$nAllUnDef  optional Add additinal Text fields (ALL/Unknown) at 0 or 1 index
	*/
	function tehsilDistrict($strLabel,$strName, $nSelId=-1, $blnAll=1,$nAllUnDef = -1)
	{
		$strUnique = mktime();
		$strQuery = "select * from tblDistrict order by dis_name";
		$nResult = MsSQLQuery($strQuery);
		$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<tr><td>" .$strLabel . "</td><td>";

		if($blnAll)
			echo "<select name='$strName'>\n\r";
		else
			echo "<select name='$strName' onChange=\"func_checkTD_$strUnique(this)\">\n\r";
		if($nAllUnDef == 0)
			echo "<option value=0000>ALL \r\n";

		if($nAllUnDef == 1)
			echo "<option value=0000>---------------- \r\n";

		while($rstRow = odbc_fetch_array($nResult))
		{
			$strCode = $rstRow["dis_id"];
			$strName = $rstRow["dis_name"];
			echo "<option value='D_".$strCode."'>".$strName . "\n\r" ;
			$strQuery = "select * from tblTehsil WHERE teh_dis_id =".$strCode. "order by teh_name";
			$nResult2 = MsSQLQuery($strQuery);

			while($rstRow2 = odbc_fetch_array($nResult2))
			{
				$tehCode = $rstRow2['teh_id'];
				$tehName = $spaces.$rstRow2['teh_name'];
				if($nSelId == $tehCode)
					echo "<option value='$tehCode' SELECTED>". $tehName . "\n\r";
				else
					echo "<option value='$tehCode'>". $tehName . "\n\r" ;
			}
		}
		echo "</select>\n\r";
		echo "</td>";
		if(!$blnAll)
		{
			echo "
			<script>
				function func_checkTD_$strUnique(obj)
				{
					if(obj.value.substring(0, 1) == 'D')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
				}
			</script>
				";
		}
	}

	function HRBResidenceReadOnly($strLabel, $empId)
	{

		if( RecCount("tblHouse, tblRoom, tblBed", "bed_emp_id = ". $empId ." AND rom_id = bed_rom_id  AND hou_id = rom_hou_id")> 0)
		{
			$strQuery = "SELECT * FROM tblHouse, tblRoom, tblBed WHERE bed_emp_id = ". $empId ." AND rom_id = bed_rom_id  AND hou_id = rom_hou_id";
			$nResult = MsSQLQuery($strQuery);
			$rstRow = odbc_fetch_array($nResult);
			$oldRes = $rstRow['bed_id'];
			$currentRes = $rstRow['hou_address']. ", Room ".$rstRow['rom_number'].", Bed ". $rstRow['bed_number'];

		}
		else
			$currentRes  = "Non Resident";
		echo "<tr><td>" .$strLabel . "</td><td>";
		echo $currentRes . "</td><td>";
	}

	/*
		function displays combo box of House/Rooms/Beds
		$strLabel = label shown in html table
		strName : Name of HTML object
		nSelId : Id of selected cost center. Default value is -1
		blnAll : if 1 then any value can be selected, if 0 then only lowest level values can be selected
		$nAllUnDef  optional Add additinal Text fields (ALL/Unknown) at 0 or 1 index
	*/
	function HRBResidence($strLabel,$strName, $nSelId=-1, $blnAll=1, $nAllUnDef=-1)
	{
		$strUnique = mktime();
		$strQuery = "select * from tblHouse order by hou_address";
		$nResult = MsSQLQuery($strQuery);
		echo "<tr><td>" .$strLabel . "</td><td>";
		if($blnAll)
			echo "<select name='$strName'>\n\r";
		else
			echo "<select name='$strName' onChange=\"func_checkHRB_$strUnique(this)\">\n\r";
		if($nAllUnDef == 0)
			echo "<option value=0000>ALL \r\n";

		if($nAllUnDef == 1)
			echo "<option value=0000>Non Resident \r\n";
		$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		while($rstRow = odbc_fetch_array($nResult))
		{
			//adding house
			$strCode = $rstRow["hou_id"];
			$strName = $rstRow["hou_address"];
			$bHou = true;

			// adding rooms
			$strQuery = "select * from tblRoom WHERE rom_hou_id =".$strCode. "order by rom_number";
			$nResult2 = MsSQLQuery($strQuery);

			while($rstRow2 = odbc_fetch_array($nResult2))
			{
				$romCode = $rstRow2['rom_id'];
				$romNumber = $spaces.$rstRow2['rom_number'];
				$bRom = true;
				// add beds
				$strQuery = "select * from tblBed WHERE bed_rom_id =".$romCode. " AND bed_emp_id = -1 order by bed_number";
				$nResult3 = MsSQLQuery($strQuery);
				while($rstRow3 = odbc_fetch_array($nResult3))
				{
					if($bHou)
					{
						echo "<option value='h".$strCode."'>". $strName . "\n\r" ;
						$bHou = false;
					}
					if($bRom)
					{
						echo "<option value='r".$romCode."'>". $romNumber . "\n\r" ;
						$bRom = false;
					}

					$bedCode = $rstRow3['bed_id'];
					$bedNumber = $spaces.$spaces.$rstRow3['bed_number'];
					if($nSelId == $bedCode)
						echo "<option value='".$bedCode."' SELECTED>". $bedNumber . "\n\r";
					else
						echo "<option value='".$bedCode."'>". $bedNumber . "\n\r" ;
				}//end of bed while
			}
		}
		echo "</select>\n\r";
		echo "</td></tr>";
		if(!$blnAll)
		{
			echo "
			<script>
				function func_checkHRB_$strUnique(obj)
				{
					if(obj.value.substring(0, 1) == 'h')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
					if(obj.value.substring(0, 1) == 'r')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
				}
			</script>
				";
		}

	}

		/*
		function displays combo box of All House/Rooms/Beds
		$strLabel = label shown in html table
		strName : Name of HTML object
		nSelId : Id of selected cost center. Default value is -1
		blnAll : if 1 then any value can be selected, if 0 then only lowest level values can be selected
		$nAllUnDef  optional Add additinal Text fields (ALL/Unknown) at 0 or 1 index
	*/
	function HRBResidenceAll($strLabel,$strName, $nSelId=-1, $blnAll=1, $nAllUnDef=-1)
	{
		$strUnique = mktime();
		$strQuery = "select * from tblHouse order by hou_address";
		$nResult = MsSQLQuery($strQuery);
		echo "<tr><td>" .$strLabel . "</td><td>";
		if($blnAll)
			echo "<select name='$strName'>\n\r";
		else
			echo "<select name='$strName' onChange=\"func_checkHRB_$strUnique(this)\">\n\r";
		if($nAllUnDef == 0)
			echo "<option value=0000>ALL \r\n";

		if($nAllUnDef == 1)
			echo "<option value=0000>Non Resident \r\n";
		$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		while($rstRow = odbc_fetch_array($nResult))
		{
			//adding house
			$strCode = $rstRow["hou_id"];
			$strName = $rstRow["hou_address"];
			$bHou = true;

			// adding rooms
			$strQuery = "select * from tblRoom WHERE rom_hou_id =".$strCode. "order by rom_number";
			$nResult2 = MsSQLQuery($strQuery);

			while($rstRow2 = odbc_fetch_array($nResult2))
			{
				$romCode = $rstRow2['rom_id'];
				$romNumber = $spaces.$rstRow2['rom_number'];
				$bRom = true;
				// add beds
				$strQuery = "select * from tblBed WHERE bed_rom_id =".$romCode. " order by bed_number";
				$nResult3 = MsSQLQuery($strQuery);
				while($rstRow3 = odbc_fetch_array($nResult3))
				{
					if($bHou)
					{
						echo "<option value='H_".$strCode."'>". $strName . "\n\r" ;
						$bHou = false;
					}
					if($bRom)
					{
						echo "<option value='R_".$romCode."'>". $romNumber . "\n\r" ;
						$bRom = false;
					}

					$bedCode = $rstRow3['bed_id'];
					$bedNumber = $spaces.$spaces.$rstRow3['bed_number'];
					if($nSelId == $bedCode)
						echo "<option value='".$bedCode."' SELECTED>". $bedNumber . "\n\r";
					else
						echo "<option value='".$bedCode."'>". $bedNumber . "\n\r" ;
				}//end of bed while
			}
		}
		echo "</select>\n\r";
		echo "</td></tr>";
		if(!$blnAll)
		{
			echo "
			<script>
				function func_checkHRB_$strUnique(obj)
				{
					if(obj.value.substring(0, 1) == 'h')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
					if(obj.value.substring(0, 1) == 'r')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
				}
			</script>
				";
		}

	}
	/*
		function displays combo box of Department/sections
		$strLabel = label shown in html table
		strName : Name of HTML object
		nSelId : Id of selected cost center. Default value is -1
		blnAll : if 1 then any value can be selected, if 0 then only lowest level values can be selected
	*/

	function DeptSectionCombo($strLabel,$strName, $nSelId=-1, $blnAll=1, $callBack = '')
	{
		$strUnique = mktime();
		$strQuery = "select * from tblDepartment order by dept_name";
		$nResult = MsSQLQuery($strQuery);
		$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<tr><td>" .$strLabel . "</td><td>";

		if($blnAll)
			echo "<select name='$strName' $callBack>\n\r";
		else
			echo "<select name='$strName' onChange=\"func_check_$strUnique(this); $callBack\">\n\r";
		if($nSelId < 0)
			echo "<option value='0000'>Dynamic Sportswear (Pvt) Ltd.\n\r" ;
		elseif($nSelId == 0)
			echo "<option value='0000'>-----------------------------------------------\n\r" ;

		while($rstRow = odbc_fetch_array($nResult))
		{
			$strCode = $rstRow["dept_id"];
			$strName = $spaces . $rstRow["dept_name"];
			echo "<option value='D_".$strCode."'>". $strName . "\n\r" ;
			$strQuery = "select * from tblSection WHERE sec_dept_id =".$strCode. "order by sec_name";
			$nResult2 = MsSQLQuery($strQuery);

			while($rstRow2 = odbc_fetch_array($nResult2))
			{
				$secCode = $rstRow2['sec_id'];
				$secName = $spaces.$spaces.$rstRow2['sec_name'];
				if($nSelId == $secCode)
					echo "<option value='$secCode' SELECTED>". $secName . "\n\r";
				else
					echo "<option value='$secCode'>". $secName . "\n\r" ;
			}
		}
		echo "</select>\n\r";
		echo "</td>";
		if(!$blnAll)
		{
			echo "
			<script>
				function func_check_$strUnique(obj)
				{
					if(obj.value.substring(0, 1) == 'D')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
					if(obj.value == '0000')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
				}
			</script>
				";
		}
	}


// Adnan Added this Function.

	/*
		function displays combo box of Employee wise Customers.
		strLabel = label shown in html table
		strName : Name of HTML object
		user_id = is the user_id from the session.
	*/


	function EmployeeCustomerAssociationCombo($strLabel,$strName,$user_id)
	{

		$strQuery = "SELECT * FROM tblMkrtCustomer INNER JOIN
                       	tblAuthorizationEditOrders ON tblMkrtCustomer.cus_id = tblAuthorizationEditOrders.edit_cust_id
					   		WHERE (tblAuthorizationEditOrders.edit_allow = 1) AND (tblAuthorizationEditOrders.edit_emp_id = ".$user_id.") ORDER BY tblMkrtCustomer.cus_name";

		$nResult = MsSQLQuery($strQuery);
		$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<tr><td>" .$strLabel . "</td><td>";

		echo "<select name='$strName'>\n\r";
		echo "<option value=0000>". " ---------------- "  . "</option>";

		while($rstRow = odbc_fetch_array($nResult))
		{
			$strCode = $rstRow["cus_id"];
			$strName = $spaces . $rstRow["cus_name"];
			echo "<option value=".$strCode.">". $strName . "\n\r" ;
		}
		echo "</select>\n\r";
		echo "</td>";
	}

// Adnan Added this Function.


	/*
		function displays combo box of Department/sections
		$strLabel = label shown in html table
		strName : Name of HTML object
		nSelId : Id of selected cost center. Default value is -1
		blnAll : if 1 then any value can be selected, if 0 then only lowest level values can be selected
	*/

	function LeaveCombo($rstRowEmp, $strLabel, $strName, $nSelId=-1, $bSpecial = 0)
	{
		$strUnique = mktime();
		$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<tr><td>" .$strLabel . "</td><td>";
		echo "<select name='$strName' onChange=\"func_check_$strUnique(this)\">\n\r";

		$rstRowLTS = empLeaveTypeSetting($rstRowEmp);


		echo "<option value='Compensatory'>Compensatory Leaves\n\r";
		if(trim($nSelId) == 'COMPENSATORY')
			echo "<option value='99' SELECTED>$spaces Compensatory\n\r";
		else
			echo "<option value='99'>$spaces Compensatory\n\r";


		echo "<option value='NORMAL'>Normal Leaves\n\r";

		$strQuery = "select * from tblLeaveType WHERE lea_category = 'NORMAL' AND lea_les_id =".$rstRowLTS['les_id']." ORDER BY lea_type";
		$nResult2 = MsSQLQuery($strQuery);
		while($rstRow2 = odbc_fetch_array($nResult2))
		{
			$secCode = $rstRow2['lea_id'];
			$secName = $spaces.$rstRow2['lea_type'];
			if($nSelId == $secCode)
				echo "<option value='$secCode' SELECTED>". $secName . "\n\r";
			else
				echo "<option value='$secCode'>". $secName . "\n\r" ;
		}


		echo "<option value='UN_PAID'>Unpaid Leaves\n\r";

		if(trim($nSelId) == 'MISCELLANEOUS')
			echo "<option value='MISCELLANEOUS' SELECTED>$spaces Miscellaneous\n\r";
		else
			echo "<option value='MISCELLANEOUS'>$spaces Miscellaneous\n\r";



		if($bSpecial > 0)
		{
			echo "<option value='SPECIAL'>Special Leaves\n\r";
			$strQuery = "select * from tblLeaveType WHERE lea_category = 'SPECIAL' AND lea_les_id =".$rstRowLTS['les_id']." ORDER BY lea_type";
			$nResult2 = MsSQLQuery($strQuery);
			while($rstRow2 = odbc_fetch_array($nResult2))
			{
				$secCode = $rstRow2['lea_id'];
				if(empty($rstRow2['lea_type']))
				{
					$getLeavName = GetRecord("tblLeaveNames", "len_id=".$secCode);
					$rstRow2['lea_type'] = $getLeavName['len_name'];
				}
				$secName = $spaces.$rstRow2['lea_type'];
				if($nSelId == $secCode)
					echo "<option value='$secCode' SELECTED>". $secName . "\n\r";
				else
					echo "<option value='$secCode'>". $secName . "\n\r" ;
			}
		}
		echo "</select>\n\r";
		echo "</td>";

		echo "
		<script>
			function func_check_$strUnique(obj)
			{
				if(obj.value == 'UN_PAID' || obj.value == 'NORMAL' || obj.value == 'SPECIAL' || obj.value == 'Compensatory')
				{
					alert('Please select lowest level Leave type');
					obj.value = '';
				}
			}
		</script>
			";
	}


	/*
	function LeaveCombo($rstRowEmp, $strLabel, $strName, $nSelId=-1, $bSpecial = 0)
	{
		$strUnique = mktime();
		$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<tr><td>" .$strLabel . "</td><td>";
		echo "<select name='$strName' onChange=\"func_check_$strUnique(this)\">\n\r";

		$rstRowLTS = empLeaveTypeSetting($rstRowEmp);
		echo "<option value='NORMAL'>Normal Leaves\n\r";
		$strQuery = "select * from tblLeaveType WHERE lea_category = 'NORMAL' AND lea_les_id =".$rstRowLTS['les_id']." ORDER BY lea_type";
		$nResult2 = MsSQLQuery($strQuery);
		while($rstRow2 = odbc_fetch_array($nResult2))
		{
			$secCode = $rstRow2['lea_id'];
			$secName = $spaces.$rstRow2['lea_type'];
			if($nSelId == $secCode)
				echo "<option value='$secCode' SELECTED>". $secName . "\n\r";
			else
				echo "<option value='$secCode'>". $secName . "\n\r" ;
		}


		echo "<option value='UN_PAID'>Unpaid Leaves\n\r";

		if(trim($nSelId) == 'MISCELLANEOUS')
			echo "<option value='MISCELLANEOUS' SELECTED>$spaces Miscellaneous\n\r";
		else
			echo "<option value='MISCELLANEOUS'>$spaces Miscellaneous\n\r";

		if($bSpecial > 0)
		{
			echo "<option value='SPECIAL'>Special Leaves\n\r";
			$strQuery = "select * from tblLeaveType WHERE lea_category = 'SPECIAL' AND lea_les_id =".$rstRowLTS['les_id']." ORDER BY lea_type";
			$nResult2 = MsSQLQuery($strQuery);
			while($rstRow2 = odbc_fetch_array($nResult2))
			{
				$secCode = $rstRow2['lea_id'];
				$secName = $spaces.$rstRow2['lea_type'];
				if($nSelId == $secCode)
					echo "<option value='$secCode' SELECTED>". $secName . "\n\r";
				else
					echo "<option value='$secCode'>". $secName . "\n\r" ;
			}
		}
		echo "</select>\n\r";
		echo "</td>";

		echo "
		<script>
			function func_check_$strUnique(obj)
			{
				if(obj.value == 'UN_PAID' || obj.value == 'NORMAL' || obj.value == 'SPECIAL')
				{
					alert('Please select lowest level Leave type');
					obj.value = '';
				}
			}
		</script>
			";
	}
	*/




	/*
		the function creates radio buttons group

		strLabel:		lable to be shown in the right cell
		arrButtons:		the lables to be shown along radio buttons
		strName:		form name for the button group
		nSelIndex:		index of selected button
	*/
	function RadioButtons($strLabel, $arrButtons, $strName, $nSelIndex = -1, $startIndex = 0, $callBack = '')
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		$nMaxIndex =sizeof($arrButtons) + $startIndex;
		for($i=$startIndex; $i<$nMaxIndex; $i++)
			if($i == $nSelIndex)
				echo "<input type=radio value=$i name=$strName checked  $callBack >" . $arrButtons[$i-$startIndex] . "<br>";
			else
				echo "<input type=radio value=$i name=$strName  $callBack >" . $arrButtons[$i-$startIndex] . "<br>";

		echo "	</td>";
		echo "</tr>";
	}

	function radiobuttonswithout_tr($strLabel, $arrButtons, $strName, $nSelIndex = -1, $startIndex = 0, $callBack = '')
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		$nMaxIndex =sizeof($arrButtons) + $startIndex;
		for($i=$startIndex; $i<$nMaxIndex; $i++)
			if($i == $nSelIndex)
				echo "<input type=radio value=$i name=$strName checked  $callBack >" . $arrButtons[$i-$startIndex] ;
			else
				echo "<input type=radio value=$i name=$strName  $callBack >" . $arrButtons[$i-$startIndex] ;

		echo "	</td>";
		echo "</tr>";
	}

	/*
		the function creates radio buttons group in Single Row

		strLabel:		lable to be shown in the right cell
		arrButtons:		the lables to be shown along radio buttons
		strName:		form name for the button group
		nSelIndex:		index of selected button
	*/
	function RadioButtonsSingleRow($strLabel, $arrButtons, $strName, $nSelIndex = -1, $startIndex = 0, $callBack = '')
	{
		echo "<tr>";
		echo "	<td valign=top><b>";

		echo		$strLabel;
		echo "	</b></td>";
		echo "</tr><tr>";
		$nMaxIndex =sizeof($arrButtons) + $startIndex;
		for($i=$startIndex; $i<$nMaxIndex; $i++)
		{
			echo "	<td>";
			if($i == $nSelIndex)
				echo "<input type=radio value=$i name=$strName checked  $callBack >" . $arrButtons[$i-$startIndex] . "<br>";
			else
				echo "<input type=radio value=$i name=$strName  $callBack >" . $arrButtons[$i-$startIndex] . "<br>";
			echo "	</td>";
		}
		echo "</tr>";
	}
	/*
		the function draws a check box in the for

		strLabel:			label in the left column
		strName:			name of check box in HTML form
		nChecked:			if true, checkbox will appear checked
							otherwise it appears unchecked
	*/
	function CheckBox($strLabel, $strName, $nChecked = 0, $nCallBack = '')
	{
		echo "<tr><td></td><td>";

		if($nChecked == 1)
			echo "<input type=checkbox name=$strName CHECKED $nCallBack> $strLabel";
		else
			echo "<input type=checkbox name=$strName $nCallBack> $strLabel";

		echo "</td></tr>";
	}

	/*
		the function draws a check box in the for

		strLabel:			label in the left column
		strName:			name of check box in HTML form
		nChecked:			if true, checkbox will appear checked
							otherwise it appears unchecked
	*/
	function CheckBoxColomn($strLabel, $strName, $nChecked = 0, $nCallBack = '')
	{
		echo "<td >";

		if($nChecked == 1)
			echo "<input type=checkbox name=$strName CHECKED $nCallBack> $strLabel";
		else
			echo "<input type=checkbox name=$strName $nCallBack> $strLabel";

		echo "</td>";
	}
	/*
		the function draws a 4 check box in the for

		strLabel:			label in the left column
		strName:			name of check box in HTML form
		arrayChecked :		array which check box should be checked
							bu default all unchecked
		arrShow             array disc which text box should be shown
		                    bu default all show

	*/
	function CheckBox4($strLabel, $strName, $arrayChecked = array(0,0,0,0), $arrayShow = array(1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<4; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		echo "</tr>";
	}

	//////////////////////////////////////////////////////////////////// adnan //////////////////////////////////////////////////

	function CheckBox6($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<6; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_validate ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_reject ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		echo "</tr>";
	}



	function CheckBoxOne($strLabel, $strName, $arrayChecked = array(0), $arrayShow = array(1))
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<1; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";
		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ." ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";
	}

	//////////////////////////////////////////////////////////////////// adnan //////////////////////////////////////////////////


	/*
		the function draws a 4 check box in the for

		strLabel:			label in the left column
		strName:			name of check box in HTML form
		arrayChecked :		array which check box should be checked
							bu default all unchecked
		arrShow             array disc which text box should be shown
		                    bu default all show

	*/
	function CheckBoxMultiOpt($strLabel, $strName, $arrayChecked,$postFix=array(), $arrayShow){
			echo "<tr>
					<td>
						$strLabel
					</td>";
			$check=array();
			for($i = 0; $i<count($arrayChecked); $i++){
				if( $arrayChecked[$i]=='1')
					$check[$i] =  "CHECKED";
				else
					$check[$i] =  "";
			}
			for($i=0; $i<count($arrayChecked); $i++){
				if($arrayShow[$i]==1)
					echo "<td ><input type=checkbox name= ".$strName ."_".$postFix[$i]." ". $check[$i]."></td> ";
				else
					echo "<td align=center><img src=/images/empty.gif>";
			}
			echo "</tr>";
	}

	function CheckBox5($strLabel, $strName, $arrayChecked = array(0,0,0,0,0), $arrayShow = array(1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<5; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_issue ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		echo "</tr>";
	}
	function CheckBox7($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<7; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_cancel ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_resume ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[6] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_print ".$check[6]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";
	}

	function CheckBox8($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<8; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_issue ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_validate ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[6] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_cancel ".$check[6]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[7] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_resume ".$check[7]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		echo "</tr>";
	}
	function CheckBox9($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		//printr($arrayShow,$strLabel);
		for($i = 0; $i<9; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";
			}else
				$check[$i] =  "";
		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_cancel ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_validate ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[6] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_forword ".$check[6]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[7] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_resume ".$check[7]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[8] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_print ".$check[8]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";
	}

	//------------------------New Check Box To Store MRF & PMRF Values----------------------//
//---------------------------------------Start Jawad----------------------------------//
	function CheckBox9MRF($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		//printr($arrayShow,$strLabel);
		for($i = 0; $i<9; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";
			}else
				$check[$i] =  "";
		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_validate ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_ack ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[6] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_cancel ".$check[6]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[7] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_receive ".$check[7]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[8] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_prcentry ".$check[8]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";
	}
//-------------------------------------------End-------------------------------------//

	function CheckBox9WithRej($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<9; $i++)
		{
			if( $arrayChecked[$i] ==1){
				$check[$i] =  "CHECKED";
			}else
				$check[$i] =  "";
		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_cancel ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_validate ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_issue ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[6] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_reject ".$check[6]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[7] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_acknowledge ".$check[7]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[8] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_print ".$check[8]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";
	}

	function CheckBoxOne1($strLabel, $strName, $arrayChecked = array(0,0), $arrayShow = array(1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<1; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		/*if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";*/
	}

	//the function draws a 2 check box in the for

	function CheckBox2($strLabel, $strName, $arrayChecked = array(0,0), $arrayShow = array(1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<2; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";
	}

	//the function draws a 3 check box in the for
	function CheckBox3($strLabel, $strName, $arrayChecked = array(0,0,0), $arrayShow = array(1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<=2; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		echo "</tr>";
	}

	//end of check box

	/*
		the function draws a check box in the for

		strLabel:			label in the left column
		strName:			name of check box in HTML form
		nChecked:			if true, checkbox will appear checked
							otherwise it appears unchecked
	*/
	function CheckBoxTxt($strLabel, $strName, $nChecked = 0, $nTxtValue = 15)
	{
		echo "<tr><td></td><td>";

		if($nChecked == 1)
			echo "<input type=checkbox name=$strName CHECKED> $strLabel";
		else
			echo "<input type=checkbox name=$strName> $strLabel";

		echo "</td><td>";
			echo "<input type=text name= " . $strName . "_txt value='$nTxtValue' size=2 maxlength=2 >";
		echo "</td></tr>";
	}

	/*
		show text in left and right cells of table

		strLeft:		text to appear in left cell
		strRight:		text to appera in right cell
	*/
	function TextCells($strLeft, $strRight)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLeft;
		echo "	</td>";
		echo "	<td valign=top>";
		echo		$strRight;
		echo "	</td>";
		echo "</tr>\r\n";
	}

	// the displays a read only text field for as date field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// strFormName:			Name of HTML form
	//
	function DateField($strLabel, $strField,  $strValue, $nSize, $nMaxLength, $strFormName, $bReadonly=false)
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength readonly>";
				if(!$bReadonly)
				{
					echo  "	<a href=\"JavaScript: CalPop_".$strUnique."('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
					<script>
						function CalPop_".$strUnique."(sInputName)
						{
							window.open('/include/code/calender.php?strFieldName=' + escape(sInputName) , 'CalPop', 'toolbar=0,width=240,height=215');
						}
					</script>";
				}


		echo "	</td>";
		echo "</tr>";
	}

	function DateFieldSimple($strLabel, $strField,  $strValue, $nSize, $nMaxLength, $strFormName, $bReadonly=false)
	{
		$strUnique = time();
		if($strValue == "1900-01-01")
			$strValue = "";
		echo "	<td>";
		echo		$strLabel;


		echo  "<input type=text readonly name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength >";
				if(!$bReadonly)
				{
					echo  "	<a href=\"JavaScript: CalPop_".$strUnique."('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
					<script>
						function CalPop_".$strUnique."(sInputName)
						{
							window.open('/include/code/calender.php?strFieldName=' + escape(sInputName) , 'CalPop', 'toolbar=0,width=240,height=215');
						}
					</script>";
				}


		echo "	</td>";

	}
	// the displays a read only text field for as field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// strFormName:			Name of HTML form
	//
	function TextLookupField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_emp_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_emp_$strField(sInputName)
				{
					window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}

	function TextLookupField4($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_emp_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_emp_$strField(sInputName)
				{
					window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}



	function TextLookupField1($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_emp_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_emp_$strField(sInputName)
				{
					window.open('/include/common/CountrySocksSize_lookup.php?strField=$strField' , 'CalPop');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}
	function TextLookupField2($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='',$strExtraValue=0)
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					var strExtraValue=0;
					if($strExtraValue!='')
						strExtraValue=$strExtraValue;

					window.open('/include/common/$strLookupDocument?strField=$strField&strExtraValue='+strExtraValue , 'CalPop', 'scrollbars=yes, toolbar=0,width=1200,height=900');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}

	//---------------------------------Look Up to Add more extra values with query string-----------------------//
	//----------------------------------------------------Jawad Malik------------------------------------------//
	function TextLookupField2A($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='',$strExtraValue=0,$update=0,$nId=0)
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					var strExtraValue=0;
					if($strExtraValue!='')
						strExtraValue=$strExtraValue;

                    var update=0;
					if($update!='')
					   update = $update;

                    var nId=0;
					if($nId!='')
					   nId = $nId;

					window.open('/include/common/$strLookupDocument?strField=$strField&strExtraValue='+strExtraValue+'&update='+update+'&nId='+nId , 'CalPop', 'scrollbars=yes, toolbar=0,width=1200,height=900');
				}
			</script>
			";
		echo "	</td>";
		echo "</tr>";
	}
	//-------------------------------------------------------End----------------------------------------------//

	function TextLookupField2S($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					window.open('/include/common/$strLookupDocument' , 'CalPop', 'scrollbars=yes, toolbar=0,width=500,height=500');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}
	function TextLookupField3($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					window.open('/include/common/$strLookupDocument?strField=$strField' , 'New', 'scrollbars=yes, toolbar=0,width=650,height=500');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}

	function TextLookupField5($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='',$ExtraValue='')
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";


		echo  "
				<input type=text name='$strField' value='$strValue'  size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					var ExtraValue = $ExtraValue;

					if(ExtraValue < 0)
						ExtraValue = document.myForm.deptSection.value;

					window.open('/include/common/$strLookupDocument?strField=$strField&nDptId='+ExtraValue , 'CalPop', 'scrollbars=yes, toolbar=0,width=500,height=500');
				}
			</script>
			";
//		window.open('/include/common/textfieldemp.php?strField=$strField' , 'CalPop', 'toolbar=0,width=500,height=550');
		echo "	</td>";
		echo "</tr>";
	}

	function TextLookupField6($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='')
	{

		$strUnique = time();
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					window.open('/include/common/$strLookupDocument?strField=$strField' , 'New', 'scrollbars=yes, toolbar=0,width=650,height=500');
				}
			</script>";
		echo "	</td>";
	}

	/*
		the function converts data format from SQL data format
		to Month date, Year format e.g November 18, 2003
	*/
	function ConvertDateFormat($strDate, $strFormat = "M j, Y")
	{
		return date($strFormat, strtotime($strDate));
	}

	// Function to draw HTML table within lookup POPUP
	//
	// strQuery				Source Query
	// strIdField			Title of ID field in DB
	// strTitleField		Title of TITLE field in DB
	//
	function drawLookUpTable($strQuery, $strIdField, $strTitleField,
		$nWidth = "100%", $strCallBack = null)
	{
		global $strTitleFieldName, $strIdFieldName, $nId;

		$nResult = MsSQLQuery($strQuery);

		echo "
			<html>
				<head>
					<title>Look Up</title>
				<head>
				<style>
					A {
						font-family : arial;
						color: black;
						font-size :  9pt;
						font-style :  normal;
						font-weight : none;
						text-decoration : underline;
						text-align: right;
					};

					td {
						font-family : arial;
						color: black;
						font-size :  9pt;
						font-style :  normal;
						font-weight : none;
						text-align: left;
					}
				</style>
				<body>
			";
		echo "<table width=$nWidth><tr><td bgcolor=silver><table cellspacing=1 border=0 cellpadding=3 width=$nWidth>\r\n";
		$nI = 0;
		while($nRow = odbc_fetch_array($nResult))
		{
			$nI++;
			$nID = $nRow[$strIdField];
			$strTitle = $nRow[$strTitleField];

			if(empty($strCallBack))
			{
				if($nID == $nId)
					echo "
						<tr bgcolor=lightblue>
							<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\">$nID</a></td>
							<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\">$strTitle</a></td>
						</tr>
					";
				else
					echo "
					<tr bgcolor=ffffff>
						<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\" class=NAV>$nID</a></td>
						<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\" class=NAV>$strTitle</a></td>
					</tr>
					";
			}
			else
			{
				$strScriptFunc = "updateParent";
				eval("echo $strCallBack(\$nRow, \$nId, \$strIdField, \$strTitleField, \$strScriptFunc);");
			}
		}
		echo "</table></td></tr></table>\r\n";

		echo "
		<script>
			function updateParent(nId, strTitle)
			{
				window.opener.eval('$strTitleFieldName').value = strTitle;
				window.opener.eval('$strIdFieldName').value = nId;
				window.close();
			}
		</script>
		</body>
		</html>
		";

	}


	function getValOfTable($strTableName, $strField, $strWhere)
	{
		if(!empty($strWhere))
			$strQuery  = "SELECT $strField AS nCnt FROM $strTableName WHERE $strWhere";
		else
			$strQuery  = "SELECT $strField AS nCnt FROM $strTableName";

		$nResult = MsSQLQuery($strQuery);
		$rstRow =odbc_fetch_array($nResult);
		return $rstRow["nCnt"];
	}

	// the displays a read only text field for employee name and a hidden field for ID,
	// in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// strFormName:			Name of HTML form
	//
	function EmpPickUp($strLabel, $strField, $nEmpId, $nSize, $nMaxLength, $strFormName)
	{
		$strUnique = time();

		if($nEmpId)
			$strValue = getValOfTable("tblEmployee", "emp_name", "emp_id=$nEmpId");


		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=hidden name=$strField value=$nEmpId>
				<input type=text name='title_$strField' value='$strValue' size=$nSize maxlength=$nMaxLength readonly>
				<a href=\"JavaScript: EmpPickUp_".$strUnique."('document.$strFormName.title_$strField', 'document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function EmpPickUp_".$strUnique."(strTitleField, strIdField)
				{
					window.open('/include/code/comp/emp_pickup.php?strTitleFieldName=' + escape(strTitleField) + '&strIdFieldName=' + escape(strIdField), 'EmpPickUp', 'scrollbars=true,toolbar=0,width=300,height=250');
				}
			</script>
			";

		echo "	</td>";
		echo "</tr>";
	}
	/*
		Function remove all <span> tags from the given string
	*/
	function RemoveSpanTags( $strString = '')
	{
		$nPtr = 0;
		// remove <span  ...> tag
		while(true)
		{
			$nTagStart = strpos( $strString, "<SPAN", $nPtr);
			if( $nTagStart === false)
				break;
			$nTagEnd = subpos( $strString, ">", $nTagStart);
			$strString = substr( $strString, 0 , $nTagStart). substr( $strString, $nTagEnd+1);
			$nPtr = $nTagStart;

		}
		$nPtr = 0;
		while(true)
		{
			$nTagStart = strpos($strString, "</SPAN", $nPtr);
			if($nTagStart === false)
				break;
			$nTagEnd = strpos($strString, ">", $nTagStart);
			$strString = substr( $strString, 0 , $nTagStart). substr( $strString, $nTagEnd+1);
			$nPtr = $nTagStart;
		}
		return $strString;
	}

	/*
		the function draws HTML table with border
	*/
	function SetStart($strLabel, $strColor, $nWidth)
	{
		if(!empty($nWidth))
			$strWidth = "width=$nWidth";
		else
			$strWidth = "";

		echo "<table $strWidth cellspacing=0 cellpadding=0 border=0>";
		echo "	<tr>";
		echo "		<td bgcolor=$strColor align=center colspan=3>";
		echo "			<img src=images/1.gif height=3><br>";
		echo "			<a href='' onClick=\"javascript: myid123.visibility='hidden'; return false;\">$strLabel</a><br>";
		echo "			<img src=images/1.gif height=3><br>";
		echo "		</td>";
		echo "	</tr>";
		echo "	<tr>";
		echo "		<td bgcolor=$strColor><img src=images/1.gif width=1></td>";
		echo "		<td width=100%>";
		echo "			<div id=myid123><table width=100% cellpadding=3 cellspacing=0 border=0><tr><td>";
	}

	function SetEnd($strColor)
	{
		echo "			</td></tr></table></div>";
		echo "		</td>";
		echo "		<td bgcolor=$strColor><img src=images/1.gif width=1><br></td>";
		echo "	</tr>";
		echo "	<tr>";
		echo "		<td bgcolor=$strColor align=center colspan=3>";
		echo			$strLabel;
		echo "		</td>";
		echo "	</tr>";
		echo "</table>";
	}
	///////////////////////////////////////////

	function ReserveWordCheck($varname){
		$reservewords=array('select','from','table','update','insert','delete','drop','create','check','','*',';','rename','alter');
		$index=0;
		foreach ($reservewords as $item){
			if ($varname==$item){
				$index=$index+1;
				break;
			}
		}
		if ($index>0)
			return 'false';
		else
			return 'true';
	}

	function Heading($strLabel)
	{
		echo "<span style='font-size: 12pt; font-weight: bold; color: black;'>$strLabel</span><br><img src=/images/1.gif height=5><br><img src=/images/blue-horz-line.jpg><br>";
	}

	function NavCell($strLabel)
	{
		echo "<tr>";
		echo "	<td valign=top><img src= /images/arrow.jpg></td>";
		echo "	<td width=100% valign=top>$strLabel</td>";
		echo "</tr>";
	}


	function GetLastDateScem($nYear = 2004)
	{

		$strdate1 = trim($nYear."-12-30");
		$strdate2 = trim($nYear."-12-31");
		if( RecCount("tblRota", "rota_scheme = 'H2' AND (rota_date = '".$strdate1."' OR rota_date = '".$strdate2."') ") ==2 )
		 {
			$query = "select * from tblRota where rota_scheme = 'H2' AND (rota_date  BETWEEN  '".$strdate1."' AND '".$strdate2."')  ORDER BY rota_date";
			$Result =  MsSQLQuery($query);
			while($rstRow = odbc_fetch_array($Result))
			{
				$arrSecema[] = trim($rstRow['rota_morning']). "_" . trim($rstRow['rota_evening']) . "_" . trim($rstRow['rota_night']) . "_" . trim($rstRow['rota_rest']);

			}
		}
		else
		{
			$arrSecema[] = "D_C_B_A";
			$arrSecema[] = "D_C_B_A";
		}

		return $arrSecema;
	}


	//  GetLastDateScem for Six week Scheme
	function GetLastDateScemSW($nYear = 2004)
	{

		$strdate = trim($nYear."-12-31");
		if( RecCount("tblRotaSW", " rota_date = '".$strdate."'") > 0 )
		 {
			$query = "select * from tblRotaSW where  rota_date = '".$strdate."'";
			$Result =  MsSQLQuery($query);
			while($rstRow = odbc_fetch_array($Result))
			{
				$strPrevRota = trim($rstRow['rota_A1'])."_".trim($rstRow['rota_A2'])."_".trim($rstRow['rota_A3'])."_".trim($rstRow['rota_A4'])."_".trim($rstRow['rota_A5'])."_".trim($rstRow['rota_A6'])."_".trim($rstRow['rota_A7'])."_".trim($rstRow['rota_B1'])."_".trim($rstRow['rota_B2'])."_".trim($rstRow['rota_B3'])."_".trim($rstRow['rota_B4'])."_".trim($rstRow['rota_B5'])."_".trim($rstRow['rota_B6'])."_".trim($rstRow['rota_B7'])."_".trim($rstRow['rota_C1'])."_".trim($rstRow['rota_C2'])."_".trim($rstRow['rota_C3'])."_".trim($rstRow['rota_C4'])."_".trim($rstRow['rota_C5'])."_".trim($rstRow['rota_C6'])."_".trim($rstRow['rota_C7']);
			}
		}
		else
		{
			$strPrevRota = "R_N_N_N_N_N_N_R_M_M_M_M_M_M_R_E_E_E_E_E_E";
		}

		return $strPrevRota;
	}

	//function produce next day rota shift scema
	//$strPrevRota Rota Scheme for Six Weeks
	function GetNextDayRotaSceme_sixWeeks($strPrevRota)
	{
		$arrRotaSW = array( "R_N_N_N_N_N_N_R_M_M_M_M_M_M_R_E_E_E_E_E_E", "R_R_N_N_N_N_N_E_R_M_M_M_M_M_N_R_E_E_E_E_E",
							"M_R_R_N_N_N_N_E_E_R_M_M_M_M_N_N_R_E_E_E_E", "M_M_R_R_N_N_N_E_E_E_R_M_M_M_N_N_N_R_E_E_E",
							"M_M_M_R_R_N_N_E_E_E_E_R_M_M_N_N_N_N_R_E_E", "M_M_M_M_R_R_N_E_E_E_E_E_R_M_N_N_N_N_N_R_E",
							"M_M_M_M_M_R_R_E_E_E_E_E_E_R_N_N_N_N_N_N_R", "R_M_M_M_M_M_R_R_E_E_E_E_E_E_R_N_N_N_N_N_N",
							"M_R_M_M_M_M_M_E_R_E_E_E_E_E_N_R_N_N_N_N_N", "M_M_R_M_M_M_M_E_E_R_E_E_E_E_N_N_R_N_N_N_N",
							"M_M_M_R_M_M_M_E_E_E_R_E_E_E_N_N_N_R_N_N_N", "M_M_M_M_R_M_M_E_E_E_E_R_E_E_N_N_N_N_R_N_N",
							"M_M_M_M_M_R_M_E_E_E_E_E_R_E_N_N_N_N_N_R_N", "M_M_M_M_M_M_R_E_E_E_E_E_E_R_N_N_N_N_N_N_R",
							"R_M_M_M_M_M_M_R_E_E_E_E_E_E_R_N_N_N_N_N_N", "E_R_M_M_M_M_M_N_R_E_E_E_E_E_R_R_N_N_N_N_N",
							"E_E_R_M_M_M_M_N_N_R_E_E_E_E_M_R_R_N_N_N_N", "E_E_E_R_M_M_M_N_N_N_R_E_E_E_M_M_R_R_N_N_N",
							"E_E_E_E_R_M_M_N_N_N_N_R_E_E_M_M_M_R_R_N_N", "E_E_E_E_E_R_M_N_N_N_N_N_R_E_M_M_M_M_R_R_N",
							"E_E_E_E_E_E_R_N_N_N_N_N_N_R_M_M_M_M_M_R_R", "R_E_E_E_E_E_E_R_N_N_N_N_N_N_R_M_M_M_M_M_R",
							"E_R_E_E_E_E_E_N_R_N_N_N_N_N_M_R_M_M_M_M_M", "E_E_R_E_E_E_E_N_N_R_N_N_N_N_M_M_R_M_M_M_M",
							"E_E_E_R_E_E_E_N_N_N_R_N_N_N_M_M_M_R_M_M_M", "E_E_E_E_R_E_E_N_N_N_N_R_N_N_M_M_M_M_R_M_M",
							"E_E_E_E_E_R_E_N_N_N_N_N_R_N_M_M_M_M_M_R_M", "E_E_E_E_E_E_R_N_N_N_N_N_N_R_M_M_M_M_M_M_R",
							"R_E_E_E_E_E_E_R_N_N_N_N_N_N_R_M_M_M_M_M_M", "N_R_E_E_E_E_E_R_R_N_N_N_N_N_E_R_M_M_M_M_M",
							"N_N_R_E_E_E_E_M_R_R_N_N_N_N_E_E_R_M_M_M_M", "N_N_N_R_E_E_E_M_M_R_R_N_N_N_E_E_E_R_M_M_M",
							"N_N_N_N_R_E_E_M_M_M_R_R_N_N_E_E_E_E_R_M_M", "N_N_N_N_N_R_E_M_M_M_M_R_R_N_E_E_E_E_E_R_M",
							"N_N_N_N_N_N_R_M_M_M_M_M_R_R_E_E_E_E_E_E_R", "R_N_N_N_N_N_N_R_M_M_M_M_M_R_R_E_E_E_E_E_E",
							"N_R_N_N_N_N_N_M_R_M_M_M_M_M_E_R_E_E_E_E_E", "N_N_R_N_N_N_N_M_M_R_M_M_M_M_E_E_R_E_E_E_E",
							"N_N_N_R_N_N_N_M_M_M_R_M_M_M_E_E_E_R_E_E_E", "N_N_N_N_R_N_N_M_M_M_M_R_M_M_E_E_E_E_R_E_E",
							"N_N_N_N_N_R_N_M_M_M_M_M_R_M_E_E_E_E_E_R_E", "N_N_N_N_N_N_R_M_M_M_M_M_M_R_E_E_E_E_E_E_R");

		// Search Previous Pattern ;
		$nkey = array_search($strPrevRota, $arrRotaSW);

		if($nkey > 40)
			$nkey = 0;
		else
			$nkey++;
		//return Next Rota
		return $arrRotaSW[$nkey];

	}//end of function




	//  Created by Adnan
	function GetLastDateScemTW($nYear = 2004)
	{
		$strdate = trim($nYear."-12-31");
		if( RecCount("tblRotaTW", " rota_date = '".$strdate."'") > 0 )
		 {
			$query = "select * from tblRotaTW where  rota_date = '".$strdate."'";
			$Result =  MsSQLQuery($query);
			while($rstRow = odbc_fetch_array($Result))
			{
				$strPrevRota = trim($rstRow['rota_A'])."_".trim($rstRow['rota_B'])."_".trim($rstRow['rota_C'])."_".trim($rstRow['rota_D'])."_".trim($rstRow['rota_E'])."_".trim($rstRow['rota_F'])."_".trim($rstRow['rota_G']);
			}
		}
		else
		{
			$strPrevRota = "E_N_M_E_N_R_M";
		}

		return $strPrevRota;
	}

	function GetNextDayRotaSceme_threeWeeks($strPrevRota, $bKey=0)
	{
		$arrRotaSW = array( "E_N_M_E_N_R_M", "E_N_M_E_N_M_R",
							"R_N_M_E_N_M_E", "N_R_M_E_N_M_E",
							"N_M_R_E_N_M_E", "N_M_E_R_N_M_E",
							"N_M_E_N_R_M_E", "N_M_E_N_M_R_E",
							"N_M_E_N_M_E_R", "R_M_E_N_M_E_N",
							"M_R_E_N_M_E_N", "M_E_R_N_M_E_N",
							"M_E_N_R_M_E_N", "M_E_N_M_R_E_N",
							"M_E_N_M_E_R_N", "M_E_N_M_E_N_R",
							"R_E_N_M_E_N_M", "E_R_N_M_E_N_M",
							"E_N_R_M_E_N_M", "E_N_M_R_E_N_M",
							"E_N_M_E_R_N_M");

		// Search Previous Pattern ;
		$nkey = array_search($strPrevRota, $arrRotaSW);

		if($nkey > 19)
			$nkey = 0;
		else
			$nkey++;
		//return Next Rota
		if(!$bKey)
			return $arrRotaSW[$nkey];
		else
			return $nkey;

	}//end of function
// Created by Adnan


	//function produce next day rota shift scema
	//$arrCurrent        last two days scema for 6+2
	function GetNextDayRotaSceme_sixTwo( $arrCurrent)
	{
		$six_two = array("B_A_D_C", "C_A_D_B", "C_B_D_A", "C_B_A_D",
						   "D_B_A_C", "D_C_A_B", "D_C_B_A", "A_C_B_D",
						   "A_D_B_C", "A_D_C_B", "B_D_C_A", "B_A_C_D");
		$Findex = 0;
		$Lindex = 0;
		//echo "<".$arrCurrent[0]." ".$arrCurrent[1]."><br>";
		if ( trim($arrCurrent[0]) ==  trim($arrCurrent[1]))
		{
			//echo $arrCurrent[1]."  =";
			for($count = 0; $count <=11; $count++)
			{
				//match and find first day scema index
				if(trim($six_two[$count]) == trim($arrCurrent[1]))
					$Findex = $count;
			}
			//then we
			$Findex = $Findex+1;
			if($Findex > 11)
				$Findex = 0;
			//echo $six_two[$Findex]. " AT ".	$Findex."<br>";
			return $six_two[$Findex];
		}
		else
		{
			for($count = 0; $count <=11; $count++)
			{
				//matches second day schema index
				if($six_two[$count] == $arrCurrent[1])
					$Lindex = $count;
			}
			if($Lindex > 11)
				$Lindex = 0;
			return $six_two[$Lindex];

		}	//end of else


	}//end of function

	// function get date string geting from database and convert
	// into specified formate
	function ConvertToDate($strDate, $formate = "F j, Y")
	{
		$arr = explode(" ", $strDate);
		$arr = explode("-", $arr[0]);
		//echo $arr[0];

		if($arr[0] > 1970)
			return date($formate, mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]));
		else
			return -1;

	}

	function GetYear_Scheme()
	{
		if(RecCount("tblRota")>0)
		{
			$strQuery = "select max(rota_id) as id from tblRota";
			$nResult = MsSqlQuery($strQuery);
			$rstRow = odbc_fetch_array($nResult);
			$strQuery = "select  rota_scheme ,rota_date from tblRota where rota_id =". $rstRow['id'];
			$nResult = MsSqlQuery($strQuery);
			$rstRow = odbc_fetch_array($nResult);
			$date = explode('-',$rstRow['rota_date']);
			$arr[0] = $date[0];
			$arr[1] = $rstRow['rota_scheme'];
			return $arr;
		}
	}
	//Rota scheme for Six weeks
	function GetYear_SchemeSW()
	{
		if(RecCount("tblRotaSW")>0)
		{
			$strQuery = "select max(rota_id) as id from tblRotaSW";
			$nResult = MsSqlQuery($strQuery);
			$rstRow = odbc_fetch_array($nResult);
			$strQuery = "select  rota_date from tblRotaSW where rota_id =". $rstRow['id'];
			$nResult = MsSqlQuery($strQuery);
			$rstRow = odbc_fetch_array($nResult);
			$date = explode('-',$rstRow['rota_date']);
			$arr[0] = $date[0];
			return $arr;
		}
	}

	function GetYear_SchemeTW()
	{
		if(RecCount("tblRotaTW")>0)
		{
			$strQuery = "select max(rota_id) as id from tblRotaTW";
			$nResult = MsSqlQuery($strQuery);
			$rstRow = odbc_fetch_array($nResult);
			$strQuery = "select  rota_date from tblRotaTW where rota_id =". $rstRow['id'];
			$nResult = MsSqlQuery($strQuery);
			$rstRow = odbc_fetch_array($nResult);
			$date = explode('-',$rstRow['rota_date']);
			$arr[0] = $date[0];
			return $arr;
		}
	}


	function GetNextDayRotaSceme_sixTwo_number($arrCurrent)
	{
		$six_two = array("B_A_D_C", "B_A_D_C", "C_A_D_B", "C_A_D_B", "C_B_D_A", "C_B_D_A", "C_B_A_D", "C_B_A_D",
						   "D_B_A_C", "D_B_A_C", "D_C_A_B", "D_C_A_B", "D_C_B_A", "D_C_B_A", "A_C_B_D", "A_C_B_D",
						   "A_D_B_C", "A_D_B_C", "A_D_C_B", "A_D_C_B",  "B_D_C_A",  "B_D_C_A", "B_A_C_D", "B_A_C_D");
		$Findex = 0;
		$Lindex = 0;
		//echo "<".$arrCurrent[0]." ".$arrCurrent[1]."><br>";
		if ( trim($arrCurrent[0]) ==  trim($arrCurrent[1]))
		{

			//echo $arrCurrent[1]."  =";
			for($count = 0; $count <=23; $count++)
			{
				//match and find first day scema index
				if(trim($six_two[$count]) == trim($arrCurrent[1]))
					$Findex = $count;
			}

			//then we
			$Findex = $Findex+1;
			if($Findex > 23)
				$Findex = 0;
			//echo $six_two[$Findex]. " AT ".	$Findex."<br>";
			return $Findex;

		}
		else
		{

			for($count = 0; $count <=23; $count++)
			{

				//matches second day schema index
				if($six_two[$count] == $arrCurrent[1])
					$Lindex = $count;
			}
			if($Lindex > 23)
				$Lindex = 0;
			return $Lindex;

		}	//end of else
	}



	function DisplaySectionStrngth($nSectionId = 0)
	{
		$Query  =  "SELECT * FROM tblSection WHERE sec_id IN( ". $nSectionId  ." ) ORDER BY sec_name";
		$nResult2 = MsSQLquery($Query);

		$strResultString =  "<center><br><table  cellpadding=3 cellspacing=0 border=1>";
		$strResultString = $strResultString .
				 "	<tr>
					<td  align=center width=150 bgcolor=silver><b> Section </td>
					<td  align=center width=150 bgcolor=silver><b>Approved Strength</td>
					<td  align=center width=150 bgcolor=silver><b>Curr. Strength</td>
				</tr>";
		$nTotal = 0;
		$nTotalApr = 0;
		while($rstRow2 = odbc_fetch_array($nResult2))
		{
			$Query = "SELECT SUM(emp_points) as pnts FROM tblEmployee WHERE emp_section_id = " . $rstRow2['sec_id'] . " AND emp_active = 1 AND emp_validated = 1 AND emp_status_id NOT IN(5,6)";
			$nResult5 = MsSQLquery($Query);
			$rstRow5 = odbc_fetch_array($nResult5);
			$ncount = $rstRow5['pnts'];
			$nTotal = $nTotal + $ncount;
			$nTotalApr = $nTotalApr + $rstRow2['sec_apr_points'];
			$strResultString = $strResultString .
				 	"<tr>
						<td  width=150>" . $rstRow2['sec_name'] . "</td>
						<td  align=right width=150> " . $rstRow2['sec_apr_points'] . " </td>
						<td  align=right width=150>" . $ncount. "</td>
					</tr>";

		} // end of section level loop

			$strResultString = $strResultString .
				 "	<tr>
						<td  width=150 align=center><b> Total </td>
						<td  align=right width=150><b> " . $nTotalApr . " </td>
						<td  align=right width=150><b>" . $nTotal . "</td>
					</tr>
			</table></center><br>";
			//echo $strResultString;
			return $strResultString;
	}

	/************************/
	// calulate overtime that has been used
	function CalculateWeakOvt( $nEmpID = 0, $nWeak , $nYear, $strDate)
	{

		$Query = "	SELECT
						SUM(att_workingMint) AS ovtHr

					FROM
						tblAttendance
					WHERE
						att_emp_id = " . $nEmpID . " AND (DATEPART(ww, att_duty_date) = "  . $nWeak . ") AND (DATEPART(yyyy, att_duty_date) = " . $nYear . ") AND  att_ovt_id <> 0";
		$nResult = MsSQLQuery($Query);
		$rstRow2 = odbc_fetch_array($nResult);
		if(empty($rstRow2['ovtHr']))
			$nWeakHours = 0;
		else
			$nWeakHours = $rstRow2['ovtHr'] / 60;
		// calulate overtime that is posted for future for curren weak

		  $Query = "	SELECT
						SUM(DATEDIFF(hh, ovt_startDate, ovt_endDate)) AS ovtHr
					FROM
						tblOverTime
					WHERE
						 DATEPART(ww, ovt_startDate) = "  . $nWeak . " AND DATEPART(yyyy,  ovt_startDate) = " . $nYear . " AND ovt_startDate >= '$strDate' AND ovt_emp_id = " . $nEmpID ;

		$nResult = MsSQLQuery($Query);
		$rstRow2 = odbc_fetch_array($nResult);
		if(!empty($rstRow2['ovtHr']))
			$nWeakHours = $nWeakHours + $rstRow2['ovtHr'];

		return $nWeakHours;
	}

	// calculate ovt Time In a Day

	function CalculateDayOvt($nEmpID = 0,  $arr )
	{

		$Query = "	SELECT
							SUM(DATEDIFF(hh, ovt_startDate, ovt_endDate)) AS ovtHr
						FROM
						    tblOverTime
						WHERE
						    DATEPART(dd, ovt_startDate) = " . $arr[1] . " AND DATEPART(mm, ovt_startDate) = " . $arr[0] . "  AND DATEPART(yyyy, ovt_startDate) = " . $arr[2] . "
							AND (ovt_emp_id = " . $nEmpID . ") AND ovt_used = 0";

		$nResult = MsSQLQuery($Query);
		$rstRow2 = odbc_fetch_array($nResult);
		if(empty($rstRow2['ovtHr']))
			$nDayHours = 0;
		else
			$nDayHours = $rstRow2['ovtHr'];

		 $Query = "	SELECT
							SUM(att_workingMint) AS ovtHr
						FROM
						    tblAttendance
						WHERE
						    DATEPART(dd, att_duty_date) = " . $arr[1] . " AND DATEPART(mm, att_duty_date) = " . $arr[0] . "  AND DATEPART(yyyy, att_duty_date) = " . $arr[2] . "
							AND (att_emp_id = " . $nEmpID . ") AND att_ovt_id > 0";

		$nResult = MsSQLQuery($Query);
		$rstRow2 = odbc_fetch_array($nResult);
		if(!empty($rstRow2['ovtHr']))
			$nDayHours =  $nDayHours + ($rstRow2['ovtHr'] / 60);
		return $nDayHours;
	}

	// $strDate       date in "Y-m-d" formate return weak of sqlserver
	function CalculateWeakNumber($strDate )
	{
		$arr = explode("-", $strDate);
		$Query = "SELECT     DATEPART(wk, '" . date("m/d/Y", mktime(0, 0, 0, $arr[1], $arr[2], $arr[0])) . "') AS wk";
		$nResult = MsSQLQuery($Query);
		$rstRow2 = odbc_fetch_array($nResult);
		return $rstRow2['wk'];
	}

	// $strDate       date in "Y-m-d" formate return First day Date of weak
	function CalculateFirstDateOfWeak($strDate )
	{
		$arr = explode("-", $strDate);
		$strDate = date("m/d/Y", mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]));
		$Query = "SELECT  (datepart(dd, '$strDate') - datepart(dw, '$strDate')+ 1) as dy";
		$nResult = MsSQLQuery($Query);
		$rstRow2 = odbc_fetch_array($nResult);
		$strDate = $arr[1] ."/" . $rstRow2['dy'] . "/" . $arr[0];
		return $strDate;
	}

// function updated for Duty Wrong Minutes Calculation on 21-Apr-2008

	function CalculateDutyTime( $strTimeIn, $strTimeOut, $nInHr, $nInMin, $nOutHr, $nOutMin, $nAdjust, $nDateIn, $nDateOut)
	{
		$strTimeIn = str_pad($strTimeIn, 4, "0000", STR_PAD_LEFT);
		$strTimeOut = str_pad($strTimeOut, 4, "0000", STR_PAD_LEFT);

		$nDutyStHr = substr($strTimeIn, 0, 2);		// get the duty start hours
		$nDutyStMin = substr($strTimeIn, 2, 2);		// get the duty start mins
		$nDutyEdHr = substr($strTimeOut, 0, 2);		// duty end hours
		$nDutyEdMin = substr($strTimeOut, 2, 2);	// duty end mins

		$arr = explode("-", $nDateIn);
		$arr2 = explode("-", $nDateOut);
		$arrDay = explode(" ", $arr[2]);
		$arr2Day = explode(" ", $arr2[2]);
		$nEntDay = $arrDay[0];
		$nExtDay =  $arr2Day[0];

		// Maximum Normal Duty Can Continue upto Next Day In case of Night Shift
		// IN Normal Case Duty End Day Should be Same

		if($nDutyStHr > $nDutyEdHr )			// if start hour is greater than end hours   .... suppose : Nite 23:00 to Morning 07:00
		{
			if((($nExtDay - $nEntDay) == 1)&&($arr2[1] == $arr[1]) && ($arr2[0] == $arr[0]))
			{
					if($nOutHr > $nDutyEdHr)
					{
						 $nOutHr=$nDutyEdHr;
						 $nOutMin=$nDutyEdMin;
					}

			}  // end else  ...... if(($nExtDay - $nEntDay) > 1)
			elseif(($nExtDay < $nEntDay)&&(($arr2[1] > $arr[1]) || ($arr2[0] > $arr[0])))   	// 31-29 > 1
			{
				$nExtDay =($nEntDay + 1);		// ExitDay = 30 + 1
				$arr2[1] = $arr[1];
				$arr2[0] = $arr[0];

			   if($nOutHr > $nDutyEdHr)
			   {
				   $nOutHr=$nDutyEdHr;
				   $nOutMin=$nDutyEdMin;
			   }
			}
			elseif(($nExtDay - $nEntDay) > 1)   	// 31-29 > 1
			{
				$nExtDay =($nEntDay + 1);		// ExitDay = 30 + 1
				$arr2[1] = $arr[1];
				$arr2[0] = $arr[0];

			   $nOutHr=$nDutyEdHr;
			   $nOutMin=$nDutyEdMin;
			}


		}	// end if ......... if($nDutyStHr > $nDutyEdHr )

		else
		{
			// $nOutHr, $nOutMin

			if(($nOutHr > $nDutyEdHr) ||($nExtDay >$nEntDay) || ($arr2[1] > $arr[1]) || ($arr2[0] > $arr[0]))
			{

				 $nOutHr=$nDutyEdHr;
				 $nOutMin=$nDutyEdMin;
			}

			$nExtDay = $nEntDay;
			$arr2[1] = $arr[1];
			$arr2[0] = $arr[0];

		}   // End else of ..... if($nDutyStHr > $nDutyEdHr )

		$nDutyStartSec = mktime($nDutyStHr, $nDutyStMin, 0, $arr[1], $nEntDay, $arr[0]);	// Duty Actual Start Time
		$nDutyEndSec = mktime($nDutyEdHr, $nDutyEdMin, 0, $arr2[1], $nExtDay, $arr2[0]);	// Duty Actual End Time
		$nNetSeconds = $nDutyEndSec - $nDutyStartSec;										// Net Actual Duty Seconds


		$nTimeInSec = mktime($nInHr, $nInMin, 0, $arr[1], $nEntDay, $arr[0]);		// time when user IN in Factory
		$nTimeOutSec = mktime($nOutHr, $nOutMin, 0, $arr2[1], $nExtDay, $arr2[0]);	// time when the user Exit from Factory
		$nTotalWorkingSeconds = $nTimeOutSec - $nTimeInSec;							// Employee Total Working Seconds ( IN_Time  -  Out_Time)

		if($nTimeInSec < $nDutyStartSec)
		{
			$nTotalWorkingSeconds =  $nTotalWorkingSeconds - ($nDutyStartSec - $nTimeInSec);	// Employee Total Working Seconds
		}

		if($nDutyEndSec < $nTimeOutSec)
		{
			$nTotalWorkingSeconds =  $nTotalWorkingSeconds - ( $nTimeOutSec - $nDutyEndSec);	// Employee Total Working Seconds
		}

		$nNetMin = $nTotalWorkingSeconds / 60;			// Conversion in Minutes from Seconds
		$nNetMin = $nNetMin - $nAdjust;

		if($nNetMin < 0 )
			$nNetMin = 0;

		return $nNetMin;								// Returns the USER Duty Time in Minutes
	}

	// function calculate Duty time hrs for shifts

	function CalculateDutyHrsShift( $nShiftId )
	{
		//Get Shift Record
		$rstRowSh  = GetRecord("tblShift", "grp_id =".$nShiftId);

		$strTimeIn = str_pad($rstRowSh['grp_time_in'], 4, "0000", STR_PAD_LEFT);
		$strTimeOut = str_pad($rstRowSh['grp_time_out'], 4, "0000", STR_PAD_LEFT);

		$nDutyStHr = substr($strTimeIn, 0, 2);		// get the duty start hours
		$nDutyStMin = substr($strTimeIn, 2, 2);		// get the duty start mins
		$nDutyEdHr = substr($strTimeOut, 0, 2);		// duty end hours
		$nDutyEdMin = substr($strTimeOut, 2, 2);	// duty end mins

		// Maximum Normal Duty Can Continue upto Next Day In case of Night Shift
		// IN Normal Case Duty End Day Should be Same
		$nShiftHrs = 0;
		$nShiftMnts = 0;

		if($nDutyStHr > $nDutyEdHr )			// if start hour is greater than end hours   .... suppose : Nite 23:00 to Morning 07:00
		{
			//Mints of Hrs
			$nShiftMnts = ((24 - $nDutyStHr) * 60);
			$nShiftMnts += ($nDutyEdHr * 60);
			//Duty End Mnts
			$nShiftMnts += $nDutyEdMin;
			//Duty Start Mnts
			$nShiftMnts -= $nDutyStMin;

		}
		else
		{
			//Mints of Hrs
			$nShiftMnts = (($nDutyEdHr - $nDutyStHr) * 60);
			//Duty End Mnts
			$nShiftMnts += $nDutyEdMin;
			//Duty Start Mnts
			$nShiftMnts -= $nDutyStMin;

		}
		$nShiftHrs = floor($nShiftMnts / 60);
		$nNetMin = ($nShiftHrs * 60);
		if($nNetMin < 0 )
			$nNetMin = 0;

		return $nNetMin;								// Returns the USER Duty Time in Minutes
	}

// function updated for Duty Wrong Minutes Calculation on 21-Apr-2008

	function CalculateMonth($DateFrom)
	{
		$arr = explode("-", $DateFrom);
		$nTime = mktime(0 ,0, 0, $arr[1], $arr[2], $arr[0]);
		if(	$nTime >= time() )
			return 0;
		else
		{
			$nTimeDiff = time() - $nTime;
			$nTimeDiff = $nTimeDiff / 84400;
			$nTimeDiff = $nTimeDiff / 30;
			return round( $nTimeDiff, 0);
		}
	}

	// function returns Employee Duty Time
	// Morning = 1, evening = 2, Night = 3
	// Rest = 4
	function rotaEmployeeDuty($shiftID, $strDate)
	{
		if( $shiftID == '1')
			$strShift = "A";
		elseif( $shiftID == '2')
			$strShift = "B";
		elseif( $shiftID == '3')
			$strShift = "C";
		elseif( $shiftID == '4')
			$strShift = "D";
		else
			$strShift = '0';

		$rstRotaRow = GetRecord("tblRota", "rota_date = '". $strDate ."'");
		if( trim($rstRotaRow['rota_morning']) == $strShift)
			$nReturn = 1;
		elseif( trim($rstRotaRow['rota_evening']) == $strShift)
			$nReturn = 2;
		elseif( trim($rstRotaRow['rota_night']) == $strShift)
			$nReturn =  3;
		else
			$nReturn = 4;
		return $nReturn;
	}

	// function return employee time table

	function DutyTimetable( $empID, $strDate, $arrRGroupSW , $arrRGroupTW)
	{

		if(RecCount("tblEmployee", "emp_id = " . $empID) > 0)
		{
			$rstRow = GetRecord("tblEmployee", "emp_id = " . $empID);
			if($rstRow['emp_Working_type'] == 0)
				$rstRecArray = GetRecord("tblEmpGroup", "grp_id = " . $rstRow['emp_empGroup_id']);
			else
			{
				if($rstRow['emp_shift'] > 0 && $rstRow['emp_shift'] < 5)
				{
					$nEmpTime = rotaEmployeeDuty($rstRow['emp_shift'], $strDate);
				}
				elseif($rstRow['emp_shift'] >= 5 && $rstRow['emp_shift'] < 31)
				{
					$strEmpGrpColm = "rota_".$arrRGroupSW[$rstRow['emp_shift']];
					$nResultRtsw = MsSQLQuery("SELECT ".$strEmpGrpColm." as rta from tblRotaSW WHERE rota_date = '".$strDate ."'");
					$rstRowRtsw = odbc_fetch_array($nResultRtsw);
					if(trim($rstRowRtsw['rta']) == "M")
						$nEmpTime = 1;
					elseif(trim($rstRowRtsw['rta']) == "E")
						$nEmpTime = 2;
					elseif(trim($rstRowRtsw['rta']) == "N")
						$nEmpTime = 3;
					else
						$nEmpTime = 4;
				}

// Duty Time Table for Rota Three Weeks  ..... Adnan added this block of Code

				elseif($rstRow['emp_shift'] >= 31 && $rstRow['emp_shift'] <= 37)
				{
					$strEmpGrpColm = "rota_".$arrRGroupTW[$rstRow['emp_shift']];
					$nResultRttw = MsSQLQuery("SELECT ".$strEmpGrpColm." as rta from tblRotaTW WHERE rota_date = '".$strDate ."'");
					$rstRowRttw = odbc_fetch_array($nResultRttw);

					if(trim($rstRowRttw['rta']) == "M")
						$nEmpTime = 1;
					elseif(trim($rstRowRttw['rta']) == "E")
						$nEmpTime = 2;
					elseif(trim($rstRowRttw['rta']) == "N")
						$nEmpTime = 3;
					else
						$nEmpTime = 4;
				}

// Duty Time Table for Rota Three Weeks  ..... Adnan added this block of Code

				if($nEmpTime < 4 && $nEmpTime > 0)
					$rstRecArray = GetRecord("tblShift", "grp_id = " . $nEmpTime);
				else
					$rstRecArray['Rec'] = 1;
			}
		}
		else
			$rstRecArray['Rec'] = 1;
		return $rstRecArray;
	}

	function makeManualMealTime($nEmpID, $strDate, $MealTime, $arrRGroupSW , $arrRGroupTW)
	{
		if( $MealTime == 3 || $MealTime == 4)
			$arrTimeTable = GetRecord("tblCanteenTimings", "id = 1");
		else
			$arrTimeTable = DutyTimetable($nEmpID, $strDate, $arrRGroupSW , $arrRGroupTW);

		if($MealTime == 0)
			$strTimeStart = $arrTimeTable['grp_breakfast_start'];
		elseif($MealTime == 1)
			$strTimeStart = $arrTimeTable['grp_lunch_start'];
		elseif($MealTime == 2)
			$strTimeStart = $arrTimeTable['grp_dinner_start'];
		elseif($MealTime == 3)
			$strTimeStart = $arrTimeTable['sehri_start'];
		elseif($MealTime == 4)
			$strTimeStart = $arrTimeTable['iftari_start'];
		elseif($MealTime == 5)
			$strTimeStart = $arrTimeTable['grp_teabreak1_start'];
		elseif($MealTime == 6)
			$strTimeStart = $arrTimeTable['grp_teabreak2_start'];
		else
			$strTimeStart = "0000";
		return 	$strTimeStart;
	}

	function GetCanteenTiming($MealTime)
	{

		$rstRow = GetRecord("tblCanteenTimings", "id = 1");
		if($MealTime == 0)
			$strTimeStart = $rstRow['breakFast_start'];
		elseif($MealTime == 1)
			$strTimeStart = $rstRow['lunch_start'];
		elseif($MealTime == 2)
			$strTimeStart = $rstRow['dinner_start'];
		elseif($MealTime == 3)
			$strTimeStart = $rstRow['sehri_start'];
		elseif($MealTime == 4)
			$strTimeStart = $rstRow['iftari_start'];
		elseif($MealTime == 5)
			$strTimeStart = $rstRow['teaBreak1_start'];
		elseif($MealTime == 6)
			$strTimeStart = $rstRow['teaBreak2_start'];
		else
			$strTimeStart = "0000";
		return 	$strTimeStart;
	}

	function JoinDateTime($strDate, $strTime)
	{
		$arr = explode(" ", $strDate);
		$arr = explode("-", $arr[0]);
		//echo $arr[0];
		$strTime = str_pad($strTime, 4, "0000", STR_PAD_LEFT);

		$nHr = substr($strTime, 0, 2);
		$nMin = substr($strTime, 2, 2);

		if($arr[0] > 1970)
			return date("m-d-Y H:i:s", mktime($nHr, $nMin, 0, $arr[1], $arr[2], $arr[0]));
		else
			return date("m-d-Y H:i:s");

	}

	// insert record of employee History
	function InsertEmpStatusHistory($nEmpID, $nEmpStatus, $nEmpIDBy)
	{
		insertRec("tblEmpStatusHistory", array("esh_emp_id"=>$nEmpID,
												"esh_status_id"=>$nEmpStatus,
												"esh_date"=>date("m/d/Y H:i:s"),
												"esh_emp_by"=>$nEmpIDBy));

	}


	/*
		function Check Autorizaion of meeages and generate message's
		$strMessage = Message to be generated
		$nEmpID = Employee ID who's message tobe generate
		$nMessageTypeID = Message type who's Authorize Users will be Recieve message
		$nDeptID = Emp Department ID
		$nSectionID= Emp Section ID
	*/
	function DistributeEmployeeMessage($strMessage, $nMessageTypeID, $nDeptID, $nSectionID, $strMailType = '', $nMsgCount = 0, $nDayCheckGreater = 1)
	{
		// distribute mail at Orgnization Level
		$rstRow1 = GetRecord("tblMsgMaster", " msm_level = 1 AND msm_message_id = ". $nMessageTypeID);
		if(is_array ( $rstRow1))
		{
			$strQuery = "SELECT * FROM tblMsgDetail WHERE msd_msm_id = ". $rstRow1['msm_id'];
			$nResult = MsSQLQuery($strQuery);
			while( $rstRow2 = odbc_fetch_array($nResult) )
				if($nDayCheckGreater == 0)
				{
					if( $nMsgCount <= $rstRow2['msd_emp_day'] )
						InsertMailMessage($strMessage, $rstRow2['msd_emp_id'], $strMailType);
				}
				else
				{
					if( $nMsgCount >= $rstRow2['msd_emp_day'])
						InsertMailMessage($strMessage, $rstRow2['msd_emp_id'], $strMailType);
				}

		}
		// distribute mail at Department Level
		$rstRow1 = GetRecord("tblMsgMaster", " msm_level = 2 AND msm_sec_id = ". $nDeptID ." AND msm_message_id = ". $nMessageTypeID);
		if(is_array ( $rstRow1))
		{
			$strQuery = "SELECT * FROM tblMsgDetail WHERE msd_msm_id = ". $rstRow1['msm_id'];
			$nResult = MsSQLQuery($strQuery);
			while( $rstRow2 = odbc_fetch_array($nResult) )
				if($nDayCheckGreater == 0)
				{
					if( $nMsgCount <= $rstRow2['msd_emp_day'] )
						InsertMailMessage($strMessage, $rstRow2['msd_emp_id'], $strMailType);
				}
				else
				{
					if( $nMsgCount >= $rstRow2['msd_emp_day'])
						InsertMailMessage($strMessage, $rstRow2['msd_emp_id'], $strMailType);
				}

		}
		// distribute mail at Section Level
		$rstRow1 = GetRecord("tblMsgMaster", " msm_level = 3 AND  msm_sec_id = ". $nSectionID ." AND msm_message_id = ". $nMessageTypeID);
		if(is_array ( $rstRow1))
		{
			$strQuery = "SELECT * FROM tblMsgDetail WHERE msd_msm_id = ". $rstRow1['msm_id'];
			$nResult = MsSQLQuery($strQuery);
			while( $rstRow2 = odbc_fetch_array($nResult) )
				if($nDayCheckGreater == 0)
				{
					if( $nMsgCount <= $rstRow2['msd_emp_day'] )
						InsertMailMessage($strMessage, $rstRow2['msd_emp_id'], $strMailType);
				}
				else
				{
					if( $nMsgCount >= $rstRow2['msd_emp_day'])
						InsertMailMessage($strMessage, $rstRow2['msd_emp_id'], $strMailType);
				}
		}

	}
	/*
		function Insert message's in mailBox
		$strMessage = Message to be generated
		$nEmpID = Employee ID who's message tobe generate
	*/
	function InsertMailMessage($strMessage, $nEmpID, $strMailType = '')
	{
		insertRec("tblMailBox", array(
										"mail_emp_id"=>$nEmpID,
										"mail_type"=>$strMailType,
										"mail_meg"=>$strMessage,
										"mail_date"=>date("m/d/Y H:i:s"),
										"mail_deleted"=>0));
	}


	function EmployeePresentINFactory($nSectionID)
	{
		$Query = "SELECT emp_comp_id, emp_name, emp_points as pnts FROM tblEmployee WHERE emp_id IN( SELECT att_emp_id FROM tblAttendance WHERE att_emp_status = 1 AND att_Section_id = " . $nSectionID . ")";
		$nResult5 = MsSQLquery($Query);
		$strEmpList = "<cenetr><table border=1>
						<tr>
							<td align=cenetr width=50 bgcolor=silver>
								<b>ID
							</td>
							<td align=cenetr width=200 bgcolor=silver>
								<b>Name
							</td>
							<td align=cenetr width=50 bgcolor=silver>
								<b>Points
							</td>
						</tr>";

		while($rstRow5 = odbc_fetch_array($nResult5))
		{
			$strEmpList = $strEmpList ."
						<tr>
							<td align=right width=50>"
								.$rstRow5['emp_comp_id']."
							</td>
							<td width=200>"
								.$rstRow5['emp_name']."
							</td>
							<td align=right width=50>"
								.$rstRow5['pnts']."
							</td>
						</tr>";
		}
		$strEmpList = $strEmpList ."</table></cenetr>";
		return $strEmpList;
	}
		/*
	function Returns Number of Abdents of Employee
	$nStartDate           End Date
	$strEndDate          Start Date
	$nEmpID              Employee db-id
	*/
	function GetEmpAttendanceRec($nStartDate, $strEndDate, $nEmpID)
	{

		//Attendace status stored in tblAttendancemaster
		// $nStatus = 1     Present
		// $nStatus = 2     off Day
		// $nStatus = 3     On unPaid Leave
		// $nStatus = 4     Absent
		// $nStatus = 5     Punished
		// $nStatus = 6     GateLock
		// $nStatus = 7     Paid Leave
		// $nStatus = 8     Present at Gezzeted Holiday
		// $nStatus = 9     Gazzeted Holiday Off Day
		// $nStatus = 10     Present at Shutt Down
		// $nStatus = 11     Shutt Down Off Day
		for ($i = 1; $i <= 11; $i++)
		{
			$arrEmpAttendance[$i] = 0;

		}

		$Query = "SELECT * FROM tblAttendanceMaster
					  WHERE	(atm_emp_id = $nEmpID) AND
						( atm_date BETWEEN '$nStartDate' AND '$strEndDate' )";
		$nResult = MsSQLQuery($Query);
		while ($rstRow = odbc_fetch_array($nResult))
		{

			switch ($rstRow['atm_status'])
			{
				case 1 :
					//echo $arrEmpAttendance[1];
					$arrEmpAttendance[1] ++ ;
					break;
				case 2 :
					$arrEmpAttendance[22] ++ ;
					$nGazzetedHoliday = RecCount("tblHoliday", " hol_start BETWEEN '". $rstRow['atm_date'] ."' AND '". $rstRow['atm_date'] ."'
								OR  hol_end BETWEEN '". $rstRow['atm_date']. "' AND '". $rstRow['atm_date'] ."'");
					if( $nGazzetedHoliday <= 0 )
					{
						$arrEmpAttendance[2] += 1;
					}
					break;
				case 3 :
					$arrEmpAttendance[3] ++ ;
					break;
				case 4 :
					$arrEmpAttendance[4] ++ ;
					break;
				case 5 :
					$arrEmpAttendance[5] ++ ;
					break;
				case 6 :
					$arrEmpAttendance[6] ++ ;
					break;
				case 7 :
					$arrEmpAttendance[7] ++ ;
					break;
				case 8 :
					$arrEmpAttendance[8] ++ ;
					break;
				case 9 :
					$arrEmpAttendance[9] ++ ;
					break;
				case 10 :
					$arrEmpAttendance[10] ++ ;
					break;
				case 11 :
					$arrEmpAttendance[11] ++ ;
					break;
				case 99 :
					$arrEmpAttendance[99] ++ ;
					break;
			}//end switch
		}

		return $arrEmpAttendance; //Attendance Record Array
	}

// Reference Comparison functions to insert the history record if the Reference has been Edited.
function BuildDoubleArrBeforeEditReference($nResultBefore)
	{
		$nResutlArrBefore = array();
		$indexArr = 0;
		while($RecordSetBefor = odbc_fetch_array($nResultBefore))
		{
			$nResutlArrBefore[$indexArr] = $RecordSetBefor;
			$indexArr++;
		}
		return   $nResutlArrBefore;
	}
	function BuildDoubleArrAfterEditReference($nResultAfter)
	{
		$nResutlArrAfter = array();
		$indexArr = 0;
		while($RecordSetBefor = odbc_fetch_array($nResultAfter))
		{
			$nResutlArrAfter[$indexArr] = $RecordSetBefor;
			$indexArr++;
		}
		return   $nResutlArrAfter;
	}

	function InsertReferenceEditHistoryNew($RecordSetBefor)
	{
		while(list($key, $value) = each($RecordSetBefor))
			{
					$ArrayTemp = array(
									 "temp_csr_no"  		=>  $RecordSetBefor[$key]['csr_id'],
									 "temp_csr_cus_id"  	=>  $RecordSetBefor[$key]['csr_cus_id'],
									 "temp_csr_ref"=>  $RecordSetBefor[$key]['csr_ref'],
									 "temp_csr_basis"		=>	$RecordSetBefor[$key]['csr_basis'],
									 "temp_csr_payment_term"		=>	$RecordSetBefor[$key]['csr_payment_term'],
									 "temp_csr_ship_term"		=>	$RecordSetBefor[$key]['csr_ship_term'],
									 "temp_csr_price_currency"		=>	$RecordSetBefor[$key]['csr_price_currency'],
									 "temp_csr_price_price"		=>	$RecordSetBefor[$key]['csr_price_price'],
									 "temp_csr_price_unit"		=>	$RecordSetBefor[$key]['csr_price_unit'],
									 "temp_csr_start_date"		=>	$RecordSetBefor[$key]['csr_start_date'],
									 "temp_csr_end_date"		=>	$RecordSetBefor[$key]['csr_end_date'],
									 "temp_csr_comments"		=>	$RecordSetBefor[$key]['csr_comments'],
									 "temp_csr_status"		=>	$RecordSetBefor[$key]['csr_status'],
									 "temp_editeddate"		=>	date("Y-m-d H:i:s A"),
									 "temp_editedby"		=>	$_SESSION['USER_ID'],
									 "temp_csr_invo_price_unit"		=>	$RecordSetBefor[$key]['csr_invo_price_unit'],
									 "temp_csr_invo_currency"		=>	$RecordSetBefor[$key]['csr_invo_currency'],
									 "temp_csr_createdby" => $RecordSetBefor[$key]['csr_createdby'],
									 "temp_csr_creation_date" => $RecordSetBefor[$key]['csr_creation_date'],
									 "temp_csr_validated_by" => $RecordSetBefor[$key]['csr_validated_by'],
									 "temp_csr_validation_date" => $RecordSetBefor[$key]['csr_validation_date']
									);
					InsertRec("tblMkrtTempCustomerReference", $ArrayTemp);
				}//end of whlie
	}

	/*
	function Returns Working Minutes Within Specified Date used in PMS
	$nStartDate           End Date
	$strEndDate          Start Date
	$nEmpID              Employee db-id
	Return Array
	*/
	function GetWorkingMinutesDaysPMS($nStartDate, $strEndDate, $nEmpID, $strEmpSalBasis = "HOURLY_BASIS_SALARY")
	{

		if(trim($strEmpSalBasis) == "HOURLY_BASIS_SALARY")
		{
			//calculating Working Hrs in Normal day's
			// add MealMinute to Normal Working Hrs temporarly
			$Query = "SELECT SUM(att_normalWork_mint) as Normal, SUM(att_overtimeWork_mint) AS overtime, SUM(att_break_mint) AS mealTime FROM tblAttendance WHERE att_emp_id = $nEmpID AND  att_entry_type = 1 AND  att_duty_date BETWEEN '$nStartDate' AND '$strEndDate'";
			$nResult = MsSQLQuery($Query);
			$rstRow = odbc_fetch_array($nResult);

			if( empty($rstRow['Normal']) || $rstRow['Normal'] < 0)
				$arrWorking['NORMAL_WORK'] = 0;
			else
				$arrWorking['NORMAL_WORK'] = $rstRow['Normal'] + $rstRow['mealTime'];

			if( empty($rstRow['overtime'] )|| $rstRow['overtime'] < 0)
				$arrWorking['OVERTIME_WORK'] = 0;
			else
				$arrWorking['OVERTIME_WORK'] = $rstRow['overtime'];

			//calculating Working Hrs in Gezzeted holiday's
			$Query = "SELECT SUM(att_normalWork_mint) as Normal, SUM(att_overtimeWork_mint) AS overtime, SUM(att_break_mint) AS mealTime FROM tblAttendance WHERE att_emp_id = $nEmpID AND  att_entry_type = 2 AND  att_duty_date BETWEEN '$nStartDate' AND '$strEndDate' ";
			$nResult = MsSQLQuery($Query);
			$rstRow = odbc_fetch_array($nResult);
			$arrWorking['NORMAL_GEZZETED'] = $rstRow['Normal'] + $rstRow['mealTime'];
			$arrWorking['OVERTIME_GEZZETED'] = $rstRow['overtime'];

			// Calculate time in factory Other than Gazzeted day's work
			$Query = "SELECT SUM(DATEDIFF(mi, att_ent_date , att_ext_date )) as workingMnt FROM tblAttendance WHERE att_emp_id = $nEmpID  AND  att_duty_date BETWEEN '$nStartDate' AND '$strEndDate' ";
			$nResult = MsSQLQuery($Query);
			$rstRow = odbc_fetch_array($nResult);
			$arrWorking['TIME_IN_FACTORY'] = $rstRow['workingMnt'];
			return $arrWorking;
		}

	}// end Function

	function BuildDoubleArrBefore($nResultBefore)
	{
		$nResutlArrBefore = array();
		$indexArr = 0;
		while($RecordSetBefor = odbc_fetch_array($nResultBefore))
		{
			$nResutlArrBefore[$indexArr] = $RecordSetBefor;
			$indexArr++;
		}
		return   $nResutlArrBefore;
	}
	function BuildDoubleArrAfter($nResultAfter)
	{
		$nResutlArrAfter = array();
		$indexArr = 0;
		while($RecordSetBefor = odbc_fetch_array($nResultAfter))
		{
			$nResutlArrAfter[$indexArr] = $RecordSetBefor;
			$indexArr++;
		}
		return   $nResutlArrAfter;
	}
	//this function is used  for
	// Marketing
	// and payroll so it is copied here
	//this function calculate Employee Efficiency based on their production
	function GetToLinkEfficiency($nEmpId, $nEmpCompId, $strFrom, $strTo)
	{
		$nAvailTime = 0;
		$nTarget = 0;
		$nTargetDzns = 0;
		$nActualDoz = 0;
		$nWorkMinute = 0;
		$nToTalOutTime = 0;
		$nEmpEff = 0;

		$ResultToelink = odbc_fetch_array(MsSQLQuery("SP_ToeLinkOperatorEff '".$strFrom."' , '".$strTo."' , ".$nEmpCompId.",".$nEmpId));

		$nTargetDzns = $ResultToelink['nTargetDzns'];
		$nActualDoz =  $ResultToelink['nActualDoz'];
		$nWorkMinute = $ResultToelink['nWorkMinute'];
		$nToTalOutTime = $ResultToelink['nToTalOutTime'];
		$nAvailTime = $ResultToelink['nWorkingMints'];
		if($nTargetDzns > 0)
			$nEmpEff = (($nActualDoz / $nTargetDzns) * ( ($nWorkMinute + $nToTalOutTime) / $nAvailTime ) * 100);

		if($nEmpEff > 100)
			$nEmpEff = 100;

		return $nEmpEff;
	}
	///Function Calculate Knitting Efficiency
	//On base of Emp Compnay Id
	//Start Date
	//End Date
	function GetEmpKnittinkEfficiency($nEmpCompId, $strFrom, $strTo)
	{
			$queryEfk="SELECT SUM(knit_target) AS target, SUM(knit_grda) AS gradea
							FROM tblPlanKnitEntry
								WHERE knit_emp_id = ".$nEmpCompId." AND (knit_date BETWEEN '".$strFrom."' AND '".$strTo."')";
		  $ResultEfk = MsSQLQuery($queryEfk);
		  $nEffPrd = 1;
		  while($RowEfk = odbc_fetch_array($ResultEfk))
		  {
				if($RowEfk['gradea'] > 0 && $RowEfk['target'] > 0)
					$nEffPrd = round(($RowEfk['gradea'] / $RowEfk['target'] * 100), 2);
		  }
		 //Return Eff
		 return $nEffPrd;
	}
	// this function returns PO Qty of specific style, and gets PO Number, Sheet , Rev , Color and Size as parameters.
	function calculatePOQtyAgainstStyle($strPO,$sheet,$rev,$color,$size)
	{

		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(tblPlanPOLotsDetail.lotd_qty), 0) AS StyleQty
										       FROM   tblPlanPOLots INNER JOIN
                      								  tblPlanPOLotsDetail ON tblPlanPOLots.po_lot_id = tblPlanPOLotsDetail.lotd_po_lot_id
											   WHERE  (tblPlanPOLots.po_dyn_no = ".$strPO.") AND (tblPlanPOLotsDetail.lotd_sheet_no = ".$sheet.") AND (tblPlanPOLotsDetail.lotd_rev_no = ".$rev.") AND
                      								  (tblPlanPOLotsDetail.lotd_size = ".$size.") AND (tblPlanPOLotsDetail.lotd_color = '".$color."') and tblPlanPOLots.po_special_lot is null "));
		return $rstRow['StyleQty'];

	}

	function calcPOQtyAgainstStyleWithCompSize($strPO,$sheet,$rev,$color,$size)
	{
		$rstSize = odbc_fetch_array(MsSQLQuery("SELECT tblMkrtSocksSizes.sok_siz_id AS sok_siz_id
												FROM   tblMkrtSocksSizes INNER JOIN
													   tblMkrtComputerCountrySocksSizes ON
													   tblMkrtSocksSizes.sok_siz_id = tblMkrtComputerCountrySocksSizes.comp_cont_sok_siz_sok_siz_id INNER JOIN
													   tblMkrtMachineSizes ON tblMkrtComputerCountrySocksSizes.comp_cont_sok_siz_siz_id = tblMkrtMachineSizes.siz_id
												WHERE  (tblMkrtComputerCountrySocksSizes.comp_cont_sok_siz_spec_id_man = ".$sheet.") AND
								                       (tblMkrtComputerCountrySocksSizes.comp_cont_sok_siz_spec_rev_no = ".$rev.") AND (tblMkrtMachineSizes.siz_id = ".$size.")"));

		$sockSize = $rstSize['sok_siz_id'];

		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(tblPlanPOLotsDetail.lotd_qty), 0) AS StyleQty
										       FROM   tblPlanPOLots INNER JOIN
                      								  tblPlanPOLotsDetail ON tblPlanPOLots.po_lot_id = tblPlanPOLotsDetail.lotd_po_lot_id
											   WHERE  (tblPlanPOLots.po_dyn_no = ".$strPO.") AND (tblPlanPOLotsDetail.lotd_sheet_no = ".$sheet.") AND (tblPlanPOLotsDetail.lotd_rev_no = ".$rev.") AND
                      								  (tblPlanPOLotsDetail.lotd_size = ".$sockSize.") AND (tblPlanPOLotsDetail.lotd_color = '".$color."')"));
		return $rstRow['StyleQty'];
	}

	////For Procurement
	/*//// Unmarked Stock in Hand
	function GetStockinHandQty($strItmCode)
	{
		$rstRowItmTran = odbc_fetch_array(MsSQLQuery("SELECT  TOP 1 ith_itm_tot_balance , ith_id FROM tblStrItemTransHistory WHERE ith_itm_code ='".$strItmCode."'  ORDER BY ith_id DESC"));	//AND (ith_dyn_job_no = 0)
		return $rstRowItmTran['ith_itm_tot_balance'];
	}
	////For Procurement
	//// Unmarked Stock in Hand
	function GetMarkedQty($strPONo, $strItmCode,$nChek="")
	{
		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT * FROM tblPrcMarkedQty WHERE  mq_itm_code='".$strItmCode."' AND  mq_dyn_job_no ".$nChek."= ".$strPONo));
		return $rstRow['mq_qty'];
	}

	function IssuedPOItemQty($strItmCode, $nPO)
	{
		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT SUM(tblStrIssueDetails.issd_qty_required) AS issd_qty_required  FROM  tblStrIssueRequisitionMaster INNER JOIN  tblStrIssueDetails ON tblStrIssueRequisitionMaster.sir_id = tblStrIssueDetails.issd_src_id
											   WHERE (tblStrIssueDetails.issd_src_type IN ('JOS', 'JOSE')) AND (tblStrIssueDetails.issd_jobno = ".$nPO.") AND (tblStrIssueDetails.issd_item_code = '".$strItmCode."')  AND (tblStrIssueRequisitionMaster.sir_cancel = 0) GROUP BY tblStrIssueDetails.issd_item_code, tblStrIssueDetails.issd_jobno, tblStrIssueDetails.issd_src_type"));
		return $rstRow['issd_qty_required'];
	}
	function  ReturnedQtyJos($strItmCode, $nPO)
	{
			$rstRow = odbc_fetch_array(MsSQLQuery("SELECT     SUM(recvd_qty_post) AS recvd_qty_post FROM  tblStrRecvDetail WHERE (recvd_job_no = ".$nPO.") AND (recvd_item_code = '".$strItmCode."') GROUP BY recvd_item_code, recvd_job_no"));
			return $rstRow['recvd_qty_post'];
	}*/
	//// Unmarked Stock in Hand
	function GetStockinHandQty($strItmCode)
	{
		$rstRowItmTran = odbc_fetch_array(MsSQLQuery("SELECT  TOP 1 ith_itm_tot_balance , ith_id FROM tblStrItemTransHistory WHERE ith_itm_code ='".$strItmCode."'  ORDER BY ith_id DESC"));	//AND (ith_dyn_job_no = 0)
		return $rstRowItmTran['ith_itm_tot_balance'];
	}
	////For Procurement
	//// Unmarked Stock in Hand
	/*function GetMarkedQty($strPONo, $strItmCode,$nChek="")
	{
		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT * FROM tblPrcMarkedQty WHERE  mq_itm_code='".$strItmCode."' AND  mq_dyn_job_no ".$nChek."= ".$strPONo));
		return $rstRow['mq_qty'];
	}*/

	function GetMarkedQty($strPONo, $strItmCode,$nChek="")
	{
		if($nChek !="")
			$rstRow = odbc_fetch_array(MsSQLQuery("SELECT SUM( mq_qty ) AS mq_qty   FROM tblPrcMarkedQty WHERE  mq_itm_code='".$strItmCode."' AND  mq_dyn_job_no ".$nChek."= ".$strPONo.""));
		else
			$rstRow = odbc_fetch_array(MsSQLQuery("SELECT * FROM tblPrcMarkedQty WHERE  mq_itm_code='".$strItmCode."' AND  mq_dyn_job_no ".$nChek."= ".$strPONo));

		return $rstRow['mq_qty'];
	}

	function GetMarkedQty_add($strPONo, $strItmCode,$nChek="")
	{
		if($nChek !="")
			$rstRow = odbc_fetch_array(MsSQLQuery("SELECT  SUM(mq_qty_add) AS mq_qty_add FROM tblPrcMarkedQty WHERE  mq_itm_code='".$strItmCode."' AND  mq_dyn_job_no ".$nChek."= ".$strPONo));
		else
			$rstRow = odbc_fetch_array(MsSQLQuery("SELECT mq_qty_add FROM tblPrcMarkedQty WHERE  mq_itm_code='".$strItmCode."' AND  mq_dyn_job_no ".$nChek."= ".$strPONo));

		return $rstRow['mq_qty_add'];
	}

	function GetBillQtyProdWast($strPONo, $strItmId )
	{
		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT     pbm_prod_wast  FROM         tblPrcPlanBillMatrial  WHERE     (pbm_itm_id = ".$strItmId.") AND (pbm_dyn_po_no = ".$strPONo.")"));
		return $rstRow['pbm_prod_wast'];
	}
	/*
	function IssuedPOItemQty($strItmCode, $nPO)
	{
		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT SUM(tblStrIssueDetails.issd_qty_issued) AS issd_qty_issued  FROM  tblStrIssueRequisitionMaster INNER JOIN  tblStrIssueDetails ON tblStrIssueRequisitionMaster.sir_id = tblStrIssueDetails.issd_src_id
											   WHERE (tblStrIssueDetails.issd_src_type IN ('JOS', 'JOSE')) AND (tblStrIssueDetails.issd_jobno = ".$nPO.") AND (tblStrIssueDetails.issd_item_code = '".$strItmCode."')  AND (tblStrIssueRequisitionMaster.sir_cancel = 0) GROUP BY tblStrIssueDetails.issd_item_code, tblStrIssueDetails.issd_jobno, tblStrIssueDetails.issd_src_type"));
		return $rstRow['issd_qty_issued'];
	}
	*/
	function IssuedPOItemQty($strItmCode, $nPO)
	{

		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT SUM(tblStrIssueDetails.issd_qty_issued) AS issd_qty_issued  FROM  tblStrIssueRequisitionMaster INNER JOIN  tblStrIssueDetails ON tblStrIssueRequisitionMaster.sir_id = tblStrIssueDetails.issd_src_id
											   WHERE (tblStrIssueDetails.issd_src_type IN ('JOS' , 'JOSPB' , 'JOSPBE', 'JOSE' , 'SHORTAGE')) AND (tblStrIssueDetails.issd_jobno = ".$nPO.") AND (tblStrIssueDetails.issd_item_code = '".$strItmCode."')  AND (tblStrIssueRequisitionMaster.sir_cancel = 0) "));//GROUP BY tblStrIssueDetails.issd_item_code, tblStrIssueDetails.issd_jobno, tblStrIssueDetails.issd_src_type
		return $rstRow['issd_qty_issued'];
	}

	function  ReturnedQtyJos($strItmCode, $nPO)
	{
			$rstRow = odbc_fetch_array(MsSQLQuery("SELECT     SUM(recvd_qty_post) AS recvd_qty_post FROM  tblStrRecvDetail WHERE recvd_src_type IN('JRMAT') AND (recvd_job_no = ".$nPO.") AND (recvd_item_code = '".$strItmCode."') GROUP BY recvd_item_code, recvd_job_no"));
			return $rstRow['recvd_qty_post'];
	}

	function ProcurementPOStatus($nPONo , $strItemCode)
	{
		$rstRow = odbc_fetch_array(MsSQLQuery("SELECT tblStrRecvDetail.* FROM  tblStrRecvPurOrderGrnLoc INNER JOIN  tblStrRecvDetail ON tblStrRecvPurOrderGrnLoc.rpol_id = tblStrRecvDetail.recvd_rsr_id
		 									   WHERE (tblStrRecvDetail.recvd_item_code = '".$strItemCode."') AND (tblStrRecvPurOrderGrnLoc.rpol_po_no = ".$nPONo.")"));
	}
	// this function is used to marked quantity
	function SetMarkQty($strPONo,$strItmID,$strItmCode, $nMarkQty)
	{
		if($nMarkQty < 0)
			return;

		if(RecCount("tblPrcMarkedQty","mq_dyn_job_no=".$strPONo." AND mq_itm_id=".$strItmID." AND mq_itm_code='".$strItmCode."'" ) <= 0 )
		{
			$arrValue["mq_dyn_job_no"] = $strPONo;
			$arrValue["mq_itm_id"] = $strItmID;
			$arrValue["mq_itm_code"] = $strItmCode;
			$arrValue["mq_qty"] = $nMarkQty;
			$arrValue["mq_qty_add"] = $nMarkQty;
			InsertRec("tblPrcMarkedQty", $arrValue);
		}
		else
		{
			//Get Existing Record
			$rstRow = GetRecord("tblPrcMarkedQty",  "mq_dyn_job_no=".$strPONo." AND mq_itm_id=".$strItmID." AND mq_itm_code='".$strItmCode."'" );
			$arrValue["mq_qty"] = ($rstRow['mq_qty'] + $nMarkQty);
			$arrValue["mq_qty_add"] = ($rstRow['mq_qty_add'] + $nMarkQty);
			UpdateRec("tblPrcMarkedQty",  "mq_dyn_job_no=".$strPONo." AND mq_itm_id=".$strItmID." AND mq_itm_code='".$strItmCode."'" , $arrValue);
		}
	}

		// Get the whole issued Qty in POs and in Work Orders against the item Code and Dyn Job Number.
		// Job Order Specific

		/*function getIssuedJobOrderQtyJOS($nDynPoNo , $itemCode , $whr = '')
		{
			$alreadyIss = 0;
			$rstQtyPO = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(tblPrcPOMMItmDetails.pmd_qty_require), 0) AS PO_ISSUED_QTY
													 FROM   tblPrcPOMMItmDetails INNER JOIN
										                    tblPrcPomMultItm ON tblPrcPOMMItmDetails.pmd_pmm_id = tblPrcPomMultItm.pmm_id
													 WHERE  (tblPrcPomMultItm.pmm_po_type IN ('CBL', 'NCL', 'IPJ')) AND (tblPrcPOMMItmDetails.pmd_jobno = ".$nDynPoNo.") AND
										                    (tblPrcPOMMItmDetails.pmd_item_code = '".$itemCode."')".$whr));

			// Dyeing Bleaching JOS
			$rstDyBleCon = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(dy_bled_order_qty), 0) AS ORD_QTY_CON FROM tblprcDyingBleachWOConDetail WHERE (dy_bled_itm_code = '".$itemCode."') AND (dy_bled_job_order_no = ".$nDynPoNo.")"));
			$rstDyBleNonCon = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(dy_bled_order_qty), 0) AS ORD_QTY_NON_CON FROM tblprcDyingBleachWONonConDetail WHERE (dy_bled_itm_code = '".$itemCode."') AND (dy_bled_job_order_no = ".$nDynPoNo.")"));

			// Rewinding JOS
			$rstRewConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(rewd_order_qty), 0) AS ORD_QTY_CON FROM tblPrcRewindingWODetailCont WHERE (rewd_itm_code = '".$itemCode."') AND (rewd_job_order_no = ".$nDynPoNo.")"));
			$rstRewNonConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(rewd_order_qty), 0) AS ORD_QTY_NON_CON FROM tblPrcRewindingWODetailNonCont WHERE (rewd_itm_code = '".$itemCode."') AND (rewd_job_order_no = ".$nDynPoNo.")"));

			// Twisting JOS
			$rstTwistConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS ORD_QTYCon FROM tblprcTwistingWOConDetail WHERE (twistd_job_order_no = ".$nDynPoNo.") AND (twistd_itm_code = '".$itemCode."')"));
			$rstTwistNonConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS ORD_QTYNon FROM tblprcTwistingWONonConDetail WHERE (twistd_job_order_no = ".$nDynPoNo.") AND (twistd_itm_code = '".$itemCode."')"));
			$twiIntrnJOSQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS ORD_QTYINTJOS FROM tblprcTwistingWOInternalDetail WHERE (twistd_job_order_no = ".$nDynPoNo.") AND (twistd_itm_code = '".$itemCode."') AND (twistd_type = 'TWINTJS')"));

			// Yarn Covering
			$rstYCConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS ORD_QTYCon
													   FROM tblprcYCoveringWOConDetail
													   WHERE (ycd_itm_code = '".$itemCode."') AND (ycd_job_order_no = ".$nDynPoNo.")"));

			$rstYCNonConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS ORD_QTYNonCon
														   FROM tblprcYCoveringWONonConDetail
														   WHERE (ycd_itm_code = '".$itemCode."') AND (ycd_job_order_no = ".$nDynPoNo.")"));

			$rstYCInternQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS ORD_QTYIntern
														   FROM tblprcYCoveringWOInternalDetail
														   WHERE (ycd_itm_code = '".$itemCode."') AND (ycd_job_order_no = ".$nDynPoNo.") AND (ycd_type = 'YCI')"));

			// Printing Work Order

			$rstPrintJOS = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(printd_order_qty), 0) AS alreadyIssued
													    FROM   tblprcPrintingWOInternalDetail
														WHERE  (printd_job_order_no = ".$nDynPoNo.") AND (printd_itm_code = '".$itemCode."') AND (printd_type = 'PRIJOS')"));

			$rstRowMisWorkOrder = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(tblprcMiscWOInternalDetail.miscd_order_qty, 0) AS miscd_order_qty  FROM         tblPrcWorkOrdersMaster INNER JOIN  tblprcMiscWOInternalDetail ON tblPrcWorkOrdersMaster.wo_id = tblprcMiscWOInternalDetail.miscd_wo_id
														WHERE (tblPrcWorkOrdersMaster.wo_type = 'MISCIJOS') AND (miscd_job_order_no = '".$nDynPoNo."') AND (tblprcMiscWOInternalDetail.miscd_itm_code = '".$itemCode."') "));

			$alreadyIss = ($rstRowMisWorkOrder['miscd_order_qty']+$rstQtyPO['PO_ISSUED_QTY'] + $rstDyBleCon['ORD_QTY_CON'] + $rstDyBleNonCon['ORD_QTY_NON_CON'] + $rstRewConQty['ORD_QTY_CON'] + $rstRewNonConQty['ORD_QTY_NON_CON'] + $rstTwistConQty['ORD_QTYCon'] + $rstTwistNonConQty['ORD_QTYNon'] + $twiIntrnJOSQty['ORD_QTYINTJOS'] + $rstYCConQty['ORD_QTYCon'] + $rstYCNonConQty['ORD_QTYNonCon'] + $rstYCInternQty['ORD_QTYIntern'] + $rstPrintJOS['alreadyIssued']);
			return $alreadyIss;
		}*/

		function getIssuedJobOrderQtyJOS($nDynPoNo , $itemCode , $whr = '')
		{
			$alreadyIss = 0;

			$rstQtyPO = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(tblPrcPOMMItmDetails.pmd_qty_require), 0) AS PO_ISSUED_QTY
													 FROM   tblPrcPOMMItmDetails INNER JOIN
										                    tblPrcPomMultItm ON tblPrcPOMMItmDetails.pmd_pmm_id = tblPrcPomMultItm.pmm_id
													 WHERE  (tblPrcPomMultItm.pmm_po_type IN ('CBL', 'NCL', 'IPJ')) AND (tblPrcPOMMItmDetails.pmd_jobno = ".$nDynPoNo.") AND
										                    (tblPrcPOMMItmDetails.pmd_item_code = '".$itemCode."')".$whr));

			// Dyeing Bleaching JOS
			$rstDyBleCon = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(dy_bled_order_qty), 0) AS ORD_QTY_CON FROM tblprcDyingBleachWOConDetail INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = dy_bled_wo_id  WHERE wo_cancel <> 1 AND (dy_bled_itm_code = '".$itemCode."') AND (dy_bled_job_order_no = ".$nDynPoNo.")"));
			$rstDyBleNonCon = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(dy_bled_order_qty), 0) AS ORD_QTY_NON_CON FROM tblprcDyingBleachWONonConDetail INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = dy_bled_wo_id WHERE wo_cancel <> 1 AND (dy_bled_itm_code = '".$itemCode."') AND (dy_bled_job_order_no = ".$nDynPoNo.")"));

			// Rewinding JOS
			$rstRewConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(rewd_order_qty), 0) AS ORD_QTY_CON FROM tblPrcRewindingWODetailCont  INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = rewd_wo_id   WHERE wo_cancel <> 1 AND (rewd_itm_code = '".$itemCode."') AND (rewd_job_order_no = ".$nDynPoNo.")"));
			$rstRewNonConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(rewd_order_qty), 0) AS ORD_QTY_NON_CON FROM tblPrcRewindingWODetailNonCont INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = rewd_wo_id WHERE (rewd_itm_code = '".$itemCode."') AND (rewd_job_order_no = ".$nDynPoNo.")"));

			// Twisting JOS
			$rstTwistConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS ORD_QTYCon FROM tblprcTwistingWOConDetail   INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = twistd_wo_id  WHERE wo_cancel <> 1 AND (twistd_job_order_no = ".$nDynPoNo.") AND (twistd_itm_code = '".$itemCode."')"));
			$rstTwistNonConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS ORD_QTYNon FROM tblprcTwistingWONonConDetail   INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = twistd_wo_id  WHERE wo_cancel <> 1 AND (twistd_job_order_no = ".$nDynPoNo.") AND (twistd_itm_code = '".$itemCode."')"));
			$twiIntrnJOSQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS ORD_QTYINTJOS FROM tblprcTwistingWOInternalDetail   INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = twistd_wo_id WHERE wo_cancel <> 1 AND (twistd_job_order_no = ".$nDynPoNo.") AND (twistd_itm_code = '".$itemCode."') AND (twistd_type = 'TWINTJS')"));

			// Yarn Covering
			$rstYCConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS ORD_QTYCon    FROM tblprcYCoveringWOConDetail  INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = ycd_wo_id   WHERE wo_cancel <> 1 AND (ycd_itm_code = '".$itemCode."') AND (ycd_job_order_no = ".$nDynPoNo.")"));

			$rstYCNonConQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS ORD_QTYNonCon
														   FROM tblprcYCoveringWONonConDetail INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = ycd_wo_id
														   WHERE wo_cancel <> 1 AND (ycd_itm_code = '".$itemCode."') AND (ycd_job_order_no = ".$nDynPoNo.")"));

			// External Miscelaneous WO JOS --- New Type
			$rstExtMiscNCQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(miscd_order_qty), 0) AS ORD_QTYMISCEXT
														   FROM tblprcMiscWOExtNCDetail INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = miscd_wo_id
														   WHERE wo_cancel <> 1 AND (miscd_itm_code = '".$itemCode."') AND (miscd_job_order_no = ".$nDynPoNo.")"));

			$rstYCInternQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS ORD_QTYIntern
														   FROM tblprcYCoveringWOInternalDetail INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = ycd_wo_id
														   WHERE  wo_cancel <> 1 AND (ycd_itm_code = '".$itemCode."') AND (ycd_job_order_no = ".$nDynPoNo.") AND (ycd_type = 'YCI')"));

			// Printing Work Order

			$rstPrintJOS = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(printd_order_qty), 0) AS alreadyIssued
													    FROM   tblprcPrintingWOInternalDetail INNER JOIN  tblPrcWorkOrdersMaster ON  wo_id = printd_wo_id
														WHERE  wo_cancel <> 1 AND (printd_job_order_no = ".$nDynPoNo.") AND (printd_itm_code = '".$itemCode."') AND (printd_type = 'PRIJOS')"));

			$rstRowMisWorkOrder = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(tblprcMiscWOInternalDetail.miscd_order_qty, 0) AS miscd_order_qty  FROM         tblPrcWorkOrdersMaster INNER JOIN  tblprcMiscWOInternalDetail ON tblPrcWorkOrdersMaster.wo_id = tblprcMiscWOInternalDetail.miscd_wo_id
														WHERE wo_cancel <> 1 AND (tblPrcWorkOrdersMaster.wo_type = 'MISCIJOS') AND (miscd_job_order_no = '".$nDynPoNo."') AND (tblprcMiscWOInternalDetail.miscd_itm_code = '".$itemCode."') "));


			  $alreadyIss = ($rstRowMisWorkOrder['miscd_order_qty'] + $rstQtyPO['PO_ISSUED_QTY'] + $rstDyBleCon['ORD_QTY_CON'] + $rstDyBleNonCon['ORD_QTY_NON_CON'] + $rstRewConQty['ORD_QTY_CON'] + $rstRewNonConQty['ORD_QTY_NON_CON'] + $rstTwistConQty['ORD_QTYCon'] + $rstTwistNonConQty['ORD_QTYNon'] + $twiIntrnJOSQty['ORD_QTYINTJOS'] + $rstYCConQty['ORD_QTYCon'] + $rstYCNonConQty['ORD_QTYNonCon'] + $rstExtMiscNCQty['ORD_QTYMISCEXT'] + $rstYCInternQty['ORD_QTYIntern'] + $rstPrintJOS['alreadyIssued']);

			return $alreadyIss;
		}

		function getIssuedPurchReqQty($PR_No , $itemCode)
		{
			$alreadyIss = 0;


			// PO already issued Qty
			$poIssQty = odbc_fetch_array(MsSQLQuery("SELECT SUM(tblPrcPOMMItmDetails.pmd_qty_require) AS pmd_qty_require
													 FROM   tblPrcPomMultItm INNER JOIN
										                    tblPrcPOMMItmDetails ON tblPrcPOMMItmDetails.pmd_pmm_id = tblPrcPomMultItm.pmm_id
													 WHERE  pmm_po_type IN ('GIC', 'GIN' , 'IPG') AND (tblPrcPOMMItmDetails.pmd_jobno = ".$PR_No.") AND
										                    (tblPrcPOMMItmDetails.pmd_item_code = '".$itemCode."')"));

			// Dyeing Bleaching general Work Orders
			$IssCont = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(dy_bled_order_qty), 0) AS Issued
												    FROM   tblprcDyingBleachWOConDetail
												    WHERE  (dy_bled_pr_no = ".$PR_No.") AND (dy_bled_type = 'DBCG') AND (dy_bled_itm_code = '".$itemCode."')"));

			$IssNonCont = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(dy_bled_order_qty), 0) AS Issued
												       FROM   tblprcDyingBleachWONonConDetail
												       WHERE  (dy_bled_pr_no = ".$PR_No.") AND (dy_bled_type = 'DBNCG') AND (dy_bled_itm_code = '".$itemCode."')"));


			// Rewinding general work Order
			$IssContRew = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(rewd_order_qty), 0) AS Issued
												       FROM   tblPrcRewindingWODetailCont
												       WHERE  (rewd_pr_no = ".$PR_No.") AND (rewd_type = 'RCG') AND (rewd_itm_code = '".$itemCode."')"));

			$IssNonContRew = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(rewd_order_qty), 0) AS Issued
														  FROM   tblPrcRewindingWODetailNonCont
														  WHERE  (rewd_pr_no = ".$PR_No.") AND (rewd_type = 'RNCG') AND (rewd_itm_code = '".$itemCode."')"));


			// Twisting general work orders
			$IssTwiNonCont = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS Issued
												          FROM   tblprcTwistingWONonConDetail
												          WHERE  (twistd_type = 'TNCG') AND (twistd_pr_no = ".$PR_No.") AND (twistd_itm_code = '".$itemCode."')"));

			$IssTwiCont = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS Issued
												       FROM   tblprcTwistingWOConDetail
												       WHERE  (twistd_type = 'TCG') AND (twistd_pr_no = ".$PR_No.") AND (twistd_itm_code = '".$itemCode."')"));

			$IssTwiIntGen = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(twistd_order_qty), 0) AS Issued
													     FROM   tblprcTwistingWOInternalDetail
													     WHERE  (twistd_type = 'TWINTGEN') AND (twistd_pr_no = ".$PR_No.") AND (twistd_itm_code = '".$itemCode."')"));

			// Yarn Covering general work orders
			$IssYCNonCont = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS Issued
													   FROM   tblprcYCoveringWONonConDetail
													   WHERE  (ycd_pr_no = ".$PR_No.") AND (ycd_type = 'YCNG') AND (ycd_itm_code = '".$itemCode."')"));

			$IssYCCont = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS Issued
													   FROM   tblprcYCoveringWOConDetail
													   WHERE  (ycd_pr_no = ".$PR_No.") AND (ycd_type = 'YCG') AND (ycd_itm_code = '".$itemCode."')"));

			$rstYCInternQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ycd_order_qty), 0) AS Issued
											           FROM tblprcYCoveringWOInternalDetail
  											           WHERE (ycd_itm_code = '".$itemCode."') AND (ycd_pr_no = ".$PR_No.") AND (ycd_type = 'YCIG')"));

			$rstYDInternQty = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(ydd_order_qty), 0) AS Issued
											           FROM tblprcYDyeingWOInternalDetail
  											           WHERE (ydd_itm_code = '".$itemCode."') AND (ydd_pr_no = ".$PR_No.") AND (ydd_type = 'YDIG')"));

			// Macelaneous work Order general.

			$rstQtyMiscGen = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(miscd_order_qty), 0) AS Issued
														  FROM   tblprcMiscWOInternalDetail
														  WHERE  (miscd_type = 'MISCIG') AND (miscd_pr_no = ".$PR_No.") AND (miscd_itm_code = '".$itemCode."')"));

			$rstMiscWONC = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(miscd_order_qty), 0) AS Issued
														  FROM   tblprcMiscWOInternalDetail
														  WHERE  (miscd_type = 'MISCENC') AND (miscd_pr_no = ".$PR_No.") AND (miscd_itm_code = '".$itemCode."')"));


			// Printing Internal General
			$rstPrintIntrn = odbc_fetch_array(MsSQLQuery("SELECT ISNULL(SUM(printd_order_qty), 0) AS Issued
													    FROM   tblprcPrintingWOInternalDetail
													    WHERE  (printd_type = 'PRIGEN') AND (printd_pr_no = ".$PR_No.") AND (printd_itm_code = '".$itemCode."')"));
			$alreadyIss = ($poIssQty['pmd_qty_require'] + $IssCont['Issued'] + $IssNonCont['Issued'] + $IssContRew['Issued'] + $IssNonContRew['Issued'] + $IssTwiNonCont['Issued'] + $IssTwiCont['Issued'] + $IssTwiIntGen['Issued'] + $IssYCNonCont['Issued'] + $IssYCCont['Issued'] + $rstYCInternQty['Issued'] + $rstYDInternQty['Issued'] + $rstQtyMiscGen['Issued'] + $rstMiscWONC['Issued'] + $rstPrintIntrn['Issued']);
			return $alreadyIss;
		}


		// function gets the Query with joins and Return the number of records.
		// set the alias of counter as ....... SELECT Count(*) AS result FROM TABLE
		function RecordsCount($Query)
		{
			$result = odbc_fetch_array(MsSQLQuery("$Query"));
			return $result['result'];
		}

		function InternalWorkOrderNo()
		{
			$rstMaxWO = odbc_fetch_array(MsSQLQuery("SELECT TOP 1 ISNULL(wo_wo_no, 0) AS wo_wo_no  FROM   tblPrcWorkOrdersMaster  WHERE  (wo_type IN ('YCI', 'TWINTJS', 'PRIJOS', 'YCIG', 'TWINTGEN', 'PRIGEN', 'MISCIG'  , 'MISCIJOS', 'YDI' , 'YDIG')) ORDER BY wo_id DESC"));
			if($rstMaxWO['wo_wo_no'] != 0)
			{
				$arr = explode("-",$rstMaxWO['wo_wo_no']);
				$maxId = $arr[0];
				$maxId = $maxId + 1;
				$rstRowMax = $maxId."-".date("y");
			}
			else
			{
				$maxId = $rstMaxWO['wo_wo_no'] + 1;
				$rstRowMax = $maxId."-".date("y");
			}

			return $rstRowMax;
		}
		function GetParentPath()
		{
			$svrName=$_SERVER['SERVER_ADDR'];
			if(isset($_SESSION['prePage']))
				return "window.opener.open('http://$svrName".$_SESSION['prePage']."','_parent');";
			else
				return "window.opener.history.go(-1);";
		}

		function CheckBoxVIEWADD($strLabel, $strName, $arrayChecked = array(0,0), $arrayShow = array(1,1) )
		{
			echo "<tr>
					<td>
						$strLabel
					</td>";
			for($i = 0; $i<2; $i++)
			{
				if( $arrayChecked[$i] ==1)
				{
					$check[$i] =  "CHECKED";

				}
				else
					$check[$i] =  "";

			}
			if($arrayShow[0]==1)
				echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
			else
				echo "<td align=center><img src=/images/empty.gif>";
			if($arrayShow[1] == 1)
				echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
			else
				echo "<td align=center><img src=/images/empty.gif>";
			echo "</tr>";
		}

	function TextLookupField10($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName, $strLookupDocument, $callBack='')
	{
		$strUnique = time();
		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>
				<a href=\"JavaScript: CalPop_$strField('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_$strField(sInputName)
				{
					window.open('/include/common/$strLookupDocument?strField=$strField' , 'New', 'scrollbars=yes, toolbar=0,width=650,height=500');
				}
			</script>
			";
	}

	function TableComboQry2($strLabel, $strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MsSQLQuery($strQuery);
		echo "<tr>
				<td>".
					$strLabel;
		echo    "</td>
				<td>";
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0000>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000> NONE \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strIDField];

						if($nID == $nSelId)
							echo "<option value=$nID selected>"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}
				}
				else
				{
					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0> NONE \r\n";

					while($rstRow = odbc_fetch_array($nResult))
					{
						$nID = $rstRow[$strDispField];

						if(trim($nID) == trim($nSelId))
							echo "<option value=$nID  selected >"  . $rstRow[$strDispField] . "\r\n";
						else
							echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
					}




				}
		echo "</select></td></tr>";
	}

	function TextFieldNoCaption($strSpace, $strField, $strValue, $nSize, $nMaxLength, $bPassword , $callBack = '', $strReadonly='')
	{
		echo "	<td> &nbsp; </td>";
		echo "	<td>";

		if($bPassword)
			echo "		<input type=password $strReadonly name=$strField value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>";
		else
			echo "		<input type=text $strReadonly name=$strField value='$strValue' size=$nSize maxlength=$nMaxLength $callBack>";

		echo "	</td>";
	}

	/////////////////////////////////////////////// Quality Efficiency ////////////////////////////////////////////////////
	function QualityEff($strFrom,$strTo,$nEmpCompId="", $nSection="")
	{
		$arrEff[]=NULL;
		$arrPO[]=NULL;
		$arrCalculatedEff[]=NULL;

		if(empty($nSection))
		{
			if(!empty($nEmpCompId))
				$strWhere=" AND (tblPlanKnitEntry.knit_qualBy = ".$nEmpCompId.")  ";

			$nResultEmp=MsSQLQuery("SELECT *
							FROM         (SELECT DISTINCT tblPlanKnitEntry.knit_qualBy, tblPlanKnitEntry.knit_po_no
						   FROM          tblPlanPO INNER JOIN
												  tblPlanKnitEntry ON tblPlanPO.po_no_dyn = tblPlanKnitEntry.knit_po_no
						   WHERE       (tblPlanPO.po_close_from_prod = 1) AND (tblPlanPO.po_close_date_prod BETWEEN '".$strFrom."' AND '".$strTo."') ".$strWhere.") tblKnitOpe INNER JOIN
							  (SELECT     SUM(tblPlanPOLotsDetail.lotd_qty) AS RequiredQty, tblPlanPOLots.po_dyn_no
								FROM          tblPlanPOLots INNER JOIN
													   tblPlanPOLotsDetail ON tblPlanPOLots.po_lot_id = tblPlanPOLotsDetail.lotd_po_lot_id
								WHERE      (tblPlanPOLots.po_special_lot IS NULL)
								GROUP BY tblPlanPOLots.po_dyn_no) tblRequired ON tblKnitOpe.knit_po_no = tblRequired.po_dyn_no LEFT OUTER JOIN
							  (SELECT     SUM(fault_qty) / 24 AS Qty, fault_po_no
								FROM          tblStrRecvBGradeFault
								WHERE      (fault_pft_id = 1)
								GROUP BY fault_po_no) tblFault ON tblRequired.po_dyn_no = tblFault.fault_po_no
								ORDER BY tblRequired.po_dyn_no");
		}
		else
		{
			$nResultEmp=MsSQLQuery("SELECT     *, '".$nEmpCompId."' AS knit_qualBy
FROM         (SELECT DISTINCT po_no_dyn
                       FROM          tblPlanPO
                       WHERE      (tblPlanPO.po_close_from_prod = 1) AND (tblPlanPO.po_close_date_prod BETWEEN '".$strFrom."' AND '".$strTo."')) tblKnitOpe INNER JOIN
                          (SELECT     SUM(tblPlanPOLotsDetail.lotd_qty) AS RequiredQty, tblPlanPOLots.po_dyn_no
                            FROM          tblPlanPOLots INNER JOIN
                                                   tblPlanPOLotsDetail ON tblPlanPOLots.po_lot_id = tblPlanPOLotsDetail.lotd_po_lot_id
                            WHERE      (tblPlanPOLots.po_special_lot IS NULL)
                            GROUP BY tblPlanPOLots.po_dyn_no) tblRequired ON tblKnitOpe.po_no_dyn = tblRequired.po_dyn_no LEFT OUTER JOIN
                          (SELECT     SUM(fault_qty) / 24 AS Qty, fault_po_no
                            FROM          tblStrRecvBGradeFault
                            WHERE      (fault_pft_id = 1)
                            GROUP BY fault_po_no) tblFault ON tblRequired.po_dyn_no = tblFault.fault_po_no
							ORDER BY tblRequired.po_dyn_no");
		}

		while($rstRow = odbc_fetch_array($nResultEmp))
		{
			$arrEff[$rstRow['knit_qualBy']]+=$nPersect=round(($rstRow['Qty']/$rstRow['RequiredQty']*100),2);
			$arrPO[$rstRow['knit_qualBy']]=$arrPO[$rstRow['knit_qualBy']]+1;
		}

		foreach($arrEff  as $empKey => $nPersent)
		{
			if($empKey<=0)
				continue;

			$nCountPO=$arrPO[$empKey];
			$arrCalculatedEff[$empKey]=round(($nPersent/$nCountPO),2);
		}
		 return $arrCalculatedEff;
	}
	/////////////////////////////////////////////// Quality Efficiency ////////////////////////////////////////////////////
	function CheckBox10($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<10; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_issue ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_validate ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[6] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_cancel ".$check[6]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[7] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_resume ".$check[7]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		if($arrayShow[8] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_print ".$check[8]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		if($arrayShow[9] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_string ".$check[9]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		echo "</tr>";
	}

	function CheckBox7WithPString($strLabel, $strName, $arrayChecked = array(0,0,0,0,0,0,0,0,0), $arrayShow = array(1,1,1,1,1,1,1,1,1) )
	{
		echo "<tr>
				<td>
					$strLabel
				</td>";
		for($i = 0; $i<9; $i++)
		{
			if( $arrayChecked[$i] ==1)
			{
				$check[$i] =  "CHECKED";

			}
			else
				$check[$i] =  "";

		}
		if($arrayShow[0]==1)
			echo "<td ><input type=checkbox name= ".$strName ."_view ".$check[0]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_cancel ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[4] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[4]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[5] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_resume ".$check[5]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";
		if($arrayShow[6] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_print ".$check[6]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		if($arrayShow[7] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_printstring ".$check[7]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		if($arrayShow[8] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_inspect ".$check[8]."></td> ";
		else
			echo "<td align=center><img src=/images/empty.gif>";

		echo "</tr>";
	}

	function TableComboQryWithOutTDs($strLabel, $strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MsSQLQuery($strQuery);
		if( $bIndexValue == true )
		{
			echo "<select name=$strName $callback ><br>";
			if($nAllUnDef == 0)
				echo "<option value=0000>ALL \r\n";
			if($nAllUnDef == 1)
				echo "<option value=0000>--------------- \r\n";

			while($rstRow = odbc_fetch_array($nResult))
			{
				$nID = $rstRow[$strIDField];

				if($nID == $nSelId)
					echo "<option value=$nID selected>"  . $rstRow[$strDispField] . "\r\n";
				else
					echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
			}
		}
		else
		{
			echo "<select name=$strName $callback ><br>";
			if($nAllUnDef == 0)
				echo "<option value=0>ALL \r\n";
			if($nAllUnDef == 1)
				echo "<option value=0>--------------- \r\n";

			while($rstRow = odbc_fetch_array($nResult))
			{
				$nID = $rstRow[$strDispField];

				if(trim($nID) == trim($nSelId))
					echo "<option value=$nID  selected >"  . $rstRow[$strDispField] . "\r\n";
				else
					echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
			}
		}
		echo "</select>";
	}

	function DateFieldWithOutTdTr($strLabel, $strField,  $strValue, $nSize, $nMaxLength, $strFormName, $bReadonly=false)
	{
		$strUnique = time();
		echo  "<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength readonly>";
				if(!$bReadonly)
				{
					echo  "	<a href=\"JavaScript: CalPop_".$strUnique."('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
					<script>
						function CalPop_".$strUnique."(sInputName)
						{
							window.open('/include/code/calender.php?strFieldName=' + escape(sInputName) , 'CalPop', 'toolbar=0,width=240,height=215');
						}
					</script>";
				}
	}
	function CheckBoxWithOutTDTR($strLabel, $strName, $nChecked = 0, $nCallBack = '')
	{
		if($nChecked == 1)
			echo "<input type=checkbox name=$strName CHECKED $nCallBack> $strLabel";
		else
			echo "<input type=checkbox name=$strName $nCallBack> $strLabel";
	}

	function Space($nTime)
	{
		$n=1;
		do
		{
			echo"&nbsp;";
			$n++;
		}
		while($n<$nTime);
	}

	function TableComboQuery($strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MsSQLQuery($strQuery);

		if( $bIndexValue == true )
		{

			echo "<select name=$strName $callback ><br>";
			if($nAllUnDef == 0)
				echo "<option value=0000>ALL \r\n";
			if($nAllUnDef == 1)
				echo "<option value=0000>--------------- \r\n";

			while($rstRow = odbc_fetch_array($nResult))
			{
				$nID = $rstRow[$strIDField];

				if($nID == $nSelId)
					echo "<option value=$nID selected>"  . $rstRow[$strDispField] . "\r\n";
				else
					echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
			}
		}
		else
		{
			echo "<select name=$strName $callback ><br>";
			if($nAllUnDef == 0)
				echo "<option value=0>ALL \r\n";
			if($nAllUnDef == 1)
				echo "<option value=0>--------------- \r\n";

			while($rstRow = odbc_fetch_array($nResult))
			{
				$nID = $rstRow[$strDispField];

				if(trim($nID) == trim($nSelId))
					echo "<option value=$nID  selected >"  . $rstRow[$strDispField] . "\r\n";
				else
					echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
			}
		}
		echo "</select>";
	}


	function get_date_from_datetime($datetime)
	{
		if($datetime == "1900-01-01 00:00:00.000")
			return;
		else
			return substr($datetime,0,10);
	}

	function encryptIt( $q ) {
	    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
	    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
	    return( $qEncoded );
	}

	function decryptIt( $q ) {
	    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
	    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
	    return( $qDecoded );
	}

	function is_user_logged_in()
	{
		if(!isset($_SESSION))
			session_start();
		if(isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] > 0)
			return true;
		else
			return false;
	}

	function current_user_type()
	{
		if(!isset($_SESSION))
			session_start();

		if(isset($_SESSION['USER_ROLE']) && $_SESSION['USER_ROLE'] != "")
			return $_SESSION['USER_ROLE'];

	}

	function getUniqueFilename($file)
    {
        if(is_array($file) and $file['name'] != '')
        {
            // getting file extension
            $fnarr          = explode(".", $file['name']);
            $file_extension = strtolower($fnarr[count($fnarr)-1]);

            // getting unique file name
            $file_name = substr(md5($file['name'].time()), 5, 15).".".$file_extension;
            return $file_name;

        } // ends for is_array check
        else
        {
            return '';

        } // else ends

    } // ends

    function makeThumbnailsWithGivenWidthHeight($updir, $img, $id, $thmbwidth, $thmbheight)
	{
	    $thumbnail_width = $thmbwidth;
	    $thumbnail_height = $thmbheight;
	    $thumb_beforeword = "thumb";
	    $arr_image_details = getimagesize("$updir" . $id . '.' . "$img"); // pass id to thumb name
	    $original_width = $arr_image_details[0];
	    $original_height = $arr_image_details[1];
	    if ($original_width > $original_height) {
	        $new_width = $thumbnail_width;
	        $new_height = intval($original_height * $new_width / $original_width);
	    } else {
	        $new_height = $thumbnail_height;
	        $new_width = intval($original_width * $new_height / $original_height);
	    }
	    $dest_x = intval(($thumbnail_width - $new_width) / 2);
	    $dest_y = intval(($thumbnail_height - $new_height) / 2);
	    if ($arr_image_details[2] == IMAGETYPE_GIF) {
	        $imgt = "ImageGIF";
	        $imgcreatefrom = "ImageCreateFromGIF";
	    }
	    if ($arr_image_details[2] == IMAGETYPE_JPEG) {
	        $imgt = "ImageJPEG";
	        $imgcreatefrom = "ImageCreateFromJPEG";
	    }
	    if ($arr_image_details[2] == IMAGETYPE_PNG) {
	        $imgt = "ImagePNG";
	        $imgcreatefrom = "ImageCreateFromPNG";
	    }
	    if ($imgt) {
	        $old_image = $imgcreatefrom("$updir" . $id . '.' . "$img");
	        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
	        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
	        $imgt($new_image, "$updir" . $id . '_' . "$thumb_beforeword" .".". "$img");
	    }
	}

   	function makeThumbnails($updir, $img, $id)
	{
	    $thumbnail_width = 199;
	    $thumbnail_height = 237;
	    $thumb_beforeword = "thumb";
	    $arr_image_details = getimagesize("$updir" . $id . '.' . "$img"); // pass id to thumb name
	    $original_width = $arr_image_details[0];
	    $original_height = $arr_image_details[1];
	    if ($original_width > $original_height) {
	        $new_width = $thumbnail_width;
	        $new_height = intval($original_height * $new_width / $original_width);
	    } else {
	        $new_height = $thumbnail_height;
	        $new_width = intval($original_width * $new_height / $original_height);
	    }
	    $dest_x = intval(($thumbnail_width - $new_width) / 2);
	    $dest_y = intval(($thumbnail_height - $new_height) / 2);
	    if ($arr_image_details[2] == IMAGETYPE_GIF) {
	        $imgt = "ImageGIF";
	        $imgcreatefrom = "ImageCreateFromGIF";
	    }
	    if ($arr_image_details[2] == IMAGETYPE_JPEG) {
	        $imgt = "ImageJPEG";
	        $imgcreatefrom = "ImageCreateFromJPEG";
	    }
	    if ($arr_image_details[2] == IMAGETYPE_PNG) {
	        $imgt = "ImagePNG";
	        $imgcreatefrom = "ImageCreateFromPNG";
	    }
	    if ($imgt) {
	        $old_image = $imgcreatefrom("$updir" . $id . '.' . "$img");
	        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
	        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
	        $imgt($new_image, "$updir" . $id . '_' . "$thumb_beforeword" .".". "$img");
	    }
	}

?>

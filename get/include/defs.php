<?php
	/*$DB_Server = "localhost";
	$DB_Username = "root";
	$DB_Password = "";
	$DB_DBName = "uob";*/
//	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
//	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	//error_reporting(E_NONE);

	// open a connection with MySQL server
	// display an error message if connection
	// was not properly openned
	function MySQLConnect()
	{
		$success= mysql_connect($GLOBALS["DB_Server"], $GLOBALS["DB_Username"], $GLOBALS["DB_Password"]);

		if (!$success)
			echo mysql_errno() . ": " . mysql_error() . "<BR>";

	}

	// send a query to MySQL server.
	// display an error message if there
	// was some error in the query
	foreach($_POST AS $key => $value) {
		${$key} = $value;
	}

	foreach($_GET AS $key => $value) {
		${$key} = $value;
	}

	foreach($_COOKIE AS $key => $value) {
		${$key} = $value;
	}

	function MySQLQuery($query)
	{

		$success= mysql_db_query($GLOBALS["DB_DBName"], $query);
		if(!$success)
		{
			echo mysql_errno().": ".mysql_error()."<BR>";
			//MySQLQuery("ROLLBACK");

			//echo "<hr>";
			//echo $query;
			//echo "<hr>";
		}
		return $success;
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
			$strQuery = "select count(*) as cnt from $strTable;";
		else
			$strQuery = "select count(*) as cnt from $strTable where $strCriteria;";

		$nResult = MySQLQuery($strQuery);
		$rstRow = mysql_fetch_array($nResult);

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
		$nResult = MySQLQuery($strQuery);

		$arrArray = array();

		while($rstRow = mysql_fetch_array($nResult))
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

		$strQuery .= " where $strWhere;";
		//echo"<br><br>".$strQuery;

		// execute query
		MySQLQuery($strQuery);
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
		$strQuery .= ");";
		// execute query
		MySQLQuery($strQuery);
		//if($strTable == "tblItemMaster")
		//{
		//	echo "<br><br>".$strQuery . "<br><br>";
		///}

		// return id of last insert record
		return mysql_insert_id();
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
			$strQuery .= "where $strCriteria;";

		//"<br>".$strQuery ;
		$nResult = MySQLQuery($strQuery);
		return mysql_fetch_array($nResult,MYSQL_ASSOC);
	}

	/*	the function deletes the record from the
		given table.

		strTable:		table name.
		strCriteria:	where criteria
	*/
	function DeleteRec($strTable, $strCriteria)
	{
		$strQuery = "delete from $strTable where $strCriteria";
		MySQLQuery($strQuery);
	}

	// the function displays the records from the given table
	// in an nicely formatted HTML table with edit and delete
	// icons along every record. It also displays next and
	// previous links to browse the entire table.
	//
	// strTable:		Table name to be shown.
	// strCriteria:		Expression for where cluase, if empty no where.
	//					is added to the query and all records from
	//					the table are selected.
	// strOrderBy:		Field names for order by clause.
	// strField:		Field name to be displayed
	// strScript:		Script name to be used for new, edit and delete
	// nRows:			Number of rows to be shown per page.
	// nStart:			Start offset of record.
	// strNewLink:		Extra parameters with new link.
	// strNewTarget:	link target on New link
	// strCallBack:		Function given in this argument is called at the end
	//					of each record.
	//
	function ShowTable($strTable, $strCriteria, $strOrderBy,
						$strField, $strScript, $nRows, $nStart, $strNewLink, $strNewTarget,
						$strCallBack = null, $strEditTarget = null)
	{
		$strColor1 = "#86A8EC";
		$strColor2 = "#D8D8D8";

		// if $arrAddlLinks is null
		if($arrAddlLinks == null)
			$arrAddlLinks = array();

		// if we are not passed any starting value
		if(empty($nStart))
			$nStart = 0;		// lets start from scratch

		$nNext = $nStart + $nRows;
		$nPrev = $nStart - $nRows;
		$nTotalRec = RecCount($strTable, $strCriteria);

		if(!empty($strNewTarget))

			$strNewTarget = "target=$strNewTarget";

		echo "<table width=100%>";
		echo "	<tr>";
		echo "		<td width=5%><a $strNewTarget href='new_$strScript?nStart=$nStart&$strNewLink'>New</a>&nbsp;&nbsp;</td>";

		$nShowingStart = $nStart+1;
		if($nStart+$nRows > $nTotalRec)
			$nShowingEnd = $nTotalRec;
		else
			$nShowingEnd = $nShowingStart + $nRows - 1;

		echo "<td align=right>";

		if($nTotalRec)
		{
			if ($nPrev > -1)
				echo "<a href='$PHP_SELF?nStart=$nPrev&$strNewLink'><! img src='/images/previous.gif' border=0></a>";
			else
				echo "<! img src='/images/previous.gif'>";

			echo "<! img src='horz_bubble.php?nStart=$nShowingStart&nEnd=$nShowingEnd&nTotal=$nTotalRec'>";

			if ($nNext < $nTotalRec)
				echo "<a href='$PHP_SELF?nStart=$nNext&$strNewLink'><! img src='/images/next.gif' border=0></a>";
			else
				echo "<! img src='/images/next.gif'>";
		}

		echo "</td>";

		// display all admins from tblAdmin
		if (empty($strCriteria))
			$strQuery = "select * from $strTable ";
		else
			$strQuery = "select * from $strTable where $strCriteria ";

		echo "	</td></tr>";
		echo "	<tr><td colspan=3><hr></td></tr>";
		echo "</table>";
		echo "<table width=100%>";

		if(!empty($strOrderBy))
			$strQuery .= " order by $strOrderBy ";

		$strQuery .= " limit $nStart, $nRows;";
		$nResult = MySQLQuery($strQuery);

		while($rstRow = mysql_fetch_array($nResult))
		{
			reset($arrAddlLinks);
			$nId = $rstRow[0];

			$strBGColor = " bgcolor=" . ($bColor ? $strColor1 : $strColor2);

			echo "<tr>";
			echo "	<td $strBGColor width=20 align=center valign=top>";
			echo "		<a $strEditTarget href='edit_$strScript?nId=$nId&nStart=$nStart'><img src='/images/icon_edit.gif' border=0 alt='Edit'></a>";
			echo "	</td>";
			echo "	<td $strBGColor width=20 align=center valign=top>";
			echo "		<a href='del_$strScript?nId=$nId&nStart=$nStart' onClick=\"return confirm('Are you sure you want to delete this?');\"><img src='/images/icon_delete.gif' border=0 alt='Delete'></a>";
			echo "	</td>";
			echo "	<td $strBGColor valign=top>";
			echo		$rstRow[$strField];
			echo "	</td>";

			if(!empty($strCallBack))
			{
				echo "	<td $strBGColor>";

				// callback function
				eval("echo $strCallBack(\$rstRow);");

				echo "\r\n</td>";
			}

			echo "</tr>";
			$bColor = !$bColor;
		}

		echo "</table>";
	}

	function ShowTable2($strTable, $strCriteria, $strOrderBy,
						$strField, $strScript, $nRows, $nStart, $strNewLink, $strNewTarget,
						$strCallBack = null, $strEditTarget = null)
	{
		$strColor1 = "#86A8EC";
		$strColor2 = "#D8D8D8";

		// if $arrAddlLinks is null
		if($arrAddlLinks == null)
			$arrAddlLinks = array();

		// if we are not passed any starting value
		if(empty($nStart))
			$nStart = 0;		// lets start from scratch

		$nNext = $nStart + $nRows;
		$nPrev = $nStart - $nRows;
		$nTotalRec = RecCount($strTable, $strCriteria);

		if(!empty($strNewTarget))

			$strNewTarget = "target=$strNewTarget";

		echo "<table width=100%>";
		echo "	<tr>";
		echo "		<td width=5%><a $strNewTarget href='new_$strScript?nStart=$nStart&$strNewLink'>New</a>&nbsp;&nbsp;</td>";

		$nShowingStart = $nStart+1;
		if($nStart+$nRows > $nTotalRec)
			$nShowingEnd = $nTotalRec;
		else
			$nShowingEnd = $nShowingStart + $nRows - 1;

		echo "<td align=right>";

		if($nTotalRec)
		{
			if ($nPrev > -1)
				echo "<a href='$PHP_SELF?nStart=$nPrev&$strNewLink'><! img src='/images/previous.gif' border=0></a>";
			else
				echo "<! img src='/images/previous.gif'>";

			echo "<! img src='horz_bubble.php?nStart=$nShowingStart&nEnd=$nShowingEnd&nTotal=$nTotalRec'>";

			if ($nNext < $nTotalRec)
				echo "<a href='$PHP_SELF?nStart=$nNext&$strNewLink'><! img src='/images/next.gif' border=0></a>";
			else
				echo "<! img src='/images/next.gif'>";
		}

		echo "</td>";

		// display all admins from tblAdmin
		if (empty($strCriteria))
			$strQuery = "select * from $strTable ";
		else
			$strQuery = "select * from $strTable where $strCriteria ";

		echo "	</td></tr>";
		echo "	<tr><td colspan=3><hr></td></tr>";
		echo "</table>";
		echo "<table width=100%>";

		if(!empty($strOrderBy))
			$strQuery .= " order by $strOrderBy ";

		$strQuery .= " limit $nStart, $nRows;";
		$nResult = MySQLQuery($strQuery);

		while($rstRow = mysql_fetch_array($nResult))
		{
			reset($arrAddlLinks);
			$nId = $rstRow[0];

			$strBGColor = " bgcolor=" . ($bColor ? $strColor1 : $strColor2);

			echo "<tr>";
			echo "	<td $strBGColor width=20 align=center valign=top>";
			echo "		<a $strEditTarget href='edit_$strScript?nId=$nId&nStart=$nStart'><img src='/images/icon_edit.gif' border=0 alt='Edit'></a>";
			echo "	</td>";
			echo "	<td $strBGColor valign=top>";
			echo		$rstRow[$strField];
			echo "	</td>";

			if(!empty($strCallBack))
			{
				echo "	<td $strBGColor>";

				// callback function
				eval("echo $strCallBack(\$rstRow);");

				echo "\r\n</td>";
			}

			echo "</tr>";
			$bColor = !$bColor;
		}

		echo "</table>";
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
	// strExtra				to write some thing extra like some onClick="alert('I'm Good')"
	//
	function TextField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $bPassword, $strExtra="")
	{
		echo "<tr>";
		echo "	<td nowrap>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		if($bPassword)
			echo "		<input type=password name=$strField value=\"$strValue\" size=$nSize maxlength=$nMaxLength $strExtra>";
		else
			echo "		<input type=text name=$strField value=\"$strValue\" size=$nSize maxlength=$nMaxLength $strExtra>";

		echo "	</td>";
		echo "</tr>";
	}

	function TextFieldWithOutTR($strField, $strValue, $nSize, $nMaxLength, $bPassword, $strExtra="")
	{
		echo "	<td>";
		if($bPassword)
			echo "<input type=password name=$strField value=\"$strValue\" size=$nSize maxlength=$nMaxLength $strExtra>";
		else
			echo "<input type=text name=$strField value=\"$strValue\" size=$nSize maxlength=$nMaxLength $strExtra>";

		echo "	</td>";
	}

	// the displays a Urdu text field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// strExtra				to write some thing extra like some onClick="alert('I'm Good')"
	//
	function UrduTextField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strExtra="")
	{
		/*echo "<script language='javascript1.2' type='text/javascript' src='UrduEditor.js'></script>";*/
		echo "<tr>";
		echo "	<td nowrap>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo "		<input type=text name=$strField id=$strField value='$strValue' size='$nSize' maxlength='$nMaxLength' onfocus=\"setEditor(this)\" $strExtra>";

		echo "	</td>";
		echo "</tr>";

		/*echo"  <script language='JavaScript' type='text/javascript'g>
					makeUrduEditor('$strField', '18');
				</script>";*/
	}

	// the displays a Urdu text field in HTML row with two columns in it.
	// left column contains label and right column contains the
	// text field.
	//
	// strLabel:			Label in left column.
	// strField:			Text field name in form.
	// strValue:			Value to be shown in text field.
	// nSize:				Size attribute of text field.
	// nMaxLength:			Max length attribute of text field.
	// strExtra				to write some thing extra like some onClick="alert('I'm Good')"
	//

	function ReadOnlyUrduTextField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strExtra="")
	{

		echo "<tr>";
		echo "	<td nowrap>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo "		<input type=text name=$strField id=$strField value='$strValue' size='$nSize' maxlength='$nMaxLength' onfocus=\"setEditor(this)\" $strExtra readonly>";

		echo "	</td>";
		echo "</tr>";
	}

	function ReadOnlyField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strExtra="")
	{
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo "		<input type=text name=$strField value=\"$strValue\" size=$nSize maxlength=$nMaxLength $strExtra READONLY>";
		echo "	</td>";
		echo "</tr>";
	}

	function ReadOnlyCell($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strExtra="")
	{
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td readonly>".$strValue."</td>";
		echo "</tr>";
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
	function DateField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName)
	{
		$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength readonly>
				<a href=\"JavaScript: CalPop_".$strUnique."('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function CalPop_".$strUnique."(sInputName)
				{
					window.open('/include/calender.php?strFieldName=' + escape(sInputName) , 'CalPop', 'toolbar=0,width=240,height=215');
				}
			</script>
			";

		echo "	</td>";
		echo "</tr>";
	}

	/*	the function displays OK and Cancel buttons in the form

	*/
	function OKCancelButtons()
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=submit name='OK' value='   OK   ' class='button blue fl-space'> <input type=submit name='Cancel' value='Cancel' class='button blue fl-space' onClick='history.go(-1); return false;'>";
		echo "	</td>";
		echo "</tr>";
	}

	// Simple OK Button
	function OKButton()
	{
		echo "	<td>";
				echo " <input type=submit name='OK' value='     OK     '>";
		echo "	</td>";
	}

	// Simple Submit Button
	function SubmitButton()
	{
		echo "	<td>";
				echo " <input type=submit name='OK' value='Submit'>";
		echo "	</td>";
	}



	/*	the function displays Verify ,Delete and Cancel  buttons in the form

	*/
	function UpdateVerifyDelButtons2($nStatus)
	{
		echo "<tr>";
		echo "	<td></td>";
		if($nStatus == 0)
		{
			echo "	<td>";
			echo "		<input type=submit name='Verify' value=' Update ' onclick=\"javascript:update();\">
						<input type=submit name='Verify' value='  Verify  ' onclick=\"javascript:verify();\">
						<input type=submit name='Delete' value=' Delete ' onclick=\"javascript:del();\">
						";
			echo "	</td>";
		}
		echo "</tr>";

		echo"<script language='javascript'>";
		echo"	function verify()
				{
					document.getElementById('strNewStatus').value='verify';
				}

				function del()
				{
					document.getElementById('strNewStatus').value='delete';
				}
				function update()
				{
					document.getElementById('strNewStatus').value='update';
				}
				";

		echo"</script>";
	}


	/*	the function displays Verify ,Delete and Cancel  buttons in the form

	*/
	function fnUpdateApproveDelButtons($nStatus)
	{
		echo "<tr>";
		echo "	<td></td>";
		if($nStatus == 0)
		{//<input type=submit name='Delete' value='  Delete  ' onclick=\"javascript:del();\">
			echo "	<td>";
			echo "		<input type=submit name='Verify' value=' Update ' onclick=\"javascript:update();\">
						<input type=submit name='Verify' value=' Approve ' onclick=\"javascript:approve();\">
						";
			echo "	</td>";
		}
		echo "</tr>";

		echo"<script language='javascript'>";
		echo"	function approve()
				{
					document.getElementById('strNewStatus').value='approve';
				}

				function del()
				{
					document.getElementById('strNewStatus').value='delete';
				}
				function update()
				{
					document.getElementById('strNewStatus').value='update';
				}
				";

		echo"</script>";
	}

	//BackButton
	function backButton($strExtra, $strAlign="")
	{
		if($strAlign!="")
		{
			echo "<tr>";
			echo "	<td colspan=2 align='$strAlign'>";
			echo"		<input type='Button' value='  Back  ' $strExtra>";
			echo "	</td>";
			echo "</tr>";
		}
		else
		{
			echo "<tr>";
			echo "	<td>";
			echo"		<input type='Button' value='  Back  ' $strExtra>";
			echo "	</td>";
			echo "	<td>";
			echo "	</td>";
			echo "</tr>";
		}
	}


	/*	the function displays Any Button with Cancel buttons in the form
		bBtntoDisplay								 true if one button is required [by default it is false]

		set first parameter if only one button is required

		first button is submit
		2nd button is of type button

	*/
	function TwoButtons($btn1 , $btn2 ,$bBtntoDisplay="false",  $strExtra="")
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		if($strExtra !="")
		{

			echo "<input type=submit name='$btn1' value='$btn1'>";
			if($bBtntoDisplay == "false")
			{
				echo "<input type=button  name='$btn2' value='$btn2' $strExtra>";
			}
		}
		else
		{
			echo "<input type=submit name='$btn1' value='$btn1'>";
			if($bBtntoDisplay == "false")
			{
				echo "<input type=button  name='$btn2' value='$btn2'>";
			}
		}
		echo "	</td>";
		echo "</tr>";
	}
	function TwoButtons1($btn1 , $btn2 ,$bBtntoDisplay="false",  $strExtra="")
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		if($strExtra !="")
		{

			echo "<input type=submit name='$btn1' value='$btn1'>";
			if($bBtntoDisplay == "false")
			{
				echo "<input type=button  name='$btn2' value='$btn2' $strExtra>";
			}
		}
		else
		{
			echo "<input type=submit name='$btn1' value='$btn1'>";
			if($bBtntoDisplay == "false")
			{
				echo "<input type=button  name='$btn2' value='$btn2'>";
			}
		}
		//echo "	</td>";
		//echo "</tr>";
	}

	/*	the function displays Any Button with Cancel buttons in the form

	*/
	function TwoButtons2($btn1 , $btn2 , $strExtra="",$nColSpan="")
	{
		echo "<tr>";
		echo "	<td colspan=$nColSpan align='center'>";
		if($strExtra !="")
		{
			echo "<input type=submit name='$btn1' value='$btn1'> <input type=submit  name='$btn2' value='$btn2' $strExtra>";
		}
		else
		{
			echo "<input type=submit name='$btn1' value='$btn1'> <input type=submit  name='$btn2' value='$btn2'>";
		}
		echo "	</td>";
		echo "</tr>";
	}

	/*	the function displays Finish and Cancel buttons in the form

	*/
	function FinishCancelButtons()
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=submit name='Finish' value=' Finish '> <input type=submit  name='Cancel' value='Cancel' onClick='history.go(-1); return false;'>";
		echo "	</td>";
		echo "</tr>";
	}



	/*	the function displays OK and Close buttons in the form

	*/
	function OKCloseButtons($okExtraField="",$colseExtraField="")
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		if($okExtraField!="")
		{
			echo "		<input type=submit name='OK' value='   OK   ' $okExtraField>";
		}
		else
		{
			echo "		<input type=submit name='OK' value='   OK   '>";
		}
		if($colseExtraField!="")
		{
			echo"		<input type=submit  name='Close' value=' Close ' onClick='window.close();return true;' $colseExtraField>";
		}
		else
		{
			echo"		<input type=submit  name='Close' value=' Close ' onClick='window.close();return true;'>";
		}
		echo "	</td>";
		echo "</tr>";
	}



	/*	the function displays Cancel buttons in the form

	*/
	function CancelButton()
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=submit  name='Cancel' value='Cancel' onClick='history.go(-1); return false;'>";
		echo "	</td>";
		echo "</tr>";
	}


	/*	the function displays OK and Cancel and Reset buttons in the form

	*/
	function OKCancelResetButtons()
	{
		echo "<tr>";
		echo "	<td></td>";
		echo "	<td>";
		echo "		<input type=submit name='OK' value='   OK   '> <input type=submit  name='Cancel' value='Cancel' onClick='history.go(-1); return false;'> <input type=submit name='OK' value=' Reset ' onClick='window.location.reload()'>";
		echo "	</td>";
		echo "</tr>";
	}



	/*	the function displays Verify ,Delete and Cancel  buttons in the form
		if nBtn =0  Three Buttons will display
		if nBtn =1  Two Buttons will display
		if nBtn =2  One Button will display


	*/
	function UpdateDelButtons($nStatus)
	{
		echo "<tr>";
		echo "	<td></td>";
		if($nStatus == 0)
		{
			echo "	<td>";
			echo "		<input type=submit name='Update' value=' Update ' onclick=\"javascript:update();\">
						<input type=submit name='Delete' value=' Delete ' onclick=\"javascript:del();\">
						";
			echo "	</td>";
		}
		echo "</tr>";

		echo"
		<script language='javascript'>;
			function del()
			{
				document.getElementById('strNewStatus').value='delete';
			}
			function update()
			{
				document.getElementById('strNewStatus').value='update';
			}
		</script>";
	}




	/*	the function displays Verify ,Delete and Cancel  buttons in the form
		if nBtn =0  Three Buttons will display
		if nBtn =1  Two Buttons will display
		if nBtn =2  One Button will display


	*/
	function UpdateVerifyDelButtons($nStatus)
	{
		echo "<tr>";
		echo "	<td></td>";
		if($nStatus == 0)
		{
			echo "	<td>";
			echo "		<input type=submit name='Verify' value=' Update ' onclick=\"javascript:update();\">
						<input type=submit name='Verify' value='  Close  ' onclick=\"javascript:verify();\">
						<input type=submit name='Delete' value=' Delete ' onclick=\"javascript:del();\">
						";
			echo "	</td>";
		}
		echo "</tr>";

		echo"<script language='javascript'>";
		echo"	function verify()
				{
					document.getElementById('strNewStatus').value='close';
					//document.getElementById('myForm').submit='true';
				}

				function del()
				{
					document.getElementById('strNewStatus').value='delete';
					//document.getElementById('myForm').submit='true';
				}
				function update()
				{
					document.getElementById('strNewStatus').value='update';
					//document.getElementById('myForm').submit='true';
				}
				";

		echo"</script>";
	}


	function CloseDelButtons($nStatus)
	{
		echo "<tr>";
		//echo "	<td></td>";
		if($nStatus == 0)
		{
			echo "	<td colspan=7 align='center'>";
			echo "		<input type=submit name='Close' value='  Close  '  onclick='closed();'>
						<input type=submit name='Delete' value='  Delete  '  onclick='del();'>
						";
			echo "	</td>";
		}
		echo "</tr>";

		echo"<script language='javascript'>";
		echo"	function closed()
				{
					document.getElementById('strNewStatus').value='close';
				}

				function del()
				{
					document.getElementById('strNewStatus').value='delete';
				}
				";

		echo"</script>";
	}


	/*	the function displays close ,in process and del  buttons in the form
		if nStatus =0  Three Buttons will display



	*/
	function updateInProcessDelBtn($nStatus)
	{

		if($nStatus == 0)
		{
			echo "<tr>";
			echo "	<td></td>";
			echo "	<td>";
			echo "		<input type=submit name='Update' value=' Update ' onclick=\"javascript:update();\">
						<input type=submit name='InProcess' value='In Process' onclick=\"javascript:inProcess();\">
						<input type=submit name='Delete' value='  Delete  ' onclick=\"javascript:del();\">
						";
			echo "	</td>";
		}
		else
		{
			echo "<tr>";
			echo "	<td colspan=2 align='left'>";
			echo "		<input type=submit name='Update' value=' Update ' onclick=\"javascript:update();\">
						";
			echo "	</td>";
		}

		echo "</tr>";
		echo"<script language='javascript'>";
		echo"	function update()
				{
					document.getElementById('strNewStatus').value='update';
				}

				function del()
				{
					document.getElementById('strNewStatus').value='delete';
				}
				function inProcess()
				{
					document.getElementById('strNewStatus').value='inprocess';
				}
				";

		echo"</script>";
	}

	/*	the function displays Verify ,Delete and Cancel  buttons in the form
		if nBtn =0  Three Buttons will display
		if nBtn =1  Two Buttons will display
		if nBtn =2  One Button will display


	*/
	function PurInvUpdateVerifyDelButtons($nStatus)
	{
		echo "<tr>";
		echo "	<td></td>";
		if($nStatus == 0 || $nStatus == 2)
		{
			echo "	<td>";
			echo "		<input type=submit name='Verify' value=' Update ' onclick=\"javascript:update();\">
						<input type=submit name='Verify' value='  Close  ' onclick=\"javascript:verify();\">
						<input type=submit name='Delete' value=' Delete ' onclick=\"javascript:del();\">
						";
			echo "	</td>";
		}
		else if($nStatus == 1)
		{
			echo "	<td>";
			echo "		<input type=submit name='Delete' value=' Delete ' onclick=\"javascript:del();\">
						";
			echo "	</td>";
		}

		echo "</tr>";
		echo"<script language='javascript'>";
		echo"	function verify()
				{
					document.getElementById('strNewStatus').value='close';
					//document.getElementById('myForm').submit='true';
				}

				function del()
				{
					document.getElementById('strNewStatus').value='delete';
					//document.getElementById('myForm').submit='true';
				}
				function update()
				{
					document.getElementById('strNewStatus').value='update';
					//document.getElementById('myForm').submit='true';
				}
				";

		echo"</script>";
	}


	/*	the function creates an hidden field

		strName:		name of hidden field
		strValue:		value to be passed in hidden field
	*/
	function HiddenField($strName, $strValue)
	{
		echo "<input type=hidden name='$strName' value='$strValue'>\r\n";
	}

	/*	the function creates a text area

		strLabel:			Label in left column.
		strField:			Text field name in form.
		strValue:			Value to be shown in text field.
		nRows:				number of rows in text area
		nCols:				number of columsn in text area
	*/
	function TextArea($strLabel, $strField, $strValue, $nRows, $nCols)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
		echo "		<textarea name=$strField rows='$nRows' cols='$nCols'>$strValue</textarea>";
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
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo "<input type=file name='$strFileName'>";

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
		sizeof($arr);
		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;

			if($j == $nSelectedVal)
			{
				if($bIndexValue == true)
				{
					echo "<option value=$j selected>" . $arr[$i] . "\r\n";

				}
				else
				{
					echo "<option selected>" . $arr[$i] . "\r\n";
				}
			}
			else
			{
				if($bIndexValue == true)
				{
					echo "<option value=$j>" . $arr[$i] . "\r\n";
				}
				else
				{
					echo "<option>" . $arr[$i] . "\r\n";
				}
			}
		}
	}



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

		$strTemp = $strField . "Date";
		echo "<select name=$strTemp>";
		ComboBox($strDy-1, $arrDay, true);
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
		$nHr = strtok($strTime, ":");
		$nMn = strtok(":");

		$arrHr = array();
		for($i = 0; $i <= 23; $i++)
			array_push($arrHr, $i);

		$strTemp = $strField . "Mn";
		echo "<select name=$strTemp>";
		ComboBox($strDy-1, $arrHr, true);
		echo "</select>";


		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";
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
	*/
	function TableCombo($strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId, $width)
	{

		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
		 	$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";

		$nResult = MySQLQuery($strQuery);

		echo "<select name=$strName style='width:$width' ><br>";

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nID = $rstRow[$strIDField];

			if($nID == $nSelId)
				echo "<option value=$nID selected>" . $rstRow[$strDispField] . "\r\n";
			else
				echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
		}

	}
	function TableCombo1($strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId ,$strCallBack)
	{

		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
		 	$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";

		$nResult = MySQLQuery($strQuery);

		echo "<select name=$strName ><br>";

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nID = $rstRow[$strIDField];

			if($nID == $nSelId)
				echo "<option value=$nID selected>" . $rstRow[$strDispField] . "\r\n";
			else
				echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
		}
		echo "<script>
		 function abc()
			{
			alert('Fill Customer Guranters');
			}
			</script>";

	}
	function TableComboReadOnly($strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId)
	{

		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
		 	$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";

		$nResult = MySQLQuery($strQuery);

		echo "<select name=$strName disabled><br>";

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nID = $rstRow[$strIDField];

			if($nID == $nSelId)
				echo "<option value=$nID selected>" . $rstRow[$strDispField] . "\r\n";
			else
				echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
		}


	}

	/*
		function displays combo box of Cost Center
		strName : Name of HTML object
		nSelId : Id of selected cost center. Default value is -1
		blnAll : if 1 then any value can be selected, if 0 then only lowest level values can be selected
	*/
	function CostCenter($strName, $nSelId = -1, $blnAll=1)
	{
		$strUnique = mktime();

		$strCCStruct = getValOfTable("tblConfiguration", "conf_value", "conf_key = 'GL_CC_STRUCTURE'");
		$nLen = strlen($strCCStruct);

		$strQuery = "select * from tblCostCenter order by cc_code";
		$nResult = MySQLQuery($strQuery);

		if($blnAll)
			echo "<select name='$strName'>\n\r";
		else
			echo "<select name='$strName' onChange=\"func_check_$strUnique(this)\">\n\r";

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nStart = 0;
			$strCode = $rstRow["cc_code"];
			$strName = $rstRow["cc_name"];
			$nId = $rstRow["cc_id"];

			if(substr($strCode, 1, 2) != str_pad("", 2, "0"))
					$strName = "&nbsp;&nbsp;&nbsp;&nbsp;" . $strName;
			if(substr($strCode, 4, 2) != str_pad("", 2, "0"))
					$strName = "&nbsp;&nbsp;&nbsp;&nbsp;" . $strName;

			if(!$blnAll)
			{
				$nChk = substr($strCode, strlen($strCode)-1, strlen($strCode));
				$nChk = doubleval($nChk);
				if($nChk == 0)
					$nId = 0;
			}

			if($nSelId == $strCode)
				echo "<option value='$strCode' SELECTED>". $strName . "\n\r";
			else
				echo "<option value='$strCode'>". $strName . "\n\r" ;
		}

		echo "</select>\n\r";

		if(!$blnAll)
		{
			echo "
			<script>
				function func_check_$strUnique(obj)
				{
					if(obj.value.substring(4, 6) == '00')
					{
						alert('Please select lowest level value');
						obj.value = '';
					}
				}
			</script>
				";
		}
	}

	//Fction Displays the ost centers with codes
	function CostCenterWithCode($strName, $nSelId = -1, $blnAll=1)
	{
		$strUnique = mktime();

		$strCCStruct = getValOfTable("tblConfiguration", "conf_value", "conf_key = 'GL_CC_STRUCTURE'");
		$nLen = strlen($strCCStruct);

		$strQuery = "select * from tblCostCenter order by cc_code";
		$nResult = MySQLQuery($strQuery);

		if($blnAll)
			echo "<select name='$strName'>\n\r";
		else
			echo "<select name='$strName' onChange=\"func_check_$strUnique(this)\">\n\r";

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nStart = 0;
			$strCode = $rstRow["cc_code"];
			$strName = $rstRow["cc_name"];
			$nId = $rstRow["cc_id"];

			if(substr($strCode, 1, 2) != str_pad("", 2, "0"))
					$strName = "&nbsp;&nbsp;&nbsp;&nbsp;" . $strName;
			if(substr($strCode, 4, 2) != str_pad("", 2, "0"))
					$strName = "&nbsp;&nbsp;&nbsp;&nbsp;" . $strName;

			if(!$blnAll)
			{
				$nChk = substr($strCode, strlen($strCode)-1, strlen($strCode));
				$nChk = doubleval($nChk);
				if($nChk == 0)
					$nId = 0;
			}

			if($nSelId == $strCode)
				echo "<option value='$strCode' SELECTED>". $strName."#".$strCode . "\n\r";
			else
				echo "<option value='$strCode'>". $strName."#".$strCode . "\n\r" ;
		}

		echo "</select>\n\r";

		if(!$blnAll)
		{
			echo "
			<script>
				function func_check_$strUnique(obj)
				{
					if(obj.value.substring(4, 6) == '00')
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
		the function creates radio buttons group

		strLabel:		lable to be shown in the right cell
		arrButtons:		the lables to be shown along radio buttons
		strName:		form name for the button group
		nSelIndex:		index of selected button
		jFun:			Javascript Fuctions;
		nJFunId:		Id with which this fuc should be attached
	*/
	function RadioButtons($strLabel, $arrButtons, $strName, $nSelIndex = -1 ,$jFun="", $nJFunId="")
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		for($i=0; $i<sizeof($arrButtons); $i++)
			if($i == $nSelIndex)
			{
				if($jFun !="" && $nJFunId == $i)
				{
					echo "<input type=radio value=$i name=$strName checked $jFun>" . $arrButtons[$i] . "<br>";
				}
				else
				{
					echo "<input type=radio value=$i name=$strName checked>" . $arrButtons[$i] . "<br>";
				}
			}
			else
			{
				if($jFun !="" && $nJFunId == $i)
				{
					echo "<input type=radio value=$i name=$strName $jFun>" . $arrButtons[$i] . "<br>";
				}
				else
				{
					echo "<input type=radio value=$i name=$strName>" . $arrButtons[$i] . "<br>";
				}
			}

		echo "	</td>";
		echo "</tr>";
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
	function drawLookUpTable($strQuery, $strIdField, $strTitleField, $nWidth = "100%", $strCallBack = null)
	{
		global $strTitleFieldName, $strIdFieldName, $nId;
		$nResult = MySQLQuery($strQuery);

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
					};

					td {
						font-family : arial;
						color: black;
						font-size :  9pt;
						font-style :  normal;
						font-weight : none;
					}
				</style>
				<body>
			";

		echo "<table width=$nWidth>
		<tr>
			<td>
				<form name='frmSearch' action='coa-code_edu_lookup.php' method='post'>
				<table width=70% align='center'>
						<td></td>
						<td>
							<input type='hidden' name='strTitleFieldName' value='$strTitleFieldName'>
							<input type='hidden' name='strIdFieldName' value='$strIdFieldName'>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
		<tr>
			<td bgcolor=silver>
				<table cellspacing=1 border=0 cellpadding=3 width=$nWidth>
					<tr bgcolor #CCCCCC height=25>
						<td><b>Code</b></td>
						<td><b>Product</b></td>
						<td><b>Categories</b></td>
						<td><b>Color</b></td>
						<td><b>Stamp</b></td>
					</tr>";

		$nI = 0;
		while($nRow = mysql_fetch_array($nResult))
		{
			$nI++;
			$nID = $nRow[$strIdField];
			$strTitle = $nRow[$strTitleField];
			$strColor    = $nRow['itm_color'];
			$strStamp    = $nRow['itm_stamp'];
			$strCatagoryid = $nRow['itm_subcat_id'];
			$rstCAT	= GetRecord("tblItemSubCategory", "subcat_id  = $strCatagoryid");
			$strCatagory = $rstCAT['subcat_name'];

			if(empty($strCallBack))
			{
				if($nID == $nId)
					echo "<tr bgcolor=lightblue>
								<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\">$nID</a></td>
								<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\">$strTitle</a></td>
								<td>$strCatagory</td>
								<td>$strColor</td>
								<td>$strStamp</td>
							</tr>";
				else
					echo "<tr bgcolor=ffffff>
								<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\" class=NAV>$nID</a></td>
								<td><a href=\"JavaScript: updateParent('$nID', '$strTitle')\" class=NAV>$strTitle</a></td>
								<td>$strCatagory</td>
								<td>$strColor</td>
								<td>$strStamp</td>
						  </tr>";
			}
			else
			{
				$strScriptFunc = "updateParent";
				eval("echo $strCallBack(\$nRow, \$nId, \$strIdField, \$strTitleField, \$strScriptFunc , \$strCatagory , \$strColor , \$strStamp);");
			}
		}
		echo "</table></td></tr></table>\r\n";

		echo "
		<script>
			function updateParent(nId, strTitle)
			{

				if(document.frmSearch.strTitleFieldName.value!='')
					window.opener.eval(document.frmSearch.strTitleFieldName.value).value = strTitle;
				window.opener.eval(document.frmSearch.strIdFieldName.value).value = nId;
				//window.opener.eval('document.myForm.myGrid_Item_Unit1').value = nId;
				window.close();
			}
		</script>
		</body>
		</html>
		";

	}

// Function to draw HTML table within lookup POPUP
	//
	// strQuery				Source Query
	// strIdField			Title of ID field in DB
	// strTitleField		Title of TITLE field in DB
	//
	function drawLookUpTable2($strQuery,$strIdField, $strTitleField,$strCatField,
		$nWidth = "100%", $strCallBack = null)
	{
		/*echo"<script language='javascript'>alert('hi');</script>";*/

		global $strTitleFieldName, $strIdFieldName, $nId;
		//echo "***".$strTitleFieldName."**". $strIdFieldName."**";

		$nResult = MySQLQuery($strQuery);

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

		echo "<table width=$nWidth>
		<tr>
			<td>
				<form name='frmSearch' action='coa-code_edu_lookup.php' method='post'>
				<table width=70% align='center'>


						<td></td>
						<td>

							<input type='hidden' name='strTitleFieldName' value='$strTitleFieldName'>
							<input type='hidden' name='strIdFieldName' value='$strIdFieldName'>
							<input type='hidden' name='strSubcatFieldName' value='$strSubcatFieldName'>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
		<tr>
			<td bgcolor=silver>
				<table cellspacing=1 border=0 cellpadding=3 width=$nWidth>\r\n";
		$nI = 0;
		while($nRow = mysql_fetch_array($nResult))
		{
			$nI++;
			$nID = $nRow[$strIdField];
			$strTitle = $nRow[$strTitleField];
			$strCat = $nRow[$strCatField];

			if(empty($strCallBack))
			{

				if($nID == $nId)
				{
					echo "
						<tr bgcolor=lightblue>
							<td><a href=\"JavaScript: updateParent('$nID', '$strTitle', '$strCat')\">$nID</a></td>
							<td><a href=\"JavaScript: updateParent('$nID', '$strTitle', '$strCat')\">$strTitle</a></td>
							<td><a href=\"JavaScript: updateParent('$nID', '$strTitle', '$strCat')\">$strCat</a></td>
						</tr>
					";
				}
				else
				{
					echo "
					<tr bgcolor=ffffff>
						<td><a href=\"JavaScript: updateParent('$nID', '$strTitle', '$strCat')\" class=NAV>$nID</a></td>
						<td><a href=\"JavaScript: updateParent('$nID', '$strTitle', '$strCat')\" class=NAV>$strTitle</a></td>
						<td><a href=\"JavaScript: updateParent('$nID', '$strTitle', '$strCat')\" class=NAV>$strCat</a></td>
					</tr>
					";
				}
			}
			else
			{
				$strScriptFunc = "updateParent";
				eval("echo $strCallBack(\$nRow, \$nId, \$strIdField, \$strTitleField, \$strCatField, \$strScriptFunc);");
			}
		}
		echo "</table></td></tr></table>\r\n";

		echo "
		<script>
			function updateParent(nId, strTitle ,strCust)
			{

				if(document.frmSearch.strTitleFieldName.value!='')
					window.opener.eval(document.frmSearch.strTitleFieldName.value).value = strCust +'-'+strTitle;
					window.opener.eval(document.frmSearch.strIdFieldName.value).value = nId;
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
		{
		 	$strQuery  = "SELECT $strField AS nCnt FROM $strTableName WHERE $strWhere";
			 //echo"<br><br>".$strQuery;
		}
		else
		{
			$strQuery  = "SELECT $strField AS nCnt FROM $strTableName";
		}
		$nResult = MySQLQuery($strQuery);
		$rstRow = mysql_fetch_array($nResult);
		return $rstRow["nCnt"];
	}


	// Function to create combo based on tblConfiguration
	//
	// strProperty				Key value to make comob of
	// strTableName				2nd Table name
	// strMatchField			Field name to match with conf_value
	// strMatchValue			Inpout match value
	// strTitleField			Field name to display in comobo
	// strObjName				Name of comobo
	// strLabel					Label to dispay for combo
	//
	function ConfigCombo($strProperty, $strTableName, $strMatchField, $strMatchValue, $strTitleField, $strObjName, $strLabel)
	{

		echo "<tr>\r\n";
		echo "	<td>\r\n";
		echo "		$strLabel\r\n";
		echo "	</td>\r\n";
		echo "	<td>\r\n";
		echo "		<SELECT name='$strObjName'>\r\n";


		$strQuery = "
						SELECT
							*
						FROM
							tblConfiguration,
							$strTableName
						WHERE
							conf_value = $strMatchField AND
							conf_key = '$strProperty'
					";
		$nResult = MySQLQuery($strQuery);
		while($nRow = mysql_fetch_array($nResult))
		{
			if($strMatchValue == $nRow[$strMatchField])
				echo "<option value='". $nRow[$strMatchField] ."' SELECTED>".$nRow[$strTitleField]."</option>\r\n";
			else
				echo "<option value='". $nRow[$strMatchField] ."'>".$nRow[$strTitleField]."</option>\r\n";
		}


		echo "		</SELECT>\r\n";
		echo "	</td>\r\n";
		echo "</tr>\r\n";
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


	function COAPickup($strLabel, $strField ,$nSize, $nMaxLength, $strFormName,$strValue)
	{
		$strUnique = time();

		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength readonly>
				<a href=\"JavaScript: COAPickup_".$strUnique."('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function COAPickup_".$strUnique."(strIdField)
				{
					window.open('/include/code/comp/coa_pickup_level3.php?strIdFieldName=' + escape(strIdField), 'CoaPickUp', 'scrollbars=yes,toolbar=0,width=500,height=500');
				}
			</script>
			";

		echo "	</td>";
		echo "</tr>";
	}

	function COAPickupLevel2($strLabel, $strField ,$nSize, $nMaxLength, $strFormName,$strValue)
	{
		$strUnique = time();

		echo "<tr>";
		echo "	<td>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strField' value='$strValue' size=$nSize maxlength=$nMaxLength readonly>
				<a href=\"JavaScript: COAPickup_".$strUnique."('document.$strFormName.$strField');\"><img src='/images/ico-cal.gif' border=0></a>
			<script>
				function COAPickup_".$strUnique."(strIdField)
				{
					window.open('/gl/coa-code_edu_lookup.php?strIdFieldName=' + escape(strIdField), 'CoaPickUp', 'scrollbars=yes,toolbar=0,width=500,height=500');
				}
			</script>
			";

		echo "	</td>";
		echo "</tr>";
	}

	function getCostCenterOfStudent($nStuId, $strField)
	{
		$strQuery = "
				SELECT D.* FROM
					tblStudent A,
					tblSession B,
					tblProgram C,
					tblCostCenter D
				WHERE
					A.stu_ses_id = B.ses_id AND
					B.ses_prg_id = C.prg_id AND
					C.prg_cc_id = D.cc_id AND
					A.stu_id = $nStuId
			";

		$nResult = MySQLQuery($strQuery);
		$nRow = mysql_fetch_array($nResult);
		return $nRow[$strField];
	}


	/*

		the method returns the opening balance of the account\
		given by $nCOACode at the date $strDate.
	*/
	function GetOpeningBalance($nCOACode, $strDate)
	{

		$dt=explode("-",$strDate);

		// open opening balance
		$rstCOA = GetRecord("tblCOA", "coa_code = '$nCOACode'");


		if ($dt[1]<7){
			$compdate=($dt[0]-1)."-7-1";
			$strQuery = "select
							sum(vcd_credit) as cr,
							sum(vcd_debit) as dr
						from
							tblVoucherDetail, tblVoucherMaster
						where
							vcd_coa_code = '$nCOACode' and
							vcd_vch_id = vch_id and
							vch_date between '".$year."-7-1' and '$strDate'";
		}
		else
		{
			$strQuery = "select
							sum(vcd_credit) as cr,
							sum(vcd_debit) as dr
						from
							tblVoucherDetail, tblVoucherMaster
						where
							vcd_coa_code = '$nCOACode' and
							vcd_vch_id = vch_id and
							vch_date between '".$dt[0]."-7-1' and '$strDate'";
		}

		$nResult = MySQLQuery($strQuery);
		$rstRow = mysql_fetch_array($nResult);

		return $rstCOA['coa_opening'] + $rstRow['dr'] - $rstRow['cr'];

	}

	function getBalance($date,$strCOACode)
	{
		$temp = MySQLQuery("select  sum(vcd_debit) as debit, sum(vcd_credit)  as credit
					from tblVoucherMaster, tblVoucherDetail
					where  vcd_coa_code = '$strCOACode' and vch_posted = 'Y' and vcd_vch_id = vch_id
					and vch_date < '$date' ");
		$rstRow = mysql_fetch_array($temp);
		$debit =  $rstRow['debit'];
		$crebit =  $rstRow['credit'];
		$balance =  $debit - $credit;
		return $balance;



	}



/*******************************************************************************************************************************
	Function 													;displayAddress
	This function displays the address of companay in pdf file
	Returns nothing

	@param $doc													;Object of class Cezpdf


********************************************************************************************************************************/
	function displayAddress($doc)
	{
		$confResult = MySQLQuery("Select * from tblConfiguration where conf_key like '%COMP_ADD%'");
		while($confRows = mysql_fetch_array($confResult))
		{
			$doc->ezText($confRows['conf_value'] , 12, array("justification" => 'right'));
		}
		$doc->ezText("", 12, array("justification" => 'right'));
		$doc->ezText("", 12, array("justification" => 'right'));
		$doc->ezSetDy(-20);
	}

	function getConfKey($strkey)
	{
		$strQuery	= "Select * from tblConfiguration where conf_key = '$strkey'";
		$confResult = MySQLQuery($strQuery);
		$rstRow		= mysql_fetch_array($confResult);
		return $rstRow['conf_value'];
	}

/*******************************************************************************************************************************
	Function 													;showAsCrDr
	//Return a number in brackets
	//if number is less than zero it will be shown as credit with brackets around it other wise it return the same no.
	//For display purpose in pdf

	@param $number												;Number which need to be shown as cr or dr


********************************************************************************************************************************/

	function showAsCrDr($number)
	{
		if($number < 0)
		{
			$number = trim($number,"-");
			$number = "(".$number.")";
		}

		return $number;
	}





/*******************************************************************************************************************************
	Function 													;changeDateFormat

	This function accept the date of db in format[yy/mm/dd] and it converts  that date into the format [dd/mm/yy]
	Returns the Date yy/mm/dd into dd/mm/yy

	@param $date												;string of date in 	[yy/mm/dd] format

********************************************************************************************************************************/
	function changeDateFormat($date)
	{
		list($year,$month,$day)	=	explode("-",$date);
		$date	= $day."-".$month."-".$year;

		return $date;
	}

/*******************************************************************************************************************************
	Function 													;changeDateInDBFormat

	This function accept the date in format[dd/mm/yy] and it converts  that date into db format [yy/mm/dd]
	Returns the Date dd/mm/yy into yy/mm/dd

	@param $date												;string of date in 	[dd/mm/yy] format

********************************************************************************************************************************/
	function changeDateInDBFormat($date)
	{
		list($day,$month,$year)	=	explode("-",$date);
		$date	= $year."-".$month."-".$day;

		return $date;
	}




/*******************************************************************************************************************************
	Function 													; getPostedFieldVal
	////Returns the Posted Form Field Value

	@param $strString											;The name of the Form Field


********************************************************************************************************************************/

//Returns the Posted Form Field Value
	function getPostedFieldVal($strString)
	{
		global $$strString;
		$strVar = $$strString;

		return $strVar;
	}

/******************************************************************************************************************************
	Funciton											;getOld

	;This funciton calculate the Debit Sum and Credit Sum and also calculate the balance BUT it calculates the SUM
	;and balance of Dates less than FROM DATE

	;Returns Debit Sum ,Credit Sum, Opening, And Also Balance in an associated array

	;@param coa_code									;This is a coa code
	;@param coa_group									;This is a group no
	;@param strFrom										;This is FROM DATE


******************************************************************************************************************************/


	/*Returns the debit and Credit sum, Opening and Total balance in array*/
	function getOld($coa_code, $coa_group,$strFrom)
	{

		$codeDigit = getCodeDigit($coa_code, $coa_group);


		$resSum = MySQLQuery("select Count(*) as Countr ,SUM(tblVoucherDetail.vcd_debit) as dr , SUM(tblVoucherDetail.vcd_credit) as cr
								from tblVoucherDetail, tblVoucherMaster
								where tblVoucherMaster.vch_id = tblVoucherDetail.vcd_vch_id
								and tblVoucherDetail.vcd_coa_code like '$codeDigit%'
								and  tblVoucherMaster.vch_posted = 'Y'
								and tblVoucherMaster.vch_date <= '$strFrom'"
						);



		$resSum =mysql_fetch_array($resSum);
		if  ($resSum['Countr'] == 0)
		{
			$sum['debit'] = 0;
			$sum['credit'] = 0;
		}
		elseif ($resSum['Countr'] > 0)
		{
			$sum['debit'] = $resSum['dr'];
			$sum['credit'] = $resSum['cr'];
		}

		$resOpening=	MySQLQuery("Select Count(*) as countrb,Sum(coa_opening) as opening
									from tblCOA
									Where  coa_code like '$codeDigit%'");


		$openingRows=mysql_fetch_array($resOpening);
		if ($openingRows['countrb'] == 0)
		{
			$sum['opening'] = 0;
		}
		elseif ($openingRows['countrb'] > 0)
		{
			$sum['opening'] = $openingRows['opening'];
		}

		$sum['balance'] = $sum['opening'] + $sum['debit'] - $sum['credit'];


		return $sum;
	}

/******************************************************************************************************************************
	Funciton											;getBwDates

	;This funciton calculate the Debit Sum and Credit Sum and also calculate the balance BUT it calculates the SUM
	;and balance of Dates less than FROM DATE and greater Than TO DATE

	;Returns Debit Sum ,Credit Sum, Opening, And Also Balance in an associated array

	;@param coa_code									;This is a coa code
	;@param coa_group									;This is a group no
	;@param strFrom										;This is FROM DATE
	;@param ToFrom										;This is TO DATE


******************************************************************************************************************************/

function getBwDates($coa_code,$coa_group, $strFrom, $strTo)
{
		$codeDigit = getCodeDigit($coa_code, $coa_group);


		$resSum = MySQLQuery("select Count(*) as Countr ,SUM(tblVoucherDetail.vcd_debit) as dr , SUM(tblVoucherDetail.vcd_credit) as cr
								from tblVoucherDetail, tblVoucherMaster
								where tblVoucherMaster.vch_id = tblVoucherDetail.vcd_vch_id
								and tblVoucherDetail.vcd_coa_code like '$codeDigit%'
								and  tblVoucherMaster.vch_posted = 'Y'
								and tblVoucherMaster.vch_date >= '$strTo'
								and tblVoucherMaster.vch_date <= '$strFrom'"
						);



		$resSum =mysql_fetch_array($resSum);

		$sum['debit'] = $resSum['dr'];
		$sum['credit'] = $resSum['cr'];
		$sum['countr'] =$resSum['Countr'];

		return $sum;
}
/******************************************************************************************************************************
	Funciton											;giveSpace

	;This funciton gives space(at start) in the name of chart of account on the basis of group	number
	;Returns Chart of Acc name with specific indentation

	;@param coa_name									;This is a coa name
	;@param coa_group									;This is a group no[1/2/3]


******************************************************************************************************************************/

function giveSpace($coa_name, $coa_group)
{
	$coa_space1 = "    ";
	if($coa_group == 1)
	{
		$coa_name= $coa_name;
	}
	else if($coa_group == 2)
	{
		$coa_name= $coa_space1.$coa_name;
	}
	else if($coa_group == 3)
	{
		$coa_name= $coa_space1.$coa_space1.$coa_name;
	}
	else if($coa_group == 4)
	{
		$coa_name= $coa_space1.$coa_space1.$coa_space1.$coa_name;
	}

	return $coa_name;
}

/******************************************************************************************************************************
	Funciton											;getMonthly

	;This funciton calculate the Debit Sum and Credit Sum BUT it calculates the SUM monthly

	;Returns Debit Sum ,Credit Sum in an associated array

	;@param coa_code									;This is a coa code
	;@param coa_group									;This is a group no[1/2/3]
	;@param month										;This is $month like 1,2,3,.....


******************************************************************************************************************************/



function getMonthly($coa_code,$coa_group,$month)
{
		$codeDigit = getCodeDigit($coa_code, $coa_group);

		$res= MySQLQuery("select Count(*) as Countr ,SUM(tblVoucherDetail.vcd_debit) as dr , SUM(tblVoucherDetail.vcd_credit) as cr
						from tblVoucherDetail, tblVoucherMaster
						where tblVoucherMaster.vch_id = tblVoucherDetail.vcd_vch_id
						and  tblVoucherMaster.vch_posted = 'Y'
						and MONTH(tblVoucherMaster.vch_date)= '$month' and  tblVoucherDetail.vcd_coa_code like '$codeDigit%' "

				);


		$res=mysql_fetch_array($res);
		$resArr['debit'] = $res['dr'];
		$resArr['credit'] = $res['cr'];
		$resArr['countr'] =$res['Countr'];

		return $resArr;


}











/******************************************************************************************************************************
	Funciton											;getCodeDigit

	;This funciton Returns the digits of COA CODE which helps to identify the group of COA CODE while retrieving data from db

	;if the group is First  group this function will return the first digit only
	;if the group is Second then this function will return the first 3 digits only
	;if the group is Third then this function will return the complete COA CODE

	;@param coa_code									;This is a coa code
	;@param coa_group									;This is a group no


******************************************************************************************************************************/
function getCodeDigit($coa_code, $coa_group)
{
		$codeDigit=0;

		if($coa_group == "1")
		{
			$codeDigit= substr($coa_code, 0, 1);
		}
		else if($coa_group == "2")
		{
			$codeDigit= substr($coa_code, 0, 3);
		}
		else if($coa_group == "3")
		{
			$codeDigit= substr($coa_code, 0, 5);
		}
		else if($coa_group == "4")
		{
			 $codeDigit= $coa_code;
		}
	return $codeDigit;
}












/******************************************************************************************************************************
	Funciton											;doFromatting

	;This funciton converts the number into float then inserts commas and decimals and then insert brackets around it.
	;Returns the number with desire formatting

	;@param number										;This is number which needs to be formatted

******************************************************************************************************************************/
function doFromatting($number)
{
	$number	= number_format(round($number, 2), 2,'.',',');
	$number = showAsCrDr($number);

	return $number;
}

function drawComboBox($nSelectedVal, $strName, $arr)
{

		for($i=0; $i < sizeof($arr); $i++)
		{
			if($arr[$i] == $nSelectedVal)
				echo "<option value=$arr[$i] selected>" . $arr[$i] . "\r\n";
			else
				echo "<option value=$arr[$i] >" . $arr[$i] . "\r\n";
		}
}


	// Checks Form Field
	//if empty redirect to path
	//
	// strPath:			Path of File on which Page should be redirected after error
	// returns:			The array if all Fields are filled completely \
	//					otherwise redirect to given path with error message attached to it

	function isFormFieldEmpty($strPath)
	{
		$nCount=0;
		$ntFields= count($_POST);
		$arrField=array();
		$strEmtyField="";

		foreach ($_POST as $strField => $strValue)
		{
			if($strValue =="")
			{
				$strEmtyField= substr($strField,3);
				$nCount--;
				break;
			}
			$nCount++;
			$arrField[$strField]=$strValue;
		}


		if($ntFields == ($nCount))
		{
			return $arrField;
		}
		else
		{
			$strErrMsg="$strEmtyField is empty";
			echo"<script language='javascript'>
						window.location='$strPath?strErrMsg=$strErrMsg';
					</script>";
			exit;
		}

	}

	// Checks Is Specified Value Entered Already in db table
	//if yes the redirect to given path
	//
	// strPath:			Path of File on which Page should be redirected after
	// returns:			False if it does not already exist
	//					otherwise redirect to given path with error message attached to it

	function isAlreadyExist($strTableName, $strField, $strWhere, $strPath, $strErrMsg="")
	{
		$arr = getValOfTable($strTableName, $strField, $strWhere);
	 	$strVal = $arr[$strField];

		if($strVal== "")
		{
			return "false";
		}
		else
		{
			if($strErrMsg == "")
			{
				$strErrMsg="$strVal is Already Exist";
			}
			echo"<script language='javascript'>
						window.location='$strPath?strErrMsg=$strErrMsg';
					</script>";
			exit;
		}
	}

	function getGridRows($gridName)
	{
		//$arrColumns = getColumnArray($gridName, getValue("nTotalColumn"));

		$nOldRows = getValue("$gridName"."_nOldRowCount");
		$nNewRows = getValue("$gridName"."_nNewRowCount");

		$nRows[0]= $nOldRows;
		$nRows[1]= $nNewRows;

		return $nRows;
	}

	// send a query to MySQL server.
	// return an error message if there
	// was some error in the query
	Function ExecuteSQLQuery($query)
	{
		$success= mysql_db_query($GLOBALS["DB_DBName"], $query);

		if(!$success)
		{
			$strErr=mysql_error();
			return $strErr;
		}
	}

	/*
		the function shows checkboxed list of Given Table

		strLabel						Label in the rigth column of the Form
		strTable						db Table Name
		strName                       	Name of CheckBox group
	*/
	function tableCheckBox($strLabel, $strTable, $strName, $nIdField, $strField)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		$strQuery = "select * from $strTable";
		$nResult = MySQLQuery($strQuery);

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nId = $rstRow[$nIdField];
			$strDisplayName = $rstRow[$strField];

			echo "<input type='checkbox' value='$nId' name='".$strName."[]'>$strDisplayName<br>";
		}
		echo "	</td>";
		echo "</tr>";
	}


	/*
		the function shows checkboxed list of Given Table

		strLabel						Label in the rigth column of the Form
		strQuery						Query to execute
		strName                       	Name of CheckBox group
	*/
	function addPackCheckBox($strLabel, $strQuery, $strName, $nIdField, $strField)
	{

		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo"	<div style='dispaly:none;'>";
		echo "	<td>";

		$nResult = MySQLQuery($strQuery);

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nId = $rstRow[$nIdField];
			$strDisplayName = $rstRow[$strField];

			echo "<input type='checkbox' value='$nId' name='".$strName."[]'>$strDisplayName<br>";

		}

		echo "	</td>";
		echo "</tr>";
	}

	//Function Returns the date in an associated array with year , month and day.
	//input date format should be yy/mm/dd

	function getDateArray($date)
	{
		list($year,$month,$day)=explode("-",$date);
		$arrDate['year']= $year;
		$arrDate['month']= $month;
		$arrDate['day']= $day;

		return $arrDate;
	}


	/*Function 					countDeletedGridRows
		Counts the number of deleted Rows in a grid.
		Return the nmber of deleted rows

		$gridName				name of the grid
	*/

	function countDeletedGridRows($gridName)
	{
		//Counting the deleted Rows
		$strDeleted = getValue("$gridName_strDeleted");
		$arrDel = explode("-", $strDeleted);
		$ndelRows= count($arrDel);
		$ndelRows--;

		return $ndelRows;
	}





	/*
		the function shows a Vendor combo box with values from a table

		strTable:			table name
		strDispField:		field name to show
		strIDField:			id field name
		strCriteria:		select criteria for where clause
		strName:			combo name
		nSelId:				id of selected record
	*/
	function VendorTableCombo($strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId)
	{

		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
			$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";

		$nResult = MySQLQuery($strQuery);

		echo "<select name=$strName><br>";

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nID = $rstRow[$strIDField];

			if($nID == $nSelId && $nSelId != -1)
				echo "<option value=$nID selected>" . $rstRow[$strDispField] . "\r\n";
			else
				echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
		}
		if($nSelId == -1)
		{
			echo "<option value=-1 selected> All \r\n";
		}
		else
		{
			echo "<option value=-1> All \r\n";
		}

	}




	/*
		the function shows a Vendor combo box with values from a table

		strTable:			table name
		strDispField:		field name to show
		strIDField:			id field name
		strCriteria:		select criteria for where clause
		strName:			combo name
		nSelId:				id of selected record
	*/
	function ProdOrderTableCombo($strTable, $strDispField, $strIDField, $strCriteria, $strName, $nSelId)
	{

		if(empty($strCriteria))
			$strQuery = "select $strDispField, $strIDField from $strTable";
		else
			$strQuery = "select $strDispField, $strIDField from $strTable where $strCriteria";

		$nResult = MySQLQuery($strQuery);

		echo "<select name=$strName onChange=\"javascript:return loadItem();\"><br>";

		while($rstRow = mysql_fetch_array($nResult))
		{
			$nID = $rstRow[$strIDField];

			if($nID == $nSelId && $nSelId != -1)
				echo "<option value=$nID selected>" . $rstRow[$strDispField] . "\r\n";
			else
				echo "<option value=$nID>" . $rstRow[$strDispField] . "\r\n";
		}
		if($nSelId == -1)
		{
			echo "<option value=-1 selected> Select \r\n";
		}
		else
		{
			echo "<option value=-1> Select \r\n";
		}


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
	// strExtra				to write some thing extra like some onClick="alert('I'm Good')"
	//
	function DiplayNoneTextField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $bPassword, $strExtra="")
	{
		echo "<tr style='display:none' id='strHiddenRow' name='strHiddenRow'>";
		echo "	<td nowrap>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		if($bPassword)
			echo "		<input type=password name=$strField value=\"$strValue\" size=$nSize maxlength=$nMaxLength $strExtra>";
		else
			echo "		<input type=text name=$strField value=\"$strValue\" size=$nSize maxlength=$nMaxLength $strExtra>";

		echo "	</td>";
		echo "</tr>";
	}


	/*
		the function creates radio buttons group

		strLabel:		lable to be shown in the right cell
		arrButtons:		the lables to be shown along radio buttons
		strName:		form name for the button group
		nSelIndex:		index of selected button
		jFun:			Javascript Fuctions;
		nJFunId:		Id with which this fuc should be attached
	*/
	function newItemRadioButtons($strLabel, $arrButtons, $strName, $nSelIndex = -1 ,$jFun="", $nJFunId="")
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		for($i=0; $i<sizeof($arrButtons); $i++)
			if($i == $nSelIndex)
			{
				if($jFun !="" && $nJFunId == $i)
				{
					echo "<input type=radio value=$i name=$strName checked $jFun>" . $arrButtons[$i] . "<br>";
				}
				else
				{
					echo "<input type=radio value=$i name=$strName checked >" . $arrButtons[$i] . "<br>";
				}
			}
			else
			{
				if($jFun !="" && $nJFunId == $i)
				{
					echo "<input type=radio value=$i name=$strName $jFun >" . $arrButtons[$i] . "<br>";
				}
				else
				{
					echo "<input type=radio value=$i name=$strName >" . $arrButtons[$i] . "<br>";
				}
			}

		echo "	</td>";
		echo "</tr>";
	}

	/*
		the function creates radio buttons group

		strLabel:		lable to be shown in the right cell
		arrButtons:		the lables to be shown along radio buttons
		strName:		form name for the button group
		nSelIndex:		index of selected button
		jFun:			Javascript Fuctions;
		nJFunId:		Id with which this fuc should be attached
	*/
	function newOrderRadioButtons($strLabel, $arrButtons, $strName, $nSelIndex = -1 ,$jFun="")
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		for($i=0; $i<sizeof($arrButtons); $i++)
			if($i == $nSelIndex)
			{
				if($jFun !="")
				{
					echo "<input type=radio value=$i name=$strName checked $jFun>" . $arrButtons[$i] . "<br>";
				}
				else
				{
					echo "<input type=radio value=$i name=$strName checked >" . $arrButtons[$i] . "<br>";
				}
			}
			else
			{
				if($jFun !="")
				{
					echo "<input type=radio value=$i name=$strName $jFun >" . $arrButtons[$i] . "<br>";
				}
				else
				{
					echo "<input type=radio value=$i name=$strName >" . $arrButtons[$i] . "<br>";
				}
			}

		echo "	</td>";
		echo "</tr>";
	}

	/*Returns the sum of coa opening for group 1 and 2 */
	function getOpeningSum($coa_code, $coa_group)
	{

		$parseNo=0;

		if($coa_group == "1")
		{
			$parseNo= substr($coa_code, 0, 1);
		}
		else if($coa_group == "2")
		{
			$parseNo= substr($coa_code, 0, 3);
		}
		else if($coa_group == "3")
		{
			$parseNo= substr($coa_code, 0, 5);
		}


		$nRes = MySQLQuery("select Count(*) as Countr ,SUM(coa_opening) as sum_coa_opening
								from tblCOA
								where coa_code like '$parseNo%' and coa_group = 4"
						);


		$nRows =mysql_fetch_array($nRes);

		$sum = $nRows['sum_coa_opening'];
		return $sum;
	}

	/*
		the function shows checkboxed list of Given Table

		strLabel						Label in the rigth column of the Form
		strName                       	Name of CheckBox group
		arrName							an array with list of check box and their index will be there id
	*/
	function formCheckBox($strLabel, $strName, $arrDisplayName)
	{
		echo "<tr>";
		echo "	<td valign=top>";
		echo		$strLabel;
		echo "	</td>";
		echo "	<td>";

		$nTCount=count($arrName);
		for($nI=0; $nI<=$nTCount ; $nI++)
		{
			echo "<input type='checkbox' value='$nI' name='".$strName."[$nI]'>$arrDisplayName[$nI]<br>";
		}
		echo "	</td>";
		echo "</tr>";
	}

	function CheckBox($strLabel, $strName, $nChecked = 0, $nCallBack = '')
	{
		echo "<tr><td></td><td class='textFieldLabelAControl'>";

		if($nChecked == 1)
			echo "<input type=checkbox name=$strName CHECKED $nCallBack> $strLabel";
		else
			echo "<input type=checkbox name=$strName $nCallBack> $strLabel";

		echo "</td></tr>";
	}

	//function to show Message()
	//$strRowColor                     This is the color of the row [red/green]
	function messageTable($strMsg,$strRowColor)
	{
		echo "<table cellspacing=0 cellpadding=0 border=0 width=100% align='center'>";

		if($strRowColor == "green")
		{
			echo "<tr bgcolor= '#006600'  valign='middle' height=25 >\r\n";
		}
		else if	($strRowColor=="red")
		{
			echo "<tr bgcolor= '#990000'  valign='middle' height=25 >\r\n";
		}

		echo "	<td colspan=2 align='center'>\r\n";
		echo "		<font color='#FFFFFF' style='text-align:center'><b>$strMsg</b></font>\r\n";
		echo "	</td>\r\n";
		echo "</tr>\r\n";
		echo "</table>";
	}


	function itemMainSubCatCombo($strLabel,$strComboName, $strSelect="")
	{	$bAlreadyAdd="false";
		echo"		<tr>\r\n";
		echo"			<td align='left' width='13%'>\r\n";
		echo"				".$strLabel;
		echo"			</td>\r\n";
		echo"			<td>\r\n";
							$strMainQuery = "select * from tblItemMainCategory";
							$nMainResult = MySQLQuery($strMainQuery);

		echo"				<select name='$strComboName'><br>";
								while($rstMainRows = mysql_fetch_array($nMainResult))
								{
									$nId = $rstMainRows['mcat_id'];
									echo"<optgroup label='".$rstMainRows['mcat_name']."' title='".$rstMainRows['mcat_name']."'>";

									$strSubQuery="select * from tblItemSubCategory where subcat_mcat_id='$nId'";
									$nSubResult = MySQLQuery($strSubQuery);

									while($rstSubRows = mysql_fetch_array($nSubResult))
									{
										$nSubId = $rstSubRows['subcat_id'];
										if($strSelect != "")
										{
											if($nSubId == $strSelect)
											{
												echo "<option value=$nSubId selected>" .$rstSubRows['subcat_name'] . "\r\n";
												$bAlreadyAdd="true";
											}
										}
										if($bAlreadyAdd=="false")
										{
											echo "<option value=$nSubId>" .$rstSubRows['subcat_name'] . "\r\n";
										}
										$bAlreadyAdd = "false";
									}
									echo"</optgroup>";
								}
		echo"				</select>";
		echo"			</td>\r\n";
		echo"		</tr>\r\n";
	}
	////////
	function itemMainSubCatCombo2($strLabel,$strComboName, $strSelect="")
	{	$bAlreadyAdd="false";
		echo"		<tr>\r\n";
		echo"			<td align='left' width='13%'>\r\n";
		echo"				".$strLabel;
		echo"			</td>\r\n";
		echo"			<td>\r\n";
							$strMainQuery = "select * from tblItemMainCategory";
							$nMainResult = MySQLQuery($strMainQuery);

		echo"				<select name='$strComboName'><br>";
								while($rstMainRows = mysql_fetch_array($nMainResult))
								{
									$nId = $rstMainRows['mcat_id'];
									echo"<optgroup label='".$rstMainRows['mcat_name']."' title='".$rstMainRows['mcat_name']."'>";

									$strSubQuery="select * from tblItemSubCategory where subcat_mcat_id='$nId'";
									$nSubResult = MySQLQuery($strSubQuery);

									while($rstSubRows = mysql_fetch_array($nSubResult))
									{
										$nSubId = $rstSubRows['subcat_id'];
										if($strSelect != "")
										{
											if($nSubId == $strSelect)
											{
												echo "<option value=$nSubId selected>" .$rstSubRows['subcat_name'] . "\r\n";
												$bAlreadyAdd="true";
											}
										}
										if($bAlreadyAdd=="false")
										{
											echo "<option value=$nSubId>" .$rstSubRows['subcat_name'] . "\r\n";
										}
										$bAlreadyAdd = "false";
									}
									echo"</optgroup>";
								}
		echo "<option value=-1> All\r\n";
		echo"				</select>";
		echo"			</td>\r\n";
		echo"		</tr>\r\n";
	}


	function itemListCombo($strLabel,$strComboName, $strSelect="")
	{	$bAlreadyAdd="false";
		echo"		<tr>\r\n";
		echo"			<td align='left' width='13%'>\r\n";
		echo"				".$strLabel;
		echo"			</td>\r\n";
		echo"			<td>\r\n";
							$strMainQuery = "select * from tblItemMainCategory";
							$nMainResult = MySQLQuery($strMainQuery);

		echo"				<select name='$strComboName'><br>";
								while($rstMainRows = mysql_fetch_array($nMainResult))
								{
									$nId = $rstMainRows['mcat_id'];
									echo"<optgroup label='".$rstMainRows['mcat_name']."' title='".$rstMainRows['mcat_name']."'>";

									$strSubQuery="select * from tblItemSubCategory where subcat_mcat_id='$nId'";
									$nSubResult = MySQLQuery($strSubQuery);

									while($rstSubRows = mysql_fetch_array($nSubResult))
									{
										$nSubId = $rstSubRows['subcat_id'];
										if($strSelect != "")
										{
											if($nSubId == $strSelect)
											{
												echo "<option value=$nSubId selected>" .$rstSubRows['subcat_name'] . "\r\n";
												$bAlreadyAdd="true";
											}
										}
										if($bAlreadyAdd=="false")
										{
											echo "<option value=$nSubId>" .$rstSubRows['subcat_name'] . "\r\n";
										}
										$bAlreadyAdd = "false";
									}
									echo"</optgroup>";
								}
		echo "<option value=-1> All\r\n";
		echo"				</select>";
		echo"			</td>\r\n";
		echo "<td align=left>";
			echo "<input type=button  value='  Search  ' onClick = refresh();>";
		echo "</td>";
		echo"		</tr>\r\n";
	}


	//function 								;PovDistCityCombo
	//display the province ,district and city name in combo

	//strLabel                              ;left cell label of table
	//strComboName                          ;Name of combo
	//nSelectedId							;id of selected field
	//strExtra								;some extra attribute or field

	function PovDistCityCombo($strLabel, $strComboName, $nSelectedId, $strExtra="" )
	{
		echo"<tr>\r\n";
		echo"	<td align='left'>\r\n";
		echo"		".$strLabel;
		echo"	</td>\r\n";
		echo"	<td>\r\n";

		//Retreiving Record from Province
		$strProvQuery="Select * From tblProvinceMaster";
		$nProvRes = MySQLQuery($strProvQuery);

		if($strExtra !="")
		{
			echo"	<select name='$strComboName' $strExtra>";
		}
		else
		{
			echo"	<select name='$strComboName'>";
		}

		while($nProvRows = mysql_fetch_array($nProvRes))
		{

			$nProvId = $nProvRows['porvince_id'];
			$strProvName = $nProvRows['province_name'];
			echo"	<optgroup label='$strProvName' title='$nProvId'>";

			//Retreiving District against each province
			$strDistQuery ="Select * From tblDistrictMaster where dist_province_id='$nProvId'";
			$nDistRes = MySQLQuery($strDistQuery);

			while($nDistRows = mysql_fetch_array($nDistRes))
			{
				$nDistId = $nDistRows['dist_id'];
				$strDistName = $nDistRows['dist_name'];
				$strDistName="   ".$strDistName;
				echo"	<optgroup label='$strDistName' title='$nDistId'>";

				//Retreiving city against each district
				$strCityQuery ="Select * From tblCityMaster where city_dist_id='$nDistId'";
				$nCityRes = MySQLQuery($strCityQuery);

				while($nCityRows = mysql_fetch_array($nCityRes))
				{
					$nCityId = $nCityRows['city_id'];
					$strCityName = $nCityRows['city_name'];
					if($nCityRows['city_code']!="")
						$strCityName = $strCityName."(".$nCityRows['city_code'].")";

					if($nSelectedId == $nCityId)
					{
						echo"	<option value='$nCityId' selected>$strCityName";
					}
					else
					{
						echo"	<option value='$nCityId'>$strCityName";
					}
				}								//end of city while loop
				echo"</optgroup>";
			}									//end of District while loop
			echo"</optgroup>";
		}										//end of province while loop
		echo"	</select>";
		echo"	</td>\r\n";
		echo"</tr>\r\n";
	}
	//This function extract the coa code[cost,sale,stock] from tblconfiguration and max code[cost,sale,stock]
	// form tblItem Master and store the max coa code [cost,sale,stock]  for the item
	//
	//Function retrun an array containing the new coa code[cost,sale,stock]

	function makingItemCoaCode($strStock,$strCost,$strSale)
	{
		/*$nSaleCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'ITM_SALE_COA'");
		$nCostCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'ITM_COST_COA'");
		$nStockCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'ITM_STOCK_COA'");	*/

		$nSaleCode 	= $strSale;
		$nCostCode 	= $strCost;
		$nStockCode = $strStock;

		//getting first 3digit of coa code
		$n3DSaleCode  = substr("$nSaleCode",0,5);
		$n3DCostCode  = substr("$nCostCode",0,5);
		$n3DStockCode = substr("$nStockCode",0,5);



		$strQuery ="select max(coa_code) as maxSaleCode from tblCOA where coa_code like '$n3DSaleCode%' and coa_group='4'";
		$strQuery;
		$nSaleResult = MySQLQuery($strQuery);
		$nSaleRows = mysql_fetch_array($nSaleResult);
		$maxSaleCode = $nSaleRows['maxSaleCode'];


		$strQuery ="select max(coa_code) as maxCostCode from tblCOA where coa_code like '$n3DCostCode%' and coa_group='4'";
		$nCostResult = MySQLQuery($strQuery);
		$nCostRows = mysql_fetch_array($nCostResult);
		$maxCostCode = $nCostRows['maxCostCode'];

		$strQuery ="select max(coa_code) as maxStockCode from tblCOA where coa_code like '$n3DStockCode%' and coa_group='4'";
		$nStockResult = MySQLQuery($strQuery);
		$nStockRows = mysql_fetch_array($nStockResult);
		$maxStockCode = $nStockRows['maxStockCode'];


		//Making sale coa code
		if($maxSaleCode =="")
		{
			$maxSaleCode = $nSaleCode+1;
		}
		else
		{
			$maxSaleCode++;
		}


		//Making cost coa code
		if($maxCostCode =="")
		{

			$maxCostCode = $nCostCode+1;
		}
		else
		{
			$maxCostCode++;
		}


		//Making stock coa code
		if($maxStockCode =="")
		{

			$maxStockCode = $nStockCode+1;
		}
		else
		{
			$maxStockCode++;
		}

		$arrCoaCode['itm_sale_coa_code'] = $maxSaleCode;
		$arrCoaCode['itm_cost_coa_code'] = $maxCostCode;
		$arrCoaCode['itm_stock_coa_code'] = $maxStockCode;

		return $arrCoaCode;
	}

	function makingFourthLevelCoaCode($strCode)
	{


		//getting first 3digit of coa code
		$n3DCode  = substr("$strCode",0,5);



		$strQuery ="select max(coa_code) as maxCode from tblCOA where coa_code like '$n3DCode%' and coa_group='4'";
		//echo $strQuery;
		$nResult = MySQLQuery($strQuery);
		$nRow = mysql_fetch_array($nResult);
		$maxCode = $nRow['maxCode'];



		//Making sale coa code
		if($maxCode =="")
		{
			$maxCode = $strCode+1;
		}
		else
		{
			$maxCode++;
		}

		return $maxCode;
	}



	//This Function inserts the Item information in tblCOA
	//nCoaCode								coa code
	//strName                               Name of account
	//nClassId								class id of account
	//nTypeId 								Type id of account
	//nOpeningBal                           Opening bal of account
	//nCoagroup                             group of account

	function insertItemInfoInTblCoa($nCoaCode, $strName, $nClassId, $nTypeId, $nOpeningBal, $nCoaGroup,$nCoaOpenEnable)
	{
		///////////////////Inserting Record in tblCOA
		//record array for
		$arrCoa=array(
					"coa_code" =>$nCoaCode,
					"coa_name" =>$strName,
					"coa_class_id"=>$nClassId,
					"coa_type_id"=>$nTypeId,
					"coa_opening"=>$nOpeningBal,
					"coa_budget"=>0,
					"coa_open_enable" => $nCoaOpenEnable,
					"coa_group"=>$nCoaGroup
				 );

		InsertRec("tblCOA", $arrCoa);
	}


	//Fucntion Make the Item Code
	//Return Maximun number and Item code in an associtaed array
	function getItemCode($strSubCat,$strUnit)
	{

		//Seleting max item number 0f item sub category id from tblItemMaster
		$strQuery="Select Max(itm_number) as maxItmNumber from tblItemMaster where itm_subcat_id='$strSubCat'";
		$masterResult=MySQLQuery($strQuery);
		$nImRows=mysql_fetch_array($masterResult);
		$nMax = $nImRows['maxItmNumber'];


		//Getting the Main Category
		$strMainCat= getValOfTable("tblItemSubCategory", "subcat_mcat_id", "subcat_id='$strSubCat'");



		if($nMax =="" )
		{
			$nMax=1;
		}
		else
		{
			$nMax++;
		}


		//Concatenatin Zeros
		$nMaxLen=strlen($nMax);

		if($nMaxLen==1)
		{
			$zeros="000";
		}
		else if($nMaxLen==2)
		{
			$zeros="00";
		}
		else if($nMaxLen==3)
		{
			$zeros="0";
		}
		else if($nMaxLen==4)
		{
			$zeros="";
		}


		/////Making an item code
		$nItemCode= $strMainCat.$strSubCat.$strUnit;
		$nItemCode=$nItemCode.$zeros.$nMax;

		$arr['nItemCode'] = $nItemCode;
		$arr['nMax'] = $nMax;
		return $arr;
	}




	/* Insert Record in table Voucher Master*/

	function insertVoucherMaster($nVchNo, $strDate, $nVchAcc, $strVchMDetail , $strVchType, $nVchAmount, $bPosted, $nVchNumDesc, $strVchsource, $strUserName)
	{
		//inserting the record in tblVoucherMaster
		$arrVchMaster=array(
					"vch_number"=>$nVchNo,
					"vch_date"=>$strDate,
					"vch_ac_num"=>$nVchAcc,
					"vch_details"=>$strVchMDetail,
					"vch_type"=>$strVchType,
					"vch_amount"=>$nVchAmount,
					"vch_posted"=>$bPosted,
					"vch_number_desc"=>$nVchNumDesc,
					"vch_source"=>$strVchsource,
					"vch_usr_name"=>$strUserName
				);

		$nRecInsId = InsertRec("tblVoucherMaster", $arrVchMaster);

		return 	$nRecInsId;
	}




	/* Insert Record in table Voucher Detail*/

	function insertVoucherDetail($nRecInsId, $nCoaCode, $strVchDetail, $strCostCenter, $nDebit, $nCredit)
	{
		////////////inserting the record in tblVoucherDetail
		$arrVchDetail=array(
					"vcd_vch_id" => $nRecInsId,
					"vcd_coa_code" => $nCoaCode,
					"vcd_detail" => $strVchDetail,
					"vcd_debit" => $nDebit,
					"vcd_cost_center" => $strCostCenter,
					"vcd_credit" => $nCredit
				);

		$newId =InsertRec("tblVoucherDetail", $arrVchDetail);

		return $newId;
	}



	/*Make the voucher number and voucher number description

	 return an associated array of voucher number and voucher number description

	 take strDate as an input

	*/

	function makeVoucherNum($strDate)
	{
		///////////////Making Voucher Number Description
		list($year ,$month, $day)= explode ("-", $strDate);

		$nVchNo = doubleval(getValOfTable("tblVoucherMaster", "MAX(vch_number)", "vch_type='JV' and MONTH(vch_date) = $month and YEAR(vch_date) = $year")) + 1 ;
		$nVchNumDesc= $month."-".$nVchNo."-"."JV";

		$arr['nVchNo'] = $nVchNo;
		$arr['nVchNumDesc'] = $nVchNumDesc;
		return $arr;
	}

	function makeVoucherNumbyType($strDate,$strType)
	{
		///////////////Making Voucher Number Description
		list($year ,$month, $day)= explode ("-", $strDate);

		$strQuery = "Select MAX(vch_number)	as maxV from tblVoucherMaster where vch_type='$strType' and MONTH(vch_date) = $month and YEAR(vch_date) = $year";

		$nRes = MySQLQuery($strQuery);
		//$nVchNo = doubleval(getValOfTable("tblVoucherMaster", "MAX(vch_number)", "vch_type=$strType and MONTH(vch_date) = $month and YEAR(vch_date) = $year"))  ;

		$nRows = mysql_fetch_array($nRes);
		$nMaxVoucher = $nRows['maxV'];
		if ($nMaxVoucher=="")
		{
			 $nVchNo = 1;
		}
		else
		{
			$nVchNo = $nMaxVoucher + 1;
		}

		$nVchNumDesc= $month."-".$nVchNo."-".$strType;
		$arr['nVchNo'] = $nVchNo;
		$arr['nVchNumDesc'] = $nVchNumDesc;
		return $arr;
	}
	/*
	get Labour Coa Code

	*/

	function getLabourCoaCode()
	{
		//Getting Labor Code from tblConfinguration
		$nLabCoaCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'LABOUR_COA'");
		/*$n3DLabCoaCode = substr("$nLabCoaCode ",0,3);


		$strQuery = "select max(coa_code) as maxLabCoaCode from tblCOA where coa_code like '%$n3DLabCoaCode%' and coa_group='3'";
		$nResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nResult);
		$maxLabCoaCode= $nRows['maxLabCoaCode'];



		if($maxLabCoaCode=="")
		{
			$maxLabCoaCode= $nLabCoaCode;
		}
		else
		{
			$maxLabCoaCode++;
		}*/

		return $nLabCoaCode;
	}

	function getCustomerCoaCode()
	{
		$nCusCoaCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'CUSTOMER_COA_KEY'");

		$n3DCusCoaCode = substr("$nCusCoaCode",0,3);

		$strQuery = "select max(coa_code) as maxCusCoaCode from tblCOA where coa_code like '%$n3DCusCoaCode%' and coa_group='3'";
		$nResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nResult);
		$maxCusCoaCode = $nRows['maxCusCoaCode'];



		if($maxCusCoaCode =="")
		{
			$maxCusCoaCode = $nCusCoaCode;
		}
		else
		{
			$maxCusCoaCode++;
		}

		return $maxCusCoaCode;
	}




	function getCommissionCoaCode()
	{
		$nComCoaCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'COMISSION_COA_CODE'");

		$n3DComCoaCode = substr("$nComCoaCode",0,3);

		$strQuery = "select max(coa_code) as maxComCoaCode from tblCOA where coa_code like '%$n3DComCoaCode%' and coa_group='3'";
		$nResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nResult);
		$maxComCoaCode = $nRows['maxComCoaCode'];



		if($maxComCoaCode =="")
		{
			$maxComCoaCode = $nComCoaCode;
		}
		else
		{
			$maxComCoaCode++;
		}

		return $maxComCoaCode;
	}


	//This Function inserts the Item information in tblCOA
	//nCoaCode								coa code
	//strName                               Name of account
	//nClassId								class id of account
	//nTypeId 								Type id of account
	//nOpeningBal                           Opening bal of account
	//nCoagroup                             group of account
	//nCoaOpenEnable						Coa opening Stock editable Flag
	//Function contains the new feild of coa open enable
	function insertInNewTblCoa($nCoaCode, $strName, $nClassId, $nTypeId, $nOpeningBal, $nCoaGroup , $nCoaOpenEnable)
	{
		///////////////////Inserting Record in tblCOA
		//record array for
		$arrCoa=array(
					"coa_code" =>$nCoaCode,
					"coa_name" =>$strName,
					"coa_class_id"=>$nClassId,
					"coa_type_id"=>$nTypeId,
					"coa_opening"=>$nOpeningBal,
					"coa_budget"=>0,
					"coa_group"=>$nCoaGroup,
					"coa_open_enable" => $nCoaOpenEnable
				 );

		//print_r($arrCoa);
		//exit;
		InsertRec("tblCOA", $arrCoa);
	}

	//funciton to get Batch Number
	//strDate                       Currnet Date
	//Return                       Batch Number & Batch Number Description in an associated array

	function makeBatchNum($strDate)
	{
		//getting the year of selected Date
		$arrDate= getDateArray($strDate);
		$year=$arrDate['year'];



		$nMax=0;
		$masterResult=MySQLQuery("Select Max(bat_number) as maxBatNumber from tblPdoBatchMaster where YEAR(bat_date)='$year'");
		$nImRows=mysql_fetch_array($masterResult);

		$nMax = $nImRows['maxBatNumber'];
		if($nMax=="")
		{
			$nMax=1;
		}
		else
		{
			$nMax++;
		}

		//Concatenatin Zeros
		$nMaxLen=strlen($nMax);

		if($nMaxLen==1)
		{
			$zeros="0000";
		}
		else if($nMaxLen==2)
		{
			$zeros="000";
		}
		else if($nMaxLen==3)
		{
			$zeros="00";
		}
		else if($nMaxLen==4)
		{
			$zeros="0";
		}
		else if($nMaxLen==5)
		{
			$zeros="";
		}



		$nBatNumber = $nMax;



		//making purcahse number description
		$nBatNumDesc="BAT-".$zeros.$nBatNumber."-".$year;

		$arrBatch['nBatNumber'] = $nBatNumber;
		$arrBatch['nBatNumDesc'] = $nBatNumDesc;

		return $arrBatch;
	}


		//function 						showLinkTable
		//arrLink						an associtated array of links
		//td width						cell width
		//strTableAlign					table alignment

		function showLinkTable($strTableName, $arrLinks,$nTdWidth="120", $strTableAlign="left",$nTableBorder="1")
		{
			echo"<style>
			TR.".$strTableName."Tr
			{
				font-family : arial;
				font-size :  9pt;
				font-style :  normal;
				color : black;
				text-decoration :  none;
				height: 24px;
				text-align: center;
				background-color:#86A8EC;
				border-color:#CCCCCC;
			}
			</style>";

			$strColor1 = "#86A8EC";
			$strColor2 = "#D8D8D8";
			$strColor3 = "#f0fff0";
			$strColor4 = "#CCCCCC";

			echo "<table cellspacing=0 cellpadding=0 border=$nTableBorder align='$strTableAlign' >";
			echo"	<tr class='".$strTableName."Tr'>";
			while (list ($strKey, $strVal) = each ($arrLinks))
			{
				echo"		<td valign='bottom' width='$nTdWidth'>";
				echo"			<a href='$strVal'>$strKey</a>";
				echo"		</td>";
			}

			echo"	</tr>";

		}


		//function											showTableData
		// $strTableName									Table Name
		// $arrVal											an associated array of colmn names and the field name of db table
		//strQuery											Query
		//bEditField										boolean [0]..1 for  for edit column  and 2 for delete column 3 for edit and delete both
		//$nTdwidth											width of table cell
		//table alignment
		//strEdtit Link										Link to be shown for edit
		//nEditId											Id Field name to be assciated with edit link
		//strDelLink										Link to be shown for delete
		//nDelId											Id Field name to be assciated with delete link
		function showTableData($strTableName, $arrVal, $strQuery, $bEditField, $nTableWidth="100%", $nTdWidth="120", $strTableAlign="left", $strEditLink="", $nEditId="" ,$strDelLink="", $nDelId="")
		{
			echo"<style>
			TR.".$strTableName."Tr
			{
				font-family : Urdu Naskh Asiatype;
				font-size :  9pt;
				font-style :  normal;
				font-weight : bold;
				color : black;
				text-decoration :  none;
				height: 24px;
				text-align: center;
				background-color:#f0fff0;
				border-color:#CCCCCC;
			}
			</style>";
			echo "<table cellspacing=0 cellpadding=0 border=1 align='$strTableAlign' width='$nTableWidth'>";
			echo"	<tr class='".$strTableName."Tr'>";
			while(list ($strKey, $strVal) = each ($arrVal))
			{
				echo"	<td>";
				echo "		<b>$strKey</b>";
				echo "	</td>\r\n";
			}
			if($bEditField == 1)
			{
				echo "	<td align='center'>";
				echo "		<b>Edit</b>";
				echo "	</td>\r\n";
			}
			if($bEditField == 2)
			{
				echo "	<td align='center'>";
				echo "		<b>Delete</b>";
				echo "	</td>\r\n";
			}
			if($bEditField == 3)
			{
				echo "	<td align='center'>";
				echo "		<b>Edit</b>";
				echo "	</td>\r\n";
				echo "	<td align='center'>";
				echo "		<b>Delete</b>";
				echo "	</td>\r\n";
			}

			echo "	</tr>";
			echo "<tr height=25>\r\n";
				dispTableData($arrVal,$strQuery,$bEditField, $strEditLink,$nEditId ,$strDelLink);
			echo "</tr>\r\n";
			echo "</table>";
		}

		//function to dispaly data of table
		//table is directly connected to [function show table data]

		function dispTableData($arrVal,$strQuery, $bEditField ,$strEditLink ,$nEditId ,$strDelLink)
		{
			$nResult = MySQLQuery($strQuery);


			//getting Total number of records
			$nTRecords = mysql_num_rows($nResult);
			$nLoopCounter=0;


			while($nRows= mysql_fetch_array($nResult))
			{

				$id = $nRows[$nEditId];
				$nLoopCounter++;
				while(list ($strKey, $strVal) = each ($arrVal))
				{
					echo "	<td align='center'>\r\n";
					echo "		&nbsp;".$nRows[$strVal];
					echo "	</td>\r\n";
				}
				if($bEditField==1)
				{
					echo "	<td align='center'>\r\n";
					echo "		<a href='$strEditLink?id=$id'><img  style='border-color:#FFFFFF'  src='../../images/icon_edit.gif' alt='edit'></a>";
					echo "	</td>\r\n";
				}
				else if($bEditField==2)
				{
					echo "	<td align='center'>\r\n";
					echo "		<a href='$strDelLink?id=$id'><img  style='border-color:#FFFFFF'  src='../../images/icon_edit.gif' alt='edit'></a>";
					echo "	</td>\r\n";
				}
				else if($bEditField==3)
				{
					echo "	<td align='center'>\r\n";
					echo "		<a href='$strEditLink?id=$id'><img  style='border-color:#FFFFFF'  src='../../images/icon_edit.gif' alt='edit'></a>";
					echo "	</td>\r\n";

					echo "	<td align='center'>\r\n";
					echo "		<a href='$strDelLink?id=$id'><img  style='border-color:#FFFFFF'  src='../../images/icon_delete.gif' alt='edit'></a>";
					echo "	</td>\r\n";
				}

				reset($arrVal);

				if($nLoopCounter != $nTRecords)
				{
					echo"</tr>";
					echo"<tr height=25>";
				}
			}
		}


		//function                        	to show dept and section in a combo
		//strlabel							Left column in the table
		//strComboName						Name of Combo box
		//nSelected Id						id of selected record


		function deptSectionCombo($strLabel,$strComboName,$nSelectedId="")
		{
			echo"		<tr>\r\n";
			echo"			<td align='left' width='13%'>\r\n";
			echo"				".$strLabel;
			echo"			</td>\r\n";
			echo"			<td>\r\n";
								$strMainQuery = "select * from tblDepartment order by dpt_name";
								$nMainResult = MySQLQuery($strMainQuery);

			echo"				<select name='$strComboName'><br>";
									while($rstMainRows = mysql_fetch_array($nMainResult))
									{
										$nId = $rstMainRows['dpt_id'];
										echo"<optgroup label='".$rstMainRows['dpt_name']."' title='".$rstMainRows['dpt_name']."'>";

										$strSubQuery="select * from tblSection where sec_dpt_id='$nId' order by sec_name";
										$nSubResult = MySQLQuery($strSubQuery);

										while($rstSubRows = mysql_fetch_array($nSubResult))
										{
											$nSubId=$rstSubRows['sec_id'];
											if($nSelectedId != "")
											{
												if($nSubId == $nSelectedId)
												{
													echo "<option value=$nSubId selected>" .$rstSubRows['sec_name'] . "\r\n";
												}
												else
												{
													echo "<option value=$nSubId>" .$rstSubRows['sec_name'] . "\r\n";
												}
											}
											else
											{
												echo "<option value=$nSubId>" .$rstSubRows['sec_name'] . "\r\n";
											}
										}
										echo"</optgroup>";
									}
			echo"				</select>";
			echo"			</td>\r\n";
			echo"		</tr>\r\n";

		}

	//This function extract the coa code[cost,,stock] from tblconfiguration and max code[cost,,stock]
	// form tblItem Master and store the max coa code [cost,,stock]  for the item
	//There is no sale code for these items(because these r expense (Like office supplies))
	//Function retrun an array containing the new coa code[cost,,stock]

	function getOtherItemCoaCode()
	{
		//$nSaleCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'ITM_SALE_COA'");
		$nCostCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'ITM_NON_PRO_COST'");
		$nStockCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= 'ITM_STOCK_COA'");
		//		echo substr("1200000",0,strpos("1200000",strstr("1200000","0")));//prints 12
		//getting first 3digit of coa code
		//$n3DSaleCode = substr("$nSaleCode",0,3);
		$n3DCostCode = substr("$nCostCode",0,3);
		$n3DStockCode = substr("$nStockCode",0,3);



		/*$strQuery ="select max(coa_code) as maxSaleCode from tblCOA where coa_code like '%$n3DSaleCode%' and coa_group='3'";
		$nSaleResult = MySQLQuery($strQuery);
		$nSaleRows = mysql_fetch_array($nSaleResult);
		$maxSaleCode = $nSaleRows['maxSaleCode'];*/


		$strQuery ="select max(coa_code) as maxCostCode from tblCOA where coa_code like '%$n3DCostCode%' and coa_group='3'";
		$nCostResult = MySQLQuery($strQuery);
		$nCostRows = mysql_fetch_array($nCostResult);
		$maxCostCode = $nCostRows['maxCostCode'];

		$strQuery ="select max(coa_code) as maxStockCode from tblCOA where coa_code like '%$n3DStockCode%' and coa_group='3'";
		$nStockResult = MySQLQuery($strQuery);
		$nStockRows = mysql_fetch_array($nStockResult);
		$maxStockCode = $nStockRows['maxStockCode'];

		/*
		//Making sale coa code
		if($maxSaleCode =="")
		{
			$maxSaleCode = $nSaleCode+1;
		}
		else
		{
			$maxSaleCode++;
		}
		*/

		//Making cost coa code
		if($maxCostCode =="")
		{

			$maxCostCode = $nCostCode+1;
		}
		else
		{
			$maxCostCode++;
		}


		//Making stock coa code
		if($maxStockCode =="")
		{

			$maxStockCode = $nStockCode+1;
		}
		else
		{
			$maxStockCode++;
		}

		//$arrCoaCode['itm_sale_coa_code'] = $maxSaleCode;
		$arrCoaCode['itm_cost_coa_code'] = $maxCostCode;
		$arrCoaCode['itm_stock_coa_code'] = $maxStockCode;

		return $arrCoaCode;
	}

	//Return the value of query strin variable
	function getQueryStringVal($strName)
	{
		if(!empty($_GET[$strName]))
		{
			$strVal = $_GET['$strName'];
			return $strVal;
		}
	}

	//Get Code against key in tblConfiguration
	function getMaxCoaCode($nConfKey)
	{
		//Getting Labor Code from tblConfinguration
		$nCoaCode = getValOfTable("tblConfiguration", "conf_value", "conf_key= '$nConfKey'");
		$n3DCoaCode = substr("$nCoaCode ",0,3);


		$strQuery = "select max(coa_code) as maxCoaCode from tblCOA where coa_code like '%$n3DCoaCode%' and coa_group='3'";
		$nResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nResult);
		$maxCoaCode= $nRows['maxCoaCode'];



		if($maxCoaCode=="")
		{
			$maxCoaCode= $nCoaCode+1;
		}
		else
		{
			$maxCoaCode++;
		}

		return $maxCoaCode;
	}


	//Fucntion  dateDiff Returns the difference between two dates assuming the format (yyyy-mm-day)
	function dateDiff($strStartDate , $strEndDate)
	{
		//converting the Start Date into number
		list($nYear,$nMonth,$nDay) = explode("-",$strStartDate);
		$nStartDate  = gregoriantojd($nMonth,$nDay,$nYear); //converts the date into number


		//converting the End Date into number
		list($nYear,$nMonth,$nDay) = explode("-",$strEndDate);
		$nEndDate = gregoriantojd($nMonth,$nDay,$nYear); //converts the date into number

		$nDiff = $nEndDate - $nStartDate;

		//echo $nDiff;
		return $nDiff;
	}

	/*
		$nDay:		(ISO Format) 1 = Monday, 7 = Sunday
	*/
	function CalcNumOfDays($nYear, $nMonth, $nDay)
	{
		$nCount = 0;

		// get first day of next month;
		if($nMonth < 12)
		{
			$nNextMonth = $nMonth + 1;
			$nNextMonthFirstDay = strtotime("$nYear-$nNextMonth-01");
		}
		else
		{
			$nNextMonth = 1;
			$nNextYear = $nYear + 1;
			$nNextMonthFirstDay = strtotime("$nNextYear-$nNextMonth-01");
		}

		// going one day back gives us the last day of this month
		$nThisMonthLastDay = $nNextMonthFirstDay - 86400;

		for($nDate = 1;; $nDate++)
		{
			$nThisDay = strtotime("$nYear-$nMonth-$nDate");
			$strDay = date("w", $nThisDay);
			$strISODay = 7 - ( (7 - $strDay) % (7 + $strDay));

			if($strISODay == $nDay) $nCount++;

			// if we have reached to the last day of this month
			if(date("Y-m-d", $nThisDay) == date("Y-m-d", $nThisMonthLastDay))
				break;
		}

		return $nCount;
	}

	//strEmpIdFieldLabel 					Left col field label
	//strEmpFieldId							Field name of Id field
	//strValue								value of Id field
	//nSize									size of Id field
	//nMaxLength							Max Length of Field
	//strFormName							form name
	//strEmpFieldNameVal					Value of Employee Field Name
	function fnEmployeeLookUp($strEmpIdFieldLabel, $strEmpFieldId, $strValue, $nSize, $nMaxLength, $strFormName,$strEmpFieldNameVal="")
	{
		//function DateField($strLabel, $strField, $strValue, $nSize, $nMaxLength, $strFormName)
		//$strUnique = time();
		echo "<tr>";
		echo "	<td>";
		echo		$strEmpIdFieldLabel;
		echo "	</td>";
		echo "	<td>";

		echo  "
				<input type=text name='$strEmpFieldId' value='$strValue' size=$nSize maxlength=$nMaxLength readonly>
				<a href=\"JavaScript: CalEmpPop('document.$strFormName.$strEmpFieldId','document.$strFormName.strEmpFieldName');\"><img src='/images/ico-cal.gif' border=0></a>";
		echo "	</td>";
		echo "</tr>";

		echo "<tr>";
		echo "	<td>";
		echo "		Employee Name :";
		echo "	</td>";
		echo "	<td>";
		echo  "		<input type=text name='strEmpFieldName' value='$strEmpFieldNameVal' size=$nSize maxlength=$nMaxLength readonly>";
		echo "	</td>";
		echo "</tr>";

		echo"	<script>
					function CalEmpPop(sInputName,strEmpFieldName)
					{
						window.open('/include/code/employee_lookup.php?strFieldName='+sInputName+'&strEmpFieldName='+strEmpFieldName, 'CalPop', 'toolbar=0,width=400,height=400, scrollbars=yes');
					}
				</script>
			";
	}


	function fnWorkingMsg()
	{
		echo"<b>working on your request .... </b>";
	}

	//Shows all year from 1900 to onward
	function fnYearCombo($strLeftColName, $strComboName,$nStartYear="1900", $nEndYear="2100" ,$nSelYear="")
	{
		echo"<tr>";
		echo"	<td>";
		echo"		$strLeftColName";
		echo"	</td>";
		echo"	<td>";
		echo"		<select name='$strComboName' >";

		while($nStartYear <= $nEndYear)
		{
			if($nSelYear == $nStartYear)
				echo"			<option value='$nStartYear' selected>$nStartYear";
			else
				echo"			<option value='$nStartYear'>$nStartYear";
			$nStartYear++;
		}
		echo"	</td>";
		echo"</tr>";
	}

	//Shows months of Year
	function fnMonthCombo($strLeftColName, $strComboName,$nStartMonth="1", $nEndMonth="12" ,$nSelYear="")
	{
		echo"<tr>";
		echo"	<td>";
		echo"		$strLeftColName";
		echo"	</td>";
		echo"	<td>";
		echo"		<select name='$strComboName' >";

		while($nStartMonth <= $nEndMonth)
		{
			if($nSelYear == $nStartMonth)
				echo"			<option value='$nStartMonth' selected>$nStartMonth";
			else
				echo"			<option value='$nStartMonth'>$nStartMonth";
			$nStartMonth++;
		}
		echo"	</td>";
		echo"</tr>";
	}


	//Total Days in month
	//SstrDate = "year-month-day"
	//
	function getTotalDaysInMonth($strDate)
	{
		$arrDate= getDateArray($strDate);
		$nYear = $arrDate['year'];
		$nMonth = $arrDate['month'];
		$nDay = $arrDate['day'];

		$nTimeStamp = strtotime("$nYear-$nMonth-$nDay");//return unix time stamp

		if($nMonth == 1)
		{
			$nDays = 31;
		}
		else if($nMonth == 2)
		{
			$nLeapYear = date("L",$nTimeStamp);
			if($nLeapYear == 0)
			{
				$nDays = 28;
			}
			else
			{
				$nDays = 29;
			}

		}
		else if($nMonth == 3)
		{
			$nDays = 31;
		}
		else if($nMonth == 4)
		{
			$nDays = 30;
		}
		else if($nMonth == 5)
		{
			$nDays = 31;
		}
		else if($nMonth == 6)
		{
			$nDays = 30;
		}
		else if($nMonth == 7)
		{
			$nDays = 31;
		}
		else if($nMonth == 8)
		{
			$nDays = 31;
		}
		else if($nMonth == 9)
		{
			$nDays = 30;
		}
		else if($nMonth == 10)
		{
			$nDays = 31;
		}
		else if($nMonth == 2)
		{
			$nDays = 30;
		}
		else if($nMonth == 2)
		{
			$nDays = 31;
		}

		return $nDays;
	}

	function getNumOfNormalHolidayInMonth($strMonth,$strYear,$strHolidayDay,$stMaxDaysOfMonth)
	{
		$count = 0;
		for($i=1;$i<=$stMaxDaysOfMonth; $i++)
		{
			//$nNo= strtotime("$i $strMonth $strYear");
			$strDay = strtolower(date("l",mktime(0,0,0,$strMonth,$i,$strYear)));

			if($strHolidayDay==$strDay)
				$count++;
		}

		return $count;
	}
	function getWasteItemDetail($masterid)
	{
		$strQuery ="SELECT itm_stock_coa_code , itm_id,itm_name,itm_Avg_price,itm_Stock FROM tblItemMaster
					WHERE itm_master_id =$masterid
					AND itm_type =4";
		$nWasteResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nWasteResult);

		$arr['waste_itm_id'] = $nRows['itm_id'];
		$arr['waste_itm_stock_coa_code'] = $nRows['itm_stock_coa_code'];
		$arr['waste_itm_name'] = $nRows['itm_name'];
		$arr['waste_itm_Avg_price'] = $nRows['itm_Avg_price'];
		$arr['waste_itm_Stock'] = $nRows['itm_Stock'];



		return $arr;
	}
	function UpdateItemStockAvgPrice($nItemID,$NewStock,$NewCostRate)
	{
		$strQuery ="select itm_Avg_price,itm_Stock from tblItemMaster
					where itm_id=$nItemID";

		$nResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nResult);
		$nOldStock = $nRows['itm_Stock'];
		$nOldRate = $nRows['itm_Avg_price'];
		$nTotalCost =($nOldStock*$nOldRate)+($NewStock*$NewCostRate);
		$nGrandNewStock=$nOldStock+$NewStock;
		$nGrandAvgPrice=$nTotalCost/$nGrandNewStock;

		$strQuery ="Update tblItemMaster set itm_Avg_price=$nGrandAvgPrice,itm_Stock=$nGrandNewStock
					where itm_id=$nItemID";

		$nResult = MySQLQuery($strQuery);
		$strMsg ="Completed";
		return $strMsg;
	}

	function UpdateItemStockAvgPricePurachseReturn($nItemID,$nReturnStocKG,$nReturnCostRateKG)
	{
		$strQuery ="Select Sum(ith_qty_in_unit) -sum(ith_qty_out_unit) as qtyinhand,Sum(ith_qty_in_unit*ith_cost_per_unit)-Sum(ith_qty_out_unit*ith_cost_per_unit) as itemCost from tblItemTransHistory
					where ith_itm_id = $nItemID ";


		$nResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nResult);
		$nCostitem = $nRows['itemCost'];
		$nInHandQty = $nRows['qtyinhand'];
		$nReturnCost = $nReturnStocKG * $nReturnCostRateKG;
		$nRemainingCost = $nCostitem - $nReturnCost;
		$nRemainingQty =  $nInHandQty - $nReturnStocKG;
		$nNewAvGPrice = round($nRemainingCost/$nRemainingQty,2);

		$strQuery ="Update tblItemMaster set itm_Avg_price=$nNewAvGPrice,itm_Stock=$nRemainingQty
					where itm_id=$nItemID";

		$nResult = MySQLQuery($strQuery);
		$strMsg ="Completed";
		return $strMsg;
	}
	function getBatchDescNum($nPdoID)
	{
		$strQuery ="SELECT bat_number_desc FROM  tblPdoBatchMaster
					WHERE bat_pdo_id=$nPdoID";
		$nBatcResult = MySQLQuery($strQuery);
		$nRows = mysql_fetch_array($nBatcResult);

		return $nRows['bat_number_desc'];
	}
	function GetMonth($nomonth)
	{
		$StrMonth;
		if ($nomonth == 1)
		{
			$StrMonth	=	"January";
		}
		else if ($nomonth == 2)
		{
			$StrMonth	=	"Februray";
		}
		else if ($nomonth == 3)
		{
			$StrMonth	=	"March";
		}
		else if ($nomonth == 4)
		{
			$StrMonth	=	"April";
		}
		 else if ($nomonth == 5)
		{
			$StrMonth	=	"May";
		}
		else if ($nomonth == 6)
		{
			$StrMonth	=	"June";
		}
		 else if ($nomonth == 7)
		{
			$StrMonth	=	"July";
		}
		else if ($nomonth == 8)
		{
			$StrMonth	=	"August";
		}
		else if ($nomonth == 9)
		{
			$StrMonth	=	"September";
		}
		else if ($nomonth == 10)
		{
			$StrMonth	=	"October";
		}
		 else if ($nomonth == 11)
		{
			$StrMonth	=	"November";
		}
		else if ($nomonth == 12)
		{
			$StrMonth	=	"December";
		}

		return $StrMonth;
	}
	MySQLConnect();

	function Heading($strLabel)
	{
		echo "<span style='font-size: 12pt; font-weight: bold; color: black;'>$strLabel</span><br><img src=/onlinepayment/admin/images/1.gif height=5><br><img src=/onlinepayment/admin/images/blue-horz-line.jpg><br>";
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
		$nResult = MySQLQuery($strQuery);
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

					while($rstRow = mysql_fetch_array($nResult))
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

					while($rstRow = mysql_fetch_array($nResult))
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
	function TableComboQryWithTDOnly($strLabel, $strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MySQLQuery($strQuery);
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

					while($rstRow = mysql_fetch_array($nResult))
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

					while($rstRow = mysql_fetch_array($nResult))
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



	function TableComboQryExtraTD($strLabel, $strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MySQLQuery($strQuery);
		echo "<tr>
					<td width='20%'>
						&nbsp;
					</td>";

			echo "<td width='20%'>".
					$strLabel;
		echo    "</td>
				<td width='60%'>";
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0000>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000>--------------- \r\n";

					while($rstRow = mysql_fetch_array($nResult))
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

					while($rstRow = mysql_fetch_array($nResult))
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

	function YMDDateCombo($strLabel, $strField, $strDate)
	{//	echo "print--------".$strDate."<br>";
		$arrDate = explode("-",$strDate);
		// echo $arrDate[0]."-----".$arrDate[1]."------".$arrDate[2]."---------<br>";

		$strDate = strtok($strDate, " ");

		if(empty($strDate))
			$strDate = date("Y-m-d");

		$strYr = strtok($strDate, "-");
		$strMn = strtok("-");
		$strDy = strtok("-");

		$arrYr = array();
		$nDisplayMaxYear = date("Y") + 5;
		for($i = 1950; $i <= $nDisplayMaxYear-15; $i++)
			array_push($arrYr, $i);

		$arrDay = array();
		for($i = 1; $i <= 31; $i++)
			array_push($arrDay, $i);

		$strTemp = $strField . "Year";
		echo "<select name=$strTemp>";
		//echo "<option value=0 selected>  Year  \r\n";
		ComboBoxYearSelected($strYr, $arrYr, false);
		echo "</select>";

		echo "&nbsp;&nbsp;&nbsp;";

		$strTemp = $strField . "Month";
		echo "<select name=$strTemp>";
		//echo "<option value=0 selected>  Month  \r\n";
		$arr = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		ComboBoxWithText1($strMn-1, $arr, true);
		echo "</select>";

		echo "&nbsp;&nbsp;&nbsp;";

		$strTemp = $strField . "Date";
		echo "<select name=$strTemp>";
		//echo "<option value=0 selected>  Date  \r\n";

		ComboBoxWithText($strDy-1, $arrDay, true);
		echo "</select>";
	}
	function ComboBoxYearSelected($nSelectedVal, $arr, $bIndexValue)
	{
		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;
/*
			$selected = "";
			if($arr[$i] == date("Y"))
			{
				$selected = 'SELECTED';
			}
			echo "<option value=$arr[$i] $selected>" . $arr[$i] . "\r\n";
*/
			$selected = "";
			if($arr[$i] == $nSelectedVal)
			{
				$selected = 'SELECTED';
			}
			echo "<option value=$arr[$i] $selected>" . $arr[$i] . "\r\n";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	function ComboBoxWithText($nSelectedVal, $arr, $bIndexValue)
	{
		/*
		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;
			echo "<option value=$j>" . $arr[$i] . "\r\n";
		}
		*/

		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;
			$selected = "";

			if($arr[$i] == ($nSelectedVal+1))
			{
				$selected = 'SELECTED';
			}
			echo "<option value=$j $selected>" . $arr[$i] . "\r\n";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}

	function ComboBoxWithText1($nSelectedVal, $arr, $bIndexValue)
	{
		for($i=0; $i < sizeof($arr); $i++)
		{
			$j = $i+1;
			$selected = "";
			if($j == ($nSelectedVal+1))
			{
				$selected = 'SELECTED';
			}

	//		if(sizeof($arr) == 12 && $nSelectedVal == 11)
	//		{
	//			echo "<script>alert(34343);<//script>";
	//		}
			echo "<option value=$j $selected>". $arr[$i] . "\r\n";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}

	// update checkbox field.
	// Dated : 20-May-2010

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
		MySQLQuery($strQuery);
	}
	// Execute the Query and return array of records.
	// get parameter $sql and execute.
	function executeQuery($sql)
	{

	}

	function TableComboQuery($strQuery, $strDispField, $strIDField, $strName, $nSelId, $nAllUnDef = -1, $callback = '', $bIndexValue = true)
	{
		$nResult = MySQLQuery($strQuery);
				if( $bIndexValue == true )
				{

					echo "<select name=$strName $callback ><br>";
					if($nAllUnDef == 0)
						echo "<option value=0000>ALL \r\n";
					if($nAllUnDef == 1)
						echo "<option value=0000>--------- \r\n";

					while($rstRow = mysql_fetch_array($nResult))
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

					while($rstRow = mysql_fetch_array($nResult))
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
	/*
		the function draws combo box fitted in table row by
		using the function ComboBox();
	*/
	function ArraySelectBox($strName, $nSelectedVal, $arr, $bIndexValue, $callBack ='', $strDisabled="")
	{
		echo "<select name=$strName $callBack $strDisabled>";
			ComboBox($nSelectedVal, $arr, $bIndexValue);
		echo "</select>";
	}
	function DateOfBirthCombo($strLabel, $strField, $strDate)
	{
		$strDate = strtok($strDate, " ");
		if(empty($strDate))
			$strDate = date("Y-m-d");

		$strYr = strtok($strDate, "-");
		$strMn = strtok("-");
		$strDy = strtok("-");

		$arrYr = array();

		for($i = 1970; $i <= ($strYr-15); $i++)
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

		$strTemp = $strField . "Date";
		echo "<select name=$strTemp>";
		ComboBox($strDy-1, $arrDay, true);
		echo "</select>";
	}

	function TimeComboHrs($strField, $strTime)
	{
		$arrTime = explode(':', $strTime);
		$nHr = $arrTime[0];
		$nMn = $arrTime[1];
		$arrHr = array();

		for($i = 0; $i <= 23; $i++)
			array_push($arrHr, $i);

		$strHr = $strField;
		$strMn = $strField;
		$arrMn = array();
		$nCounter = 0;
		for($i = 0; $i <= 59; $i += 5)
		{
			array_push($arrMn, $i);
			if($i ==$nMn)
				$nShowMn = $nCounter;
			$nCounter++;
		}
		echo "<select name=$strHr>";
			ComboBox($nHr, $arrHr, true);
		echo "</select>";
	}

	function TimeComboMins($strField, $strTime)
	{
		$arrTime = explode(':', $strTime);

		// $nHr = $arrTime[0];
		$nMn = $arrTime[0];
		$arrHr = array();

		for($i = 0; $i <= 23; $i++)
			array_push($arrHr, $i);

		$strHr = $strField;
		$strMn = $strField;
		$arrMn = array();
		$nCounter = 0;
		for($i = 0; $i <= 59; $i += 5)
		{
			array_push($arrMn, $i);
			if($i ==$nMn)
				$nShowMn = $nCounter;
			$nCounter++;
		}
		echo "<select name=$strMn>";
			ComboBox($nMn, $arrMn, true);
		echo "</select>";
	}

	function CheckBox4($strLabel, $strName, $arrayChecked = array(0,0,0,0), $arrayShow = array(1,1,1,1) )
	{
		echo "<tr>
				<td class='textFieldLabelAControl'>
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
			echo "<td align=center><img src=/CMS/admin/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/CMS/admin/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/CMS/admin/images/empty.gif>";
		if($arrayShow[3] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_delete ".$check[3]."></td> ";
		else
			echo "<td align=center><img src=/CMS/admin/images/empty.gif>";

		echo "</tr>";
	}

	function CheckBox3($strLabel, $strName, $arrayChecked = array(0,0,0), $arrayShow = array(1,1,1) )
	{
		echo "<tr>
				<td class='textFieldLabelAControl'>
					$strLabel
				</td>";
		for($i = 0; $i<3; $i++)
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
			echo "<td align=center><img src=/CMS/admin/images/empty.gif>";
		if($arrayShow[1] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_add ".$check[1]."></td> ";
		else
			echo "<td align=center><img src=/CMS/admin/images/empty.gif>";
		if($arrayShow[2] == 1)
			echo "<td><input type=checkbox name= ".$strName ."_edit ".$check[2]."></td> ";
		else
			echo "<td align=center><img src=/CMS/admin/images/empty.gif>";

		echo "</tr>";
	}

	function getMaxVoucherNo()
	{
		$voucherNo = 0;
		$rowVoucher1 = mysql_fetch_array(MySQLQuery("SELECT MAX(IFNULL(payment_voucher_no,0)) AS VoucherNo FROM tblstudentfeepayment"));
		$rowVoucher2 = mysql_fetch_array(MySQLQuery("SELECT MAX(IFNULL(payment_voucher_no,0)) AS VoucherNo FROM tblstudentfeepaymentindividual"));
		$voucherNo = max($rowVoucher1['VoucherNo'],$rowVoucher2['VoucherNo']);
		$voucherNo = $voucherNo + 1;
		return $voucherNo;
	}

	function GetRecords($sql)
	{
		//echo $sql;
		$check=MySQLQuery($sql);
		$return=array();
		$i=0;
		while ($row = mysql_fetch_array($check))
		 {
			$return[$i]=$row;
			//echo "<br/> Addinng row <br/>";
			$i++;
		}
		return $return;
	}
	function getExtension($str)
	{
		 $i = strrpos($str,".");
		 if (!$i) { return ""; }
		 $l = strlen($str) - $i;
		 $ext = substr($str,$i+1,$l);
		 return $ext;
	 }

	 function passwordHashCode($userinput)
	 {
		$pass = urlencode($userinput);
		$pass_crypt = crypt($pass);
	 	return $pass_crypt;
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

	function current_user_region()
	{
		if(!isset($_SESSION))
			session_start();
		$userLoc = GetRecord("location_user", "id_user = ".$_SESSION['USER_ID']);

		return $userLoc['id_region'];

	}

	function current_user_territory()
	{
		if(!isset($_SESSION))
			session_start();
		$userLoc = GetRecord("location_user", "id_user = ".$_SESSION['USER_ID']);

		return $userLoc['id_territory'];

	}

	function current_user_country()
	{
		if(!isset($_SESSION))
			session_start();
		$userLoc = GetRecord("location_user", "id_user = ".$_SESSION['USER_ID']);
		$userLoc = GetRecord("region", "id = ".$userLoc['id_region']);
		return $userLoc['id_country'];

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

	function create_log($mstId, $cname, $note)
	{
		$arrVal = array(
                    "note_subject" => $cname,
                    "note_type" => 3,
                    "note_ref_id" => $mstId,
										"create_date" => date("Y-m-d H:i:s"),
                    "note" => $note,
										"id_user" => $_SESSION['USER_ID']
                   );
        InsertRec("note_detail", $arrVal);
	}

	function create_log_next_step($mstId, $cname, $note, $tipo)
	{
		$arrVal = array(
										"note_subject" => $cname,
										"note_type" => $tipo,
										"note_ref_id" => $mstId,
										"create_date" => date("Y-m-d H:i:s"),
										"note" => $note,
										"id_user" => $_SESSION['USER_ID']
									 );
				InsertRec("note_detail", $arrVal);
	}
	function log_actividad($modulo, $accion, $id_usuario, $mensaje){

		$arrVal = array(
										"id_module" => $modulo,
										"id_action" => $accion,
										"date_create" => date("Y-m-d H:i:s"),
										"description" => $mensaje,
										"status" => 1,
										"id_user" => $id_usuario
									 );
				InsertRec("log_activity", $arrVal);
	}

	function optener_category_roll($id_user, $id_category){
		$getRoleCategory = GetRecords("select * from acces_category where id_user = ".$id_user." and stat = 1");
		foreach ($getRoleCategory as $key => $value) {
				 if($value['id_category']==$id_category){
					 return "selected";
				 }else{
					 return "";
				 }
			}
	}

	function optener_category_roll_by_id($id_user){
		$getRoleCategory = GetRecords("select * from acces_category where id_user = ".$id_user." and stat = 1");
			 return $getRoleCategory;
		 }
?>

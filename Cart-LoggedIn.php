<?php

	//Db check (no.Quantity == no.CartItems)
	$DbCheck1 = mysql_query("SELECT CartArray FROM members WHERE Username='$SessionUsername'");
	while ($DbRow1 = mysql_fetch_assoc($DbCheck1)){
		@$DbCheckCA = $DbRow1['CartArray'];
		@$ECheckCA = explode(",", $DbCheckCA);
	}
	$DbCheck2 = mysql_query("SELECT CartQuantity FROM members WHERE Username='$SessionUsername'");
	while ($DbRow2 = mysql_fetch_assoc($DbCheck2)){
		@$DbCheckCQ = $DbRow2['CartQuantity'];
		@$ECheckCQ = explode(",", $DbCheckCQ);
	}
	@$DbCheckCount1 = count($ECheckCA);
	@$DbCheckCount2 = count($ECheckCQ);
	if ($DbCheckCount1 < $DbCheckCount2 || $DbCheckCount1 > $DbCheckCount2) {
		mysql_query("UPDATE members SET CartArray='' WHERE Username='$SessionUsername'");
		mysql_query("UPDATE members SET CartQuantity='' WHERE Username='$SessionUsername'");
		unset($_SESSION['CartArray']);
	}
	
	//New Cart Item || New array
	if (isset($_POST['ProductId'])) {
	    $ProductId = $_POST['ProductId'];
		$WasFound = false;
		$i = 0;
		if (!isset($_SESSION["CartArray"]) || count($_SESSION["CartArray"]) < 1) {
			$_SESSION["CartArray"] = array(0 => array("ItemId" => $ProductId, "Quantity" => 1));
		} else {
			foreach ($_SESSION["CartArray"] as $EachItem) { 
			    $i++;
			    while (list($Key, $Value) = each($EachItem)) {
					if ($Key == "ItemId" && $Value == $ProductId) {
						array_splice($_SESSION["CartArray"], $i-1, 1, array(array("ItemId" => $ProductId, "Quantity" => $EachItem['Quantity'] + 1)));
						$WasFound = true;
					}
			    }
		    } 
			if ($WasFound == false) {
				array_push($_SESSION["CartArray"], array("ItemId" => $ProductId, "Quantity" => 1));
			}
		}
		@$FirstRunAI = true;
		@$CartCountAI = count($_SESSION['CartArray']);
		for ($i=0; $i < $CartCountAI; $i++) { 
			if ($FirstRunAI == true) {
				$ArrayAI = array($_SESSION['CartArray'][$i]['ItemId']);
				$FirstRunAI = false;
			}else{
				array_push($ArrayAI , $_SESSION['CartArray'][$i]['ItemId']);
			}
			@$ArrayAddItem  = $ArrayAI;
		}
		@$FirstRunAI2 = true;
		@$CartCountAI2 = count($_SESSION['CartArray']);
		for ($i=0; $i < $CartCountAI2; $i++) { 
			if ($FirstRunAI2 == true) {
				$ArrayAI2 = array($_SESSION['CartArray'][$i]['Quantity']);
				$FirstRunAI2 = false;
			}else{
				array_push($ArrayAI2 , $_SESSION['CartArray'][$i]['Quantity']);
			}
			@$ArrayAddItem2  = $ArrayAI2;
		}
		@$CartImplodeAI = implode(',', $ArrayAddItem);
		mysql_query("UPDATE members SET CartArray='$CartImplodeAI' WHERE Username='$SessionUsername'");
		@$CartImplodeAI2 = implode(',', $ArrayAddItem2);
		mysql_query("UPDATE members SET CartQuantity='$CartImplodeAI2' WHERE Username='$SessionUsername'");
		
		header("location: Cart.php"); 
	    exit();
	}
	
	//Delete item
		if (isset($_POST['RemoveProduct']) && $_POST['RemoveProduct'] != "") {
	 	$RemoveProduct = $_POST['RemoveProduct'];
		if (count(@$_SESSION["CartArray"]) <= 1) {
			unset($_SESSION["CartArray"]);
		} else {
			unset($_SESSION["CartArray"]["$RemoveProduct"]);
			sort($_SESSION["CartArray"]);
		}
		@$FirstRunDI = true;
		@$CartCountDI = count($_SESSION['CartArray']);
		for ($i=0; $i < $CartCountDI; $i++) { 
			if ($FirstRunDI == true) {
				$ArrayDI = array($_SESSION['CartArray'][$i]['ItemId']);
				$FirstRunDI = false;
			}else{
				array_push($ArrayDI , $_SESSION['CartArray'][$i]['ItemId']);
			}
			@$ArrayDeleteItem  = $ArrayDI ;
		}
		@$FirstRunDI2 = true;
		@$CartCountDI2 = count($_SESSION['CartArray']);
		for ($i=0; $i < $CartCountDI2; $i++) { 
			if ($FirstRunDI2 == true) {
				$ArrayDI2 = array($_SESSION['CartArray'][$i]['Quantity']);
				$FirstRunDI2 = false;
			}else{
				array_push($ArrayDI2 , $_SESSION['CartArray'][$i]['Quantity']);
			}
			@$ArrayDeleteItem2  = $ArrayDI2;
		}
		//Deleteing the item from DB
		@$CartImplodeDI = implode(',', $ArrayDeleteItem);
		mysql_query("UPDATE members SET CartArray='$CartImplodeDI' WHERE Username='$SessionUsername'");
		//Deleteing the item's quantity from DB
		@$CartImplodeDI2 = implode(',', $ArrayDeleteItem2);
		mysql_query("UPDATE members SET CartQuantity='$CartImplodeDI2' WHERE Username='$SessionUsername'");
	}

	//Change the quantity
	if (isset($_POST['ChangeQuantity']) && $_POST['ChangeQuantity'] != "") {
		$ChangeQuantity = $_POST['ChangeQuantity'];
		$Quantity = $_POST['Quantity'];
		$Quantity = preg_replace('#[^0-9]#i', '', $Quantity);
		if ($Quantity >= 100) { $Quantity = 99; }
		if ($Quantity < 1) { $Quantity = 1; }
		if ($Quantity == "") { $Quantity = 1; }
		$i = 0;
		foreach ($_SESSION["CartArray"] as $EachItem) { 
			$i++;
			while (list($Key, $Value) = each($EachItem)) {
				if ($Key == "ItemId" && $Value == $ChangeQuantity) {
					array_splice($_SESSION["CartArray"], $i-1, 1, array(array("ItemId" => $ChangeQuantity, "Quantity" => $Quantity)));
				}
			}
		}
	$FirstRunQ = true;
	$CartCountQ = count($_SESSION['CartArray']);
	for ($i=0; $i < $CartCountQ; $i++) { 
		if ($FirstRunQ == true) {
			$ArrayQ = array($_SESSION['CartArray'][$i]['Quantity']);
			$FirstRunQ = false;
		}else{
			array_push($ArrayQ, $_SESSION['CartArray'][$i]['Quantity']);
		}
		$ArrayQuantity = $ArrayQ;
	}
	$CartImplode = implode(',', $ArrayQuantity);
	mysql_query("UPDATE members SET CartQuantity='$CartImplode' WHERE Username='$SessionUsername'");
	}
	
	//Clear the cart
	if (isset($_GET['Cart']) && $_GET['Cart'] == "Clear") {
		mysql_query("UPDATE members SET CartArray='' WHERE Username='$SessionUsername'");
		mysql_query("UPDATE members SET CartQuantity='' WHERE Username='$SessionUsername'");
	    unset($_SESSION["CartArray"]);
	    header( 'Location: Cart.php' );
	}

	//Getting the cart DB's
	$Query2 = mysql_query("SELECT CartArray FROM members WHERE Username='$SessionUsername'");
	while ($Row2 = mysql_fetch_assoc($Query2)){
		@$DbCartArray = $Row2['CartArray'];
		@$ECartArray = explode(",", $DbCartArray);
	}
	@$DbCount1 = count($ECartArray);
	$Query3 = mysql_query("SELECT CartQuantity FROM members WHERE Username='$SessionUsername'");
	while ($Row3 = mysql_fetch_assoc($Query3)){
		@$DbCartQuantity = $Row3['CartQuantity'];
		@$ECartQuantity = explode(",", $DbCartQuantity);
	}
	//Creating the main array
	if (!$DbCartQuantity == "" || !$DbCartArray == "") {
		$FirstRun = true;
		for ($i=0; $i < $DbCount1; $i++) {
			if ($FirstRun == true) {
				$Array = array($i => array("ItemId" => $ECartArray[$i], "Quantity" => $ECartQuantity[$i]));
				$FirstRun = false;
			}else{
				array_push($Array, array("ItemId" => $ECartArray[$i], "Quantity" => $ECartQuantity[$i]));
			}
			$_SESSION['CartArray'] = $Array;
		}
	}else{
		unset($_SESSION['CartArray']);
	}

	//Displaying the cart
	$CartDisplay = "";
	$CartTotal = "";
	$ProductIdArray = '';
	if (!isset($_SESSION["CartArray"]) || count($_SESSION["CartArray"]) < 1 || $_SESSION["CartArray"] == "") {
	  	$CartDisplay = "<p><center style='font-size: 20px; color: grey; padding-top:60px;'>Your cart is empty!</center></p>";
	} else {
		$i = 0;
	    foreach ($_SESSION["CartArray"] as $EachItem) {
			$ItemId = $EachItem['ItemId'];
			$Sql2 = mysql_query("SELECT * FROM products WHERE Id='$ItemId' LIMIT 1");
			while ($Row2 = mysql_fetch_array($Sql2)) {
				@$ProductName = ucfirst($Row2['ProductName']);
				@$ProductPrice = $Row2['Price'];
				@$ProductDetails = $Row2['Details'];
			}
			//Maths
			@$PriceTotal = $ProductPrice * $EachItem['Quantity'];
			@$CartTotal = $PriceTotal + $CartTotal;
			@$Quantity = $EachItem['Quantity'] + $Quantity;
			@$_SESSION['QuantityTotal'] = $Quantity;
			//Displayed money var
			@$DisProductPrice = number_format($ProductPrice,2);
			@$DisPriceTotal = number_format($PriceTotal,2);
			@$DisCartTotal = number_format($CartTotal,2);
			$x = $i + 1;
			$ProductIdArray .= "$ItemId-".$EachItem['Quantity'].",";
			$CartDisplay .= "
					<tr>
					  	<td id='CartTableContent' width='125px'><center><a href='Product.php?id=$ItemId'><img src='Style/Images/$ItemId.png' alt='$ProductName'/></a></center></td>
					    <td id='CartTableContent' width='300px' style='padding-left:30px;'><a href='Product.php?id=$ItemId'>$ProductName</a></td>
					    <td id='CartTableContent' >£$DisProductPrice</td>
					    <td id='CartTableContent' >
					    	<form id='form1' name='form1' method='post' action=''>
								<input name='Quantity' type='text' id='Textfield' value='".$EachItem['Quantity']."' size='1' maxlength='2' />
								<input type='submit' name='ChangeQuantity' id='Button' value='Change' />
								<input name='ChangeQuantity' type='hidden' value='".$ItemId."' />
							</form>
					    </td>
					    <td id='CartTableContent' >£$DisPriceTotal</td>
					    <td id='CartHeader'>
					    	<center>
					    		<form action='Cart.php' method='post'>
					    			<input id='CartButton' name='RemoveProduct'".$ItemId."' type='submit' value='Remove' />
					    			<input name='RemoveProduct' type='hidden' value='".$i."' />
					    		</form>
					    	</center>
					    </td>
					</tr>
			";
			$i++; 
	    }
	}

?>
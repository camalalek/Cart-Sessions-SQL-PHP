<?php

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
		header("location: Cart.php"); 
	    exit();
	}
	//Clear the cart
	if (isset($_GET['Cart']) && $_GET['Cart'] == "Clear") {
	    unset($_SESSION["CartArray"]);
	    header( 'Location: Cart.php' );
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
	}

	//Displaying the cart
	$CartDisplay = "";
	$CartTotal = "";
	$ProductIdArray = '';
	if (!isset($_SESSION["CartArray"]) || count($_SESSION["CartArray"]) < 1) {
	    $CartDisplay = "<p><center style='font-size: 20px; color: grey; padding-top:60px;'>Your cart is empty!</center></p>";
	} else {
		$i = 0; 
	    foreach ($_SESSION["CartArray"] as $EachItem) {
			$ItemId = $EachItem['ItemId'];
			$Sql = mysql_query("SELECT * FROM products WHERE Id='$ItemId' LIMIT 1");
			while ($Row = mysql_fetch_array($Sql)) {
				@$ProductName = ucfirst($Row['ProductName']);
				@$ProductPrice = $Row['Price'];
				@$ProductDetails = $Row['Details'];
			}
			//Maths
			$PriceTotal = $ProductPrice * $EachItem['Quantity'];
			$CartTotal = $PriceTotal + $CartTotal;
			@$Quantity2 = $EachItem['Quantity'] + $Quantity2;
			$_SESSION['QuantityTotal'] = $Quantity2;
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
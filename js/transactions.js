var pIndex = 0;
var transact = 'sale';

function setPrice() {
	product_id = $('#product_id').val();
	account_id = $('#account_id').val();
	product = getProduct(productsArray, product_id);
	selling_price = product.buying_price;
	units = $('#units').val();

	if(transact == 'purchase') {
		selling_price = product.buying_price;
		if(units == 'pack') selling_price = product.buying_price * product.pack_size;
	} else {
		if(account_id == 1) {
			selling_price = product.wholesale_price;
			if(units == 'pack') selling_price = product.wholesale_price * product.pack_size;
		} else {
			selling_price = product.retail_price;
			if(units == 'pack') selling_price = product.retail_price * product.pack_size;
		}	
	}
	$('#price').val(selling_price);
}

function setFinalPrice() {
	product_id = $('#final_product_id').val();
	pricegroup = 'Wholesale';
	product = getProduct(productsArray, product_id);

	selling_price = product.wholesale_price;
	units = $('#final_units').val();

	selling_price = product.wholesale_price;
	if(units == 'pack') selling_price = product.wholesale * product.pack_size;
	
	$('#final_price').val(selling_price);
}

function setUnits() {
	setTimeout(function() {
		product_id = $('#product_id').val();
		account_id = $('#account_id').val();
		product = getProduct(productsArray, product_id);
		
		if(product == null) {
			$('#units').html('<option>Units of measure</option>');
		} else {
			$('#units').html('<option value="each">'+product.units+'</option><option value="pack">'+product.pack+' of '+product.pack_size+'</option>');	
		}
		setPrice();
	}, 100);
}

function setFinalUnits() {
	setTimeout(function() {
		product_id = $('#final_product_id').val();
		product = getProduct(productsArray, product_id);
		
		if(product == null) {
			$('#final_units').html('<option>Units of measure</option>');
		} else {
			$('#final_units').html('<option value="each">'+product.units+'</option><option value="pack">'+product.pack+' of '+product.pack_size+'</option>');	
		}
		setFinalPrice();
	}, 100);
}

function addProduct(){
	product_id = $('#product_id').val();
	quantity = $('#quantity').val();
	units = $('#units').val();
	account_id = $('#account_id').val();
	
	if(product_id < 1) {
		alert('Please select a product');
		return;
	} 
	
	if(quantity.length < 1) {
		alert('Quantity must be a number');
		return;
	}
	
	if(quantity.length < 1) {
		alert('Quantity must be greater than 0');
		return;
	}
	
	product = getProduct(productsArray, product_id);
	qty = quantity;
	name = product.product_name;
	pack = product.pack;
	pack_size = product.pack_size;
	buying_price = product.buying_price;
	wholesale_price = product.wholesale_price;
	retail_price = product.retail_price;
	selling_price = $('#price').val();
	
	if(units == 'pack') {
		units = product.pack+'/'+product.pack_size;
		pack_size = product.pack_size;
		buying_price = product.buying_price * pack_size;
	} else {
		units = product.units;
		pack_size = 1;
	}
	
	amount = parseFloat(selling_price) * parseFloat(qty);

	if($('#'+product_id).length != 0) {
		alert(name+' already exist in the products list');
		return;
	}
	
	transactionsRow = '<tr id="'+product_id+'">'+
	'<td class="text-right">'+qty+'</td>'+
	'<td>'+name+'</td>'+
	'<td>'+units+'</td>'+
	'<td class="text-right">'+(parseFloat(selling_price)).formatMoney(0, '.', ',')+'</td>'+
	'<td class="text-right"><b>'+(parseFloat(amount)).formatMoney(0, '.', ',')+'</b></td>'+
	'<td class="text-right">'+
	'<span class="icon tipsy" data-placement="top" title="Remove" onclick="removeTransaction('+pIndex+', '+product_id+')">'+
	'<span class="text-muted"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span></span>'+
	'</td>'+
	'</tr>';
	
	thisProduct = new Product(product_id, name, units, pack, pack_size, qty, buying_price, selling_price);
	transactionProducts.push(thisProduct);

	$('#products_table').append(transactionsRow);
	$('.tipsy').tooltip();
	calculateTransaction();
	pIndex++;
}

function addFinalProduct(){
	product_id = $('#final_product_id').val();
	quantity = $('#final_quantity').val();
	units = $('#final_units').val();
	pricegroup = 'Wholesale';
	
	if(product_id < 1) {
		alert('Please select a product');
		return;
	} 
	
	if(quantity.length < 1) {
		alert('Quantity must be a number');
		return;
	}
	
	if(quantity.length < 1) {
		alert('Quantity must be greater than 0');
		return;
	}
	
	product = getProduct(productsArray, product_id);
	qty = quantity;
	name = product.product_name;
	pack = product.pack;
	pack_size = product.pack_size;
	buying_price = product.buying_price;
	wholesale_price = product.wholesale_price;
	retail_price = product.retail_price;

	if(units == 'pack') {
		units = product.pack+'/'+product.pack_size;
		pack_size = product.pack_size;
		selling_price = product.wholesale_price;
		selling_price = $('#final_price').val();
	} else {
		units = product.units;
		pack_size = 1;
		selling_price = $('#final_price').val();
	}
	
	if($('#final_'+product_id).length != 0) {
		alert(name+' already exist in the products list');
		return;
	}
	
	amount = parseFloat(selling_price) * parseFloat(qty);
	
	transactionsRow = '<tr id="final_'+product_id+'">'+
	'<td class="text-right">'+qty+'</td>'+
	'<td>'+name+'</td>'+
	'<td>'+units+'</td>'+
	'<td class="text-right">'+(parseFloat(selling_price)).formatMoney(0, '.', ',')+'</td>'+
	'<td class="text-right"><b>'+(parseFloat(amount)).formatMoney(0, '.', ',')+'</b></td>'+
	'<td class="text-right">'+
	'<span class="icon tipsy" data-placement="top" title="Remove" onclick="removeFinalTransaction('+pIndex+', '+product_id+')">'+
	'<span class="text-muted"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span></span>'+
	'</td>'+
	'</tr>';
	
	thisProduct = new Product(product_id, name, units, pack, pack_size, qty, buying_price, selling_price);
	transactionFinalProducts.push(thisProduct);
	
	$('#final_products_table').append(transactionsRow);
	$('.tipsy').tooltip();
	pIndex++;
	
	calculateFinalTransaction();
}

var subTotal = 0;
var transactionProducts = [];
var transactionFinalProducts = [];
function Product(pId, pName, pUnits, pPack, pPackSize, pQuantity, pBuyingPrice, pSellingPrice) {
	this.id = pId;
	this.name = pName;
	this.units = pUnits;
	this.pack = pPack;
	this.pack_size = pPackSize;
	this.buying_price = pBuyingPrice;
	this.selling_price = pSellingPrice;
	this.quantity = pQuantity;
	this.amount = this.selling_price * this.quantity;
}

function calculateTransaction() {
	subTotal = 0;
	vat = 0;
	discount = 0;
	payable = 0;
	paid = 0;
	change = 0;
	
	for (var item in transactionProducts) {
		subTotal += transactionProducts[item].amount;
	}
	
	$('#total').val((subTotal).formatMoney(0, '.', ','));
	if($("#check_vat").is(':checked')) vat = subTotal * 18 / 100;
	$('#vat').val((parseFloat(vat)).formatMoney(0, '.', ','));
	if(isNumeric(removeCommas($('#discount').val()))) {
		discount = removeCommas($('#discount').val()); 
	}
	payable = subTotal + vat - discount;
	$('#payable').val((parseFloat(payable)).formatMoney(0, '.', ','));
	if(isNumeric(removeCommas($('#paid').val()))) {
		paid = removeCommas($('#paid').val());
	}
	change = paid - payable;
	//if(change < 0) change = 0;
	$('#change').val((parseFloat(change)).formatMoney(0, '.', ','));
	
	$productsString = JSON.stringify(transactionProducts);
	$('#products_list').val($productsString);
}

function calculateFinalTransaction() {
	subTotal = 0;
	vat = 0;
	discount = 0;
	payable = 0;
	paid = 0;
	change = 0;
	
	for (var item in transactionFinalProducts) {
		subTotal += transactionFinalProducts[item].amount;
	}
	
	payable = subTotal + vat - discount;
	$('#final_payable').val((parseFloat(payable)).formatMoney(0, '.', ','));
	
	$productsString = JSON.stringify(transactionFinalProducts);
	$('#final_products_list').val($productsString);
}

function calculateChange() {
	payable = removeCommas($('#payable').val());
	paid = removeCommas($('#paid').val());

	change = paid - payable;
	$('#change').val((parseFloat(change)).formatMoney(0, '.', ','));
}

function removeTransaction(iIndex, pId)  {
	for(i=0; i<transactionProducts.length; i++) {
		if(transactionProducts[i].id == pId) transactionProducts.splice(i, 1);
	}
	$('#'+pId).fadeOut(500, function() {
		$('#'+pId).remove();
		calculateTransaction();
	});	
}

function removeFinalTransaction(iIndex, pId)  {
	for(i=0; i<transactionFinalProducts.length; i++) {
		if(transactionFinalProducts[i].id == pId) transactionFinalProducts.splice(i, 1);
	}
	$('#final_'+pId).fadeOut(500, function() {
		$('#final_'+pId).remove();
	});	
}

function getProduct(productsArray, productId) {
	for(i=0; i<productsArray.length; i++) {
		if(productsArray[i].product_id == productId) return productsArray[i];
	}
	return null;
}

function addQuantity(qty) {
	if($('#quantity').val().length < 1) $('#quantity').val(0);
	$('#quantity').val(parseFloat($('#quantity').val()) + parseFloat(qty));
	if($('#quantity').val() < 0) $('#quantity').val(0);
}

function removeCommas(num) {
	num = num.replace('.00', '');
	return parseFloat(num.replace(/[^0-9-.]/g, ''));
}

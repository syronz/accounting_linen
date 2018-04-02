$(document).ready(function(){
	var router = {};
	router.home = {file:'home', panel:'#homePanel'};
	router.examList = {file:'exam_list', panel:'#labPanel'};
	router.logout = {file:"logout"};
	router.userList = {file:'user', panel:'#settingPanel'};
	router.userActivity = {file:'user_activity', panel:'#reportPanel'};
	router.report = {file:'user_activity', panel:'#reportPanel'};
	router.permissionList = {file:'permission', panel:'#settingPanel'};
	router.backup = {file:'backup', panel:'#settingPanel'};
	router.logout = {file:'logout', panel:'#settingPanel'};
	router.cashList = {file:'cash', panel:'#fundPanel'};
	router.dollarRateList = {file:'dollar_rate', panel:'#fundPanel'};
	router.accountList = {file:'account', panel:'#fundPanel'};
	router.payinList = {file:'payin', panel:'#fundPanel'};
	router.payoutList = {file:'payout', panel:'#fundPanel'};
	router.stuffList = {file:'stuff', panel:'#productPanel'};
	router.catList = {file:'cat', panel:'#productPanel'};
	router.sellFactureDaily = {file:'sell_facture_daily', panel:'#sellPanel'};
	router.branchList = {file:'branch', panel:'#fundPanel'};
	router.sellFactureCustomerNew = {file:'sell_facture_customer_new', panel:'#sellPanel'};
	router.sellFactureCustomerList = {file:'sell_facture_customer_list', panel:'#sellPanel'};
	router.sellFactureCustomerListLoan = {file:'sell_facture_customer_list_loan', panel:'#sellPanel'};
	router.printSellFactureCustomer = {file:'print_sell_facture_customer', panel:'#sellPanel'};
	router.payinSellFactureCustomer = {file:'payin_sell_facture_customer', panel:'#sellPanel'};
	router.sellFactureReport = {file:'sell_facture_report', panel:'#sellPanel'};
	router.sellFactureReportDay = {file:'sell_facture_report_day', panel:'#sellPanel'};
	router.sellFactureCatDay = {file:'sell_facture_cat_day', panel:'#sellPanel'};
	router.sellStuffReport = {file:'sell_stuff_report', panel:'#sellPanel'};
	router.sellStuffReportDay = {file:'sell_stuff_report_day', panel:'#sellPanel'};
	router.accountBalanceReport = {file:'account_balance_report', panel:'#fundPanel'};
	$('a').click(function(){
		// routRefresh();
		// return false;
	});

	function showPanel(x){
		$('.sidePanel').hide();
		$(x).show();
	}

	function loadMain(arrHash,variable){
		showPanel(router[arrHash[0]].panel);
		// console.log(router[arrHash[0]].file);
		$('#main').load('include/'+router[arrHash[0]].file+'.php?id='+arrHash[1],function(response, status, xhr){
			$('#loading').css('display','none');
		});
	}

	$( "#search_form" ).submit(function( event ) {
		$('#jtableDiv').jtable('reload');
		event.preventDefault();
	});

	function routRefresh(){
		var str = window.location.hash;
		var str = str.substr(1);
		var arrHash = str.split('>');

		
		if(arrHash[0] == 'print'){
			window.history.back(); 
			var printDivCSS = new String ('<link href="scripts/jtable.2.3.1/themes/metro/blue/jtable.css" rel="stylesheet" type="text/css" />'+'<link href="css/printStyle.css" rel="stylesheet">');
			// var styles = '<style> td{text-decoration:none;} </style>';
			window.frames["print_frame"].document.body.innerHTML=printDivCSS + document.getElementById("main").innerHTML;
			window.frames["print_frame"].window.focus();
			window.frames["print_frame"].window.print();
		}else{
			loadMain(arrHash);
		}
	}

	$(window).on('hashchange', function(){
		routRefresh();
	});

	window.location.hash = 'home';


	/************************************************* START backup & restore */

	$('body').on('click','.doRestore',function(){
		// alert($(this).attr('name'));
		$('#btnProgressRestore').trigger('click');
		$('#restoreProgressBar').css({width:'0'});
		$('#restoreProgressBar').animate({width:'100%'},'slow',function(){
			// $('#modalProgressBar').hide();
			// modalProgressBar

		});
		$.get('control/backup.control.php?action=restore',{fileName:$(this).attr('name')},function(){
			console.log('restore complete');
		});
	});

	/************************************************* END backup & restore  */


	/************************************************* START sell_facture_customer */
$('body').on('change','.categoryListSellFacture',function(){
	var element = $(this).parent().parent().find('.stuffListSellFacture');
	var idCat = $(this).val();
	$.get('control/stuff.control.php?action=stuffPerCat&',{idCat:idCat},function(result){
		element.html(result);
	});

	var element2 = $(this).parent().parent().find('.hiddenM2');
	$.get('control/cat.control.php?action=getM2&',{idCat:idCat},function(result){
		element2.val(result);
	});
});

$('body').on('change','.stuffListSellFacture',function(){
	var element = $(this).parent().parent().find('.price');
	var idStuff = $(this).val();
	$.get('control/stuff.control.php?action=stuffPrice&',{idStuff:idStuff},function(result){
		element.val(result);
		console.log(result);
	});
});

// $('body').on('focus','.stuffDetail',function(){
//     $(this).css('position','absolute');
//     $(this).animate({width:'350px',marginLeft:'-200px'},'fast');
// });
// $('body').on('blur','.stuffDetail',function(){
//     $(this).animate({width:'170px',marginLeft:'0'},'fast');
// });

$('body').on('click keypress keyup change','.stuffWidth,.stuffHeight,.stuffQTY,.stuffListSellFacture,.categoryListSellFacture, #prePayment, .price, .stuffDetail',function(){
	calculateFacture();
});

var stuff = {};

function calculateFacture(){
	stuff = {};
	var cats = $('.categoryListSellFacture');
	var catCount = cats.length;
	var stuffs = $('.stuffListSellFacture');
	var widths = $('.stuffWidth');
	var heights = $('.stuffHeight');
	var qtys = $('.stuffQTY');
	var prices = $('.price');
	var sumTotalPrice = 0;
	var i = 0;
	

	cats.each(function(index){

		var price = $(this).parent().parent().find('.price').val();
		var minM2 = $(this).parent().parent().find('.hiddenM2').val();
		var idStuff = $(this).parent().parent().find('.stuffListSellFacture').val();
		var width = parseInt($(this).parent().parent().find('.stuffWidth').val());
		var height = parseInt($(this).parent().parent().find('.stuffHeight').val());
		var qty = parseInt($(this).parent().parent().find('.stuffQTY').val());
		var detail = $(this).parent().parent().find('.stuffDetail').val();

		var m2 = Math.round(width * height / 100)/100;
		if(m2 < minM2)
			m2 = minM2;

		$(this).parent().parent().find('.m2').html(m2);
		var totalPrice =  Math.round(m2 * price * qty * 100) / 100;
		$(this).parent().parent().find('.totalPrice').html(totalPrice);
		if(!isNaN(totalPrice))
			sumTotalPrice += parseFloat(totalPrice);
		$(this).parent().parent().find('.m2').html(m2 * qty);

		stuff[i] = {};
		stuff[i]['idCat'] = $(this).val();
		stuff[i]['idStuff'] = idStuff;
		stuff[i]['m2'] = m2;
		stuff[i]['width'] = width;
		stuff[i]['height'] = height;
		stuff[i]['price'] = price;
		stuff[i]['qty'] = qty;
		stuff[i]['price'] = price;
		stuff[i]['totalPrice'] = totalPrice;
		stuff[i]['detail'] = detail;
		i++;
		
	});
	$('#sumTotalPrice').html(sumTotalPrice);
	var prePayment = $('#prePayment').val();
	$('#remainedMoney').html(sumTotalPrice - parseFloat(prePayment));
}

$('body').on('click','.categoryListSellFacture:last',function(){
	var element = $(this).parent().parent();
	element.clone().insertAfter(element);
});

$('body').on('click','#submitSellFactureCustomer',function(){
	var thisElement = $(this);
	thisElement.attr('disabled','disabled');
	var customer = {};
	customer.name = $('#customerName').val();
	customer.phone = $('#customerPhone').val();
	customer.address = $('#customerAddress').val();
	customer.id = $('#customerId').val();
	var money = {};
	money.totalPrice = $('#sumTotalPrice').html();
	money.prePayment = $('#prePayment').val();
	console.log(stuff);
	$.get('control/sell_facture_customer.control.php?action=create&',{customer:customer,data:stuff,money:money},function(result){
		console.log(result);
		var msg = JSON.parse(result);
		if(msg.Result == 'NO'){
			$('#sellFactureCustomerError').css('display','inline').text(msg.Message);
			thisElement.removeAttr('disabled');
		}
		else if(msg.Result == 'OK'){
			window.location.hash = '#printSellFactureCustomer>'+msg.Record.id;
		}
		else
			$('#sellFactureCustomerError').css('display','inline').text('<?php dic_show("Database ERROR!") ?>');
	});
});

function checkIdCustomer(idCustomer){
	$.get('control/account.control.php?action=customerId',{data:idCustomer},function(result){
		console.log(result);
		if(result){
			$('#customerIdSuccess').css('display','inline').text(result);
			$('#customerIdWarning').css('display','none');
		}
		else{
			$('#customerIdWarning').css('display','inline');
			$('#customerIdSuccess').css('display','none');
		}
	});
}

$('body').on('change keyup keydown blur','#customerId',function(){
	checkIdCustomer($(this).val());
});

$('body').on('click change','#customerRegisteredName',function(){
	$('#customerId').val($(this).val());
	checkIdCustomer($(this).val());
	//$('#patientId');
});



	/************************************************* END sell_facture_customer  */

/*----------------------------------------- start sell_facture_daily  */
$('body').on('click','#sendToBranchListFacture',function(){
	var m2 = $('.hiddenM2').val();
	var id_cat= $('.categoryListSellFacture').val();
	var id_stuff= $('.stuffListSellFacture').val();
	var detail= $('.stuffDetail').val();
	var width= $('.stuffWidth').val();
	var height= $('.stuffHeight').val();
	var qty= $('.stuffQTY').val();
	var price= $('.price').val();
	var id_account= $('.idAccount').val();

	var data = {m2:m2,id_cat:id_cat,id_stuff:id_stuff,detail:detail,width:width,height:height,qty:qty,price:price,id_account:id_account};

	$.post('control/sell_facture_daily.control.php?action=create',data,function(result){
		// console.log(result);
		$('#jtableDiv').jtable('reload');
	});

	$('.hiddenM2').val('');
	$('.stuffListSellFacture').val('');
	$('.stuffDetail').val('');
 	$('.stuffWidth').val('');
	$('.stuffHeight').val('') 
	$('.stuffQTY').val('');
 	$('.price').val('');


	// console.log(m2);
	//$('#patientId');
});


/*----------------------------------------- end sell_facture_daily  */

});




window.setTimeout("updateTime()", 0);
window.setInterval("updateTime()", 1000);
function updateTime() {
	document.getElementById("theTimer").firstChild.nodeValue =
	new Date().toTimeString().substring(0, 5);
}
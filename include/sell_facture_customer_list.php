<?php
	require_once '../class/database.class.php';
	if(!isset($_SESSION['user']))
		$db->go_to_login();
?>
	<div id="testShow">
	</div>
	<div id="jtableDiv" style="width:846px;"></div>
	<script type="text/javascript">
		$(document).ready(function () {

			$('#jtableDiv').jtable({
				title: '<?php dic_show("Sell Facture Customer"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/sell_facture_customer.control.php?action=list',
					createAction: false,
					updateAction: 'control/sell_facture_customer.control.php?action=update',
					deleteAction: 'control/sell_facture_customer.control.php?action=delete'
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%'
					},	
					id_account: {
						title: '<?php dic_show('Branch'); ?>',
						options : 'control/account.control.php?action=json_list'
					},
					id_user: {
						title: '<?php dic_show('User'); ?>',
						options : 'control/user.control.php?action=json_list',
						create: false,
						edit: false,
					},
					type: {
						title: '<?php dic_show('Type'); ?>'
					},	
					state: {
						title: '<?php dic_show('State'); ?>'
					},			
					pre_payment: {
						title: '<?php dic_show('pre_payment'); ?>',
					},
					total_price: {
						title: '<?php dic_show('total_price'); ?>',
					},
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false,
						// list:false
					},
					print:{
						title: '',
						width: '2%',
						sorting: false,
						edit: false,
						create: false,
						display: function (std) {
							var $img = $('<a href="#printSellFactureCustomer>'+std.record.id+'"><img src="images/small/print0.png" title="<?php dic_show('Print Facture'); ?>" class="printExamIcon" /></a>');
							return $img;
						}
					},
					payin:{
						title: '',
						width: '2%',
						sorting: false,
						edit: false,
						create: false,
						display: function (std) {
							var $img = $('<a href="#payinSellFactureCustomer>'+std.record.id+'"><img src="images/small/dollar0.png" title="<?php dic_show('Payin'); ?>" class="printExamIcon" /></a>');
							return $img;
						}
					},
					
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
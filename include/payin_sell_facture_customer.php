<?php
	require_once '../class/database.class.php';
	if(!isset($_SESSION['user']))
		$db->go_to_login();

	require_once '../class/account.class.php';
	require_once '../class/cat.class.php';
	require_once '../class/sell_facture_customer.class.php';

	$factureInfo = sell_facture_customer::sellFactureCustomerInfo($_GET['id']);
	$customerInfo = account::rowInfoArray('account',$factureInfo['head']['id_account']);
	// dsh($customerInfo);
	$userInfo = account::rowInfoArray('user',$factureInfo['head']['id_user']);
?>
	<div id="testShow">
	</div>

<style>
.tblName{
	width: 100%;
}
.tblName td{
	width: 12.5%;
	font-family: arial;
	font-size: 14px;
}


</style>


<table class="tblName table">
	<tr>
		<td><?php dic_show('Seller Name: '); ?></td>
		<td><?php echo $userInfo['name']; ?></td>
		<td></td>
		<td><?php dic_show('Date'); ?></td>
		<td><?php echo substr($factureInfo['head']['date'],0,10); ?></td>
	</tr>
	<tr>
		<td><?php dic_show('Customer Name: '); ?></td>
		<td><?php echo $customerInfo['name']; ?></td>
		<td></td>
		<td><?php dic_show('Customer Phone: '); ?></td>
		<td><?php echo $customerInfo['phone']; ?></td>
	</tr>
	<?php if($customerInfo['address']): ?>
	<tr>
		<td><?php dic_show('Address: '); ?></td>
		<td colspan="4"><?php echo $customerInfo['address']; ?></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td><?php dic_show('State: '); ?></td>
		<td colspan="4"><?php dic_show($factureInfo['head']['state']); ?></td>
	</tr>
</table>
<br>
	<div id="jtableDiv" style="width:846px;"></div>
	<script type="text/javascript">
		$(document).ready(function () {

			$('#jtableDiv').jtable({
				title: '<?php dic_show("Payin For Sell Facture"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id ASC',
				actions: {
					listAction: 'control/payin.control.php?action=listSellFactureCustomerPayin&idSellFacture=<?php echo $_GET['id'];?>',
					createAction: 'control/payin.control.php?action=createForFacture&idSellFacture=<?php echo $_GET['id'];?>&idAccount=<?php echo $customerInfo['id'];?>',
					updateAction: 'control/payin.control.php?action=update',
					deleteAction: 'control/payin.control.php?action=delete'
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%'
					},						
					id_user: {
						title: '<?php dic_show('User'); ?>',
						options: 'control/user.control.php?action=json_list',
						create: false,
						edit: false,
					},
					id_account: {
						title: '<?php dic_show('Account'); ?>',
						options: 'control/account.control.php?action=json_list',
						create: false,
						edit: false,
					},
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false
					},
					dollar: {
						title: '<?php dic_show('$'); ?>',
					},
					dinar: {
						title: '<?php dic_show('IQD'); ?>',
						create: false,
						edit: false,
						list:false
					},
					dollar_rate: {
						title: '<?php dic_show('$ rate'); ?>',
						create: false,
						edit: false,
						list:false
					},
					id_fund: {
						title: '<?php dic_show('id_fund'); ?>',
						create: false,
						edit: false,
						list:false
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>',
					},
					not_add_to_cash: {
						title: '<?php dic_show(""); ?>',
						type: 'checkbox',
                		values: { 1: '<?php dic_show("Not Add To Cash"); ?>', 0: '<?php dic_show("Not Add To Cash"); ?>' },
                		list: false
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
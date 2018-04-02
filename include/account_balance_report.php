<?php
	require_once '../class/database.class.php';
	if(!isset($_SESSION['user']))
		$db->go_to_login();
	$idAccount = $_GET['id'];
	$account = database::rowInfoObject('account',$idAccount);
	// dsh($account);
?>
<style>
.jtable-command-column , .jtable-command-column-header{
	display: none;
}
</style>
	<div id="testShow">
	</div>
	<div id="jtableDiv" style="width:846px;"></div>
	<script type="text/javascript">
		$(document).ready(function () {

			$('#jtableDiv').jtable({
				title: '<?php dic_show("All Transaction For Account : "); echo $account->name; ?>',
				paging: true,
				pageSize: 10,
				sorting: false,
				// defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/account.control.php?action=accountBalanceReport&idAccount=<?php echo $idAccount;?>',
					createAction: false,
					updateAction: false,
					deleteAction: false
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%',
						sorting:false
					},	
					description: {
						title: '<?php dic_show('Description'); ?>',
						sorting:false
					},
					id_user: {
						title: '<?php dic_show('User'); ?>',
						options : 'control/user.control.php?action=json_list',
						create: false,
						edit: false,
					},
					date: {
						title: '<?php dic_show('Date'); ?>',
						sorting:false
					},
					payin: {
						title: '<?php dic_show('PayIN'); ?>',
					},
					payout: {
						title: '<?php dic_show('PayOut'); ?>',
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>',
						sorting:false
					},	
					balance: {
						title: '<?php dic_show('Balance'); ?>',
						sorting:false
					},			
					
							
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
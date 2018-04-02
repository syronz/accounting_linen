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
				title: '<?php dic_show("Payin"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/payin.control.php?action=list',
					createAction: 'control/payin.control.php?action=create',
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
						options: 'control/account.control.php?action=json_listWithType',
					},
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false
					},
					dollar: {
						title: '<?php dic_show('$'); ?>',
					},
					// dinar: {
					// 	title: '<?php dic_show('IQD'); ?>',
					// },
					// dollar_rate: {
					// 	title: '<?php dic_show('$ rate'); ?>',
					// 	create: false,
					// 	edit: false
					// },
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
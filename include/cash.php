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
				title: '<?php dic_show("Cash"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/cash.control.php?action=list',
					createAction: 'control/cash.control.php?action=create',
					updateAction: 'control/cash.control.php?action=update',
					deleteAction: 'control/cash.control.php?action=delete'
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
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false
					},
					type: {
						title: '<?php dic_show('Type'); ?>',
						create: false,
						edit: false
					},
					id_f: {
						title: '<?php dic_show('#'); ?>',
						create: false,
						edit: false,
						list:false
					},
					dollar: {
						title: '<?php dic_show('$'); ?>',
					},
					dinar: {
						title: '<?php dic_show('IQD'); ?>',
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>',
					},
					box_dollar: {
						title: '<?php dic_show('box $'); ?>',
						create: false,
						edit: false
					},
					box_dinar: {
						title: '<?php dic_show('box IQD'); ?>',
						create: false,
						edit: false
					},
					dollar_rate: {
						title: '<?php dic_show('dollar_rate'); ?>',
						create: false,
						edit: false,
						list:false
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
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
				title: '<?php dic_show("Dollar Price\'s"); ?>',
				paging: true,
				pageSize: 20,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/dollar_rate.control.php?action=list',
					createAction: 'control/dollar_rate.control.php?action=create',
					updateAction: 'control/dollar_rate.control.php?action=update',
					deleteAction: 'control/dollar_rate.control.php?action=delete'
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
					price: {
						title: '<?php dic_show('Price'); ?>',
					},
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>'
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
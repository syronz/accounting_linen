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
				title: '<?php dic_show("User\'s List"); ?>',
				paging: true,
				pageSize: 20,
				sorting: true,
				defaultSorting: 'id ASC',
				actions: {
					listAction: 'control/user.control.php?action=list',
					createAction: 'control/user.control.php?action=create',
					updateAction: 'control/user.control.php?action=update',
					deleteAction: 'control/user.control.php?action=delete'
				},
				fields: {
						id: {
							key: true,
							create: false,
							edit: false,
							title: '<?php dic_show('id'); ?>',
							width: '5%'
						},						
						name: {
							title: '<?php dic_show('user'); ?>',
						},
						username: {
							title: '<?php dic_show('username'); ?>',
						},
						password: {
							title: '<?php dic_show('password'); ?>',
							width: '15%',
							list:false
						},
						id_permission: {
							title: '<?php dic_show('id_permission'); ?>',
							options: 'control/permission.control.php?action=json_list'
						},
						phone: {
							title: '<?php dic_show('phone'); ?>'
						},
						register_date:{
							title: '<?php dic_show('register_date'); ?>',
							type: 'date',
							create: false,
							edit: false,
						}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
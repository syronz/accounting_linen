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
				title: '<?php dic_show("Permission\'s List"); ?>',
				paging: true,
				pageSize: 20,
				sorting: true,
				defaultSorting: 'id ASC',
				actions: {
					listAction: 'control/permission.control.php?action=list',
					createAction: 'control/permission.control.php?action=create',
					updateAction: 'control/permission.control.php?action=update',
					deleteAction: 'control/permission.control.php?action=delete'
				},
				fields: {
						id: {
							key: true,
							create: false,
							edit: false,
							title: '<?php dic_show('#'); ?>',
							width: '5%'
						},
						name: {
							title: '<?php dic_show('Name'); ?>',
						},						
						permission: {
							title: '<?php dic_show('Perm'); ?>',
						},
						
						user: {
							title: '<?php dic_show('User'); ?>',
						},
						user_activity: {
							title: '<?php dic_show('Activity'); ?>'
						}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
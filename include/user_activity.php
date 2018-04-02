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
				title: '<?php dic_show("User Activity\'s List"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/user_activity.control.php?action=list',
					createAction: false,
					updateAction: false,
					deleteAction: false
				},
				fields: {
						id: {
							key: true,
							create: false,
							edit: false,
							title: '<?php dic_show('#'); ?>',
							width: '5%'
						},						
						ip: {
							title: '<?php dic_show('IP'); ?>',
						},
						id_user: {
							title: '<?php dic_show('User'); ?>',
							options: 'control/user.control.php?action=json_list',
							width: '10%'
						},
						date: {
							title: '<?php dic_show('date'); ?>'
						},
						action: {
							title: '<?php dic_show('action'); ?>',
						},
						detail: {
							title: '<?php dic_show('detail'); ?>',
						}						
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
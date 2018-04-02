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
				title: '<?php dic_show("Category List"); ?>',
				paging: true,
				pageSize: 20,
				sorting: true,
				defaultSorting: 'id ASC',
				actions: {
					listAction: 'control/cat.control.php?action=list',
					createAction: 'control/cat.control.php?action=create',
					updateAction: 'control/cat.control.php?action=update',
					deleteAction: 'control/cat.control.php?action=delete'
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
						title: '<?php dic_show('Name'); ?>',
					},
					less: {
						title: '<?php dic_show('Type'); ?>',
						options: {0:'NOT Less',1:'Less'}
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>'
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
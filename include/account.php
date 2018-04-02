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
				title: "<?php dic_show('Account\'s List'); ?>",
				paging: true,
				pageSize: 20,
				sorting: true,
				defaultSorting: 'id ASC',
				actions: {
					listAction: 'control/account.control.php?action=list',
					createAction: 'control/account.control.php?action=create',
					updateAction: 'control/account.control.php?action=update',
					deleteAction: 'control/account.control.php?action=delete'
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
					type: {
						title: '<?php dic_show('Type'); ?>',
						options: {customer:'customer',user:'user',branch:'branch',expense:'expense'}
					},
					phone: {
						title: '<?php dic_show('Phone'); ?>',
					},
					date: {
						title: '<?php dic_show('Register Date'); ?>',
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
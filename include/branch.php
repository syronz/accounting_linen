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
				title: "<?php dic_show('Branch\'s List'); ?>",
				paging: true,
				pageSize: 20,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/account.control.php?action=branchLists',
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
						options: {branch:'branch'}
					},
					phone: {
						title: '<?php dic_show('Phone'); ?>',
					},
					date: {
						title: '<?php dic_show('Register Date'); ?>',
						create: false,
						edit: false
					},
					balance: {
						title: '<?php dic_show('Balance'); ?>',
						create: false,
						edit: false
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>'
					},
					print:{
						title: '',
						width: '2%',
						sorting: false,
						edit: false,
						create: false,
						display: function (std) {
							var $img = $('<a href="#accountBalanceReport>'+std.record.id+'"><img src="images/small/print0.png" title="<?php dic_show('Print Facture'); ?>" class="printExamIcon" /></a>');
							return $img;
						}
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
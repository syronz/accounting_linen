<?php
	require_once '../class/database.class.php';
	if(!isset($_SESSION['user']))
		$db->go_to_login();
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
				title: '<?php dic_show("Sell Facture Report [By Day]"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/sell_facture_report.control.php?action=reportListDay',
					createAction: false,
					updateAction: false,
					deleteAction: false
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('Day'); ?>',
						width: '5%'
					},	
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false,
						sorting:false
						// list:false
					},
					sell_branch: {
						title: '<?php dic_show('sell_branch'); ?>',
						create: false,
						edit: false,
						sorting:false
					},
					sell_customer: {
						title: '<?php dic_show('sell_customer'); ?>',
						create: false,
						edit: false,
						sorting:false
					},
					total_sell: {
						title: '<?php dic_show('total_sell'); ?>',
						create: false,
						edit: false,
						sorting:false
					},
					print:{
						title: '',
						width: '2%',
						sorting: false,
						edit: false,
						create: false,
						display: function (std) {
							var $img = $('<a href="#sellFactureReportDay>'+std.record.date+'"><img src="images/small/print0.png" title="<?php dic_show('Print Facture'); ?>" class="printExamIcon" /></a>');
							return $img;
						}
					}
					
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
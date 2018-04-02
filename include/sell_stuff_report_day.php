<?php
	require_once '../class/database.class.php';
	if(!isset($_SESSION['user']))
		$db->go_to_login();
	$DATE = $_GET['id'];
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
				defaultSorting: 'date DESC',
				actions: {
					listAction: 'control/sell_stuff_report.control.php?action=stuffReportDay&date=<?php echo $DATE;?>',
					createAction: false,
					updateAction: false,
					deleteAction: false
				},
				fields: {
					// id: {
					// 	key: true,
					// 	create: false,
					// 	edit: false,

					// 	title: '<?php dic_show('Day'); ?>',
					// 	width: '5%',
					// 	sorting:false
					// },	
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false,
						// sorting:false
						// list:false
					},
					zebra: {
						title: '<?php dic_show('zebra'); ?>',
						create: false,
						edit: false,
						sorting:false
					},
					store: {
						title: '<?php dic_show('store'); ?>',
						create: false,
						edit: false,
						sorting:false
					},
					madani: {
						title: '<?php dic_show('madani'); ?>',
						create: false,
						edit: false,
						sorting:false
					},
					slight: {
						title: '<?php dic_show('slight'); ?>',
						create: false,
						edit: false,
						sorting:false
					},
					// print:{
					// 	title: '',
					// 	width: '2%',
					// 	sorting: false,
					// 	edit: false,
					// 	create: false,
					// 	display: function (std) {
					// 		var $img = $('<a href="#sellStuffReportDay>'+std.record.date+'"><img src="images/small/print0.png" title="<?php dic_show('Print Facture'); ?>" class="printExamIcon" /></a>');
					// 		return $img;
					// 	}
					// }
					
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
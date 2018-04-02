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
				title: '<?php dic_show("Sell Facture Report By Day"); echo " / $DATE"; ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/sell_facture_report.control.php?action=sellListDay&date=<?php echo $DATE;?>',
					createAction: false,
					updateAction: false,
					deleteAction: false
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%',
						sorting:false
					},	
					id_account: {
						title: '<?php dic_show('Branch'); ?>',
						options : 'control/account.control.php?action=json_list_branch',
						sorting:false
					},
					id_user: {
						title: '<?php dic_show('User'); ?>',
						options : 'control/user.control.php?action=json_list',
						create: false,
						edit: false,
						list:false
					},
					id_cat: {
						title: '<?php dic_show('Category'); ?>',
						options : 'control/cat.control.php?action=json_list',
						sorting:false
					},
					id_stuff: {
						title: '<?php dic_show('Stuff'); ?>',
						// options : 'control/stuff.control.php?action=json_list'
						dependsOn: 'id_cat', 
	                    options: function (data) {
	                        if (data.source == 'list') {
	                            return 'control/stuff.control.php?action=json_list';
	                        }
	                        return 'control/stuff.control.php?action=json_list_by_cat&id_cat=' + data.dependedValues.id_cat;
	                    }
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>',
						sorting:false
					},				
					width: {
						title: '<?php dic_show('Width'); ?>',
					},
					height: {
						title: '<?php dic_show('Height'); ?>',
					},
					qty: {
						title: '<?php dic_show('QTY'); ?>',
					},
					m2: {
						title: '<?php dic_show('m2'); ?>',
						create: false,
						edit: false,
					},
					price: {
						title: '<?php dic_show('Price'); ?>',
					},
					total_price: {
						title: '<?php dic_show('Total Price'); ?>',
						create: false,
						edit: false,
					}					
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
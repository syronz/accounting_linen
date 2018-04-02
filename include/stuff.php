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
				title: '<?php dic_show("Stuff List"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/stuff.control.php?action=list',
					createAction: 'control/stuff.control.php?action=create',
					updateAction: 'control/stuff.control.php?action=update',
					deleteAction: 'control/stuff.control.php?action=delete'
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%'
					},	
					id_cat: {
						title: '<?php dic_show('Category'); ?>',
						options : 'control/cat.control.php?action=json_list'
					},					
					name: {
						title: '<?php dic_show('Name'); ?>',
					},
					code: {
						title: '<?php dic_show('code'); ?>',
						create: false,
						edit: false,
						list:false
					},
					price: {
						title: '<?php dic_show('price'); ?>'
					},
					qty: {
						title: '<?php dic_show('qty'); ?>'
					},
					detail: {
						title: '<?php dic_show('Detail'); ?>'
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
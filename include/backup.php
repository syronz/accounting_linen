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
				title: '<?php dic_show("Backup"); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'date DESC',
				actions: {
					listAction: 'control/backup.control.php?action=list',
					createAction: 'control/backup.control.php?action=create',
					updateAction: false,
					deleteAction: 'control/backup.control.php?action=delete'
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%',
						sorting: false
					},						
					name: {
						title: '<?php dic_show('Name'); ?>',
						edit: false,
						sorting: false
					},
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false,
						sorting: false
					},
					download: {
						title: '<?php dic_show('Download'); ?>',
						sorting: false,
						create: false,
						edit: false
					},
					restore: {
						title: '<?php dic_show('Restore'); ?>',
						sorting: false,
						create: false,
						edit: false,
						list:false
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>

	<button class="btn btn-primary" style="display:none;" data-toggle="modal" data-target=".bs-example-modal-lg" id="btnProgressRestore">Large modal</button>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="modalProgressBar">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="padding:0 10px 0 10px">
    	<h4 style="text-align:center;"> Restore </h4>
      <div class="progress progress-striped active">
		  <div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0" id="restoreProgressBar">
		    <span class="sr-only">0% Complete</span>
		  </div>
	  </div>
    </div>
  </div>
</div>
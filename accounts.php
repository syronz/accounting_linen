<?php
	require_once 'class/database.class.php';
?>
<html>
<?php require_once 'head_tag.php'; ?>
  <body>
  	<div id="wrapper">
  		<?php require_once 'nav.php'; ?>
		<div id="main">
			<div id="jtable_div" style="width: 900px;"></div>
		</div>
		<?php require_once 'footer.php'; ?>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {

		    //Prepare jTable
			$('#jtable_div').jtable({
				title: '<?php dic_show('Accounts'); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id ASC',
				actions: {
					listAction: 'control/accounts.control.php?action=list',
					createAction: 'control/accounts.control.php?action=create',
					updateAction: 'control/accounts.control.php?action=update',
					deleteAction: 'control/accounts.control.php?action=delete'
				},
				fields: {
					facture_detail:{
						title: '',
	                    width: '2%',
	                    sorting: false,
	                    edit: false,
	                    create: false,
	                    display: function (std) {
                        //Create an image that will be used to open child table
                        var $img = $('<img src="images/list_metro.png" title="click to open" />');
                        //Open child table when user clicks the image
                        $img.click(function () {
                            $('#jtable_div').jtable('openChildTable',
                                    $img.closest('tr'),
                                    {
                                        title: '<?php dic_show('account detail:'); ?>' + std.record.id + ' ',
                                        actions: {
                                        	listAction: 'control/accounts.control.php?action=detail_lists&id_account='+std.record.id,
											createAction: false,
											updateAction: false,
											deleteAction: false
											/*listAction: 'control/buy_facture.control.php?action=detail_list&id_facture='+std.record.id,
											createAction: 'control/buy_facture.control.php?action=create_detail&id_facture='+std.record.id,
											updateAction: false,
											deleteAction: 'control/department.control.php?action=delete'*/
										},
										fields: {
											id: {
												key: true,
												list:false
											},
											balance: {
												title: '<?php dic_show("Balance"); ?>'
											},
											income: {
												title: '<?php dic_show("Payment"); ?>'
											},
											outcome: {
												title: '<?php dic_show("Payout"); ?>'
											},
											date:{
												title: '<?php dic_show("Date"); ?>',
												type: 'date'
											}
										}
                                    }, function (data) { //opened handler
                                        data.childTable.jtable('load');
                                });
                        });
                        //Return image to show on the person row
                        return $img;
                    	}
					},
					id: {
						title: '<?php dic_show("NO"); ?>',
						key: true,
						create: false,
						edit: false,
						//list: false
					},
					owner_name: {
						title: '<?php dic_show("Owner Name"); ?>'
					},
					account_number: {
						title: '<?php dic_show("Phone"); ?>'
					},
					
					// balance: {
					// 	title: '<?php dic_show("Balance"); ?>',
					// 	create:false,
					// 	edit:false
					// },
					b_dollar: {
						title: '<?php dic_show("Balance $"); ?>',
						create:false,
						edit:false
					},
					// b_dinar: {
					// 	title: '<?php dic_show("Balance IQD"); ?>',
					// 	create:false,
					// 	edit:false
					// },
					// b_tman: {
					// 	title: '<?php dic_show("Balance Tman"); ?>',
					// 	create:false,
					// 	edit:false
					// },
					id_user: {
						title: '<?php dic_show("User"); ?>',
						create: false,
						edit: false,
						list: false
					},
					date_created:{
						title: '<?php dic_show("Date Created"); ?>',
						type: 'date',
						create:false,
						edit:false
					},
					detail: {
						title: '<?php dic_show("detail"); ?>',
						type: 'textarea'
					}
				}
			});

			//Load person list from server
			$('#jtable_div').jtable('load');

		});

	</script>
 
  </body>
</html>

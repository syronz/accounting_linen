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
				title: '<?php dic_show("Exam\'s List"); ?>',
				paging: true,
				pageSize: 20,
				sorting: true,
				defaultSorting: 'id ASC',
				actions: {
					listAction: 'control/exam.control.php?action=list',
					createAction: false,
					updateAction: 'control/exam.control.php?action=update',
					deleteAction: 'control/exam.control.php?action=delete'
				},
				fields: {
					exam_detail:{
						title: '<?php dic_show(""); ?>',
						width: '2%',
						sorting: false,
						edit: false,
						create: false,
						display: function (std) {
						//Create an image that will be used to open child table
						var $img = $('<img src="images/small/list_metro.png" title="<?php dic_show('Show Tests') ?>" class="testsListIcon" />');
						//Open child table when user clicks the image
						$img.click(function () {
							$('#jtableDiv').jtable('openChildTable',
									$img.closest('tr'),
									{
										title: '<?php dic_show('exam #: '); ?>' + std.record.id + ' - <?php dic_show("Test") ?>',
										actions: {
											listAction: 'control/test.control.php?action=examList&idExam='+std.record.id,
											// listAction: 'control/normal_range.control.php?action=detail_list&id_test='+std.record.id,
											createAction: false,
											updateAction: 'control/test.control.php?action=insertResult',
											deleteAction: false
										},
										fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%'
					},
					id_exam: {
						title: '<?php dic_show('id_exam'); ?>',
						// options: 'control/exam.control.php?action=json_list&part=exam',
						create: false,
						edit: false,
						list:false
					},
					id_profile: {
						title: '<?php dic_show('profile'); ?>',
						options: 'control/profile.control.php?action=json_list&part=exam',
						create: false,
						edit: false
					},
					id_test: {
						title: '<?php dic_show('test'); ?>',
						options: 'control/test.control.php?action=json_list&part=exam',
						create: false,
						edit: false
					},			
					// min_age: {
					// 	title: '<?php dic_show('min_age'); ?>'
					// },
					// max_age: {
					// 	title: '<?php dic_show('max_age'); ?>'
					// },
					// gender: {
					// 	title: '<?php dic_show('gender'); ?>',
					// 	options:{man:'man',woman:'woman',both:'both'}
					// },
					// date: {
					// 	title: '<?php dic_show('date_created'); ?>',
					// 	edit: false,
					// 	create: false,
					// 	list:false
					// },
					result: {
						title: '<?php dic_show('result'); ?>'
					},
					checker: {
						title: '<?php dic_show('flag'); ?>',
						create: false,
					},
					normal: {
						title: '<?php dic_show('normal'); ?>',
						create: false,
						edit: false
					},
					detail: {
						title: '<?php dic_show('detail'); ?>'
					},
					// in_print: {
					// 	title: '<?php dic_show('in_print'); ?>'
					// }
																	
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
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%'
					},	
					id_patient: {
						title: '<?php dic_show('patient'); ?>',
						// options: 'control/patient.control.php?action=json_list&part=exam',
						create: false,
						edit: false
					},			
					date_recieved: {
						title: '<?php dic_show('date_recieved'); ?>',
						create: false,
						edit: false
					},
					date_released: {
						title: '<?php dic_show('date_released'); ?>',
						create: false,
						edit: false,
						list:false
					},
					age: {
						title: '<?php dic_show('age'); ?>',
						create: false,
						edit: false
					},
					state: {
						title: '<?php dic_show('state'); ?>',
						create: false,
						edit: false
					},
					total_price: {
						title: '<?php dic_show('price'); ?>',
						create: false,
						edit: false
					},
					discount: {
						title: '<?php dic_show('discount'); ?>',
						create: false,
						edit: false
					},
					detail: {
						title: '<?php dic_show('detail'); ?>',
						create: false,
					},
					print:{
						title: '',
						width: '2%',
						sorting: false,
						edit: false,
						create: false,
						display: function (std) {
							var $img = $('<a href="#lab>examPrintResult>'+std.record.id+'"><img src="images/small/print0.png" title="<?php dic_show('Print Exam'); ?>" class="printExamIcon" /></a>');
							return $img;
						}
					}
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
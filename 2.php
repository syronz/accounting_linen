<?php
	require_once 'class/database.class.php';
?>
<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Bootstrap 101 Template</title>
		<!-- Bootstrap -->
		<link href="css/bootstrap.min.css" rel="stylesheet">


		<!-- DSH 
		<script src="js/jquery-2.1.1.min.js"></script>-->
		

		<link href="themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
    	<link href="scripts/jtable.2.3.1/themes/metro/blue/jtable.css" rel="stylesheet" type="text/css" />
    	<!-- <link href="themes/jtable.css" rel="stylesheet" type="text/css" /> -->
    	<script src="js/jquery-2.1.1.min.js" type="text/javascript"></script>
    	<script src="scripts/jquery-ui-1.10.3.custom.js" type="text/javascript"></script>
    	<script src="scripts/jtable.2.3.1/jquery.jtable.js" type="text/javascript"></script>


		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style>
			body {
				background-color: #fff;
			}
			.container div {
				/*border: 1px solid red;*/
			}
			.pagination {
				margin: 0 0 -5px 0;
				padding: 0;
			}
			.panel-footer {
				padding: 3px;
			}
			.list-group {
				/*min-height: 550px;*/
			}
			.jtable_text {
				display:inline-block;
				vertical-align: top;
				padding: 7px 2px 0 5px;
				font-size: smaller;
				color: #555;
				font-family: courier;
			}
			.jtable_select {
				vertical-align: top;
				margin-top: 1px;
				background-color: #fff;
				padding: 3px;
				border-radius: 3px;
				border: 1px solid #ddd;
				color: #428bca;
			}
			.float_right {
				float: right;
			}
			.alert {
				margin-bottom: 0;
			}

			a{
				color:#fff;
			}

			select{
				color: #000;
			}
		</style>
	</head>
	
	<body>
		<script type='text/javascript' src='js/resizable-tables.js'></script>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-md-12">
					<nav class="navbar navbar-default" role="navigation">
						<div class="container-fluid">
							<!-- Brand and toggle get grouped for better mobile display -->
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">	<span class="sr-only">Toggle navigation</span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>

								</button>	<a class="navbar-brand" href="#Brand">Brand</a>

							</div>
							<!-- Collect the nav links, forms, and other content for toggling -->
							<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
								<ul class="nav navbar-nav">
									<li class="active"><a href="#link">Link</a>
									</li>
									<li><a href="#link2">Link2</a>
									</li>
									<li class="dropdown">	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>

										<ul class="dropdown-menu">
											<li><a href="#Action">Action</a>
											</li>
											<li><a href="#">Another action</a>
											</li>
											<li><a href="#">Something else here</a>
											</li>
											<li class="divider"></li>
											<li><a href="#">Separated link</a>
											</li>
											<li class="divider"></li>
											<li><a href="#">One more separated link</a>
											</li>
										</ul>
									</li>
								</ul>
								<ul class="nav navbar-nav navbar-left">
									<li><a href="#">Link</a>
									</li>
									<li class="dropdown">	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>

										<ul class="dropdown-menu">
											<li><a href="#">Action</a>
											</li>
											<li><a href="#">Another action</a>
											</li>
											<li><a href="#">Something else here</a>
											</li>
											<li class="divider"></li>
											<li><a href="#">Separated link</a>
											</li>
										</ul>
									</li>
								</ul>
								<form class="navbar-form navbar-right" role="search">
									<div class="form-group">
										<input type="search" class="form-control" placeholder="Search">
									</div>
									<button type="submit" class="btn btn-default">Submit</button>
								</form>
							</div>
							<!-- /.navbar-collapse -->
						</div>
						<!-- /.container-fluid -->
					</nav>
				</div>
			</div>
			<div class="row">
				<div class="col-md-9">
					<!-- <ol class="breadcrumb">
						<li><a href="#">Home</a></li>
						<li><a href="#">Library</a></li>
						<li class="active">Data</li>
					</ol> -->
					
						<div id="jtable_div" ></div>
					
				</div>
				<div class="col-md-3  col-md-offset-0">
					<div class="list-group">	<a href="#" class="list-group-item active">
						Cras justo odio
					</a>
	<a href="#" class="list-group-item">Dapibus ac facilisis in</a>
	<a href="#" class="list-group-item">Morbi leo risus</a>
	<a href="#" class="list-group-item">Porta ac consectetur ac</a>
	<a href="#" class="list-group-item">Vestibulum at eros</a>

					</div>
					<div class="thumbnail">
						<img src="images/light.png" alt="...">
						<div class="caption">
								<h3>Thumbnail label</h3>

							<p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="well well-sm" style="margin-bottom:0">Footer




						<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        dfdsfsdfsdf
        dsfdsf
        sfsdfsdfsd
        fdsfsdf
        fdsfsd
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>








					</div>
				</div>
			</div>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) 
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="js/bootstrap.min.js"></script>

		<script type="text/javascript">
		$(document).ready(function () {

		    //Prepare jTable
			$('#jtable_div').jtable({
				title: '<?php dic_show('<a href="#gggg">Accounts</a>'); ?>',
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

	 <script>
		$(document).ready(function(){
			$('a').click(function(){
				console.log(window.location.hash);
			});
		});
		</script>
	</body>

</html>
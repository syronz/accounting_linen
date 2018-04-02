<?php
	require_once '../class/database.class.php';
	require_once '../class/account.class.php';
	require_once '../class/cat.class.php';

	// require_once '../class/patient.class.php';
	// $option_profiles = profile::profile_as_options();


?>
<style>
.panel-group a{
	color: gray;
}

/*.sellTable input[type=text]{
	width:50px;
}*/
.sellTable .stuffWidth, .sellTable .stuffHeight, .sellTable .price{
	width: 45px;
}
.sellTable .stuffQTY{
	width: 25px;
}
.sellTable .stuffDetail{
	width: 170px;
}
.sellTable select{
	width:105px;
}
.sellTable{
	table-layout:fixed;
}


</style>
<div class="panel panel-primary">
	<div class="panel-heading"><a href="#sellFactureCustomerList"><?php dic_show('Sell Facture List'); ?></a><?php dic_show(' [Add New Facture]'); ?></div>
	<div class="panel-body">
		<form class="form-horizontal">		


<div class="panel-group" id="accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
					<?php dic_show('New Customer'); ?>
				</a>
			</h4>
		</div>
		<div id="collapseOne" class="panel-collapse collapse in">
			<div class="panel-body">
				

				<!-- Prepended text-->
<div class="form-group">
	<label class="col-md-3 control-label" for="customerName"></label>
	<div class="col-md-7">
		<div class="input-group">
			<span class="input-group-addon"><?php dic_show('Name'); ?></span>
			<input id="customerName" name="customerName" class="form-control" placeholder="Customer Name" type="text" required="">
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="customerPhone"></label>
	<div class="col-md-7">
		<div class="input-group">
			<span class="input-group-addon"><?php dic_show('Phone'); ?></span>
			<input id="customerPhone" name="customerPhone" class="form-control" placeholder="Customer Phone" type="text" required="">
		</div>
	</div>
</div>


<!-- Textarea -->
<div class="form-group">
	<label class="col-md-3 control-label" for="customerAddress"><?php dic_show('Address'); ?></label>
	<div class="col-md-7">                     
		<textarea class="form-control" id="customerAddress" name="customerAddress"></textarea>
	</div>
</div>


			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
					<?php dic_show('Registered Customer'); ?>
				</a>
			</h4>
		</div>
		<div id="collapseTwo" class="panel-collapse collapse">
			<div class="panel-body">
				
				<div class="form-group">
			<label class="col-md-3 control-label" for="customerId">
				<span class="label label-success result_label" id="customerIdSuccess">Success</span>
				<span class="label label-warning result_label" id="customerIdWarning"><?php dic_show('This ID not exist!'); ?></span>
			</label>
			<div class="col-md-6">
				<div class="input-group">
					<span class="input-group-addon">ID</span>
					<input id="customerId" name="customerId" class="form-control" placeholder="customer ID #" type="text">
				</div>
				<p class="help-block"><?php dic_show('Please Enter Customer ID'); ?></p>
			</div>
		</div>

		<!-- Select Basic -->
		<div class="form-group">
			<label class="col-md-3 control-label" for="customerRegisteredName">Customer Name</label>
			<div class="col-md-6">
				<select id="customerRegisteredName" name="customerRegisteredName" class="form-control">
					<?php echo account::customerAsOptions();?>
				</select>
			</div>
		</div>
			</div>
		</div>
	</div>
</div>


<div>
<table class="table table-bordered sellTable" style="direction:rtl;">
			<thead>
				<col width="5%" >
			<col width="15%" >
			<col width="15%" >
			<col width="22.5%" >
			<col width="7.5%" >
			<col width="7.5%" >
			<col width="5%" >
			<col width="5%" >
			<col width="7.5%" >
			<col width="10%" >
				<tr>
					<th><?php dic_show('#') ?></th>
					<th><?php dic_show('Category') ?></th>
					<th><?php dic_show('Code') ?></th>
					<th><?php dic_show('Detail') ?></th>
					<th><?php dic_show('Width') ?></th>
					<th><?php dic_show('Height') ?></th>
					<th><?php dic_show('QTY') ?></th>
					<th><?php dic_show('M2') ?></th>
					<th><?php dic_show('Price') ?></th>
					<th><?php dic_show('T Price') ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>
						<input type="hidden" class="hiddenM2" value="0">
						<select class="categoryListSellFacture">
							<option value="0">Category</option>
							<?php echo cat::catAsOptions(); ?>
						</select>
					</td>
					<td>
						<select class="stuffListSellFacture">
						</select>
					</td>
					<td>
						<input type="text" class="stuffDetail" name="stuffDetail">
					</td>
					<td>
						<input type="text" class="stuffWidth" name="stuffWidth">
					</td>
					<td>
						<input type="text" class="stuffHeight" name="stuffHeight">
					</td>
					<td>
						<input type="text" class="stuffQTY" name="stuffQTY" value="1">
					</td>
					<td class="m2"></td>
					<td>
						<input type="text" class="price" name="price">
					</td>
					<td class="totalPrice"></td>
				</tr>
				<tr>
					<td>2</td>
					<td>
						<input type="hidden" class="hiddenM2" value="0">
						<select class="categoryListSellFacture">
							<option value="0">Category</option>
							<?php echo cat::catAsOptions(); ?>
						</select>
					</td>
					<td>
						<select class="stuffListSellFacture">
						</select>
					</td>
					<td>
						<input type="text" class="stuffDetail" name="stuffDetail">
					</td>
					<td>
						<input type="text" class="stuffWidth" name="stuffWidth">
					</td>
					<td>
						<input type="text" class="stuffHeight" name="stuffHeight">
					</td>
					<td>
						<input type="text" class="stuffQTY" name="stuffQTY" value="1">
					</td>
					<td class="m2"></td>
					<td>
						<input type="text" class="price" name="price">
					</td>
					<td class="totalPrice"></td>
				</tr>
				<tr>
					<td>3</td>
					<td>
						<input type="hidden" class="hiddenM2" value="0">
						<select class="categoryListSellFacture">
							<option value="0">Category</option>
							<?php echo cat::catAsOptions(); ?>
						</select>
					</td>
					<td>
						<select class="stuffListSellFacture">
						</select>
					</td>
					<td>
						<input type="text" class="stuffDetail" name="stuffDetail">
					</td>
					<td>
						<input type="text" class="stuffWidth" name="stuffWidth">
					</td>
					<td>
						<input type="text" class="stuffHeight" name="stuffHeight">
					</td>
					<td>
						<input type="text" class="stuffQTY" name="stuffQTY" value="1">
					</td>
					<td class="m2"></td>
					<td>
						<input type="text" class="price" name="price">
					</td>
					<td class="totalPrice"></td>
				</tr>
				<tr>
					<td>4</td>
					<td>
						<input type="hidden" class="hiddenM2" value="0">
						<select class="categoryListSellFacture">
							<option value="0">Category</option>
							<?php echo cat::catAsOptions(); ?>
						</select>
					</td>
					<td>
						<select class="stuffListSellFacture">
						</select>
					</td>
					<td>
						<input type="text" class="stuffDetail" name="stuffDetail">
					</td>
					<td>
						<input type="text" class="stuffWidth" name="stuffWidth">
					</td>
					<td>
						<input type="text" class="stuffHeight" name="stuffHeight">
					</td>
					<td>
						<input type="text" class="stuffQTY" name="stuffQTY" value="1">
					</td>
					<td class="m2"></td>
					<td>
						<input type="text" class="price" name="price">
					</td>
					<td class="totalPrice"></td>
				</tr>
				<tr>
					<td>5</td>
					<td>
						<input type="hidden" class="hiddenM2" value="0">
						<select class="categoryListSellFacture">
							<option value="0">Category</option>
							<?php echo cat::catAsOptions(); ?>
						</select>
					</td>
					<td>
						<select class="stuffListSellFacture">
						</select>
					</td>
					<td>
						<input type="text" class="stuffDetail" name="stuffDetail">
					</td>
					<td>
						<input type="text" class="stuffWidth" name="stuffWidth">
					</td>
					<td>
						<input type="text" class="stuffHeight" name="stuffHeight">
					</td>
					<td>
						<input type="text" class="stuffQTY" name="stuffQTY" value="1">
					</td>
					<td class="m2"></td>
					<td>
						<input type="text" class="price" name="price">
					</td>
					<td class="totalPrice"></td>
				</tr>


				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2"><?php dic_show('Sum Total')?></td>
					<td id="sumTotalPrice"></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2"><?php dic_show('Pre Payment')?></td>
					<td><input type="text" name="prePayment" id="prePayment"  style="width:165px;"></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2"><?php dic_show('Remained')?></td>
					<td id="remainedMoney"></td>
				</tr>
			</tbody>
		</table>
</div>

<button type="button" class="btn btn-primary right" id="submitSellFactureCustomer">Submit</button>
<label class="col-md-10 control-label" for="customerId">
	<span class="label label-danger result_label"  id="sellFactureCustomerError"><?php dic_show('Error'); ?></span>
</label>


<script>


</script>



</form>

	</div>
	
	
</div>
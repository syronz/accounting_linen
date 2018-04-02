<?php
	require_once '../class/database.class.php';
	require_once '../class/account.class.php';
	require_once '../class/cat.class.php';
	if(!isset($_SESSION['user']))
		$db->go_to_login();

	$branchList = json_decode(account::json_list_branch());
	$branchList = $branchList->Options;
	// $catList = json_decode(cat::json_list('cat'));
	// dsh($catList);
	// $catList = $catList->Options;
	// foreach ($branchList as $key => $value) {
	// 	echo "{$value->Value}";
	// }
?>
<style>
.tr_facture input{
	width: 50px;
}
.tr_facture input[type='number']{
	width: 50px;
}
table{
	width:846px; overflow:hidden;direction:rtl;
}
table th{
	text-align: center;
}
.categoryListSellFacture{
	width: 100px;
}
.stuffListSellFacture{
	width: 60px;
}
</style>
	<div id="testShow">
	</div>
	<div>
		<form>
			<table style="" class="table" >
				<tr>
                    <th>ناوی وەکیل</th>
                    <th><span class="tahoma">کۆدی بەرهەم</span></th>
                    <th><span class="tahoma">کۆدی بەرهەم</span></th>
                    <th><span class="tahoma">تێبینی</span></th>
                    <th><span class="tahoma">پانی</span></th>
                    <th><span class="tahoma">بەرزی</span></th>
                    <th><span class="tahoma">عدد</span></th>
                    <th width="40"><span class="tahoma">م2</span></th>
                    <th><span class="tahoma">نرخ</span></th>
                    <th><span class="tahoma">پارەکەی</span></th>
                </tr>
                <tr class="tr_facture">
                    <td>
                    	
                        <select name="customerName" class="idAccount">
                        	<?php
                        		foreach ($branchList as $key => $value) {
                        			echo "<option value='{$value->Value}'>{$value->DisplayText}</option>";
                        		}
                        	?>
                        </select>
                    </td>
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

                <tr style="display:none;">
                    <td>
                    	<input type="hidden" class="hiddenM2" value="2">
                        <select name="catName" class="categoryListSellFacture">
                        </select>
                    </td>
                    
                </tr>
			</table>
		</form>
	</div>

	<button id="sendToBranchListFacture">زیاد كردن</button>

	<div id="jtableDiv" style="width:846px;direction:rtl;"></div>
	<script type="text/javascript">
		$(document).ready(function () {

			$('#jtableDiv').jtable({
				title: '<?php dic_show("Sell Facture Daily"); echo " / ".date("Y-m-d H:i:s",time()); ?>',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'id DESC',
				actions: {
					listAction: 'control/sell_facture_daily.control.php?action=list',
					createAction: 'control/sell_facture_daily.control.php?action=create',
					updateAction: 'control/sell_facture_daily.control.php?action=update',
					deleteAction: 'control/sell_facture_daily.control.php?action=delete'
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						title: '<?php dic_show('id'); ?>',
						width: '5%'
					},	
					id_account: {
						title: '<?php dic_show('Branch'); ?>',
						options : 'control/account.control.php?action=json_list_branch'
					},
					id_user: {
						title: '<?php dic_show('User'); ?>',
						options : 'control/user.control.php?action=json_list',
						create: false,
						edit: false,
					},
					id_cat: {
						title: '<?php dic_show('Category'); ?>',
						options : 'control/cat.control.php?action=json_list'
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
						title: '<?php dic_show('Detail'); ?>'
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
					},
					date: {
						title: '<?php dic_show('Date'); ?>',
						create: false,
						edit: false,
						// list:false
					}
					
				}
			});
			$('#jtableDiv').jtable('load');
		});

	</script>
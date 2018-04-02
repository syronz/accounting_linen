<?php
	require_once '../class/database.class.php';
	require_once '../class/account.class.php';
	require_once '../class/cat.class.php';
	require_once '../class/sell_facture_customer.class.php';

	$factureInfo = sell_facture_customer::sellFactureCustomerInfo($_GET['id']);
	// require_once '../class/patient.class.php';
	// $option_profiles = profile::profile_as_options();


	// dsh($factureInfo);
	$customerInfo = account::rowInfoArray('account',$factureInfo['head']['id_account']);
	$userInfo = account::rowInfoArray('user',$factureInfo['head']['id_user']);
?>
<style>
.tblName{
	width: 100%;
}
.tblName td{
	width: 12.5%;
	font-family: arial;
	font-size: 14px;
}


</style>
<img src="images/shkar.jpg" style="width:100%">


<table class="tblName">
	<tr>
		<td><?php dic_show('Seller Name: '); ?></td>
		<td><?php echo $userInfo['name']; ?></td>
		<td></td>
		<td><?php dic_show('Date'); ?></td>
		<td><?php echo substr($factureInfo['head']['date'],0,10); ?></td>
	</tr>
	<tr>
		<td><?php dic_show('Customer Name: '); ?></td>
		<td><?php echo $customerInfo['name']; ?></td>
		<td></td>
		<td><?php dic_show('Customer Phone: '); ?></td>
		<td><?php echo $customerInfo['phone']; ?></td>
	</tr>
	<?php if($customerInfo['address']): ?>
	<tr>
		<td><?php dic_show('Address: '); ?></td>
		<td colspan="4"><?php echo $customerInfo['address']; ?></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td><?php dic_show('State: '); ?></td>
		<td colspan="4"><?php dic_show($factureInfo['head']['state']); ?></td>
	</tr>
</table>


<table class="table table-bordered sellTable" style="direction:rtl;" border="1" cellspacing="0">
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
				<!-- <tr>
					<td>1</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="m2"></td>
					<td></td>
					<td class="totalPrice"></td>
				</tr> -->
				<?php
					$i = 1;
					$sumTotalPrice = 0;
					$sumTotalm2 = 0;
					foreach ($factureInfo['stuffs'] as $key => $value) {
						$catInfo = cat::rowInfoArray('cat',$value['id_cat']);
						$stuffInfo = cat::rowInfoArray('stuff',$value['id_stuff']);
						$sumTotalPrice += $value['total_price'];
						echo '<tr>';
						echo "<td>$i</td>";
						echo "<td>{$catInfo['name']}</td>";
						echo "<td>{$stuffInfo['name']}</td>";
						echo "<td>{$value['detail']}</td>";
						echo "<td>{$value['width']}</td>";
						echo "<td>{$value['height']}</td>";
						echo "<td>{$value['qty']}</td>";
						echo "<td>".$value['m2']*$value['qty']."</td>";
						echo "<td>{$value['price']}</td>";
						echo "<td>{$value['total_price']}</td>";
						echo '</tr>';
						$i++;
						$sumTotalm2 += $value['m2']*$value['qty'];
					}
				?>
				<tr>
				<td colspan="7"></td>
				<td>
					<?php
						echo $sumTotalm2;
					?>
				</td>
				<td colspan="2"></td>
				</tr>


				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2"><?php dic_show('Sum Total')?></td>
					<td id="sumTotalPrice"><?php /*echo $sumTotalPrice;*/ echo $factureInfo['head']['total_price'];?> $ </td>
				</tr>
				<tr>
					<?php
						foreach ($factureInfo['payins'] as $key => $value) {
							$i = $key + 1;
							echo "<tr><td>$i</td>";
							echo "<td colspan='2'>".dic_return('Payin')." </td>";
							echo "<td>{$value['dollar']} $</td>";
							echo "<td colspan='3'>{$value['date']}</td></tr>";
						}
					?>
				</tr>
				<tr>
					<td></td>
					<td colspan="2"><?php dic_show('Remained')?></td>
					<td id="remainedMoney"><?php echo $factureInfo['head']['remained']; ?> $</td>
				</tr>
			</tbody>
		</table>

<img src="images/shkar_footer.jpg" style="width:100%">






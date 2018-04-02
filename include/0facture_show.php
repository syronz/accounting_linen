<?php
/*
 * Acounting App for PHP
 * Programmer : Diako Sharifi
 * Email : sabina.diako@gmail.com
 * Site : fryao.com
 * 
 * This program is not free software. you cant redistribute it and/or modify
*/

if(strpos($_SESSION['perms'],'view_facture')===false)
    die(lang('<div id="no_permission">you dont have permition to see this part</div>'));


require_once '../class/user.php';
require_once '../class/facture.php';
$id_facture = explode('=',$_POST['variables']);
if($_SESSION['username'])
    $facture_info = $facture->facture_info_id($id_facture[1]);
else 
    die('you most log in firs');


//var_dump($id_facture);
$id_customer = $facture->find_id_customer($facture_info['base']['customer_name']);
$customer_info = $facture->customer_info_id($id_customer);
$user_info = $user->user_info_id($facture_info['base']['id_user']); 
//var_dump($facture_info);
?>
<style type="text/css">
     table{
        margin: 10px auto;
        width: 640px;
    }
    table th:first-child{
        width: 5px;
    }
    table td:first-child{
        text-align: right;
    }
    table td:last-child{
        text-align: right;
        width: 70px;
    }
    table td,table th{
        padding: 5px;
    }
    table #sum_total{
        text-align: center;
    }
    input[type=checkbox]{
        margin-top: 7px;
    }
    .stuff_quantite{
        width: 45px;
    }
    .stuff_price{
        width: 80px;
    }
    th, td, caption{
        text-align: center;
    }
    
    table{
/*        margin: 10px auto;
        width: 400px;*/
    }
    table th:nth-child(2){
/*    width: 350px;*/
    }
    h3,h5{
        margin-top: 20px;
        margin-bottom: 2px;
        text-align: center;
        color: maroon;
    }
    
</style>
<div id="print_this">
<!--        <fieldset class="content_facture">-->
<div class="facture_header"></div>
            <table style="direction: rtl;" id="tbl_facture">
                <tbody>
                    <tr>
                        <th colspan="2" style="direction: rtl; text-align: right;"><?php lang('Seller Name: '); echo $user_info['name']; ?></th>
                        <th colspan="4" style="direction: rtl; text-align: right;"><?php lang('Date: '); echo date("Y-m-d", $facture_info['base']['date']); ?></th>
                    </tr>
                    <tr>
                        <td colspan="2"><?php lang('Customer Name: '); echo $facture_info['base']['customer_name']; ?></td>
                        <td colspan="4"><?php lang('Customer Phone: '); echo $customer_info['phone']; ?></td>
                    </tr>
                    <?php
                        if($customer_info['address'])
                            echo "<tr>
                                <td colspan='1'>".lang_return('Address: ')."</td>
                                <td colspan='5'>{$customer_info['address']}</td>
                            </tr>";
                    
                    ?>
                    <tr>
                        <td colspan="2"><?php lang('State: ');?></td>
                        <td colspan="4"><?php lang($facture_info['base']['state']); ?></td>
                    </tr>
                </tbody>
            </table>  
            <table style="direction: rtl;" id="tbl_facture">
                    <tr>
                        <th><?php lang('.no'); ?></th>
                        <th><?php lang('Stuff Name'); ?></th>
                        <th><?php lang('Detail'); ?></th>
                        <th><?php lang('Width'); ?></th>
                        <th><?php lang('Length'); ?></th>
                        <th><?php lang('num'); ?></th>
                        <th><?php lang('m<sup>2</sup>'); ?></th>
                        <th><?php lang('price'); ?></th>
                        <th><?php lang('total'); ?></th>
                    </tr>
                    <?php
                    $i=0;
                    if($facture_info['detail'])
                        foreach ($facture_info['detail'] as $key => $value) {
                            $i++;
                            $m2 = $value['m2'];
                            $total = $m2 * $value['price'];
                            echo "<tr>
                                    <td>$i</td>
                                    <td style='direction:rtl; text-align:right;'>{$value['stuff_name']}</td>
                                    <td>{$value['detail']}</td>
                                    <td>{$value['width']}</td>
                                    <td>{$value['quantity']}</td>
                                    <td>{$value['num']}</td>
                                    <td>$m2</td>
                                    <td>".toMoney($value['price'])."</td>
                                    
                                    <td>".toMoney($total)."</td>
                                </tr>";
                        }
                    
                        
                       
                    
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center;"> <?php lang('Sum Total'); ?> </td>
                        <td></td>
                        <td> <?php echo toMoney($facture_info['base']['total_money']); ?></td>
                    </tr>
                    
                    <?php
                        if($facture_info['base']['type'] == 'loan'){
                            echo   "<tr>
                                        <td colspan='5' style='text-align: center;'>".  lang_return('Recieved Money')."</td>
                                        <td></td>
                                        <td>".toMoney($facture_info['base']['recieved_money'])."</td>
                                    </tr>";
                        }
                    
                    ?>
            </table>
            <?php
            
            for($i=0;$i<3;$i++){
//                echo '<div>';
                $svg_width = $facture_info['detail'][$i]['width'];
                $svg_height = $facture_info['detail'][$i]['quantity'];
                $svg_width_f = $svg_width;
                $svg_height_f = $svg_height;
                if(1 && $svg_width_f!=0){
                    $num_r = 15 / $svg_width_f;
                    $svg_width_f = 15;
                    $svg_height_f *= $num_r;
                }
                if($svg_height_f > 10){
                    $num_m = 10 / $svg_height_f;
                    $svg_height_f = 10;
                    $svg_width_f *= $num_m;
                }
                
            
            ?>
            <div style="direction: ltr;margin-left: 10px; float: left;">
            <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="200px" height="120px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
                viewBox="0 0 20 15"
                xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <style type="text/css">
                    <![CDATA[
                        .str1 {stroke:#373435;stroke-width:0.1}
                        .str0 {stroke:black;stroke-width:0.1}
                        .fil0 {fill:none}
                        .fil1 {fill:#EEEEEE}
                        .fil2 {fill:black}
                        .fnt0 {font-weight:normal;font-size:2px;font-family:'Arial'}
                    ]]>
                    </style>
                </defs>
                <g id="Layer_x0020_1">
                    <metadata id="CorelCorpID_0Corel-Layer"/>
                    <rect class="fil0 str0" x="5" y="4" width="<?php echo round($svg_width_f,2); ?>" height="<?php echo round($svg_height_f,2); ?>"/>
                    <rect class="fil1 str1" x="5" y="4" width="<?php echo round($svg_width_f,2); ?>" height="2"/>

                    <text x="<?php echo $svg_width_f/2+2; ?>" y="3"  class="fil2 fnt0"><?php echo round($svg_width,2).'cm'; ?></text>
                    <text x="-1" y="<?php echo $svg_height_f/2+5; ?>"  class="fil2 fnt0"><?php echo round($svg_height,2).'cm'; ?></text>
                </g>
            </svg>
            </div>

<?php } ?>
<!--        </fieldset>-->
<br>
<div class="clear"></div>
        <h5><?php lang('Payment Table') ?></h5>
        <table cellspacing='0' style="width: 500px; direction: rtl;">
                <tr>
                    <th></th>
                    <th><?php lang('Detail') ?></th>
                    <th><?php lang('User ID') ?></th>
                    <th style="width: 130px;"><?php lang('Date') ?></th>
                    <th><?php lang('Amount') ?></th>
                    
                </tr>
                <?php
                    $i=0;
                    $sum = 0;
                    if($facture_info['payment'])
                        foreach ($facture_info['payment'] as $key => $value) {
                            $i++;
                            $sum += $value['amount'];
                            $date = date("Y-m-d", $value['date']);
                            echo "<tr>
                                    <td>$i</td>
                                    <td>{$value['detail']}</td>
                                    <td>{$value['id_user']}</td>
                                    <td>$date</td>
                                    <td>".toMoney($value['amount'])."</td>
                                </tr>";
                        }
                    ?>
                <tr>
                    <td colspan="3" style="text-align: center;"><?php lang('Sum Of Payments') ?></td>
                    <td></td>
                    <td><?php echo toMoney($sum) ?></td>
                </tr>
                <tr style="color:red;">
                    <td colspan="3" style="text-align: center;"><?php lang('Rest Of Facture') ?></td>
                    <td></td>
                    <td><?php echo toMoney($facture_info['base']['total_money']-$sum) ?></td>
                </tr>
            </table>
        <div class="facture_footer"></div>
        </div>           
<div id="print_here">
    <img src="img/printer-icon.png" alt="print"> <?php lang('Print Page ');?>
</div>
<iframe name=print_frame width=0 height=0 frameborder=0 src=about:blank></iframe>

        
        <?php 
            if($facture_info['base']['state'] == 'in progress'):
        ?>

        
            <div id="dsh_form">
            <div id="wrapper">
                
                <div id="steps">
                    <form id="formElem"  method="post">
                        <fieldset class="step" style="direction: ltr;">
                            <legend><?php lang('Add Payment'); ?></legend>
                            <p>
                                <label for="payment_amount"><?php lang('Payment Amount:'); ?></label>
                                <input id="payment_amount" name="payment_amount" type="text" />
                                <label for="payment_detail"><?php lang('Payment Detail:'); ?></label>
                                <input id="payment_detail" name="payment_detail" type="text" />
                                <label for="payment_date"><?php lang('Date:'); ?></label>
                                <input id="payment_date" name="payment_date" type="date" value="<?php echo date("Y-m-d", time());?>"/>
                            </p>
                            <p>
                                <button id="btn_submit" type="button"<?php lang('Submit'); ?></button><br/>
                                <span style="color: red" id="msg_system"></span>
                            </p>
                        </fieldset>
                    </form>  
                </div>
            </div>
            </div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#formElem #btn_submit').on('click',function(){ 
            var payment_amount = $('#payment_amount').val();
            var payment_detail = $('#payment_detail').val();
            var payment_date = $('#payment_date').val();
            var id_customer = <?php echo $id_customer; ?>;
            var id_facture = <?php echo $facture_info['base']['id']; ?>;
            
//            console.log(id_customer);
            $.post('engine/ajax.php', {payment_amount:payment_amount,payment_detail:payment_detail,
                payment_date:payment_date,id_customer:id_customer,id_facture:id_facture,
                url : 'add_payment'}, function(data){
                    $('#msg_system').html(data);
                });
            $(this).hide();
        });   
    });
</script>
<?php
endif;
?>

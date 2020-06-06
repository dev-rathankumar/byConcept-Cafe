<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<html>
<head>

    
    <title><?php echo __('z-report');?></title>
    <style type="text/css">
        #invoice-POS{
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
            padding:2mm;
            margin: 0 auto;
            width: 100%;
            background: #FFF;
        }
            
            
            ::selection {background: #f31544; color: #FFF;}
            ::moz-selection {background: #f31544; color: #FFF;}
            h1{
                font-size: 1.5em;
                color: #000;
            }
            h2{font-size: .9em;}
            h3{
            font-size: 1.2em;
            font-weight: 300;
            line-height: 2em;
            }
            p{
            font-size: 1em;
            color: #000;
            line-height: 1.2em;
            }
            
            #top, #mid,#bot{ /* Targets all id with 'col-' */
                border-bottom: 1px solid #EEE;
            }

            #top{min-height: 100px;}
            #mid{min-height: 80px;} 
            #bot{ min-height: 50px;}

            
            .info{
            display: block;
            //float:left;
            margin-left: 0;
            }
            .title{
            float: right;
            }
            .title p{text-align: right;} 
            table{
                width: 100%;
                border-collapse: collapse;
            }
            td{
            //padding: 5px 0 5px 15px;
            //border: 1px solid #EEE
            }
            .tabletitle{
                font-weight: bold;
            }
            .service{border-bottom: 1px solid #EEE;}
       
            .itemtext{font-size: 1em;}

            #legalcopy{
                margin-top: 5mm;
            }

            
            
            }
    </style>
</head>
<body style="margin:0;">

<div id="invoice-POS">
    
    <center id="top">
      <div class="logo"></div>
      <div class="info"> 
        <h2><?php echo __('Z Report');?></h2>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
    
    <div id="mid">
      <div class="info">
        <h2>Session Info</h2>
        <p> 
            Session : <?php echo $info_title ; ?></br>
            User : <?php echo get_the_author_meta( 'nicename', $author_id ); ?></br>
            Email   : <?php echo get_the_author_meta( 'email', $author_id ); ?></br>
            
        </p>
        
      </div>
    </div><!--End Invoice Mid-->
    
    <div id="bot">
                <div id="table-info">
						<table>
                            <tr class="service">
								<td class="tableitem"><p class="tabletitle"><?php echo __('Open Time','openpos');?></p></td>
								<td class="tableitem"><p class="itemtext"><?php echo $login_date; ?></p></td>
                            </tr>
                            <tr class="service">
								<td class="tableitem"><p class="tabletitle"><?php echo __('Close Time','openpos');?></p></td>
								<td class="tableitem"><p class="itemtext"><?php echo $logout_date; ?></p></td>
                            </tr>
							<tr class="service">
								<td class="tableitem"><p class="tabletitle"><?php echo __('Open Cash','openpos');?></p></td>
								<td class="tableitem"><p class="itemtext"><?php echo wc_price($open_balance); ?></p></td>
                            </tr>
                            <tr class="service">
								<td class="tableitem"><p class="tabletitle"><?php echo __('Close Cash','openpos');?></p></td>
								<td class="tableitem"><p class="itemtext"><?php echo wc_price($close_balance); ?></p></td>
							</tr>
						</table>
                    </div><!--End Table-->
                    <br/>
                    <div id="table-payment">
						<table>
							<tr class="tabletitle">
								<td class="item"><h2><?php echo __('Method','openpos');?></h2></td>
								<td class="Rate"><h2><?php echo __('Total','openpos');?></h2></td>
                            </tr>
                            <?php foreach($report_payment_methods as $item): ?>
                                <tr class="service">
                                    <td class="tableitem"><p class="itemtext"><?php echo $item['payment_name']; ?></p></td>
                                    <td class="tableitem"><p class="itemtext"><?php echo wc_price($item['total']); ?></p></td>
                                </tr>
                            <?php endforeach; ?>

							

						</table>
					</div><!--End Table-->

                    <br/>

					<div id="table">
						<table>
							<tr class="tabletitle">
								<td class="item"><h2><?php echo __('Item','openpos');?></h2></td>
								<td class="Hours"><h2><?php echo __('Qty','openpos');?></h2></td>
								<td class="Rate"><h2><?php echo __('Total','openpos');?></h2></td>
                            </tr>
                            <?php foreach($report_items as $item): ?>
                                <tr class="service">
                                    <td class="tableitem"><p class="itemtext"><?php echo $item['name']; ?></p></td>
                                    <td class="tableitem"><p class="itemtext"><?php echo $item['qty']; ?></p></td>
                                    <td class="tableitem"><p class="itemtext"><?php echo wc_price($item['total']); ?></p></td>
                                </tr>
                            <?php endforeach; ?>

							<tr class="service">
								<td class="tableitem" colspan="3">&nbsp;</td>
							</tr>

                            <tr class="service">
								<td class="tableitem" colspan="2"><p class="tabletitle"><?php echo __('Shipping Total');?></p></td>
								<td class="tableitem"><p class="itemtext"><?php echo wc_price($shipping_total); ?></p></td>
                            </tr>
                            <tr class="service">
								<td class="tableitem" colspan="2"><p class="tabletitle"><?php echo __('Tax Total');?></p></td>
								<td class="tableitem"><p class="itemtext"><?php echo wc_price($tax_total); ?></p></td>
                            </tr>
                            <tr class="service">
								<td class="tableitem" colspan="2"><p class="tabletitle"><?php echo __('Sale Total');?></p></td>
								<td class="tableitem"><p class="itemtext"><?php echo wc_price($sale_total); ?></p></td>
                            </tr>
                            <tr class="service">
								<td class="tableitem"><p class="tabletitle"><?php echo __('Item Discount');?></p></td>
								<td class="tableitem"></td>
								<td class="tableitem"><p class="itemtext"><?php echo wc_price($item_discount_total); ?></p></td>
							</tr>
							<tr class="service">
								<td class="tableitem"><p class="tabletitle"><?php echo __('Cart Discount');?></p></td>
								<td class="tableitem"></td>
								<td class="tableitem"><p class="itemtext"><?php echo wc_price($cart_discount_total); ?></p></td>
							</tr>

						</table>
                    </div><!--End Table-->
                    
                    

					

				</div><!--End InvoiceBot-->
  </div><!--End Invoice-->
</body>
<script type="text/javascript">
   window.print();
</script>
</html>
<?php
include ("../_init.php");

//  Load Language File
$language->load('product');

// LOAD PRODUCT MODEL
$product_model = $registry->get('loader')->model('product');
$p_id = $request->get['code'];
$product = $product_model->getProduct($p_id);
if (!$product) {
	exit('Product not found');
}

$store_name = store('name');
$req_symbology = $request->get['symbology'];
$limit = (int)($request->get['limit']/5);
$code = $product['p_code'];
$product_name = $product['p_name'];
$product_price = number_format($product['sell_price'], 2);
$generator = barcode_generator();
$symbology = barcode_symbology($generator, $req_symbology);
$limit_array = array(5, 10, 15, 20, 50, 100, 150, 200); 

$Hooks->do_action('Before_Showing_Barcode_List');
?>

<div class="row no-print">
	<div class="col-md-4">
		<select id="barcode-limit" class="form-control generate-barcode">
			<?php foreach ($limit_array as $limit_number) : ?>
				<option value="<?php echo $limit_number; ?>"<?php echo $limit_number == $limit*5 ? ' selected' : null; ?>>
					<?php echo $limit_number; ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-md-4">
		<select id="barcode-symbology" class="form-control generate-barcode">
			<option value="code_39"<?php echo $req_symbology == 'code_39' ? ' selected' : null; ?>>CODE 39</option>
			<option value="code_93"<?php echo $req_symbology == 'code_93' ? ' selected' : null; ?>>CODE 93</option>
			<option value="code_128"<?php echo $req_symbology == 'code_128' ? ' selected' : null; ?>>CODE 128</option>
			<option value="ean_2"<?php echo $req_symbology == 'ean_2' ? ' selected' : null; ?>>EAN 2</option>
			<option value="ean_5"<?php echo $req_symbology == 'ean_5' ? ' selected' : null; ?>>EAN 5</option>
		</select>
	</div>
	<div class="col-md-4">
		<div class="print-btn text-right">
			<button class="btn btn-md btn-block btn-warning" onClick="window.print()"><span class="fa fa-print"></span> <?php echo $language->get('button_print'); ?></button>
		</div>
	</div>
</div>

<div class="table-responsive">
	<table id="barcode-list" class="table table-bordered">
	<?php for ($i=0; $i < $limit; $i++) : ?>
		<tbody>
			<tr>
				<td>
					<h4 class="shop-name"><?php echo $store_name; ?></h4>
					<p class="product-name"><?php echo $product_name; ?></p>
					<img src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($code, $symbology, 1)); ?>">
					<p class="code"><?php echo $code; ?></p>
					<p class="price"><?php echo $language->get('text_price'); ?>: <?php echo $currency->getCode();?> <?php echo $product_price; ?></p>
				</td>
				<td>
					<h4 class="shop-name"><?php echo $store_name; ?></h4>
					<p class="product-name"><?php echo $product_name; ?></p>
					<img src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($code, $symbology, 1)); ?>">
					<p class="code"><?php echo $code; ?></p>
					<p class="price"><?php echo $language->get('text_price'); ?>: <?php echo $currency->getCode();?> <?php echo $product_price; ?></p>
				</td>
				<td>
					<h4 class="shop-name"><?php echo $store_name; ?></h4>
					<p class="product-name"><?php echo $product_name; ?></p>
					<img src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($code, $symbology, 1)); ?>">
					<p class="code"><?php echo $code; ?></p>
					<p class="price"><?php echo $language->get('text_price'); ?>: <?php echo $currency->getCode();?> <?php echo $product_price; ?></p>
				</td>
				<td>
					<h4 class="shop-name"><?php echo $store_name; ?></h4>
					<p class="product-name"><?php echo $product_name; ?></p>
					<img src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($code, $symbology, 1)); ?>">
					<p class="code"><?php echo $code; ?></p>
					<p class="price"><?php echo $language->get('text_price'); ?>: <?php echo $currency->getCode();?> <?php echo $product_price; ?></p>
				</td>
				<td>
					<h4 class="shop-name"><?php echo $store_name; ?></h4>
					<p class="product-name"><?php echo $product_name; ?></p>
					<img src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($code, $symbology, 1)); ?>">
					<p class="code"><?php echo $code; ?></p>
					<p class="price"><?php echo $language->get('text_price'); ?>: <?php echo $currency->getCode();?> <?php echo $product_price; ?></p>
				</td>
			</tr>
		</tbody>
	<?php endfor; ?>
	</table>
</div>

<?php $Hooks->do_action('After_Showing_Barcode_List');?>

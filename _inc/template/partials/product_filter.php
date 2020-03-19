<div class="btn-group">
  <button type="button" class="btn btn-info">
  	<span class="fa fa-fw fa-filter"></span> 
  	<?php if (isset($request->get['sup_id'])) : ?>
  		<?php echo get_the_supplier($request->get['sup_id'], 'sup_name'); ?> (<?php echo total_product_of_supplier($request->get['sup_id']); ?>)
    <?php else: ?>
    	<?php echo $language->get('label_all_product'); ?>
    <?php endif; ?>
  </button>
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
      <span class="caret"></span>
      <span class="sr-only">Toggle Dropdown</span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li>
      <a href="product.php">
        <span>
          <?php echo $language->get('label_all_product'); ?>
        </span>
      </a>
    </li>
    <?php
    $statement = $db->prepare("SELECT DISTINCT(`sup_id`) FROM `products` LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`) WHERE `p2s`.`store_id` = ?");
    $statement->execute(array(store_id()));
    $suppliers = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($suppliers as $the_supplier) :
      $the_supplier_id = $the_supplier['sup_id'];
      $statement1 = $db->prepare("SELECT * FROM `suppliers` WHERE `sup_id` = ?");
      $statement1->execute(array($the_supplier_id));
      $the_supplier = $statement1->fetch(PDO::FETCH_ASSOC);
      if ($the_supplier) : ?>
        <li class="supplier-name<?php echo isset($request->get['sup_id']) && $request->get['sup_id'] == $the_supplier_id ? ' active' : null; ?>">
            <a href="product.php?sup_id=<?php echo $the_supplier_id; ?>">
              <span><?php echo $the_supplier['sup_name']; ?> (<?php echo total_product_of_supplier($the_supplier_id); ?>)</span>
            </a>
        </li>
    <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</div>
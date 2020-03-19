<div class="table-responsive">
  <h4 class="text-center"><b><?php echo $language->get('text_title_sells_overview'); ?></b></h4>
  <table class="table table-striped table-condenced">
    <tbody>
      <tr>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_invoice_amount'); ?></h4>
          <h2 class="price">
              <?php 
              $total_selling_price = selling_price(from(), to());
              echo currency_format($total_selling_price); ?>
          </h2>
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'show_buy_price')) : ?>
            <p class="description">
              <?php echo $language->get('text_buying_price'); ?>
              <label class="control-label">
                <span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_buy_price_of_sell'); ?>"></span>
              </label> : 
              <?php echo currency_format(sell_buying_price(from(), to())); ?>
            </p>
          <?php endif; ?>
          <br>
          <a href="report_sell_itemwise.php" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_discount_amount'); ?></h4>
          <h2 class="price">
            <?php echo currency_format(discount_amount(from(), to())); ?>
          </h2>
        </td>
        <td class="text-center bg-danger">
          <h4><?php echo $language->get('text_due_given'); ?></h4>
          <h2 class="price">
            <?php 
              $total_due_amount = due_amount(from(), to());
              echo currency_format($total_due_amount);?>
          </h2>
          <br>
          <a href="invoice.php?type=due" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
      </tr>
      <tr>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_due_collection'); ?></h4>
          <h2 class="price">
            <?php $due_collection_amount = due_collection_amount(from(), to());
            echo currency_format($due_collection_amount); ?>
          </h2>
          <br>
          <a href="report_customer_due_collection.php" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
        <td class="text-center bg-warning">
          <h4><?php echo $language->get('text_cash_received'); ?></h4>
          <h2 class="price">
            <?php 
              $total_received_amount = received_amount(from(), to());
              echo currency_format($total_received_amount); ?>
          </h2>
          <br>
          <a href="report_sell_payment.php" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'show_profit')) : ?>
          <td class="text-center bg-success">
            <?php 
              $profit_amount = profit_amount(from(), to());
            ?>
            <h4><?php echo $language->get('text_profit_or_loss'); ?></h4>
            <h2 class="price">
              <?php echo currency_format($profit_amount); ?>
            </h2>
          </td>
        <?php endif; ?>
      </tr>
    </tbody>
  </table>

  <table class="table table-striped table-condenced">
    <tbody>  
      <tr>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_order_tax'); ?></h4>
          <h2 class="price">
            <?php 
            $order_tax = get_tax('order_tax',from(), to());
            echo currency_format($order_tax); ?>
          </h2>
        </td>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_item_tax'); ?></h4>
          <h2 class="price">
            <?php 
            $item_tax = get_in_or_exclusive_tax('exlcusive',from(), to());
            echo currency_format($item_tax); ?>
          </h2>
        </td>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_total_tax'); ?></h4>
          <h2 class="price">
            <?php 
            $total_tax = $order_tax + $item_tax;
            echo currency_format($total_tax); ?>
          </h2>
        </td>
      </tr>
    </tbody>
  </table>

  <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
  <h4 class="text-center"><b><?php echo $language->get('text_selling_tax'); ?> (GST)</b></h4>
  <table class="table table-striped table-condenced">
    <tbody>  
      <tr>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_igst'); ?></h4>
          <h2 class="price">
            <?php $igst = get_tax('igst', from(), to());
            echo currency_format($igst); ?>
          </h2>
        </td>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_cgst'); ?></h4>
          <h2 class="price">
            <?php 
            $cgst = get_tax('cgst', from(), to());
            echo currency_format($cgst); ?>
          </h2>
        </td>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_sgst'); ?></h4>
          <h2 class="price">
            <?php 
              $sgst = get_tax('sgst', from(), to());
              echo currency_format($sgst); ?>
          </h2>
        </td>
      </tr>
    </tbody>
  </table>
  <?php endif; ?>

</div>
<div class="table-responsive">
  <h4 class="text-center"><b><?php echo $language->get('text_title_Buy_overview'); ?></b></h4>
  <table class="table table-striped table-condenced">
    <tbody>

      <tr>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_buy_amount'); ?></h4>
          <h2 class="price">
            <?php $buying_price = buying_price(from(), to());
            echo currency_format($buying_price); ?>
          </h2>
          <br>
          <a href="report_buy_itemwise.php" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
        <td class="text-center bg-danger">
          <h4><?php echo $language->get('text_due_taken'); ?></h4>
          <h2 class="price">
            <?php 
              $total_due_amount = buying_due_amount(from(), to());
              echo currency_format($total_due_amount);?>
          </h2>
          <br>
          <a href="purchase.php?type=due" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_due_paid'); ?></h4>
          <h2 class="price">
            <?php $due_paid_amount = buying_due_paid_amount(from(), to());
            echo currency_format($due_paid_amount); ?>
          </h2>
          <br>
          <a href="report_supplier_due_paid.php" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
      </tr>

      <tr>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_total_paid'); ?></h4>
          <h2 class="price">
            <?php 
              $total_received_amount = buying_total_paid(from(), to());
              echo currency_format($total_received_amount); ?>
          </h2>
          <br>
          <a href="purchase.php?type=paid" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
        <td class="text-center"></td>
        <td class="text-center"></td>
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
            $order_tax = get_buy_tax('order_tax',from(), to());
            echo currency_format($order_tax); ?>
          </h2>
        </td>
        <td class="text-center bg-warning">
          <h4><?php echo $language->get('text_item_tax'); ?></h4>
          <h2 class="price">
            <?php 
            $item_tax = get_in_or_exclusive_buy_tax('exclusive',from(), to());
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
          <br>
          <a href="expense.php" target="_blink">
            <?php echo $language->get('button_details'); ?> &rarr;
          </a>
        </td>
      </tr>
    </tbody>
  </table>

  <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
  <h4 class="text-center"><b><?php echo $language->get('text_buying_tax'); ?> (GST)</b></h4>
  <table class="table table-striped table-condenced">
    <tbody>  
      <tr>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_igst'); ?></h4>
          <h2 class="price">
            <?php $igst = get_buy_tax('igst', from(), to());
            echo currency_format($igst); ?>
          </h2>
        </td>
        <td class="text-center bg-warning">
          <h4><?php echo $language->get('text_cgst'); ?></h4>
          <h2 class="price">
            <?php 
            $cgst = get_buy_tax('cgst', from(), to());
            echo currency_format($cgst); ?>
          </h2>
        </td>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_sgst'); ?></h4>
          <h2 class="price">
            <?php 
              $sgst = get_buy_tax('sgst', from(), to());
              echo currency_format($sgst); ?>
          </h2>
        </td>
      </tr>
    </tbody>
  </table>
  <?php endif; ?>

</div>
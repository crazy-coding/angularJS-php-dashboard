<table class="table table-bordered profit_calc">
  <tbody>
    <tr>
      <td class="text-right bg-yellow">
        <strong>
          <?php echo $language->get('text_selling_price'); ?>
        </strong>
      </td>
      <td class="text-right bg-green">
        <?php 
          $order_tax = get_tax('order_tax', from(), to());
          $item_tax = get_tax('item_tax', from(), to());
          $tax = $order_tax + $item_tax;
          $totalSellingPrice = selling_price(from(), to()) - $tax;
          echo currency_format($totalSellingPrice);
        ?>
      </td>
    </tr>
    <tr>
      <td class="text-right bg-yellow">
        <strong>
          <?php echo $language->get('text_tax_amount'); ?>
         </strong>
        </td>
      <td class="text-right bg-green">
        <?php
          echo currency_format($tax);
        ?>
      </td>
    </tr>
    <?php 
    $totalPurchasePrice = sell_buying_price(from(), to());
    if ($user->getGroupId() == 1 || $user->hasPermission('access', 'show_buy_price')) : ?>
      <tr>
        <td class="text-right bg-yellow">
          <strong>
            <?php echo $language->get('text_buying_price'); ?>
           </strong>
          </td>
        <td class="text-right bg-green">
          <?php
            
            echo currency_format($totalPurchasePrice);
          ?>
        </td>
      </tr>
    <?php endif; ?>
    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'show_profit')) : ?>
      <tr>
        <td class="text-right bg-blue">
          <strong>
            <?php echo $language->get('text_profit'); ?>
           </strong>
          </td>
        <td class="text-right bg-blue">
          <?php echo currency_format($totalSellingPrice - $totalPurchasePrice); ?>
        </td>
      </tr>
    <?php endif; ?>
    <tr class="info">
      <td class="text-center bg-gray">&nbsp;</td>
      <td class="text-center bg-red">
        <strong>
          <?php echo $language->get('text_due_amount'); ?>: 
        </strong>
        <?php echo currency_format(due_amount(from(), to())); ?>
      </td>
    </tr>
  </tbody>
</table>
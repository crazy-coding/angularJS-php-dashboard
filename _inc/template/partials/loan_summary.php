<div class="table-responsive">
  <table class="table table-striped table-condenced">
    <tbody>
      <tr>
        <td class="text-center bg-info">
          <h4><?php echo $language->get('text_total_loan'); ?></h4>
          <h2 class="price">
              <?php 
              $total_loan = get_total_loan(from(), to());
              echo currency_format($total_loan); ?>
          </h2>
        </td>
        <td class="text-center bg-success">
          <h4><?php echo $language->get('text_total_paid'); ?></h4>
          <h2 class="price">
            <?php echo currency_format(get_total_loan_paid(from(), to())); ?>
          </h2>
        </td>
        <td class="text-center bg-danger">
          <h4><?php echo $language->get('text_total_due'); ?></h4>
          <h2 class="price">
            <?php 
              $total_due = get_total_laon_due(from(), to());
              echo currency_format($total_due);?>
          </h2>
          <br>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<?php
  $hide_colums = "1, ";
?> 
<div class="table-responsive" ng-controller="ReportStockController">
  <table id="report_stock" class="table table-striped" data-hide-colums="<?php echo $hide_colums; ?>">
    <thead>
      <tr class="bg-gray">
        <th class="w-5">
          <?php echo $language->get('label_serial_no'); ?>
        </th>
        <th class="w-25">
          <?php echo $language->get('supplier_name'); ?>
        </th>
        <th class="w-40">
          <?php echo sprintf($language->get('label_name'), null); ?>
        </th>
        <th class="text-right w-15">
          <?php echo $language->get('label_available'); ?>
        </th>
        <th class="text-right w-15">
          <?php echo $language->get('label_price'); ?>
        </th>
      </tr>
    </thead>
    <tfoot>
      <tr class="bg-gray">
        <th class="w-5">
          <?php echo $language->get('label_serial_no'); ?>
        </th>
        <th class="w-25">
          <?php echo $language->get('supplier_name'); ?>
        </th>
        <th class="w-40">
          <?php echo sprintf($language->get('label_name'), null); ?>
        </th>
        <th class="text-right w-15">
          <?php echo $language->get('label_available'); ?>
        </th>
        <th class="text-right w-15">
          <?php echo $language->get('label_price'); ?>
        </th>
      </tr>
    </tfoot>
  </table>
</div>
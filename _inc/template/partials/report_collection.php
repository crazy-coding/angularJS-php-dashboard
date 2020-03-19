<?php
  $hide_colums = "";
  //  Load Language File
  $language->load('aa/report_collection');
?> 
<div class="table-responsive" ng-controller="ReportCollectionController">
  <table id="report_collection" class="table table-striped table-bordered" data-hide-colums="<?php echo $hide_colums; ?>">
    <thead>
      <tr class="bg-gray">
        <th class="w-5"><?php echo $language->get('label_serial_no'); ?></th>
        <th class="w-30"><?php echo $language->get('label_username'); ?></th>
        <th class="w-10"><?php echo $language->get('label_total_inv'); ?></th>
        <th class="w-10"><?php echo $language->get('label_net_amount'); ?></th>
        <th class="w-10"><?php echo $language->get('label_prev_due_collection'); ?></th>
        <th class="w-10"><?php echo $language->get('label_due_collection'); ?></th>
        <th class="w-10"><?php echo $language->get('label_due_given'); ?></th>
        <th class="w-10"><?php echo $language->get('label_received'); ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr class="bg-gray">
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>
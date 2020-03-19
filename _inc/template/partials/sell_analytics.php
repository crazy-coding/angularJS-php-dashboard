<?php
$year = from() ? date('Y', strtotime(from())) : year();
$month = from() ? date('m', strtotime(from())) : month();
?>
<div class="box box-info"> 
  <div class="box-header with-border">
    <h4 class="box-title">
      <?php echo $language->get('text_sells_analytics'); ?>
      &rarr;<?php echo date("F", mktime(0, 0, 0, $month, 10)) . ', ' .$year; ?>
    </h4>
    <div class="box-tools pull-right">
      <div class="btn-group">
        <a class="btn btn-xs btn-default" href="sell-analytics-chart.js" id="save-analytics-chart-as-jpg"><span class="fa fa-fw fa-download"></span>Download as PNG</a>
      </div>
    </div>
  </div>
  <div class="box-body">
      <canvas id="sell-analytics-chart" class="comparison-chart"></canvas>
  </div>
  <div class="box-footer text-center">
    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_analytics')) : ?>
      <a href="report_sell_itemwise.php">
        <?php echo $language->get('text_details'); ?> <i class="fa fa-arrow-circle-right"></i>
      </a>
    <?php else:?>
        &nbsp;
    <?php endif;?>
  </div>
</div>

<?php 
$days_array = array();
$sells_array = array();
$received_array = array();
$total_days = get_total_day_in_month() + 1;
for ($i=1; $i < $total_days; $i++) { 
  $days_array[] = "Days: " . $i;
  $total = selling_price_daywise($year, $month, $i);
  $sells_array[] = $total ? number_format((float)$total, 2, '.', '') : 0;
  $total = received_amount_daywise($year, $month, $i);
  $received_array[] = $total ? number_format((float)$total, 2, '.', '') : 0;
  $total = profit_amount_daywise($year, $month, $i);
  $profit_array[] = $total ? number_format((float)$total, 2, '.', '') : 0;
}
?>

<script type="text/javascript"> 
$(function() {
  var labels = <?php echo json_encode($days_array); ?>;
  var sellData = <?php echo json_encode($sells_array); ?>;
  var receivedData = <?php echo json_encode($received_array); ?>;
  var profitData = <?php echo json_encode($profit_array); ?>;
  var ctx = document.getElementById("sell-analytics-chart");
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: labels,
          datasets: [
              {
                  label: "Sells",
                  borderColor: "#27CDF7",
                  borderWidth: "1",
                  backgroundColor: "#27CDF7",
                  pointHighlightStroke: "rgba(26,179,148,1)",
                  data: sellData
              },
              {
                  label: "Received",
                  borderColor: "#27CDF7",
                  borderWidth: "1",
                  backgroundColor: "#00A65A",
                  pointHighlightStroke: "rgba(26,179,148,1)",
                  data: receivedData
              },
              {
                  label: "Profit",
                  borderColor: "#f39c12",
                  borderWidth: "1",
                  backgroundColor: "#f39c12",
                  pointHighlightStroke: "rgba(26,179,148,1)",
                  data: profitData
              }
          ]
      },
      options: {
          responsive: true,
          tooltips: {
              mode: 'index',
              intersect: false
          },
          hover: {
              mode: 'nearest',
              intersect: true
          },
          barPercentage: 0.5
      }
  });
  $("#save-analytics-chart-as-jpg").on("click",function(e) {
    var link = $(this);
    var canvas = document.getElementById("sell-analytics-chart");
    var img    = canvas.toDataURL("image/png");
    link.attr("href",img);
    link.attr("download","sell-analytics-"+window.formatDate(new Date())+".png");
  });
});
</script>
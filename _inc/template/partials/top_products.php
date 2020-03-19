<?php 
if (top_product(from(), to(), 10)) {
  foreach (top_product(from(), to(), 10) as $product) {
    $top_product['name'][] = $product['item_name'];
    $top_product['quantity'][] = $product['quantity'];
  } 
} else {
  $top_product['name'] = array();
  $top_product['quantity'] = array();
}
?>
<div class="row">
  <div class="col-md-4 col-md-offset-4">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">
          <?php echo $language->get('text_top_product'); ?>
        </h3>
      </div>
      <div class="box-body">
        <canvas id="topProduct" height="250"></canvas>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  var topProducts = <?php echo json_encode(array_values($top_product['name'])); ?>;
  var topProductsQuantity = <?php echo json_encode(array_values($top_product['quantity'])); ?>;
  var ctx = document.getElementById("topProduct");
  var myPieChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: topProducts,
        datasets: [
            {
              label: "Top",
              backgroundColor: ["#e6194B", "#f58231", "#ffe119", "#3cb44b", "#4363d8", "#f032e6", "#42d4f4", "#9A6324", "#469990", "#fabebe"],
              data: topProductsQuantity
            },
        ],
      },
      options: {
          responsive: true,
          tooltips: {
              mode: 'index',
              intersect: true
          },
          hover: {
              mode: 'nearest',
              intersect: true
          }
      }
  });
});
</script>
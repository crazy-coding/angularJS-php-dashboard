<?php if (from()) : ?>
    <?php 
    $query_string = '';
    if (!empty($request->get)) {
        $inc = 1;
        foreach ($request->get as $key => $value) {
          if (!in_array($key, array('from', 'to'))) {
            if ($inc == 1) {
                $query_string = '?'.$key.'='.$value;
            } else {
                $query_string .= '&'.$key.'='.$value;
            }
            $inc++;
          }
        }
    } 
    $from = date('Y-m-d 00:00:00', strtotime(from())); 
      $to = to() ? date('Y-m-d 23:59:59', strtotime(to())) : date('Y-m-d 23:59:59', strtotime(from()));
    ?>

    <div class="apply-filter">
        <a href="<?php echo relative_url().$query_string; ?>" class="btn btn-xs btn-info" title="Remove this filter">
            <strong><?php echo format_date(date('Y-m-d', strtotime($from))); ?>
            </strong>&nbsp;<i>To</i> &nbsp;
            <strong><?php echo format_date(date('Y-m-d 23:59:59', strtotime($to))); ?></strong> 
            <i class="fa fa-fw fa-close text-red"></i>
        </a>
    </div>

    

<?php endif; ?>
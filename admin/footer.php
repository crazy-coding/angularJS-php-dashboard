    <footer class="main-footer">
    	<div class="pull-right hidden-xs">
            <?php echo $language->get('text_version'); ?>
            <?php echo settings('version'); ?>
    	</div>
    	<div class="copyright">Copyright © <?php echo date('Y'); ?> <a href="http://itsolution24.com">ITsolution24.com</a>, All rights reserved.</div>
    </footer>
</div>
<!-- End Wrapper -->

<!-- Start Filter Box -->
<div id="filter-box" class="text-center">
    <div class="jumbotron">
        <div class="container">
            <form action="" method="get">
                <?php if (!empty($request->get)) : ?>
                    <?php foreach ($request->get as $key => $value) : ?>
                      <?php if (!in_array($key, array('from', 'to'))) : ?>
                        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
                      <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="col-md-1"></div>
                <div class="col-md-4 form-group-lg">
                    <input class="form-control date" type="date" name="from" value="<?php echo isset($request->get['from']) ? $request->get['from'] : null;?>" placeholder="From" readonly>
                </div>
                <div class="col-md-4 form-group-lg">
                    <input class="form-control date" type="date" name="to" value="<?php echo isset($request->get['to']) ? $request->get['to'] : null;?>" placeholder="To" readonly>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-block btn-lg btn-danger" type="submit">
                        <span class="fa fa-search"></span>
                    </button>
                </div>
                <div class="col-md-1"></div>
            </form>
        </div>
    </div>
    <div id="close-filter-box">
        <span class="fa fa-angle-up" title="Close"></span>
    </div>
</div>
<!-- End Filter Box -->

<script type="text/javascript">
var from = "<?php echo from() ? format_date(from()) : format_date('Y/m/d'); ?>";
var to = "<?php echo to() ? format_date(to()) : format_date('Y/m/d'); ?>";
</script>

<!-- Runtime JS -->
<?php 
foreach ($scripts as $script) : ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php endforeach; ?>

<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of #MODERN POS.</p>
        </div>
    </div>
</noscript>

</body>
</html>
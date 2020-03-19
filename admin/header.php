<?php
$body_class = $document->getBodyClass();
$title = $document->getTitle();
$description = $document->getDescription();
$keywords = $document->getKeywords();
$styles = $document->getStyles();
$scripts = $document->getScripts(); 
?>
<!DOCTYPE html>
<html lang="<?php echo $document->langTag($active_lang);?>" <?php echo !isset($angular_disabled) ? 'ng-app="angularApp"' : null; ?>>
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title ? $title . ' &raquo; ' : null; ?><?php echo store('name'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">

    <!-- Meta Description -->
    <?php if ($description) : ?>
    <meta name="description" content="<?php echo $description; ?>">
    <?php endif; ?>

    <!-- Meta Keywords -->
    <?php if ($keywords) : ?>
    <meta name="keywords" content="<?php echo $keywords; ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <?php if ($store->get('favicon')): ?>
        <link rel="shortcut icon" href="../assets/itsolution24/img/logo-favicons/<?php echo $store->get('favicon'); ?>">
    <?php else: ?>
        <link rel="shortcut icon" href="../assets/itsolution24/img/logo-favicons/nofavicon.png">
    <?php endif; ?>

    <?php if (DEMO || USECOMPILEDASSET) : ?>

        <link type="text/css" href="../assets/itsolution24/cssmin/main.css" type="text/css" rel="stylesheet">

    <?php  else: ?>
  
    <!-- Plugin CSS -->
        <!-- Bootstrap CSS -->
        <link type="text/css" href="../assets/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">

        <!-- jquery UI CSS -->
        <link type="text/css" href="../assets/jquery-ui/jquery-ui.min.css" type="text/css" rel="stylesheet">

        <!-- Font-Awesome CSS -->
        <link type="text/css" href="../assets/font-awesome/css/font-awesome.css" type="text/css" rel="stylesheet">

        <!-- Morris CSS -->
        <link type="text/css" href="../assets/morris/morris.css" type="text/css" rel="stylesheet">

        <!-- Select2 CSS -->
        <link type="text/css" href="../assets/select2/select2.min.css" type="text/css" rel="stylesheet">

        <!-- Datepicker3 CSS-->
        <link type="text/css" href="../assets/datepicker/datepicker3.css" type="text/css" rel="stylesheet">

        <!-- Bootstrap Timepicker CSS -->
        <link type="text/css" href="../assets/timepicker/bootstrap-timepicker.min.css" type="text/css" rel="stylesheet">

        <!-- Bootstrap3 Wysihtml5 CSS -->
        <link type="text/css" href="../assets/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" type="text/css" rel="stylesheet">

        <!-- Perfect-scrollbar CSS -->
        <link type="text/css" href="../assets/perfectScroll/css/perfect-scrollbar.css" type="text/css" rel="stylesheet">

        <!-- Toastr CSS -->
        <link type="text/css" href="../assets/toastr/toastr.min.css" type="text/css" rel="stylesheet">

        <!-- Filemanager Dialogs CSS -->
        <link type="text/css" href="../assets/itsolution24/css/filemanager/dialogs.css" type="text/css" rel="stylesheet">

        <!-- Filemanager Main CSS -->
        <link type="text/css" href="../assets/itsolution24/css/filemanager/main.css" type="text/css" rel="stylesheet">

    <!-- Theme CSS -->
        <link type="text/css" href="../assets/itsolution24/css/theme.css" type="text/css" rel="stylesheet">

        <!-- Skin Black CSS -->
        <link type="text/css" href="../assets/itsolution24/css/skins/skin-black.css" type="text/css" rel="stylesheet">

        <!-- Skin Blue CSS -->
        <link type="text/css" href="../assets/itsolution24/css/skins/skin-blue.css" type="text/css" rel="stylesheet">

        <!-- Skin Green CSS-->
        <link type="text/css" href="../assets/itsolution24/css/skins/skin-green.css" type="text/css" rel="stylesheet">

        <!-- Skin Red CSS -->
        <link type="text/css" href="../assets/itsolution24/css/skins/skin-red.css" type="text/css" rel="stylesheet">

        <!-- Skin Yellow CSS -->
        <link type="text/css" href="../assets/itsolution24/css/skins/skin-yellow.css" type="text/css" rel="stylesheet">

        <!-- Datatables CSS -->
        <link type="text/css" href="../assets/DataTables/datatables.min.css" type="text/css" rel="stylesheet">

        <!-- Main CSS -->
        <link href="../assets/itsolution24/css/main.css" type="text/css" rel="stylesheet">

        <!-- Responsive CSS -->
        <link href="../assets/itsolution24/css/responsive.css" type="text/css" rel="stylesheet">

        <!-- Print CSS -->
        <link href="../assets/itsolution24/css/print.css" media="print" type="text/css" rel="stylesheet">

    <?php endif; ?>

    <!-- Add Runtime CSS -->
    <?php foreach ($styles as $style) : ?>
    <link type="text/css" href="<?php echo $style['href']; ?>" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>">
    <?php endforeach; ?>

    <script type="text/javascript">
        var baseUrl = "<?php echo trim(root_url(),'/'); ?>";
        var adminDir = "<?php echo ADMINDIRNAME; ?>";
        var user = <?php echo json_encode(get_the_user(user_id())); ?>;
        var lang = "<?php echo $active_lang;?>";
        var settings = <?php echo json_encode(get_all_preference()); ?>;
        var store = <?php echo json_encode(store()); ?>;
        var totalProduct = <?php echo total_product(); ?>;
        var deviceType = '<?php echo ($deviceType); ?>';
        var filemanager = '<?php echo get_preference('ftp_hostname') && get_preference('ftp_username') ? 'ftp' : 'local'; ?>';
        var redirectTo = "<?php echo isset($request->get['redirect_to']) ? $request->get['redirect_to'] : 'admin/dashboard'; ?>";
        <?php if ($store->get('logo')): ?>
            var logo = '<?php echo root_url(); ?>/assets/itsolution24/img/logo-favicons/<?php echo $store->get('logo'); ?>';
        <?php else: ?>
            var logo = '<?php echo root_url(); ?>/assets/itsolution24/img/logo-favicons/nologo.png';
        <?php endif; ?>
    </script>

    <?php if (DEMO || USECOMPILEDASSET) : ?>

        <script src="../assets/itsolution24/jsmin/main.js" type="text/javascript"></script>

    <?php else : ?>

        <!-- jQuery JS  -->
        <script src="../assets/jquery/jquery.min.js" type="text/javascript"></script> 

        <!-- jQuery Ui JS -->
        <script src="../assets/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>

        <!-- Bootstrap JS -->
        <script src="../assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

        <!-- Chart JS -->
        <script src="../assets/chartjs/Chart.min.js" type="text/javascript"></script>

        <!-- Jquery Sparkline JS -->
        <script src="../assets/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>

        <!-- Bootstrap Datepicker JS -->
        <script src="../assets/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>

        <!-- Bootstrap Timepicker JS-->
        <script src="../assets/timepicker/bootstrap-timepicker.min.js" type="text/javascript" ></script>

        <!-- Bootstrap3 Wysihtml5 All JS -->
        <script src="../assets/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>

        <!-- Select2 JS -->
        <script src="../assets/select2/select2.min.js" type="text/javascript"></script>

        <!-- Perfect Scrollbar JS -->
        <script src="../assets/perfectScroll/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>

        <!-- Sweetalert JS-->
        <script src="../assets/sweetalert/sweetalert.min.js" type="text/javascript"></script>

        <!-- Totastr JS -->
        <script src="../assets/toastr/toastr.min.js" type="text/javascript"></script>

        <!-- Accounting JS -->
        <script src="../assets/accounting/accounting.min.js" type="text/javascript"></script>

        <!-- Underscore JS -->
        <script src="../assets/underscore/underscore.min.js" type="text/javascript"></script>

        <!-- IE JS -->
        <script src="../assets/itsolution24/js/ie.js" type="text/javascript"></script>

        <!-- Theme JS -->
        <script src="../assets/itsolution24/js/theme.js" type="text/javascript"></script>

        <!-- Common JS -->
        <script src="../assets/itsolution24/js/common.js" type="text/javascript"></script>

        <!-- Main JS-->
        <script src="../assets/itsolution24/js/main.js" type="text/javascript"></script>

        <!-- Datatables JS -->
        <script src="../assets/DataTables/datatables.min.js" type="text/javascript"></script>

        <!-- Angular JS -->
        <script src="../assets/itsolution24/angularmin/angular.js" type="text/javascript"></script> 

        <!-- Angular App JS -->
        <script src="../assets/itsolution24/angular/angularApp.js" type="text/javascript"></script>

        <!-- Angular Modal JS -->
        <script src="../assets/itsolution24/angularmin/modal.js" type="text/javascript"></script>

        <!-- Anguar Filemanager JS -->
        <script src="../assets/itsolution24/angularmin/filemanager.js" type="text/javascript"></script>
    <?php endif; ?>
</head>
<body class="hold-transition sidebar-mini<?php echo $body_class; ?>">
<div class="hidden"><?php include('../assets/itsolution24/img/iconmin/icon.svg');?></div>
<div class="wrapper">
<?php include '../_inc/template/partials/top.php'; ?>
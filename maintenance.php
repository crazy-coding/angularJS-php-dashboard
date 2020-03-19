<?php 
include ("_init.php"); 
if (!file_exists(ROOT.DIRECTORY_SEPARATOR.'.maintenance')) {
    header('Location: index.php', true, 302);
}?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title>Site Maintenance</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <style type="text/css">
		body { text-align: center; padding: 100px; }
		h1 { font-size: 50px; }
		body { font: 20px Helvetica, sans-serif; color: #333; }
		#wrapper { display: block; text-align: left; width: 650px; margin: 0 auto; }
        a { color: #dc8100; text-decoration: none; }
        a:hover { color: #333; text-decoration: none; }
        #content p {
            line-height: 1.444;
        }
        @media screen and (max-width: 768px) {
          body { text-align: center; padding: 20px; }
          h1 { font-size: 30px; }
          body { font: 20px Helvetica, sans-serif; color: #333; }
          #wrapper { display: block; text-align: left; width: 100%; margin: 0 auto; }
        }
    </style>
</head>
<body>
    <section id="wrapper">
        <h1>Resetting, Please wait...</h1>
        <div id="content">
            <p>System will reset modern pos data to default. Please, keep patience a bit. We are coming back in live soon!</p>
            <p>&mdash; <a target="_blink" href="https://itsolution24.com" title="ITsolution24.com">ITsolution24.com</a></p>
        </div>
    </section>
</body>
</html>
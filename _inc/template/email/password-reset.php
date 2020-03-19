Dear <?php echo $recipient_name; ?>! 
<br/>
Recently a request was submitted to reset a password for your account. If this was a mistake, just ignore this email and nothing will happen.
<br/><br/>
To reset your password, visit the following link:
<a href="<?php echo $reset_pass_link; ?>"><?php echo $reset_pass_link; ?></a>
<br/><br/>
Regards,
<br/>
<?php echo $from_name; ?>
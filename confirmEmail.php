<?php
require_once 'init.php';
require_once 'util.php';
require_once 'apiFunctions.php';
connectDB();
bifPageheader('confirm e-mail');

$userid = GETvalue('id');
log_message("confirming address for id $userid");
$row = dbQueryByID('select email from user where id=?',$userid);
if ($row)
    {
    $code = GETvalue('code');
    $expectedCode = md5('bif-' . strtolower($row['email']));
    if ($code == $expectedCode)
        {
        log_message("email confirmed for $row[email]");
        addPrivilege($userid,'confirmed');
        echo '<p>Thank you.  Your e-mail address is confirmed. You are now able to submit proposals for the Infringement Festival.<br>While you are at it why not <a href="http://infringebuffalo.org/forum/" target="_blank">join our Infringement Forum!</a>  While there you can ask questions to our orginizers, collaborate with fellow artists, and generally become more involved with Infringement!  We look forward to hearing from you.</p>';
        }
    else
        {
        log_message("WARNING: email confirmation for $row[email] failed: expected '$expectedCode', got '$code'");
        echo "<p>I'm sorry, but the e-mail verification has failed, due to an incorrect verification code.  If you reached this page via an e-mail sent to you by the Infringement proposal system, please contact depape@buffalo.edu for help.</p>";
        }
    }
else
    {
    echo "<p>Could not verify e-mail - the user id $userid is not valid.</p>";
    }

bifPagefooter();
?>

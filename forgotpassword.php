<?php
require_once 'init.php';
require_once 'util.php';
connectDB();

bifPageheader('forgotten password');

$username = $db->real_escape_string(htmlentities(POSTvalue('username'),ENT_COMPAT | ENT_HTML5, "UTF-8"));

$data = dbQueryByString('select id from user where email=?',$username);
if ($data)
    {
    $chars = array_merge(range('a', 'z'),range(0, 9));
    $password = '';
    for ($i=0; $i < 8; $i++)
        $password .= $chars[mt_rand(0,count($chars)-1)];
    $encPassword = md5($password);
    $stmt = dbPrepare('update user set newpassword=? where email=?');
    $stmt->bind_param('ss',$encPassword,$username);
    $stmt->execute();
    $stmt->close();
    log_message("generated new password for $username");

    $email = $username;
    $subject = 'Buffalo Infringement password';
    $body = "A new password has been generated for your infringebuffalo.org login.\r\nThe new password is:\r\n   " . $password . "\r\n\r\n(If you did not request a new password, you may safely ignore this message and continue to use your old password.)\r\n";
    loggedMail($email, $subject, $body);
    echo '<p>Done.  Check your e-mail for the new password.<br/>(Be sure to also check your spam folder if you don\'t see the message with your password soon.)</p><br/><br/><p>Contact Dave Pape [depape@buffalo.edu] with any technical problems.</p>';
    }
else
    {
    log_message("forgotpassword failed - no user '$username'");
    echo '<p>No such account was found</p>';
    }

bifPagefooter();
?>

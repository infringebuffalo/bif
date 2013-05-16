<?php
require_once 'init.php';
require_once 'util.php';
connectDB();

if (loggedIn())
    {
    session_destroy();
    session_start();
    }

$username = htmlentities(POSTvalue('username'));

if (preg_match('/^(Viagra|Cialis|Levitra)$/', $username))
    {
    $_SESSION['createaccountError'] = 'No spam please';
    header('Location: index.php');
    log_message('spam registration blocked');
    die();
    }

if (!preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $username))
    {
    $_SESSION['createaccountError'] = 'Failed to create login: valid e-mail address required';
    header('Location: loginForm.php');
    log_message("bad-address registration blocked ($username)");
    die();
    }

$password = POSTvalue('password');

if ($password != POSTvalue('passwordconfirm'))
    {
    $_SESSION['createaccountError'] = 'Failed to create login: password confirmation was not the same as password';
    header('Location: loginForm.php');
    log_message('registration with mismatched password blocked');
    die();
    }
else
    {
    $row = dbQueryByString('select count(*) from user where email=?',$username);
    if (($row) && ($row['count(*)'] != 0))
        {
        $_SESSION['createaccountError'] = 'Failed to create login: account already exists';
        header('Location: loginForm.php');
        log_message("duplicate registration blocked ($username)");
        die();
        }
    else
        {
        $name = htmlentities(POSTvalue('name'));
        $phone = htmlentities(POSTvalue('phone'));
        $snailmail = htmlentities(POSTvalue('snailmail'));
        $encPassword = md5($password);
        $userid = newEntityID('user');
        $stmt = dbPrepare('insert into user (id,email,password,newpassword,name,phone,snailmail) values (?,?,?,?,?,?,?)');
        $stmt->bind_param('issssss',$userid,$username,$encPassword,$encPassword,$name,$phone,$snailmail);
        $stmt->execute();
        $stmt->close();
        $_SESSION['userid'] = $userid;
        $_SESSION['username'] = $username;
        $_SESSION['privs'] = '';
        log_message("created account $userid $username");
        }
    }

header('Location: index.php');
?>

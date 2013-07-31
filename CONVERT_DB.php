<?php
die('disabled');

require 'init.php';
connectDB();
requirePrivilege('admin');

function dbClear($table)
    {
    $stmt = dbPrepare('delete from ' . $table);
    $stmt->execute();
    $stmt->close();
    }
dbClear('batch');
dbClear('card');
dbClear('entity');
dbClear('festival');
dbClear('image');
dbClear('listing');
dbClear('log');
dbClear('note');
dbClear('proposal');
dbClear('proposalBatch');
dbClear('user');
dbClear('venue');
$stmt = dbPrepare('alter table entity AUTO_INCREMENT=1');
$stmt->execute();
$stmt->close();

/********  Convert userlogin -> user  *************/
$stmt = dbPrepare('select email,name,password,newpassword,privs,phone,snailmail from old_userlogin order by id');
$stmt->execute();
$stmt->bind_result($email,$name,$password,$newpassword,$privs,$phone,$snailmail);
$users = array();
while ($stmt->fetch())
    $users[] = array($email,$name,$password,$newpassword,$privs,$phone,$snailmail);
$stmt->close();
foreach ($users as $u)
    {
    $id = newEntityID('user');
    $stmt = dbPrepare('insert into `user` set id=?, email=?, name=?, password=?, newpassword=?, privs=?, phone=?, snailmail=?');
    $stmt->bind_param('isssssss',$id,$u[0],$u[1],$u[2],$u[3],$u[4],$u[5],$u[6]);
    $stmt->execute();
    $stmt->close();
    }
echo "user table converted<br>\n"; flush();

/********  Create a festival entry     *************/
$festivalid = newEntityID('festival');
$stmt = dbPrepare('insert into `festival` (`id`, `name`, `description`, `startDate`, `numberOfDays`) values (?,?,?,?,?)');
$name='Buffalo Infringement Festival 2013';
$desc='';
$startDate='2013-07-25';
$numDays=11;
$stmt->bind_param('isssi',$festivalid,$name,$desc,$startDate,$numDays);
$stmt->execute();
$stmt->close();
echo "festival entry created<br>\n"; flush();

/********  Convert old venue -> venue  *************/
$stmt = dbPrepare('select name,shortname,owner,address,phone,website,contact1,contactphone1,contactemail1,venuetype,allowedperformances,bestperformances,performancespace,wallspace from old_venue');
$stmt->execute();
$stmt->bind_result($name,$shortname,$owner,$address,$phone,$website,$contact1,$contactphone1,$contactemail1,$venuetype,$allowedperformances,$bestperformances,$performancespace,$wallspace);
$venues = array();
while ($stmt->fetch())
    {
    $name = stripslashes($name);
    $shortname = stripslashes($shortname);
    $venues[] = array($name,$shortname,serialize(array('owner'=>$owner,'address'=>$address,'phone'=>$phone,'website'=>$website,'contact'=>$contact1,'contact phone'=>$contactphone1,'contact e-mail'=>$contactemail1,'venue type'=>$venuetype,'allowed performances'=>$allowedperformances,'best performances'=>$bestperformances,'performance space'=>$performancespace,'wall space'=>$wallspace)));
    }
$stmt->close();
foreach ($venues as $v)
    {
    $id = newEntityID('venue');
    $stmt = dbPrepare('insert into `venue` (id,name,shortname,festival,info) values (?,?,?,?,?)');
    $stmt->bind_param('issis',$id,$v[0],$v[1],$festivalid,$v[2]);
    $stmt->execute();
    $stmt->close();
    }

echo "venue table converted<br>\n"; flush();
?>

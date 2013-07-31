<?php

function bifPageheader($title,$headerExtras='')
{
echo <<<ENDSTRING
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
ENDSTRING;
echo $headerExtras;
echo <<<ENDSTRING
<style type="text/css">
a:hover { color: black; background: white; }
table.bif {
  width: 100%;
  border: none;
  border-collapse: collapse;
  border-spacing: 0;
  empty-cells: show;
  }
table.menubar { text-align: center; }
table.colorized tr:nth-child(even) {background: #e0e0e0; }
table.colorized tr:nth-child(odd) { background: #ffffff; }
.lfloat { float:left; margin-right:0.5em; display:inline}
.rfloat { float:right; margin-left:0.5em; display:inline}
img { border: 0; display:inline }
table.bifcal { margin-left: 3em; margin-bottom: 1em}
table.bifcal td { background: #ddd; font-size:150% }
table.bifcal th { background: #ff8; font-size:150% }
h2 { font-family: Georgia, serif; }
td.good { background: #8f8; }
td.off {}
</style>
<link type="text/css" rel="stylesheet" media="all" href="/style2.css" />
<link type="text/css" rel="stylesheet" media="all" href="/table.css" />
<title>
ENDSTRING;
if ($title != '')
    echo $title . ' | ';
echo <<<ENDSTRING
Buffalo Infringement Festival</title>
</head>
<body>
<table class="bif">
      <tr>
        <td class="bifTopWrapper">
          <table class="bif bif2">
            <tr>
              <td id="sloganLeft">buffalo<br/>infringement<br/>festival</td>
              <td id="logoRight"><img src="/skyline.png" width="292" alt="" /> </td>
            </tr>
          </table>
          <table class="bif menubar">
            <tr>
              <td><img src="/images/lg-ul.png" class="bif" alt=""  /></td>
              <td class="lgu" colspan="4"></td>
              <td><img src="/images/lg-ur.png" class="bif" alt="" /></td>
            </tr>
            <tr>
              <td class="lgl"></td>
              <td class="lgcnt"><a href="/" title="" class="active">Home</a> | <a href="/schedule.php" title="" class="active">Schedule</a> | <a href="/db2" title="" class="active">Proposals</a> | <a href="/support.php" title="">Support</a> | <a href="/publicity.php" title="" class="active">Publicity</a> |  <a href="http://www.infringementfestival.com/">International</a> | <a href="/about.php" title="" class="active">About</a> | <a href="/contact.php" title="" class="active">Contact</a></td>
              <td class="lgcnt"><a href="http://www.facebook.com/home.php?#/group.php?gid=22033482171"><img src='/images/facebook.png' /></a></td>
              <td class="lgcnt"><a href="https://twitter.com/InfringeBuffalo"><img src='/images/twitter.png' /></a></td>
              <td class="lgcnt"><a href="http://flickr.com/groups/infringebuffalo/"><img src='/images/flickr.png' /></a></td>
              <td class="lgr"></td>
              </tr>
            <tr>
              <td><img src="/images/lg-dl.png" class="bif" alt="" /></td>
              <td class="lgd" colspan="4"></td>
              <td><img src="/images/lg-dr.png" class="bif" alt="" /></td>
              </tr>
            </table>
          </td>
        </tr>

      <tr>
        <td id="regionContent">
<table  class="bifo" cellpadding="0" cellspacing="0">
  <thead>
    <tr class="ht">
      <td class="htl" ><img src="/images/or-ul.png" alt="" /></td>
      <td class="htc" ><img src="/images/or-u.png" alt="" /></td>
      <td class="htr" ><img src="/images/or-ur.png" alt="" /></td>
    </tr>
    <tr class="hm"><td class="hml" /><th  class="hmc">
ENDSTRING;
echo $title;
echo <<<ENDSTRING
</th>      <td class="hmr" /></tr>
    <tr class="hb"><td class="hbl" /><td class="hbc" /><td class="hbr" /></tr>
  </thead>
  <tbody>
    <tr class="rt"><td class="rtl"  /><td class="rtc"  /><td class="rtr"  /></tr>
 <tr class="rm even">
<td class="rml" /><td><hr />
ENDSTRING;
}


function bifPagefooter()
{
echo <<<ENDSTRING
</td><td class="rmr" /></tr>

    <tr class="rb">
      <td class="rbl" ><img src="/images/lw-dl.png" alt="" /></td>
      <td class="rbc" />
      <td class="rbr" ><img src="/images/lw-dr.png" alt="" /></td>
    </tr></tbody>
</table>
</td>
</tr>
</table>
</body>
</html>
ENDSTRING;
}
?>

<?php
require_once 'init.php';
require '../bif.php';
connectDB();
requirePrivilege('scheduler');

bifPageheader('new proposal');
?>
<p>
This will create a new, blank proposal entry of the selected type.
It will be owned by the user whose e-mail is given below.  If no account
exists for that e-mail, one will be created.
Once the proposal is made, tell the person to log in and fill in all
the necessary information (for each field on the form, click on it, edit the text, hit "save").
By default, this tool sets the availability to "yes" for all days of the festival.
</p>

<form method="POST" action="createProposal.php">
Type: <select name="formtype">
<option value="literary">literary</option>
<option value="theatre">theatre</option>
<option value="music">music</option>
<option value="dance">dance</option>
<option value="visualart">visualart</option>
<option value="film">film</option>
</select>
<br>
Title: <input type="text" name="title" size="60">
<br>
Proposer's e-mail: <input type="text" name="proposer" size="60">
<br>
Proposer's name: <input type="text" name="proposername" size="60">
<p>
<input type="submit" value="Create">
</form>
<?php
bifPagefooter();
?>

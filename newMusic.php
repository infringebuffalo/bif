<?php
require_once 'init.php';
require_once 'proposalUtil.php';
require '../bif.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

$header = proposalFormHeader() . <<<ENDSTRING
<script type="text/javascript">
function validateForm()
{
var f = document.forms["musicform"];
var fields = ["title", "website", "description_org", "bandgear", "numberperformers", "bandnames", "secondcontactname", "secondcontactemail", "secondcontactphone", "secondcontactaddress", "volunteer", "infringe"];
var okay = true;
for (i=0; i < fields.length; i++)
    if ((f[fields[i]].value == null) || (f[fields[i]].value == ""))
        okay = false;
if (!okay)
    {
    alert("All fields must be filled out before this proposal can be submitted");
    return false;
    }
else
    return true;
}
</script>
ENDSTRING;
bifPageheader('new music proposal',$header);
?>

<p>
Please use this form if you are submitting for a predominantly musical performance. If not, please return to <a href="/db2">www.infringebuffalo.org</a> and select another genre for your project.
</p>
<p>
It is important to be as detailed as possible to help us place you in the proper venue. We will try our best to accommodate all requests and needs, but please understand that it may not be possible. All spaces are donated and we will do the best we can.
</p>
<p>
Please check your emails regularly and provide a phone number where you can be reached. If we cannot get in touch with you, we will assume you do not want to take part in this year's festival and your proposal will be deleted.
</p>
<div style="background:#f88; text-align:center">Note: all fields must be filled in before this form is submitted.</div>

<form method="POST" action="submitProposal.php" name="musicform" onsubmit="return validateForm()">
<?php echo contactForm(true,true); ?>
<div class="projectForm">
<h3>Project</h3>
<table cellpadding=3>
<?php
echo proposalFormType('music');
echo proposalFormTitle('Project title (Band, DJ, or Stage Name)');
echo proposalFormTextinput('Website','website');
echo proposalFormOtherWebsite();
echo proposalFormOver21();
echo proposalFormOtherShows();
echo proposalFormMainMusicGenre();
echo proposalFormSecondMusicGenre();
echo proposalFormDescriptionOrg();
echo proposalFormDescriptionWeb();
echo proposalFormDescriptionBrochure();
echo proposalFormImageLink();
echo proposalFormOtherBandGroups();
echo proposalFormHavePA();
echo proposalFormSharePA();
echo proposalFormWithoutAmp();
echo proposalFormBusking();
echo proposalFormShareDrums();
echo proposalFormDJOwnTables();
echo proposalFormBandGear();
echo proposalFormShareEquipment();
echo proposalFormHowLoud();
echo proposalFormSetupTime();
echo datesCanDo();
echo proposalFormNumberPerformances(5,3);
echo "</table>\n</div>\n";
echo otherQuestions('perform', true);
echo "<p>\n";
echo festivalAgreement('
<li>The main Music Coordinator is Curt Rotterdam.  You must <b>add his e-mail (steelcrazybooking@gmail.com) to your "accepted senders list"</b> so that messages do not go into your spam folder.  His phone number is 716-602-2464. Other music coordinators may be assigned to you as the application process goes forward.</li>
<li><b>Certain sound expenses may occur at some venues</b>, and money may be taken from the door to pay for these costs.</li>
<li><b>No performer is guaranteed financial compensation</b>.  Many shows are free, and the only way of compensation is pass the hat / donation.</li>
<li>If selected to play with an out of town act during a group show, <b>you may be asked to donate portions of your share from the door to the out of town act.</b></li>
<li>You must <b>be at your scheduled venue at least 45 minutes before your scheduled performance time</b>.</li>
<li>If you are scheduled into a group show with other acts, it is proper etiquette, time permitting, to <b>arrive early and stay late to watch the other performers on the bill.</b></li>
');
?>
<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

<?php
require_once 'init.php';
require_once 'proposalUtil.php';
require '../bif.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

bifPageheader('new street performance proposal',proposalFormHeader());
?>

<form method="POST" action="submitProposal.php">
<?php echo contactForm(true); ?>
<div class="projectForm">
<h3>Project</h3>
<table cellpadding=3>
<?php
echo proposalFormType('street');
echo proposalFormTitle();
echo proposalFormTextinput('Website','website');
echo proposalFormOtherWebsite();
echo proposalFormDescriptionOrg();
echo proposalFormDescriptionWeb();
echo proposalFormDescriptionBrochure();
echo proposalFormImageLink();
echo proposalFormPerformers();
echo proposalFormOver21();
echo proposalFormOtherBandGroups();
echo proposalStreetExperience();
echo proposalStreetLicense();
echo proposalFormWithoutAmp();
echo proposalFormNeedOutlet();
echo proposalFormPerformanceSpace();
echo proposalFormStreetVenueFeatures();
echo proposalFormStreetVenueDescription();
echo proposalFormPrearrangedVenue();

echo proposalFormOtherShows();
echo datesCanDo();
?>
</table>
</div>

<?php echo otherQuestions(); ?>

<p>
<?php print festivalAgreement('
<li>The Street Performances Coordinator is Dave Adamczyk. You must <b>add his email (dga8787@aol.com) to your "accepted senders list"</b> so that the emails do not go into your spam folder. You will stay in contact with him, knowing that if you don\'t, your proposal will be deleted.</li>
'); ?>
<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

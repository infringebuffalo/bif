<?php
require_once 'init.php';
require_once 'proposalUtil.php';
require '../bif.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

bifPageheader('new theatre proposal',proposalFormHeader());
?>

<form method="POST" action="submitProposal.php">
<?php echo contactForm(true); ?>
<div class="projectForm">
<h3>Project</h3>
<table cellpadding=3>
<?php
echo proposalFormType('theatre');
echo proposalFormTitle();
echo proposalFormTextinput('Organization','organization');
echo proposalFormTextinput('Website','website');
echo proposalFormDescriptionOrg();
echo proposalFormDescriptionWeb();
echo proposalFormDescriptionBrochure();
echo proposalFormImageLink();
echo proposalFormNumberPerformers();
echo proposalFormSetupTime();
echo proposalFormPerformanceLength();
echo proposalFormStrikeTime();
echo proposalFormNumberPerformances();
echo proposalFormPrearrangedVenue();
echo proposalFormIsStreetTheatre();
echo proposalFormNontraditionalVenue();
echo proposalFormVenueDescription();
echo proposalFormVenueFeatures();
echo proposalFormOver21();
echo proposalFormOtherShows();
echo datesCanDo();
?>
</table>
</div>

<?php echo otherQuestions(); ?>

<p>
<?php print festivalAgreement(); ?>
<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

<?php
require_once 'init.php';
require_once 'proposalUtil.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

bifPageheader('new theatre proposal',proposalFormHeader());
?>

<p>
Please use this form if you plan to perform Theater. Also mixed-art performances with a theatrical bent, and any anything that doesn't seem appropriate for any other genre . If you consider what you plan to do to be mainly theatrical ...this form is for you. If not, please return to <a href="/db2">www.infringebuffalo.org</a> and select another genre for your project.
</p>
<p>
It is important to be as detailed as possible to help us place you in the proper venue. We understand that you might not know all the specifics of your project right now. PLEASE update your information as your project comes together. We will try our best to accommodate all requests and needs, but please understand that it may not be possible. All spaces are donated and we will do the best we can. Please check your emails regularly and provide a phone number where you can be reached. If we cannot get in touch with you, we will bother you incessantly (including but not limited to flying monkeys) and may even delete your proposal from this years festival! (We would really hate to do that, BUT if your situation changes, let us know and we will remove you from the festival with out all the fuss.)
</p>

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
<?php print festivalAgreement('
<li>The Theatre Coordinator is Carly Weiser. You must <b>add her email (infringecarly@gmail.com) to your "accepted senders list"</b> so that the emails do not go into your spam folder. You will stay in contact with her, knowing that if you don\'t, your proposal will be deleted.</li>
'); ?>
<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

<?php
require_once 'init.php';
require_once 'proposalUtil.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

bifPageheader('new literary arts proposal',proposalFormHeader());
?>
<p>
Please use this form if you plan to read/perform/display poetry, fiction, non-fiction or some other literary endeavor. Also for comedy and mixed-art performances with a literary bent. If you consider what you plan to do to be mainly literary...this form is for you. If not, please return to <a href="/db2">www.infringebuffalo.org</a> and select another genre for your project.
</p>
<p>
It is important to be as detailed as possible to help us place you in the proper venue. We will try our best to accommodate all requests and needs, but please understand that it may not be possible. All spaces are donated and we will do the best we can. Please check your emails regularly and provide a phone number where you can be reached. If we cannot get in touch with you, we will assume you do not want to take part in this year's festival and your proposal will be deleted.</p>

<form method="POST" action="submitProposal.php">
<?php echo contactForm(); ?>
<div class="projectForm">
<h3>Project</h3>
<table cellpadding=3>
<?php
echo proposalFormType('literary');
echo proposalFormTitle();
echo proposalFormTextinput('Name of the group putting on the project','organization');
echo proposalFormTextinput('Website','website');
echo proposalFormDescriptionOrg();
echo proposalFormDescriptionWeb();
echo proposalFormDescriptionBrochure();
echo proposalFormImageLink();
echo proposalFormPerformerNames();
echo proposalFormOver21();
echo proposalFormPrearrangedVenue();
echo proposalFormVenueFeatures();
echo proposalFormNumberPerformances();
echo proposalFormOtherShows();
echo datesCanDo();
?>
</table>
</div>

<?php echo otherQuestions(); ?>

<p>
<?php print festivalAgreement('
<li>The Poetry Coordinator is Carly Weiser.  You must <b>add her e-mail (infringecarly@gmail.com) to your "accepted senders list"</b> so that the emails do not go into your spam folder.</li>
'); ?>
<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

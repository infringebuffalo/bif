<?php
require_once 'init.php';
require_once 'proposalUtil.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

bifPageheader('new film/video proposal',proposalFormHeader());
?>

<p>
Please use this form if you plan on submitting a VISUAL FILM PROJECT. If you consider what you plan to do to be a film project, please return to <a href="/db2">www.infringebuffalo.org</a> and select another genre for your project.
</p>
<p>
It is important to be as detailed as possible to help us place you in the proper venue. We will try our best to accommodate all requests and needs, but please understand that it may not be possible. All spaces are donated and we will do the best we can. Please check your emails regularly and provide a phone number where you can be reached. If we cannot get in touch with you, we will assume you do not want to take part in this year's festival and your proposal will be deleted.
</p>
<p>
Films must be submitted on DVD.   We will need at least THREE non copy protected duplications as there may be multiple showings in different venues.  In the past we have had artists submit film proposals before their film was finished.  That is not a significant problem but if the film project is not finished or physically received by the project submission deadline, the proposal will be deleted.   Instructions for submitting your actual video will be given at a later date.</p>

<form method="POST" action="submitProposal.php">
<?php echo contactForm(); ?>
<div class="projectForm">
<h3>Project</h3>
<table cellpadding=3>
<?php
echo proposalFormType('film');
echo proposalFormTitle();
echo proposalFormTextinput('Production company','organization');
echo proposalFormTextinput('Length of film (minutes)','length');
echo proposalFormDescriptionOrg();
echo proposalFormDescriptionWeb();
echo proposalFormDescriptionBrochure();
echo proposalFormImageLink();
echo proposalFormFamilyFriendly();
echo proposalFormOver21();
echo proposalFormFilmVenueFeatures();
echo proposalFormOtherShows();
echo datesCanDo();
?>
</table>
</div>

<?php echo otherQuestions(); ?>

<p>
<?php print festivalAgreement('<li>An <b>advance copy</b> of all films must be delivered to the festival for pre-screening programming purposes</li><li>At least one member of the cast or crew <b>must be in attendance</b> to deliver and receive the film at all scheduled screening dates</li>'); ?>

<input type="Submit" name="submit" value="Submit" />
</p>
</form>

</body>
</html>

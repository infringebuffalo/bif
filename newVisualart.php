<?php
require_once 'init.php';
require_once 'proposalUtil.php';
require '../bif.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

bifPageheader('new visual art proposal',proposalFormHeader());
?>

<p>
Please use this form if you are an artist looking to have a show in this year's Infringement.  If not, please return to <a href="/db2">www.infringebuffalo.org</a> and select another genre for your project.
</p>
<p>
It is important to be as detailed as possible to help us place you in the proper venue.  We will try our best to accommodate all requests and need, but please understand that it may not be possible.  All spaces are donated and we will do the best we can.  Please check your emails regularly and provide a phone number where you can be reached.  If we cannot get in touch with you, we will assume you do not want to take part in this year's festival and your proposal will be deleted.
</p>

<form method="POST" action="submitProposal.php">
<?php echo contactForm(); ?>
<div class="projectForm">
<h3>Project</h3>
<table cellpadding=3>
<?php
echo proposalFormType('visualart');
echo proposalFormTitle('Title of piece/series (one series per proposal)');
echo proposalFormTextinput('Website','website');
echo proposalFormDescriptionOrg();
echo proposalFormDescriptionWeb();
echo proposalFormDescriptionBrochure();
echo proposalFormImageLink();
echo proposalFormMedium();
echo proposalFormNumberPieces();
echo proposalFormTextinput('Dimensions of each piece','dimensions');
echo proposalFormEntireDimensions();
echo proposalFormPrearrangedVenue();
echo proposalFormVenueDescription();
echo proposalFormVisualartPresentation();
echo proposalFormOtherShows();
?>
</table>
</div>

<?php echo otherQuestions(); ?>

<p>
<?php print festivalAgreement('
<li><b>Your work must be ready to install</b> and you must bring your own hanging/installation tools.  For example: hammer and nails.</li>
<li>Your Visual Arts Coordinators are Cat McCarthy and Amy Duengfelder.  You should <b>add their email (visualinfringement@live.com) to your "accepted senders list"</b> so that the emails do not go into your spam folder. </li>
'); ?>
<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

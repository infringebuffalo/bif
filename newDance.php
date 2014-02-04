<?php
require_once 'init.php';
require_once 'proposalUtil.php';
require '../bif.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

bifPageheader('new dance proposal',proposalFormHeader());
?>

<p>
Welcome to the application for the Dance (and Movements Arts) genre of the Buffalo Infringement Festival. In the past years, we have had an awesomely diverse offering of dance, including: theatrical, burlesque, contact improv, bellydance, fire, hula hooping, contortion, salsa, swing, modern, interpretive, arial, ballet, breakdancing, ethnic and cultral dance, street dance, and dance that could not be labeled. Our "definition" of dance is somewhat limitless and very open minded. What kind of dance do YOU plan to bring to Infringement 2014? We can't wait to see!
</p>
<p>
It is important to be as detailed as possible to help us place you in the proper venue. We will try our best to accommodate all requests and needs, but please understand that it may not be possible. All spaces are donated and we will do the best we can. Please check your emails regularly and provide a phone number where you can be reached. If we cannot get in touch with you, we will assume you do not want to take part in this year's festival and your proposal will be deleted.
</p>

<form method="POST" action="submitProposal.php">
<?php echo contactForm(); ?>
<div class="projectForm">
<h3>Project</h3>
<table cellpadding=3>
<?php
echo proposalFormType('dance');
echo proposalFormTitle();
echo proposalFormTextinput('Name of the group putting on the project','organization');
echo proposalFormTextinput('Website','website');
echo proposalFormDescriptionOrg();
echo proposalFormDescriptionWeb();
echo proposalFormDescriptionBrochure();
echo proposalFormImageLink();
echo proposalFormPerformerNames();
echo proposalFormOver21();
echo proposalFormSetupTime();
echo proposalFormPerformanceLength();
echo proposalFormStrikeTime();
echo proposalFormNumberPerformances();
echo proposalFormPrearrangedVenue();
echo proposalFormVenueFeatures();
echo proposalFormNontraditionalVenue();
echo proposalFormPerformWithBand();
echo proposalFormAdmission();
echo proposalFormOtherShows();
echo datesCanDo();
?>
</table>
</div>

<?php echo otherQuestions(); ?>

<p>
<?php print festivalAgreement('
<li>The Dance Coordinator is Leslie Fineberg (aka Leslie Jean-Jellybean / Euphraxia Dance). You must <b>add her email (danceundertheradar@gmail.com) to your "accepted senders list"</b> so that the emails do not go into your spam folder. You will stay in contact with her, knowing that if you don\'t, your proposal will be deleted.</li>
'); ?>

<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

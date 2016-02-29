<?php
require_once 'init.php';
require_once 'proposalUtil.php';
connectDB();
requirePrivilege(array('scheduler','confirmed'));

$css = <<<ENDSTRING
<style type="text/css">
.proposalTH {display: block; text-align: left}
.proposalTD {display: block}
</style>
ENDSTRING;
bifPageheader('new street performance proposal',proposalFormHeader().$css);
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
echo proposalFormStreetExperience();
echo proposalFormStreetLicense();
echo proposalFormWithoutAmp();
echo proposalFormNeedOutlet();
echo proposalFormPerformanceSpace();
echo proposalFormStreetVenueFeatures();
echo proposalFormStreetVenueDescription();
echo proposalFormPrearrangedVenue();
echo proposalFormTipsOrScene();
echo proposalFormScheduledOrBusking();
echo proposalFormStreetNontraditionalVenue();
echo proposalFormImprovisatory();
echo proposalFormInteractive();
echo proposalFormFamilyFriendly();
echo proposalFormSetupTime();
echo proposalFormPerformanceLength();
echo proposalFormStrikeTime();
echo proposalFormOtherShows();
echo proposalFormNumberPerformances();
echo datesCanDo();
echo proposalFormEquipment();
echo proposalFormShareEquipment();
echo "<tr><th class='proposalTH' style='background:#6f6'>MUSIC ACTS</th></tr>\n";
echo proposalFormMainMusicGenre();
echo proposalFormSecondMusicGenre();
echo proposalFormHowLoud();
echo proposalFormVocalist();
echo proposalFormHaveDanceOrTheatre();
echo proposalFormPlayWithDancers();
echo proposalFormPlayWithFilm();
echo "<tr><th class='proposalTH' style='background:#6f6'>DANCE/MOVEMENT ACTS</th></tr>\n";
echo proposalFormStreetDanceHaveSound();
echo proposalFormStreetDanceOwnSound();
echo proposalFormStreetDanceLiveMusic();
echo proposalFormTextinput('If yes, what would you prefer to dance to?','streetdancepreferredmusic');
echo proposalFormTextinput('What can\'t you dance to? (be specific)','streetdancecantmusic');
echo "<tr><th class='proposalTH' style='background:#6f6'>THEATRE ACTS</th></tr>\n";
echo proposalFormTextinput('Theatre genre:','streettheatregenre');
echo proposalFormYNinput('Do you have props/wardrobe?','streettheatreprops');
echo "<tr><th class='proposalTH' style='background:#6f6'>LITERARY ACTS</th></tr>\n";
echo proposalFormTextinput('Literary genre:','streetliterarygenre');
echo "<tr><th class='proposalTH' style='background:#6f6'>FILMS</th></tr>\n";
echo proposalFormYNinput('Do you have your own projector?','streetfilmprojector');
echo proposalFormYNinput('Do you have your own screen?','streetfilmscreen');
echo proposalFormYNinput('Do you have permission to use a wall?','streetfilmwall');
echo proposalFormYNinput('Does your film have a sound component? (you must supply your own equipment)','streetfilmsound');
echo proposalFormYNinput('Would you like musicians to improvise to your film?','streetfilmmusicians');
echo proposalFormTextinput('If yes, any specific people in mind?','streetfilmmusiciansdetail');
echo "<tr><th class='proposalTH' style='background:#6f6'>VISUAL ART</th></tr>\n";
echo proposalFormTextinput('Medium:','streetartmedium');
echo proposalFormStreetArtMaking();
?>
</table>
</div>

<br><br>
<?php echo otherQuestions(); ?>

<p>
<?php print festivalAgreement('
<li>The Street Performances Coordinator is Dave Adamczyk. You must <b>add his email (dga8787@aol.com) to your "accepted senders list"</b> so that the emails do not go into your spam folder. You will stay in contact with him, knowing that if you don\'t, your proposal will be deleted.</li>
<li>There is <b>no guaranteed financial compensation</b>.</li>
<li>You must <b>be at your location early</b> to start on time.</li>
<li>You must maintain good etiquette in <b>support of fellow buskers</b> and help create a community.</li>
<li>You understand that <b>weather is always a factor</b>.</li>
<li>You must <b>obtain a street-performer\'s permit</b> to legally street-perform in Buffalo.</li>
<li><b>If you do anything illegal during your act you are on your own.</b> The Infringement Festival cannot do anything if you are asked to stop by the Buffalo Police Department.</li>
<li>If busking, you will not perform at one location for more than 2 hours at a time, and will <b>share the space</b> with other acts who would like to perform.</li>
<li> You will do your best to <b>cooperate</b> with the reasonable requests of store-owners and people in the neighborhood.</li>
'); ?>
<input type="Submit" name="submit" value="Submit" />
</p>
</form>

<?php
bifPagefooter();
?>

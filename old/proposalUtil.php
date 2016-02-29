<?php
require_once 'scheduler.php';

function proposalFormHeader()
    {
    return <<< ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function hoverFunc(event)
  {
  $(this).parent().find(".helptext").fadeIn();
  }

function unhoverFunc(event)
  {
  $(this).parent().find(".helptext").fadeOut();
  }

function readyFunc()
  {
  $(".questionmark").hover(hoverFunc,unhoverFunc);
  }
$(document).ready(readyFunc)
</script>
<style type="text/css">
div.helptext { position: absolute; display: none; background: #ffb0b0; width: 90% }
</style>
ENDSTRING;
    }

function contactForm($secondcontact=FALSE, $band=FALSE)
    {
    $s = '<div class="contact"><h3>Contact info</h3><table cellpadding=3 id="contactinputs">';
    $stmt = dbPrepare("select name,email,phone,snailmail from user where id=?");
    $stmt->bind_param('i', $_SESSION['userid']);
    $stmt->execute();
    $stmt->bind_result($name,$email,$phone,$snailmail);
    $stmt->fetch();
    $stmt->close();
    $s .= '<tr><th width="20%">Proposer / primary contact</th><td>' . $name . '</td><input type="hidden" name="contactname" value="' . $name . '" /></tr>';
    $s .= '<tr><th>E-mail</th><td>' . $email . '</td><input type="hidden" name="contactemail" value="' . $email . '" /></tr>';
    $s .= '<tr><th>Phone</th><td>' . $phone . '</td><input type="hidden" name="contactphone" value="' . $phone . '" /></tr>';
    $s .= '<tr><th>Address</th><td>' . $snailmail . '</td><input type="hidden" name="contactaddress" value="' . $snailmail . '" /></tr>';
    $s .= '<tr><th>Facebook address</th><td><input type="text" name="contactfacebook" /></td></tr>';
    $s .= '<tr><th>Best method to contact you</th><td> <select name="bestcontactmethod"> <option value="phone">phone</option> <option value="email">email</option> <option value="facebook">facebook</option> </select> </td></tr>';
    $s .= '</table>';
    if ($secondcontact)
        {
        $s .= '<table>';
        $s .= '<tr><th width="20%">Secondary contact name</th><td><input type="text" name="secondcontactname"></td></tr>';
        $s .= '<tr><th>E-mail</th><td><input type="text" name="secondcontactemail"></td></tr>';
        $s .= '<tr><th>Phone (including area code)</th><td><input type="text" name="secondcontactphone"></td></tr>';
        $s .= '<tr><th>Address</th><td><textarea name="secondcontactaddress" rows="3" cols="40"></textarea></td></tr>';
        $s .= '</table>';
        }
    if ($band)
        {
        $s .= '<table>';
        $s .= '<tr><th width="20%">Number of band members</th><td><input type="text" name="numberperformers"></td></tr>';
        $s .= '<tr><th>Names and roles of <em>all</em> band/project members (Who\'s in the band, and what do they play?)</th><td><textarea name="bandnames" rows="3" cols="40"></textarea></td></tr>';
        $s .= '</table>';
        }
    $s .= '</div>';
    return $s;
    }

/*
function datesCanDo($verb="perform")
    {
    global $festivalStartDate, $festivalNumberOfDays;
    $s = '<tr><th>Dates that you can ' . $verb . '</th>
<td>
<table border="1" >
<tr><td> <em>Sunday</em></td><td> <em>Monday</em></td><td> <em>Tuesday</em></td><td> <em>Wednesday</em></td><td> <em>Thursday</em></td><td> <em>Friday</em></td><td> <em>Saturday</em></td></tr>' . "\n";
    $s .= '<tr>';
    for ($i = date('w', $festivalStartDate); $i > 0; $i--)
        {
        $s .= '<td></td>';
        }
    for ($i = 0; $i < $festivalNumberOfDays; $i++)
        {
        $date = dayToTimestamp($i);
        if (date('w',$date) == 0)
            $s .= '</tr><tr>';
        $s .= sprintf('<td><input type="checkbox" name="can_day%d" value="1" checked/> %s</td>', $i, date('M j', $date));
        }
    $s .= '</tr></table></td></tr>';
    return $s;
    }
*/

function datesCanDo($verb="perform")
    {
    global $festivalStartDate, $festivalNumberOfDays;
    $s = "<tr><th class='proposalTH'>Enter your availability to $verb on the following dates:";
    $s .= <<<ENDSTRING
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Please let us know your availability for day and evening hours on all festival days, to the best of your ability.<br>We understand that this far out you may not be able to determine your exact schedule.<br>After the sign up period closes and we start scheduling in earnest, having correct availability becomes MOST important.<br>Please update your availability as it becomes clear to you!!
</div>
</th>
ENDSTRING;
    $s .= "<td class='proposalTD'>\n";
    for ($i = 0; $i < $festivalNumberOfDays; $i++)
        {
        $date = dayToTimestamp($i);
        $s .= date('M j', $date) . "<input type='text' name='can_day$i' width='20'><br>\n";
        }
    $s .= "</td></tr>\n";
    return $s;
    }

function festivalAgreement($other='')
    {
    $s = 'By submitting this proposal, you agree to the following festival ground rules:';
    $s .= '<ul>
<li>Communication is important.  <b>Keep in touch with your genre organizer and venue contact.</b> If you need to cancel a performance, you must notify them in advance. You must check your e-mail and voicemail regularly, and respond to requests from your genre organizer. </li>
<li>We ask that you conduct yourself in a courteous, professional manner.  <b>Treat your venue with the utmost care</b> - no littering or destroying.  Leave it in the same condition as when you arrived.</li>
<li><b>You are responsible for promoting your own performances.</b> Other than the festival website and the printed schedule, all promotion is up to you, including--but not limited to: press releases, fliers, business cards, Facebook event pages, etc. If you do not tell people about your shows, they probably won\'t show up. PLEASE include the festival name or logo on any printed or online material you produce.</li>
<li>You must <b>attend at least one planning/informational meeting</b> before the festival. We will inform you of when these meetings are scheduled.  If you live outside of Western New York or your schedule does not allow you to attend the regular meeting, you are responsible for contacting your genre organizer to schedule a one-on-one meeting.</li>
<li>You may have to <b>be flexible</b> with your schedule and your performances leading up to and during the festival.  With a festival of this size, put together entirely by volunteers, there are bound to be some scheduling mishaps, etc. We need you to "roll with the punches" so to speak, and make the best of whatever happens during the festival. The Infringement must go on!</li> ';
    $s .= $other;
    $s .= '</ul>';
    return $s;
    }

function otherQuestions($verb='perform',$band=false)
    {
    $s = <<<ENDSTRING
<div class="otherQuestions"><h3>Other questions</h3><table cellpadding=3>
<tr>
<th style="width:40%" class="proposalTH">How can you volunteer to help the festival?<br/>(examples: venue czar, audio tech, lighting tech, equipment gopher)</th>
<td class='proposalTD'><textarea name="volunteer" rows="2" cols="60"></textarea></td>
</tr>
<tr>
<th class="proposalTH">What makes your proposal appropriate for Infringement? (What's "infringey" about it?)</th>
<td class='proposalTD'><textarea name="infringe" rows="2" cols="60"></textarea></td>
</tr>
<tr><th class="proposalTH">If anyone involved in this proposal will be traveling from out of town, please give details - where from, and will you need housing?</th><td class='proposalTD'><input type="text" name="outoftown" size="60" /></td></tr>
<tr><th class="proposalTH">If you have been part of any previous Buffalo Infringement Festivals, what years and what projects?</th><td class='proposalTD'><input type="text" name="pastfestivals" size="60" /></td></tr>
ENDSTRING;
    if ($band)
        {
        $s .= <<< ENDSTRING
<tr><th class="proposalTH">Have you been pre-drafted for the Anti-Warped Tour?</th><td class='proposalTD'><select name="antiwarped"><option value="n">no</option><option value="y">yes</option></td></tr>
<tr><th class="proposalTH">If you have been courted for the Anti-Warped Tour, do you want other gigs?</th><td class='proposalTD'><select name="antiwarpedplus"> <option value="y">yes</option> <option value="n">no, we just want Anti-Warped</option> </td></tr>
<tr><th class="proposalTH">Are you willing to perform at our opening or closing ceremonies? <img src="questionmark.png" class="questionmark" /><div class="helptext">These 2 shows are general fundraisers for the festival.</div></th><td class='proposalTD'><select name="openingceremonies"> <option value="n">no</option> <option value="y">yes</option> </td></tr>
<tr><th class="proposalTH">Is there a specific venue or type of venue you would like to perform at if possible?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
(coffee house, bookstore, rock club, gallery, theatre, record store, park, back yard, etc.)
</div>
</th>
<td class='proposalTD'><input type="text" name="venue" size="60" /></td>
</tr>
ENDSTRING;
        }
    $s .= '<tr><th class="proposalTH">Do you have any specific questions or concerns about Infringement, the application process or your project? We will try to address any concerns you have with our limited volunteer staff.</th><td class="proposalTD"><input type="text" name="questions" size="60" /></td></tr>';
    $s .= "</table></div>\n";
    return $s;
    }

function textInput($field, $size=60, $id='')
  {
  global $info;
  if ($id != '')
    $id = 'id="' . $id . '" ';
  if (array_key_exists($field,$info)) $val = htmlentities(stripslashes($info[$field]),ENT_COMPAT | ENT_HTML5, "UTF-8");
  else $val = '';
  return '<td class="proposalTD"><input ' . $id . 'type="text" name="' . $field . '" size="' . $size . '" value="' . $val . '" /></td>';
  }

function textareaInput($field, $rows=6)
  {
  global $info;
  if (array_key_exists($field,$info)) $val = stripslashes($info[$field]);
  else $val = '';
  return '<td class="proposalTD"><textarea name="' . $field . '" rows="' . $rows . '" cols="60">' . $val . '</textarea></td>';
  }

function yesnoInput($field)
  {
  global $info;
  $s = '<td class="proposalTD"><select name="' . $field . '">';
  $s .= '<option value="n"';
  if ($info[$field] == '0') $s .= ' selected';
  $s .= '>no</option>';
  $s .= '<option value="y"';
  if ($info[$field] == '1') $s .= ' selected';
  $s .= '>yes</option>';
  $s .= '</td>';
  return $s;
  }

function medialengthInput()
  {
  global $info;
  $vals = array('10' => '0 - 10 minutes', '30' => '11 - 30 minutes', '60' => '31 - 60 minutes', '90' => 'over 60 minutes');
  $s = '<td class="proposalTD"><select name="length">';
  foreach ($vals as $k => $v)
    {
    $s .= '<option value="' . $k . '"';
    if ($info['length'] == $k) $s .= ' selected';
    $s .= '>' . $v . '</option>';
    }
  $s .= '</select></td>';
  return $s;
  }

function proposalFormType($formtype)
    {
    return "<input type='hidden' name='formtype' value='$formtype' />\n";
    }

function proposalFormTitle($label='Title')
    {
    return <<<ENDSTRING
<tr>
<th width="15%" class="proposalTH">$label
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
A title is mandatory.<br/>(We can't have a dozen different "untitled" shows.)
</div>
</th>
<td class="proposalTD"><input type="text" name="title" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormTextinput($label,$name)
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">$label</th>
<td class="proposalTD"><input type="text" name="$name" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormYNinput($label,$name)
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">$label</th>
<td class="proposalTD"><select name="$name"><option value="n" selected>no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormOtherWebsite()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Facebook, ReverbNation, Bandcamp, or other website</th>
<td class="proposalTD"><input type="text" name="otherwebsite" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormDescriptionOrg()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Description of your project<br>(for organizing committee)
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Please be as detailed as possible (what your project is, the subject material, style, age restrictions, etc.) This should include everything we need to know to put your production in an appropriate venue.  This is <em>not</em> the description that will be published in the festival schedule - it is only for internal use.
</div>
</th>
<td class="proposalTD"><textarea name="description_org" rows="6" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormDescriptionWeb()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Long description of your project for festival website
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Do not include anything you don't want published on-line. (if you don't fill in these descriptions we MIGHT just make something up for you!)
</div>
</th>
<td class="proposalTD"><textarea name="description_web" rows="6" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormDescriptionBrochure()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Short description of your project for the printed schedule
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
140 Character description. Only 140 characters will be printed so count carefully. Make it catchy!
</div>
</th>
<td class="proposalTD"><textarea name="description_brochure" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormImageLink()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Image link
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Please provide a link to an image to be used on your project page on the festival website.
</div>
</th>
<td class="proposalTD"><input type="text" name="imagelink" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormMedium()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Medium
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
If you use mixed or multiple media, please specify what they are.
</div>
</th>
<td class="proposalTD"><input type="text" name="medium" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormNumberPieces()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Number of individual pieces
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
If this is a series, please specify how many are in the series.
</div>
</th>
<td class="proposalTD"><input type="text" name="numberpieces" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormEntireDimensions()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Dimensions of entire project
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
How much wall or floor space will you need in total?  Give a rough estimate.<br>This includes how much space you will need for projector throws.
</div>
</th>
<td class="proposalTD"><input type="text" name="entiredimensions" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormNumberPerformers()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Number of performers
</th>
<td class="proposalTD"><input type="text" name="numberperformers" size="20" /></td>
</tr>
ENDSTRING;
    }

function proposalFormSetupTime()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Setup time
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
How long will you need to set up your show before each performance
</div>
</th>
<td class="proposalTD"><input type="text" name="setuptime" size="20" /></td>
</tr>
ENDSTRING;
    }

function proposalFormPerformanceLength()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Length of performance
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
The duration of the actual performance
</div>
</th>
<td class="proposalTD"><input type="text" name="length" size="20" /></td>
</tr>
ENDSTRING;
    }

function proposalFormStrikeTime()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Strike time
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
How long will you need to take down your show, after each performance.
<br>You should be able to clear out completely, to make way for other shows in the same venue.  If some elements of your show must be installed permanently, please note that in the description.
</div>
</th>
<td class="proposalTD"><input type="text" name="striketime" size="20" /></td>
</tr>
ENDSTRING;
    }

function proposalFormNumberPerformances($max=8,$default=5)
    {
    $s = <<<ENDSTRING
<tr>
<th class="proposalTH">Desired number of performances
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
We will do our best to fulfill all requests, but cannot guarantee to give you as many performances as you ask for.  The actual number will depend on venue availability.
</div>
</th>
<td class="proposalTD">
<select name="numberperformances">
ENDSTRING;
    for ($i=1; $i <= $max; $i++)
        {
        $s .= '<option value="' . $i .'"';
        if ($i == $default)
            $s .= ' selected';
        $s .= '>' . $i . '</option>' . "\n";
        }
    $s .= "</select>\n</tr>\n";
    return $s;
    }

function proposalFormPrearrangedVenue()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Do you have a pre-arranged venue?</th>
<td class="proposalTD"><select name="hasvenue"><option value="n" selected>no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormIsStreetTheatre()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Street theatre
<div class="helptext">
Is your production a street theatre?
</div>
</th>
<td class="proposalTD"><select name="streettheatre"><option value="n">no</option><option value="y" selected>yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormNontraditionalVenue()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Are you interested in performing in a non-traditional venue?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
We don't have many "proper" theatres in the festival, please check "YES" if you COULD perform in an art gallery, a store front, a bar, in somebodys living room, outside, perhaps rooftop? (this includes ALL street theatre performances.)
</div>
</th>
<td class="proposalTD"><select name="nontraditionalvenue"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetNontraditionalVenue()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Are you interested in performing in a non-traditional or unexpected location?
</th>
<td class="proposalTD"><select name="nontraditionalvenue"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormImprovisatory()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Is your act improvisatory?</th>
<td class="proposalTD"><select name="improvisatory"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
<tr>
<th class="proposalTH">If improvisatory, would you be willing to improvise with other artists in your or another genre (please explain)?</th>
<td class="proposalTD"><input type="text" name="improvcollaborate" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormInteractive()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Is your act interactive?</th>
<td class="proposalTD"><select name="interactive"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormVenueDescription()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Description of desired venue
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
This could be your pre-arranged venue, venues you know of and would like to perform in, or just a general description.
</div>
</th>
<td class="proposalTD"><input type="text" name="venue" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormVenueFeatures()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Any specific features requested at your venue
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Dressing rooms, theatrical lighting, sound system, video system, storage space, etc.<br>Please try to obtain any equipment you may need, as we have very limited resources and cannot guarantee anything.<br>We will try to accommodate you when we can but please be aware that this is a DIY festival and the success of your show is up to you.
</div>
</th>
<td class="proposalTD"><textarea name="venuefeatures" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormFilmVenueFeatures()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Please list any specific things you would like to have in a venue (indoors, outdoors, chairs, projection screen, sound system, etc).
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Please try to secure any extraordinary equipment you may need (Full PA, microphone, multiple projectors, additional power or lighting etc) as we have very limited resources and cannot guarantee all these items at all venues. We will try to accommodate you when we can but please be aware that this is a DIY festival and the success of your project is up to you.
</div>
</th>
<td class="proposalTD"><textarea name="venuefeatures" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormOver21()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Is everyone in your group age 21 or older?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Minors are certainly allowed (and encouraged) to perform at the festival. However, some venues do not allow us to book acts with performers who are under 21.
</div>
</th>
<td class="proposalTD"><select name="over21"><option value="n">no</option><option value="y" selected>yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormFamilyFriendly()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Is your project suitable for “family friendly” venues? 
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
If it contains adult themes, situations or language, the answer would be “NO”.<br>A “NO” answer does not preclude your submission but it helps us find an appropriate venue / time for showing.
</div>
</th>
<td class="proposalTD"><select name="familyfriendly"><option value="n">no</option><option value="y">yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormVisualartPresentation()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">If you have a presentation/performance that goes with your work, what days and times during the festival are you available to do it?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
If you have static visual art, it will be on display the entirety of the festival.
</div>
</th>
<td class="proposalTD"><textarea name="visualartpresentation" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormOtherShows()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Are you (or members of your project) part of any other Infringement proposals, in any category? To help us to avoid scheduling conflicts, please list them here:</th>
<td class="proposalTD"><textarea name="othershows" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormMainMusicGenre()
    {
    return "<tr>\n<th class='proposalTH'>Classification: Main genre</th>\n<td class='proposalTD'><select name='genre'>\n" . musicGenresOptionList() . "</select>\n</td>\n</tr>\n";
    }

function proposalFormSecondMusicGenre()
    {
    return "<tr>\n<th class='proposalTH'>Secondary genre</th>\n<td class='proposalTD'><select name='secondgenre'>\n" . musicGenresOptionList() . "</select>\n</td>\n</tr>\n";
    }

function musicGenresOptionList()
    {
    $genres = array('rock', 'electronic', 'acoustic', 'jazz', 'blues', 'hip hop', 'rap', 'dance', 'country', 'folk', 'punk', 'metal', 'noise', 'avant garde', 'soul / R & B', 'pop', 'world', 'latin', 'classical', 'singer/songwriter', 'jam', 'indie-rock', 'Americana', 'garage-rock', 'hardcore', 'reggae', 'ska', 'psychedelic', 'Christian', 'other');
    $s = '';
    foreach ($genres as $g)
        {
        $s .= "<option value='$g'>$g</option>\n";
        }
    return $s;
    }

function proposalFormOtherBandGroups()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Current/previous groups/projects that you/bandmates are/have been part of
</th>
<td class="proposalTD"><input type="text" name="otherbandgroups" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormHavePA()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Do you have your own PA system?
</th>
<td class="proposalTD"><select name="havepa"><option value="n">no</option><option value="y" selected>yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormSharePA()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Do you have a PA to share in a group show?
</th>
<td class="proposalTD"><select name="sharepa"><option value="n">no</option><option value="y" selected>yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormWithoutAmp()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Can you play without amplification?
</th>
<td class="proposalTD"><select name="withoutamp"><option value="n">no</option><option value="y" selected>yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormBusking()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Would you be interested in busking?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Meaning: No amps, no PA.  Outside of storefronts, and designated sidewalks.
</div>
</th>
<td class="proposalTD"><select name="busking"><option value="n">no</option><option value="y" selected>yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormShareDrums()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Is your drummer willing to share his/her kit?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Provided they bring snare, cymbals, hi-hat, throne.
</div>
</th>
<td class="proposalTD"><select name="sharedrums"><option value="n">no</option><option value="y" selected>yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormDJOwnTables()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">If you are a DJ/electronica artist: Do you have your own tables and mixer?
</th>
<td class="proposalTD"><select name="djowntables"><option value="n">no</option><option value="y" selected>yes</option></td>
</tr>
ENDSTRING;
    }

function proposalFormBandGear()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Describe in detail: band gear, number of vocalists, electronic devices, props, or anything else we should know</th>
<td class="proposalTD"><textarea name="bandgear" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormShareEquipment()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Have equipment to share? If so, please list.
<div class="helptext">
For example: mics, amps, turntables, PA, mixer, etc.
</div>
</th>
<td class="proposalTD"><textarea name="shareequipment" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormHowLoud()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">How loud must you play at?
</th>
<td class="proposalTD"><select name="howloud">
<option value="1">1 (acoustic - not loud)</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10 (metal or drum & bass - loud!!!)</option>
</td>
</tr>
ENDSTRING;
    }

function proposalFormPerformerNames()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Names of all performers
<div class="helptext">
Please include first and last names of everyone involved in your project
</div>
</th>
<td class="proposalTD"><textarea name="performernames" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormPerformers()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Names and roles of all performers / project members
</th>
<td class="proposalTD"><textarea name="performers" rows="3" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormEquipment()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Detail equipment: band gear, electronic devices, props, set design, art supplies, anything we should know about?
</th>
<td class="proposalTD"><textarea name="equipment" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

function proposalFormPerformWithBand()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Would you be willing to peform to the live music of one of our Infringement bands?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
We would love you to! You would then be scheduled to come dance while that band is playing their music, at one or more venues!
</div>
</th>
<td class="proposalTD"><select name="performwithband"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormAdmission()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Admission charge for your show
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Examples: free, pay what you can, or set cost.<br>Please understand that charging more than $10 admission is not allowed by the Infringement Festival.<br>If you plan to charge, we would recommend you kept it as "pay what you can" or "donation".
</div>
</th>
<td class="proposalTD"><input type="text" name="admission" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetExperience()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Do you have experience street performing?
</th>
<td class="proposalTD"><select name="streetexperience"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetLicense()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Do you have a street performer's license for Buffalo valid through the festival?
</th>
<td class="proposalTD"><select name="streetlicense"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormTipsOrScene()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Would you say you are more interested in making tips or making a scene, if you had to choose?
</th>
<td class="proposalTD"><select name="tipsorscene"><option value="tips">tips</option><option value="scene">scene</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormScheduledOrBusking()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Do you prefer to be scheduled or are you primarily busking (finding your own location)?
</th>
<td class="proposalTD" class="proposalTD"><select name="scheduledorbusking"><option value="scheduled">scheduled</option><option value="busking">busking</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormNeedOutlet()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Do you need access to an outlet? 
</th>
<td class="proposalTD"><select name="needoutlet"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormVocalist()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Does your act have a vocalist?
</th>
<td class="proposalTD"><select name="vocalist"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormPlayWithDancers()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Would you be willing to play with dancers who need music?
</th>
<td class="proposalTD"><select name="playwithdancers"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormPlayWithFilm()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Would you be willing to improvise to a silent projected film?
</th>
<td class="proposalTD"><select name="playwithfilm"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormHaveDanceOrTheatre()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Does your act have a dance or theatrical component?
</th>
<td class="proposalTD"><input type="text" name="havedanceortheatre" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetDanceHaveSound()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Does your act have a sound component?
</th>
<td class="proposalTD"><select name="streetdancehavesound"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetDanceOwnSound()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Will you provide your own sound?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Please include performers' information under "Names and roles" above
</div>
</th>
<td class="proposalTD"><select name="streetdanceownsound"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetDanceLiveMusic()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Would you be willing to perform to live music?
</th>
<td class="proposalTD"><select name="streetdancelivemusic"><option value="n">no</option><option value="y">yes</option></select></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetArtMaking()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Are you interested in making art in public or just displaying?
</th>
<td class="proposalTD"><input type="text" name="streetartmaking" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormPerformanceSpace()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">What size performance space does your act require?
<img src="questionmark.png" class="questionmark" />
<div class="helptext">
Examples: none (projections), minimal - up to 5x5 feet, large (large multi-instrument group, group activities, large theater piece, etc)- up to 20x20 feet
</div>
</th>
<td class="proposalTD"><input type="text" name="performancespace" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetVenueDescription()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Ideal location type and/or specific locations you would like to perform at?
</th>
<td class="proposalTD"><input type="text" name="venue" size="60" /></td>
</tr>
ENDSTRING;
    }

function proposalFormStreetVenueFeatures()
    {
    return <<<ENDSTRING
<tr>
<th class="proposalTH">Other REQUIRED location features
</th>
<td class="proposalTD"><textarea name="venuefeatures" rows="2" cols="60"></textarea></td>
</tr>
ENDSTRING;
    }

?>

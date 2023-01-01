<?php

namespace Database\Seeders;

use HMS\Entities\ContentBlock;
use HMS\Entities\ContentBlockType;
use HMS\Repositories\ContentBlockRepository;
use Illuminate\Database\Seeder;

class ContentBlockSeeder extends Seeder
{
    /**
     * The Page type ContentBlocks and there default content.
     *
     * @var array
     */
    protected $pageBlocks = [
        [
            'view' => 'pages.contactUs',
            'block' => 'main',
            'content' => "<h2>Ways to get in touch</h2>\r\n  <p>\r\n    Unlike a regular business we do not have any permanent staff, everything is done by regular members of the space just volunteering there time.<br>\r\n    Below is a lists various of ways of getting in touch with us.\r\n  </p>\r\n  <dl>\r\n    <dt><a href=\"https://groups.google.com/group/nottinghack?hl=en\" target=\"_blank\">Google Groups</a></dt>\r\n    <dd>Community mailing list for ...</dd>\r\n\r\n    <dt><a href=\"http://slack.nottinghack.org.uk/\" target=\"_blank\">Slack</a></dt>\r\n    <dd>Team Slack for ...</dd>\r\n\r\n    <dt><a href=\"mailto:membership@nottinghack.org.uk\" target=\"_blank\">Membership Team Email</a></dt>\r\n    <dd>Email to the membership team for ...</dd>\r\n\r\n    <dt><a href=\"mailto:trustees@nottinghack.org.uk\" target=\"_blank\">Trustees Email</a></dt>\r\n    <dd>Email the trustees</dd>\r\n  </ul>",
        ],
        [
            'view' => 'pages.privacy_and_terms',
            'block' => 'main',
            'content' => "<p>\r\n  Lots of words\r\n</p>",
        ],
        [
            'view' => 'pages.cookie_policy',
            'block' => 'main',
            'content' => "<p>\r\n  Lots of words\r\n</p>",
        ],
        [
            'view' => 'pages.awaitingApproval',
            'block' => 'main',
            'content' => "<p>Your membership details are awaiting approval from our Membership Team.</p>\n<p>They'll be giving your information a quick check, and if all is well they'll move your membership on to the next stage.</p>\n<p>If there are any queries, they will send you an email with more information.</p>\n<p>This normally takes no more than 48 hours.</p>",
        ],
        [
            'view' => 'pages.awaitingPayment',
            'block' => 'main',
            'content' => "<p>\r\n  Your membership details have been approved and we are now waiting on your payment to show up in our account. If you have further questions, please talk to the Membership Team.\r\n</p>",
        ],
        [
            'view' => 'pages.donate',
            'block' => 'main',
            'content' => '',
        ],
        [
            'view' => 'pages.registrationComplete',
            'block' => 'main',
            'content' => "<h4>\r\n  Thank you for registering with Nottingham Hackspace. Here are the next steps.\r\n</h4>\r\n<p>\r\n  You will have just received an email with a link to verify you email address. Please check your in-box and give the link a quick click.\r\n</p>\r\n<p>\r\n  Now you have filled in your details, our membership admins will be notified. They'll give your information a quick check, and if all is well they'll move your membership on to the next stage. If there is an issue, they will send you an email with details of what needs correcting.\r\n</p>\r\n<p>\r\n  Once it's all checked and accounted for, you will get the Nottingham Hackspace bank details, as well as a unique payment reference for your account.<br>\r\n  Use these details to set up a standing order for your membership fee.<br>\r\n  The reference must be used exactly as is when setting up your standing order for our automated systems to recognise your membership payments.\r\n</p>\r\n<p>\r\n  Membership is pay-what-it's-worth-to-you on a monthly basis, and you can always change the amount you're paying if you find yourself using the space more or less than you first thought.\r\n</p>\r\n<p>\r\n  When your standing order is set up and your first payment is made, even if the money leaves your account, payments are not instant between all banks and records don't update immediately, so it may take 3 to 4 days before it's visible in the hackspace account. Our automated system checks our account at midnight on weekdays.<br>\r\n  When your payment does show, you'll receive an email confirming membership, you'll get the door codes and an invitation to collect your RFID card at a Wednesday Open Hack Night.<br>\r\n  Once you've collected your card, you are free to visit at any time, twenty four hours a day.\r\n</p>\r\n<hr>\r\n<h4>A few important details:</h4>\r\n<p>\r\n  Nottingham Hackspace is incorporated as a non-profit company, registration number 07766826.<br>\r\n  Everyone who works on stuff for the hackspace is a volunteer; the hackspace has no staff, just members.<br>\r\n  So far, it has also been entirely funded and is self-sustaining through members contributions rather than grants.\r\n</p>",
        ],
        [
            'view' => 'home',
            'block' => 'welcome',
            'content' => '',
        ],
        [
            'view' => 'welcome',
            'block' => 'main',
            'content' => "<h2>What is Nottinghack?</h2>\r\n<p>Nottinghack is a Nottingham based group for hackers, makers and crafty creatives!</p>\r\n<p>Hacking is NOT to be confused with network hacking, identity theft and computer virus propagation, etc. Nottinghack does not condone anything illegal; hardware Hacking is a creative, educational hobby!</p>\r\n<p>Who is it for? If you like to build, make &amp; learn it’s for you. You’ll probably be interested in learning about and sharing knowledge of electronics, crafts, robotics, DIY, hardware hacking, photography, computing, reverse engineering, prototyping, film making, animation, building RC vehicles and other creative challenges and projects.</p>\r\n<p>You’ll be looking for a group who can share tools, techniques and time; pool resources for bigger projects, get funding, discounts on kits and components and start classes, all in a safe friendly environment!</p>\r\n<p><a href=\"http://nottinghack.org.uk/?page_id=10\" target=\"_blank\">Read more...</a></p>\r\n\r\n<h2>What is HMS?</h2>\r\n<p>HMS (Hackspace Management System) is a program designed to help keep track of members, it's a bit basic at the moment, but we have big plans for it. It's current main goal is to make new member registration easier.</p>\r\n\r\n<h2>Interested in Nottingham Hackspace?</h2>\r\n<p>Excellent! Have you had a tour yet? If not come down to one of our open hack-nights (every Wednesday from 6:30pm at the address below). Already in the building? Look for the human near <a href=\"http://www.flickr.com/photos/nottinghack/7048461835/\" target=\"_blank\">Ein the duck</a>, they'll be able to help you.</p>\r\n<p>You may also want to follow Nottingham Hackspace on your choice of social network: <a href=\"http://twitter.com/#!/hsnotts\" target=\"_blank\">Twitter</a>, <a href=\"http://groups.google.com/group/nottinghack\" target=\"_blank\">Google Group</a>, <a href=\"http://www.flickr.com/photos/nottinghack\" target=\"_blank\">Flickr</a>, <a href=\"http://www.youtube.com/user/nottinghack\" target=\"_blank\">YouTube</a>, or <a href=\"http://www.facebook.com/pages/NottingHack/106946729335123\" target=\"_blank\">Facebook</a>.</p>\r\n<p>We also have a <a href=\"http://nottinghack.org.uk/blog\" target=\"_blank\">Blog</a>.</p>",
        ],
        [
            'view' => 'auth.register',
            'block' => 'user',
            'content' => '<p>Thank you for showing your interest in becoming a member of Nottingham Hackspace, please fill in the fields below to create your account. Once submitted your details will be reviewed by a member of our Membership Team (this normally takes less than 24 hours). Once reviewed you will be emailed details on the next step to becoming a member.</p>',
        ],
        [
            'view' => 'auth.register',
            'block' => 'profile',
            'content' => "<p>Nottingham Hackspace is an incorporated non-profit, run entirely by members. As such, we have to maintain a membership register for inspection by Companies House. Any information you provide won't be used for anything other than hackspace business, and certainly won't be passed on or sold to any third parties.</p>",
        ],
        [
            'view' => 'banking.transactions.index',
            'block' => 'details',
            'content' => "<p>These are the details needed to start or change your standing order with the Hackspace.</p>\r\n<p>To set up your standing order, you need our account number, sort code, and reference code.  In order for your membership to start, these all need to be correct in your standing order - especially your reference code.</p>",
        ],
        [
            'view' => 'banking.transactions.index',
            'block' => 'transactions',
            'content' => "<p>These are the transactions we have received from you.</p>\r\n<p>If a transaction has not appeared, please check with your bank that you have entered in the right account number, sort code, and reference code.  <strong>We cannot automatically match your transactions with your HMS account unless you use the correct reference code.</strong></p>",
        ],
        [
            'view' => 'gatekeeper.have_left',
            'block' => 'card',
            'content' => 'Thank you for confirming you have left the space.',
        ],
        [
            'view' => 'gatekeeper.space_access',
            'block' => 'temporaryAccess',
            'content' => "<p>Stay informed, find the latest information <a href=\"https://wiki.nottinghack.org.uk/wiki/Coronavirus_guidelines\">on the wiki.</a></p>\r\n<p>\r\n  Please note that the <strong>22nd March - 2nd April</strong> we expect the new Hackspace toilets to be installed. This means no Hackspace toilet use.<br>\r\n  There are some available in the main Roden House corridors, but if you feel these are inadequate or not suitable please do not book during this time.\r\n</p>",
        ],
        [
            'view' => 'governance.proxies.designate',
            'block' => 'main',
            'content' => "<p>\r\n  Another member has ask you to represent them at an upcoming Meeting by acting as their Proxy.<br>\r\n  Before accepting be sure you will be at the meeting yourself.<br>\r\n  If the meeting will include items to vote on make sure to talk over your opinions and how you wish your proxy votes to be cast.<br>\r\n  Most votes, including Resolutions to change the Constitution, are asked as a Yes/No/Abstain decision.\r\n</p>",
        ],
        [
            'view' => 'governance.voting.index',
            'block' => 'main',
            'content' => "<p>\r\n  On this page you can see your current calculated status and also state a voting preference<br>\r\n  There are many ways in which your status can automatically change between Voting and Non-Voting, even with a stated preference.<br>\r\n  <strong>Voting & Non-voting member</strong> status is a complicated subject for more information please read up about it on the wiki.<br>\r\n  <a href=\"https://wiki.nottinghack.org.uk/wiki/Voting_%26_Non-voting_member\" target=\"_blank\">Wiki: Voting & Non-voting member</a><br>\r\n</p>",
        ],
        [
            'view' => 'members.project.index',
            'block' => 'main',
            'content' => "<p>It is important that all projects left at the hackspace display a Do-Not-Hack label. This is so we know who it belongs to and that it is being actively worked on.</p>\r\n\r\n<p>Please use the Add New Project button to start your project. From there, you can fill out the details of your project.</p>\r\n\r\n<p>Once you've added details of your project you can then print Do-Not-Hack labels for it. Please note: you must be in the hackspace and connected to the hackpace WiFi in order to print off a label.</p>\r\n\r\n<p>Please manually update the date on the label as/when you work on the project. This is so others can see it is an active project and has not been abandoned. Abandonded projects may be disposed of, but the label should allow us to contact you first if this should become necessary.</p>\r\n\r\n<p>When you have completed your project, please click the Mark Complete button.</p>\r\n\r\n<p>If you need to resume your project and don't want to start it as a new project, you can restart it by clicking Resume Project.</p>",
        ],
        [
            'view' => 'snackspace.transaction.index',
            'block' => 'acceptors',
            'content' => "<p>\r\n  Please use the note or coin acceptors located in the first floor members box room to add money to your Snackspace account.\r\n</p>",
        ],
        [
            'view' => 'team.how_to_join',
            'block' => 'main',
            'content' => "<p>If you would like to join a team, the first step is to join Slack which you can do at <a href=\"http://slack.nottinghack.org.uk\" target=\"_blank\">slack.nottinghack.org.uk</a></p>\r\n\r\n<p>Once you're on Slack, add yourself to the channels that are of interest, then post a message announcing yourself and offering to help.</p>\r\n\r\n<p>There is also more information about the teams on our <a href=\"https://wiki.nottinghack.org.uk/wiki/Teams\" target=\"_blank\">Teams wiki page</a>.\r\n\r\n<p>Once you've made contact with the team and are happy you do wish to join, let the trustees know and they will officially add you to the team and its email group.</p>\r\n</p>",
        ],
        [
            'view' => 'tools.booking.index',
            'block' => 'directions',
            'content' => "<p>To add a booking, select and drag on the calendar below.</p>\r\n<p>To re-schedule the booking, click and move the booking to a new time.</p>\r\n<p>To cancel a booking, click on the booking then confirm cancellation.</p>",
        ],
        [
            'view' => 'tools.tool.index',
            'block' => 'main',
            'content' => '',
        ],
        [
            'view' => 'user.show',
            'block' => 'main',
            'content' => "<p>The Hackspace only uses these details to get in touch with you if needed, or if we are legally mandated to pass them on.</p>\r\n<p>We highly recommend you keep your details updated, especially your email address, as this is our main method of communication, including when door codes are changed and other important information.</p>",
        ],
        [
            'view' => 'logo',
            'block' => 'svg',
            'content' => '<svg version="1.1" x="0px" y="0px" width="75" height="75" viewBox="0 0 220 220" enable-background="new 0 0 223.489 220.489" id="logo" style="fill: #195905;">
            <path id="logo-path" d="m 187.42396,32.576354 c -42.87821,-42.874396 -112.393767,-42.874396 -155.264347,0 -42.879484,42.878211 -42.879484,112.391236 0,155.266896 42.87058,42.87567 112.389957,42.87567 155.264347,0 42.87567,-42.87566 42.87567,-112.388685 0,-155.266896 z m -34.95429,114.891786 -25.04366,-25.03984 7.87686,-7.87687 -4.16546,-4.1642 -21.17074,21.17201 4.16037,4.16801 8.15287,-8.15668 25.05002,25.04113 -37.53878,37.53878 -25.046204,-25.04239 4.272304,-4.26849 -29.852708,-29.85653 -4.277392,4.27358 -25.039847,-25.04366 37.540057,-37.540065 25.041119,25.041119 -8.157951,8.155416 5.127019,5.12575 21.177093,-21.17329 -5.12448,-5.125747 -7.881947,7.880677 -25.042386,-25.043663 37.266593,-37.260239 25.0373,25.041119 -4.26721,4.273576 29.85144,29.853979 4.27485,-4.273576 25.04111,25.044944 z"></path>
          </svg>',
        ],
    ];

    /**
     * The Email type ContentBlocks and there default content.
     *
     * @var array
     */
    protected $emailBlocks = [
        [
            'view' => 'emails.interestRegistered',
            'block' => 'main',
            'content' => "After setting up login details, you'll be asked to fill in some more information about yourself, namely your address and a contact phone number. Don't worry, we won't share this information with anyone, unless legally obliged to do so.\r\n\r\nOnce you've submitted the information we need, one of our member admins will be notified. They'll give your information a quick check, and if all is well they'll move your membership on to the next stage. This is the part where you get the Nottingham Hackspace bank details, as well as a unique payment reference code for your account. It is very important you include this reference on your payments. \r\n\r\nUse these details to set up a bank standing order for your membership fee. Membership is pay-what-it's-worth-to-you on a monthly basis, and you can always change the amount you're paying if you find yourself using the space more or less than you first thought.\r\n\r\nWhen your standing order is set up and your first payment is made, even if the money leaves your account, payments are not instant between all banks and records don't update immediately, so it may take 3 to 4 days before it's visible in the hackspace account. Our automated system checks our account at midnight on weekdays. When your payment does show, you'll receive an email confirming membership, you'll get the door codes and an invitation to collect your RFID card at a Wednesday Open Hack Night. Once you've collected your card, you are free to visit at any time, twenty four hours a day.\r\n\r\nA few important details: Nottingham Hackspace is incorporated as a non-profit company, registration number 07766826. Everyone who works on stuff for the hackspace is a volunteer; the hackspace has no staff, just members. So far, it has also been entirely funded and is self-sustaining through members contributions rather than grants.\r\n\r\nPlease do consider volunteering. We are always looking for help.",
        ],
        [
            'view' => 'emails.gatekeeper.booking_approved',
            'block' => 'additional',
            'content' => "You must leave and swipe out of the Hackspace before the end time of your booking. You must swipe your RFID card on an exit door (this is any door, except the door at the top of the spiral staircase. This door is only an “in” door.  \r\nPlease ensure that you wipe all surfaces you have been in contact with before you leave.",
        ],
        [
            'view' => 'emails.gatekeeper.booking_approved_automatically',
            'block' => 'additional',
            'content' => "You must leave and swipe out of the Hackspace before the end time of your booking. You must swipe your RFID card on an exit door (this is any door, except the door at the top of the spiral staircase. This door is only an “in” door.  \r\nPlease ensure that you wipe all surfaces you have been in contact with before you leave.",
        ],
        [
            'view' => 'emails.gatekeeper.booking_cancelled_with_reason',
            'block' => 'additional',
            'content' => '',
        ],
        [
            'view' => 'emails.gatekeeper.booking_ended',
            'block' => 'additional',
            'content' => "You must leave and swipe out of the Hackspace before the end time of your booking. You must swipe your RFID card on an exit door (this is any door, except the door at the top of the spiral staircase. This door is only an “in” door.  \r\nPlease ensure that you wipe all surfaces you have been in contact with before you leave.",
        ],
        [
            'view' => 'emails.gatekeeper.booking_made',
            'block' => 'additional',
            'content' => '',
        ],
        [
            'view' => 'emails.gatekeeper.booking_rejected',
            'block' => 'additional',
            'content' => '',
        ],
        [
            'view' => 'emails.membership.approved',
            'block' => 'main',
            'content' => "Your details have now been approved by the Membership Team.\r\n\r\nThe next step in setting up your hackspace membership is for you to set up a bank standing order to Nottingham Hackspace using the details below.\r\n\r\nAs explained on your tour around the Hackspace, we have a \"Pay what you think the space is worth to you\" system. We are entirely member funded and rely almost exclusively on membership contributions to keep the Hackspace open.  \r\nWe recommend, if you are planning on using the space semi-regularly, that £15 is a fair monthly membership contribution.\r\n\r\nFrom time to time all membership contributions will be reviewed and members may be asked to increase their contribution.  \r\nRead more about the rules governing membership here: [Membership of the Hackspace](https://rules.nottinghack.org.uk/en/latest/membership-of-the-hackspace.html)",
        ],
        [
            'view' => 'emails.membership.approved',
            'block' => 'additional',
            'content' => "Once we've received your first payment (which may take 3-4 days to show up in our account after it leaves yours), we'll send an email confirming membership. You can then collect your RFID card at a Wednesday Open Hack Night. You will then have 24 hour access to the space.",
        ],
        [
            'view' => 'emails.membership.eighteenCongratulations',
            'block' => 'additional',
            'content' => '',
        ],
        [
            'view' => 'emails.membership.membershipComplete',
            'block' => 'main',
            'content' => "Thanks for becoming a member of Nottingham Hackspace. Here are all of the remaining details you need, though there's one last step before you'll have full 24 hour access.\r\n\r\nGatekeeper is our RFID entry system for which you need a suitable card set up, which we provide. Visit on a Wednesday Open Hack Night and ask people to point you to one of the membership team, or please contact membership@nottinghack.org.uk if you are unable to attend one.",
        ],
        [
            'view' => 'emails.membership.membershipMayBeRevoked',
            'block' => 'main',
            'content' => 'We have not seen a membership payment from you recently. If we do not see one soon your membership to Nottingham Hackspace will be revoked.',
        ],
        [
            'view' => 'emails.membership.membershipMayBeRevoked',
            'block' => 'additional',
            'content' => 'If you no longer wish to be a member and have intentionally cancelled your standing order we are sorry to see you go, but would like to thank you for being a member and supporting the hackspace. Your membership will end in a couple of weeks time. Before your membership finally ends please ensure you remove any projects or materials you may have at the hackspace.',
        ],
        [
            'view' => 'emails.membership.membershipReinstated',
            'block' => 'main',
            'content' => "Thanks for reinstating your membership of Nottingham Hackspace. Here is a reminder of some details you might need.\r\n\r\nGatekeeper is our RFID entry system for which you need a suitable card set up, which we provide. If you aren't sure if your former RFID card will work, or if you need a new one, please visit on the next open hack night and ask people to point you to one of the membership team, or please contact membership@nottinghack.org.uk if you are unable to attend one. It will be £1 for a new RFID card.",
        ],
        [
            'view' => 'emails.membership.membershipRevoked',
            'block' => 'main',
            'content' => 'We are sorry to see you go, but as you have not made a payment recently your Nottingham Hackspace membership has been revoked and your access to the space has been suspended.',
        ],
        [
            'view' => 'emails.snackspace.current_member_debt',
            'block' => 'additional',
            'content' => "For the first eight years of Nottingham Hackspace, we allowed members up to £20 credit. However in 2019, this became unsustainable when the combined debt of all members, current and former, surpassed £2,500.\r\n\r\nIn July 2019, that credit limit was reduced to zero.",
        ],
        [
            'view' => 'emails.snackspace.ex_member_debt',
            'block' => 'additional',
            'content' => 'For the first eight years of Nottingham Hackspace, we allowed members up to £20 credit. However in 2019, this became unsustainable when the combined debt of all members, current and former, surpassed £2,500.',
        ],
        [
            'view' => 'emails.teams.reminder',
            'block' => 'main',
            'content' => "Please note this is your reminder that in one week we will have the monthly Member's Meeting.\r\n\r\nIf you believe your team has anything to report back over the past 4 weeks, please let us know by the SUNDAY before the Member's Meeting. The Member's Meeting is always the first Wednesday of the month.\r\n\r\nIf we do not hear from you with a round up, there won't be time to discuss any news at the meeting so it will have to be delayed until the month after.\r\n\r\nIf you've got any questions, please do email us at <trustees@nottinghack.org.uk>, or reply to this email.",
        ],
        [
            'view' => 'emails.teams.membershipUnderPaid',
            'block' => 'main',
            'content' => '',
        ],
        [
            'view' => 'emails.teams.membershipMayBeRevokedDueToUnderPayment',
            'block' => 'main',
            'content' => 'Though we have seen payments from you recently they are below the minimum amount. If we do not see a payment equal or above the minimum soon your membership to Nottingham Hackspace will be revoked.',
        ],
        [
            'view' => 'emails.teams.membershipMayBeRevokedDueToUnderPayment',
            'block' => 'additional',
            'content' => 'If you no longer wish to be a member and have intentionally reduced your standing order below the minimum to continue supporting us we are sorry to see you go, but would like to thank you for being a member and continuing to supporting the hackspace with your donation. Your membership will end in a couple of weeks time. Before your membership finally ends please ensure you remove any projects or materials you may have at the hackspace.',
        ],
        [
            'view' => 'emails.membership.membershipRevokedDueToUnderPayment',
            'block' => 'main',
            'content' => 'We are sorry to see you go, but as your recent payments where below the minimum amount your Nottingham Hackspace membership has been revoked and your access to the space has been suspended.',
        ],
        [
            'view' => 'emails.teams.membershipExUnderPaid',
            'block' => 'main',
            'content' => '',
        ],
    ];

    /**
     * @var ContentBlockRepository
     */
    protected $contentBlockRepository;

    /**
     * Create a new TableSeeder instance.
     *
     * @param ContentBlockRepository $contentBlockRepository
     */
    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->pageBlocks as $blockDetails) {
            if (! $this->contentBlockRepository->has($blockDetails['view'], $blockDetails['block'])) {
                $contentBlock = new ContentBlock;

                $contentBlock->setType(ContentBlockType::PAGE);
                $contentBlock->setView($blockDetails['view']);
                $contentBlock->setBlock($blockDetails['block']);
                $contentBlock->setContent($blockDetails['content']);

                $this->contentBlockRepository->save($contentBlock);
            }
        }

        foreach ($this->emailBlocks as $blockDetails) {
            if (! $this->contentBlockRepository->has($blockDetails['view'], $blockDetails['block'])) {
                $contentBlock = new ContentBlock;

                $contentBlock->setType(ContentBlockType::EMAIL);
                $contentBlock->setView($blockDetails['view']);
                $contentBlock->setBlock($blockDetails['block']);
                $contentBlock->setContent($blockDetails['content']);

                $this->contentBlockRepository->save($contentBlock);
            }
        }
    }
}

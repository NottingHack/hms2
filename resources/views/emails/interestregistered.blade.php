<html>
    <body>
        <p>
            Hello!
        </p>

        <p>
            Thank you for registering your interest in Nottingham Hackspace.
        </p>

        <p>
            If you'd like to become a Nottingham Hackspace member, the first step is is to <a href="{{ secure_url('register',  $token)}}">create an HMS account</a>. Also, please add "{{ $membershipEmail }}" and "{{ $trusteesEmail }}" to your address book to reduce the chances of hackspace email going into your spam.
        </p>

        <p>
            After creating login details, you'll be asked to fill in some more information about yourself, namely your address and a contact phone number . Don't worry, we won't share this information with anyone, unless legally obliged to do so.
        </p>

        <p>
            Once you've filled in your details, one of our member admins will be notified. They'll give your information a quick check, and if all is well they'll move your membership on to the next stage. This is the part where you get the Nottingham Hackspace bank details, as well as a unique payment reference for your account (please use this reference if possible; it makes it easier to correlate your membership payments to you as a member). Use these details to set up a standing order for your membership fee. Membership is pay-what-you-like on a monthly basis, and you can always change the amount you're paying if you find yourself using the space more or less than you first thought.
        </p>

        <p>
            When the standing order is set-up, once your payment shows up in our bank account. Even if the money leaves your account, payments are not instant between all banks and records don't update immediately, so it may take 3 to 4 days before it's visible in the hackspace account. When it does show, you'll be contacted directly by one of the member admins to arrange first-access; this is where you're given the door codes and your RFID card to get in to the space. Once that's done, you are free to visit at any time, twenty four hours a day.
        </p>

        <p>
            A few important details: Nottingham Hackspace is incorporated as a non-profit company, registration number 07766826. Everyone who works on stuff for the hackspace is a volunteer; the hackspace has no staff, just members. So far, it has also been entirely funded and is self-sustaining through members contributions rather than grants.
        </p>

        <p>
            Here's the URL for the public google group:<br>
            <a href="{{ $groupLink }}">{{ $groupLink }}</a>
        </p>

        <p>
            Here are the hackspace rules:<br>
            <a href="{{ $rulesHTML }}">{{ $rulesHTML }}</a>
        </p>

        <p>
            If you have any questions, just email.
        </p>

        <p>
            Thanks,<br>
            Nottinghack Member Admin Team
        </p>
    </body>
</html>
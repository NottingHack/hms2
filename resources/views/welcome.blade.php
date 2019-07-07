@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Welcome to HMS!</h1>
  <p class="lead">Already a member? <a href="{{ route('login') }}">Log in!</a></p>

  <h2>What is Nottinghack?</h2>
  <p>Nottinghack is a Nottingham based group for hackers, makers and crafty creatives!</p>
  <p>Hacking is NOT to be confused with network hacking, identity theft and computer virus propagation, etc. Nottinghack does not condone anything illegal; hardware Hacking is a creative, educational hobby!</p>
  <p>Who is it for? If you like to build, make &amp; learn it’s for you. You’ll probably be interested in learning about and sharing knowledge of electronics, crafts, robotics, DIY, hardware hacking, photography, computing, reverse engineering, prototyping, film making, animation, building RC vehicles and other creative challenges and projects.</p>
  <p>You’ll be looking for a group who can share tools, techniques and time; pool resources for bigger projects, get funding, discounts on kits and components and start classes, all in a safe friendly environment!</p>
  <p><a href="http://nottinghack.org.uk/?page_id=10" target="_blank">Read more...</a></p>

  <h2>What is HMS?</h2>
  <p>HMS (Hackspace Management System) is a program designed to help keep track of members, it's a bit basic at the moment, but we have big plans for it. It's current main goal is to make new member registration easier.</p>

  <h2>Interested in Nottingham Hackspace?</h2>
  <p>Excellent! Have you had a tour yet? If not come down to one of our open hack-nights (every Wednesday from 6:30pm at the address below). Already in the building? Look for the human near <a href="http://www.flickr.com/photos/nottinghack/7048461835/" target="_blank">Ein the duck</a>, they'll be able to help you.</p>
  <p>You may also want to follow Nottingham Hackspace on your choice of social network: <a href="http://twitter.com/#!/hsnotts" target="_blank">Twitter</a>, <a href="http://groups.google.com/group/nottinghack" target="_blank">Google Group</a>, <a href="http://www.flickr.com/photos/nottinghack" target="_blank">Flickr</a>, <a href="http://www.youtube.com/user/nottinghack" target="_blank">YouTube</a>, or <a href="http://www.facebook.com/pages/NottingHack/106946729335123" target="_blank">Facebook</a>.</p>
  <p>We also have a <a href="http://nottinghack.org.uk/blog" target="_blank">Blog</a>.</p>
</div>
@endsection

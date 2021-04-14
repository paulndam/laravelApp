Hello,
<p>{{ $content }}</p>
<p>Your email is {{$email}}.</p>
<p>Your initial password is {{$password}}.</p>

<a href="http://{{env('SITE_IP')}}/invite/{{$token}}" style="background: #4ebd75; padding: 10px 20px; text-decoration: none;">View Invite</a>
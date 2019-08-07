{{ $email->preview_text }}

Greenhead
http://greenhead.net
  
{{ date('F j, Y', strtotime($email->send_at)) }}

@foreach ($email->getArticles() as $index => $article)
{{ $article->title }} 
{{ $article->url() . '?' . $email->getGoogleTracker() }} 
@endforeach
  
Greenhead | 114 Scott Street | Little Rock, AR 72201 
PHONE: 501-372-1443 | FAX: 501-375-7933

https://www.facebook.com/greenhead.net 
https://twitter.com/huntarkansas
https://www.instagram.com/greenheadmagazine/
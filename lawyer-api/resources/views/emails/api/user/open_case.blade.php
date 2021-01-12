<h1>{{$action}}</h1>
<p><a href="{{route('cases.index',['id'=>$case->id])}}">Open in Admin</a></p>
<p><b>id:</b> {{$case->id}}</p>
<p><b>title:</b> {{$case->title}}</p>
<p><b>message:</b>
    <br>
    {{$case->message}}</p>

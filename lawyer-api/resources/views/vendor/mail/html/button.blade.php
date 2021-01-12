<span style="display:block; text-align:center;">
    <a href="{{ $url }}" target="_blank" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; background:#653db2; color:#FFF; text-decoration:none; width:200px; max-width:200px; text-align:center; display:inline-block; border-radius:35px;">
        <span style="display:block; min-height:20px; max-height:20px;">
            <img src="{{asset('images/spacer.gif')}}" width="1" height="20" alt="spacer">
        </span>
        <span>{{ $slot }}</span>
        <span style="display:block; min-height:20px; max-height:20px;">
            <img src="{{asset('images/spacer.gif')}}" width="1" height="20" alt="spacer">
        </span>
    </a>
</span>

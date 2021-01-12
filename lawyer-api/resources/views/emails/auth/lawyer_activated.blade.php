@component('mail::message')
    <td>
        <div style="height:50px; max-height:50px; min-height:50px;">
            <img src="{{asset('images/spacer.gif')}}" width="1" height="50">
        </div>
        <span style="font-size:24px; color:#4f4f4f; font-weight:bold; text-align:center; display:block;">{{__('emails.auth.lawyer_activated.title')}}</span>
        <div style="height:50px; max-height:50px; min-height:50px;">
            <img src="{{asset('images/spacer.gif')}}" width="1" height="50">
        </div>
        <div>
            <img src="{{asset('images/email_verified.png')}}"
                 alt="{{__('emails.auth.lawyer_activated.title')}}"
                 title="{{__('emails.auth.lawyer_activated.title')}}" width="180">
        </div>
        <div style="height:20px; max-height:20px; min-height:20px;">
            <img src="{{asset('images/spacer.gif')}}" width="1" height="20">
        </div>
        <span style="font-size:16px; color:#4f4f4f; text-align:center">
            {{__('emails.auth.lawyer_activated.body')}}
        </span>
        <div style="height:50px; max-height:50px; min-height:50px;">
            <img src="{{asset('images/spacer.gif')}}" width="1" height="25">
        </div>

        <div style="height:50px; max-height:50px; min-height:50px;">
            <img src="{{asset('images/spacer.gif')}}" width="1" height="50">
        </div>
    </td>
@endcomponent
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{config('app.name')}}</title>
</head>

<body style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; background:url({{asset('images/bg_purple.jpg')}}) repeat-x left top;">
<div style="height:40px; max-height:40px; min-height:40px;">
    <img src="{{asset('images/spacer.gif')}}" width="1" height="40">
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><a href="{{route('home',['client'])}}" target="_blank">
                <img src="{{asset('images/ammurapi_logo.png')}}" width="90" alt="{{config('app.name')}}"
                     title="{{config('app.name')}}"/>
            </a>
        </td>
    </tr>
</table>
<div style="height:20px; max-height:20px; min-height:20px;">
    <img src="{{asset('images/spacer.gif')}}" width="1" height="20">
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="10"></td>
        <td align="center">
            <div style="max-width:650px; background:#EDEEF2; border:1px solid #d8d8d8; border-radius:20px;  text-align:center;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="25">&nbsp;</td>
                        {{ $header or '' }}

                        {{ Illuminate\Mail\Markdown::parse($slot) }}

                        {{ $subcopy or '' }}

                        {{ $footer or '' }}
                        <td width="25">&nbsp;</td>
                    </tr>
                </table>
            </div>
            <div style="height:20px; max-height:20px; min-height:20px;">
                <img src="{{asset('images/spacer.gif')}}" width="1" height="20">
            </div>
            <div style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333;">{{__('emails.layout.need_help')}}
                <a href="mailto:{{config('mail.support_email')}}" target="_blank" style="color:#039; font-weight:bold;">
                    {{config('mail.support_email')}}
                </a>
                <div style="height:20px; max-height:20px; min-height:20px;">
                    <img src="{{asset('images/spacer.gif')}}" width="1" height="20">
                </div>
                <div style="text-align:center; border-bottom: 1px solid #EDEEF2; max-width:650px;">
                    <div style="color:#333; font-family:Arial, Helvetica, sans-serif; font-size:14px;">
                        {{__('emails.layout.connect_with_us')}}
                    </div>
                    <div style="height:15px; max-height:15px; min-height:15px;">
                        <img src="{{asset('images/spacer.gif')}}" width="1" height="15">
                    </div>
                    <a href="https://www.facebook.com/Ammurapi.LTD" target="_blank">
                        <img src="{{asset('images/ic_facebook.png')}}" width="22"
                             alt="{{__('emails.layout.facebook')}}">
                    </a>&nbsp;&nbsp;&nbsp;
                    <a href="http://www.linkedin.com/in/Ammurapi-ltd" target="_blank">
                        <img src="{{asset('images/ic_linkedin.png')}}" width="22"
                             alt="{{__('emails.layout.linkedin')}}">
                    </a>&nbsp;&nbsp;&nbsp;
                    <a href="https://twitter.com/Ammurapi_LTD" target="_blank">
                        <img src="{{asset('images/ic_twitter.png')}}" width="22"
                             alt="{{__('emails.layout.twitter')}}">
                    </a>&nbsp;&nbsp;&nbsp;
                    <div style="height:20px; max-height:20px; min-height:20px;">
                        <img src="{{asset('images/spacer.gif')}}" width="1" height="20">
                    </div>
                </div>
                <div style="height:25px; max-height:20px; min-height:20px;">
                    <img src="{{asset('images/spacer.gif')}}" width="1" height="20">
                </div>
                <div style="height:25px; max-height:20px; min-height:20px;">
                    <img src="{{asset('images/spacer.gif')}}" width="1" height="20">
                </div>
            </div>
            <div style="height:25px; max-height:25px; min-height:25px;">
                <img src="{{asset('images/spacer.gif')}}" width="1" height="25">
            </div>
            <div style="height:125px; max-height:125px; min-height:125px;">
                <img src="{{asset('images/spacer.gif')}}" width="1" height="125">
            </div>
        </td>
        <td width="10"></td>
    </tr>
</table>

</body>
</html>
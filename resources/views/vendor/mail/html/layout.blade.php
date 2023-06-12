@props(['title'])
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ru" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
<meta name="x-apple-disable-message-reformatting">
<title>{{ $title }}</title>
<style>html {
-webkit-text-size-adjust: none;
-ms-text-size-adjust: none;
hyphens: none;
-moz-hyphens: none;
-webkit-hyphens: none;
-ms-hyphens: none;
}
</style>
<style>@media only screen and (max-width: 480px) {
u + .body .full-wrap {
width: 100% !important;
width: 100vw !important
}
}</style>
<style>@-ms-viewport {
width: device-width
}</style>
<!--[if (gte mso 9)|(IE)]>
<style type="text/css">table {
border-collapse: collapse !important;
}
</style>
<![endif]-->
</head>
<body style="padding:0;margin:0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="full-wrap">
<tr>
<td align="center" bgcolor="#F3F3F3" style="line-height: normal; word-break:normal;-webkit-text-size-adjust:none; -ms-text-size-adjust: none;">
{{ $header ?? '' }}
<div style="height: 16px; line-height: 16px; font-size: 14px;">&nbsp;</div>
<!-- BODY -->
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td align="center" valign="top" style="padding: 0px 10px;">
<div>
<!--[if (gte mso 9)|(IE)]>
<table width="600" border="0" cellspacing="0" cellpadding="0" style="width: 600px;">
<tr>
<td>
<![endif]-->
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;">
<tr>
<td align="center" bgcolor="#FFFFFF" style="border-radius: 8px;">
<div>
<!--[if (gte mso 9)|(IE)]>
<table width="572" border="0" cellspacing="0" cellpadding="0" style="width: 572px;">
<tr>
<td>
<![endif]-->
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 572px;">
<tr>
<td align="left" style="padding: 0px 10px;">
<div style="height: 20px; line-height: 20px; font-size: 16px;">&nbsp;</div>
{{ Illuminate\Mail\Markdown::parse($slot) }}
<div style="height: 10px; line-height: 10px; font-size: 10px;">&nbsp;</div>
</td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</div>
</td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</div>
</td>
</tr>
</table>
<!-- BODY END-->
<div style="height: 16px; line-height: 16px; font-size: 14px;">&nbsp;</div>
{{ $footer ?? '' }}
</td>
</tr>
</table>
</body>
</html>

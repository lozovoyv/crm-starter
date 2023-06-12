@props(['url'])
<table width="100%">
<tr>
<td align="center">
<div>
<!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
href="{{ $url }}"
style="height:48px;v-text-anchor:middle;width:278;" arcsize="8%" strokecolor="#0B57A1" fillcolor="#0B57A1">
<w:anchorlock>
</w:anchorlock>
<center style="color: #ffffff; font-size: 16px; font-weight: normal; font-family: Arial, Helvetica, sans-serif;">{{ $slot }}</center>
</v:roundrect>
<![endif]-->
<a target="_blank" href="{{ $url }}"
style="background-color:#0B57A1;font-size:16px;font-weight:normal;line-height:36px;padding:0 16px;border:1px solid #0B57A1;color:#ffffff;border-radius:4px;display:inline-block;font-family:Arial, Helvetica, sans-serif;text-align:center;text-decoration:none;-webkit-text-size-adjust:none;mso-hide:all">{{ $slot }}</a>
<div style="height: 18px; line-height: 30px; font-size: 28px;">&nbsp;</div>
</div>
</td>
</tr>
</table>

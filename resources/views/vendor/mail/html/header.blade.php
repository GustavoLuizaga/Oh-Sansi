<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://firebasestorage.googleapis.com/v0/b/connectway-867ac.appspot.com/o/logo-ohsansi.PNG?alt=media&token=59bc89c5-5472-4e4a-93a8-1ee977bd02f7"
     alt="Logo Ohsansi"
     width="160"
     height="60"
     style="display: block; margin: 0 auto;">

@else
{{ $slot }}
@endif
</a>
</td>
</tr>

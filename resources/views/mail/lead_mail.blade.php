@component('mail::layout')
@slot('header')
@include('vendor.mail.html.header')
@endslot

@php
$isArray       = is_array($content);
$titulo        = $isArray ? ($content['titulo'] ?? '') : '';
$header_left   = $isArray ? ($content['header_left'] ?? []) : [];
$header_right  = $isArray ? ($content['header_right'] ?? []) : [];
$incums        = $isArray ? ($content['incumplimientos'] ?? []) : [];
$comentarios   = $isArray ? ($content['comentarios'] ?? '') : '';
$mejoras       = $isArray ? ($content['mejoras'] ?? '') : '';
@endphp

@if(!$isArray)
{!! $content !!}
@else
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
<tr>
<td align="center" style="font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:bold;">
{{ $titulo }}
</td>
</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:14px;">
<tr>
<td valign="top" width="50%" style="padding-right:8px;">
<table width="100%" cellpadding="6" cellspacing="0" border="0" style="border:1px solid #c7c7c7;font-family:Arial,Helvetica,sans-serif;font-size:13px;">
@foreach($header_left as $k => $v)
<tr>
<td width="45%" style="background:#efefef;border-bottom:1px solid #ddd;"><strong>{{ $k }}</strong></td>
<td style="border-bottom:1px solid #ddd;">{{ $v }}</td>
</tr>
@endforeach
</table>
</td>
<td valign="top" width="50%" style="padding-left:8px;">
<table width="100%" cellpadding="6" cellspacing="0" border="0" style="border:1px solid #c7c7c7;font-family:Arial,Helvetica,sans-serif;font-size:13px;">
@foreach($header_right as $k => $v)
<tr>
<td width="45%" style="background:#efefef;border-bottom:1px solid #ddd;"><strong>{{ $k }}</strong></td>
<td style="border-bottom:1px solid #ddd;">{{ $v }}</td>
</tr>
@endforeach
</table>
</td>
</tr>
</table>

<table width="100%" cellpadding="10" cellspacing="0" border="0" style="margin-bottom:15px;font-family:Arial,Helvetica,sans-serif;">
<tr>
<td style="border:1px solid #c7c7c7;background:#f5f5f5;">
<strong>Incumplimiento de VARIABLES</strong>
<div style="margin-top:6px;">
@php $hayFallo=false; @endphp
@foreach($incums as $item)
    @if(!empty($item['marcada']))
        @php $hayFallo=true; @endphp
        • {{ $item['nombre'] ?? 'Variable' }}<br>
    @endif
@endforeach
@unless($hayFallo)
    No se encontraron variables con fallo.
@endunless
</div>

</td>
</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:15px;font-family:Arial,Helvetica,sans-serif;">
<tr>
<td style="background:#0070c0;color:#fff;font-weight:bold;padding:10px 12px;font-size:15px;">
Detalle de la escucha (agrega minutos de inconsistencia)
</td>
</tr>
<tr>
<td style="border:1px solid #c7c7c7;padding:12px;background:#ffffff;font-size:14px;line-height:1.45;">
{!! nl2br(e($comentarios)) !!}
</td>
</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-family:Arial,Helvetica,sans-serif;">
<tr>
<td style="background:#1f497d;color:#fff;font-weight:bold;padding:10px 12px;font-size:15px;">
¿Cuál era la forma correcta?
</td>
</tr>
<tr>
<td style="border:1px solid #c7c7c7;padding:12px;background:#ffffff;font-size:14px;line-height:1.45;">
{!! nl2br(e($mejoras)) !!}
</td>
</tr>
</table>
@endif

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('Todos los derechos reservados.')
@endcomponent
@endslot
@endcomponent

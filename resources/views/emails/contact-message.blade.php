@php $m = $contactMessage; @endphp
<!DOCTYPE html>
<html lang="az">
<head><meta charset="utf-8"><title>Saytdan yeni müraciət</title></head>
<body style="margin:0;padding:24px;background:#f3f6fa;font-family:Arial,Helvetica,sans-serif;color:#17212B">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:10px;overflow:hidden">
    <tr>
      <td style="background:#153F70;color:#ffffff;padding:18px 24px;font-size:18px;font-weight:bold">
        {{ $m->type === 'partner' ? 'Tərəfdaşlıq müraciəti' : 'Saytdan yeni müraciət' }}
      </td>
    </tr>
    <tr>
      <td style="padding:24px">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5">
          <tr><td style="padding:6px 0;color:#5B6B7A;width:120px">Ad, soyad</td><td style="padding:6px 0"><strong>{{ $m->name }}</strong></td></tr>
          @if ($m->company)<tr><td style="padding:6px 0;color:#5B6B7A">Şirkət</td><td style="padding:6px 0">{{ $m->company }}</td></tr>@endif
          @if ($m->country)<tr><td style="padding:6px 0;color:#5B6B7A">Ölkə</td><td style="padding:6px 0">{{ $m->country }}</td></tr>@endif
          <tr><td style="padding:6px 0;color:#5B6B7A">E-poçt</td><td style="padding:6px 0">@if ($m->email)<a href="mailto:{{ $m->email }}">{{ $m->email }}</a>@else — @endif</td></tr>
          <tr><td style="padding:6px 0;color:#5B6B7A">Telefon</td><td style="padding:6px 0">@if ($m->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $m->phone) }}">{{ $m->phone }}</a>@else — @endif</td></tr>
          <tr><td style="padding:6px 0;color:#5B6B7A">Dil</td><td style="padding:6px 0">{{ strtoupper($m->locale ?? '') }}</td></tr>
          <tr><td style="padding:6px 0;color:#5B6B7A">Tarix</td><td style="padding:6px 0">{{ $m->created_at?->timezone('Asia/Baku')->format('d.m.Y H:i') }}</td></tr>
        </table>
        <div style="margin-top:18px;padding:16px;background:#EAF4FF;border-radius:8px;font-size:14px;line-height:1.6;white-space:pre-line">{{ $m->message }}</div>
        @if ($m->email)
          <p style="margin:18px 0 0;font-size:13px;color:#5B6B7A">Bu məktuba "Cavab ver" basaraq birbaşa müraciət edənə yaza bilərsiniz.</p>
        @endif
      </td>
    </tr>
  </table>
</body>
</html>

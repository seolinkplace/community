@php
$copyText = implode("\n", array_filter([
    'Exception: ' . $record->exception_class,
    'Message: ' . $record->message,
    'File: ' . $record->file . ':' . $record->line,
    'URL: ' . $record->method . ' ' . ($record->url ?? ''),
    'User: ' . ($record->user_id ? $record->user_type.':'.$record->user_id : 'Guest'),
    'IP: ' . ($record->ip ?? '—'),
    'Time: ' . $record->created_at->format('d.m.Y H:i:s'),
    'UUID: ' . $record->uuid,
    $record->input ? 'Input: ' . json_encode($record->input, JSON_UNESCAPED_UNICODE) : null,
    $record->trace ? "Trace:\n" . $record->trace : null,
]));
@endphp

<div style="font-size:14px;line-height:1.6;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
        <div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Exception</div>
            <div style="font-family:monospace;color:#ef4444;word-break:break-all;">{{ $record->exception_class }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Time</div>
            <div>{{ $record->created_at->format('d.m.Y H:i:s') }} ({{ $record->created_at->diffForHumans() }})</div>
        </div>
        <div style="grid-column:span 2;">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Message</div>
            <div style="font-weight:600;word-break:break-all;">{{ $record->message }}</div>
        </div>
        <div style="grid-column:span 2;">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">File</div>
            <div style="font-family:monospace;font-size:12px;word-break:break-all;">{{ $record->file }}:{{ $record->line }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">URL</div>
            <div style="word-break:break-all;">{{ $record->method }} {{ $record->url ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">User</div>
            <div>{{ $record->user_id ? $record->user_type.':'.$record->user_id : 'Guest' }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">IP</div>
            <div>{{ $record->ip ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">User Agent</div>
            <div style="font-size:11px;word-break:break-all;">{{ $record->user_agent ?? '—' }}</div>
        </div>
        @if($record->input)
        <div style="grid-column:span 2;">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Input</div>
            <pre style="background:#f8fafc;border-radius:6px;padding:8px;font-size:11px;overflow-x:auto;">{{ json_encode($record->input, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
        <div style="grid-column:span 2;">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">UUID</div>
            <div style="font-family:monospace;font-size:11px;color:#94a3b8;">{{ $record->uuid }}</div>
        </div>
    </div>

    @if($record->trace)
    <div style="margin-bottom:16px;">
        <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;">Stack Trace</div>
        <pre style="background:#0f172a;color:#4ade80;border-radius:6px;padding:12px;font-size:11px;overflow-x:auto;max-height:300px;">{{ $record->trace }}</pre>
    </div>
    @endif

    <div style="padding-top:12px;border-top:1px solid #f1f5f9;">
        <button
            data-copytext="{{ htmlspecialchars($copyText, ENT_QUOTES, 'UTF-8') }}"
            onclick="
                var t=this.getAttribute('data-copytext');
                navigator.clipboard.writeText(t).then(function(){
                    document.getElementById('errspn-{{ $record->id }}').textContent='Copied!';
                    setTimeout(function(){document.getElementById('errspn-{{ $record->id }}').textContent='Copy';},2000);
                }).catch(function(){
                    var ta=document.createElement('textarea');
                    ta.value=t;ta.style.cssText='position:fixed;opacity:0;';
                    document.body.appendChild(ta);ta.select();
                    document.execCommand('copy');document.body.removeChild(ta);
                    document.getElementById('errspn-{{ $record->id }}').textContent='Copied!';
                    setTimeout(function(){document.getElementById('errspn-{{ $record->id }}').textContent='Copy';},2000);
                });
            "
            style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:6px;cursor:pointer;font-size:12px;font-weight:500;color:#475569;">
            <svg style="width:13px;height:13px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <span id="errspn-{{ $record->id }}">Copy</span>
        </button>
    </div>
</div>

@php $lastId = $messages->last()?->id ?? 0; @endphp

<div class="bg-white rounded-xl border border-gray-200 flex flex-col" style="height: 520px;">

    {{-- Повідомлення --}}
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3">
        @foreach($messages as $msg)
        <div class="flex {{ $msg->sender_type === $senderType ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-md">
                <div class="text-xs text-gray-400 mb-1 {{ $msg->sender_type === $senderType ? 'text-right' : '' }}">
                    {{ $msg->sender_name }} · {{ $msg->created_at->format('d.m H:i') }}
                </div>
                <div class="px-4 py-2 rounded-2xl text-sm
                    {{ $msg->sender_type === $senderType
                        ? 'bg-gray-900 text-white rounded-tr-sm'
                        : ($msg->sender_type === 'admin'
                            ? 'bg-purple-100 text-purple-900 rounded-tl-sm'
                            : 'bg-gray-100 text-gray-800 rounded-tl-sm') }}">
                    {{ $msg->body }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Форма --}}
    <div class="border-t border-gray-200 p-3 flex gap-2">
        <input type="text" id="chat-input"
               placeholder="Повідомлення..."
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
        <button id="chat-send"
                class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">
            →
        </button>
    </div>
</div>

<script>
(function(){
    var lastId    = {{ $lastId }};
    var pollUrl   = "{{ $pollUrl }}";
    var sendUrl   = "{{ $sendUrl }}";
    var csrfToken = "{{ csrf_token() }}";
    var box       = document.getElementById('chat-messages');
    var input     = document.getElementById('chat-input');
    var btn       = document.getElementById('chat-send');

    function scrollBottom() {
        box.scrollTop = box.scrollHeight;
    }

    function appendMessage(msg) {
        var isMine = msg.sender_type === "{{ $senderType }}";
        var isAdmin = msg.sender_type === 'admin';
        var wrap = document.createElement('div');
        wrap.className = 'flex ' + (isMine ? 'justify-end' : 'justify-start');
        wrap.innerHTML = '<div class="max-w-xs lg:max-w-md">'
            + '<div class="text-xs text-gray-400 mb-1 ' + (isMine ? 'text-right' : '') + '">'
            + msg.sender_name + ' · ' + msg.time + '</div>'
            + '<div class="px-4 py-2 rounded-2xl text-sm '
            + (isMine ? 'bg-gray-900 text-white rounded-tr-sm'
                : (isAdmin ? 'bg-purple-100 text-purple-900 rounded-tl-sm'
                    : 'bg-gray-100 text-gray-800 rounded-tl-sm'))
            + '">' + msg.body.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</div></div>';
        box.appendChild(wrap);
        scrollBottom();
        lastId = msg.id;
    }

    function poll() {
        fetch(pollUrl + '?since=' + lastId)
            .then(function(r){ return r.json(); })
            .then(function(data){
                (data.messages || []).forEach(appendMessage);
            })
            .catch(function(){})
            .finally(function(){ setTimeout(poll, 5000); });
    }

    function send() {
        var body = input.value.trim();
        if (!body) return;
        btn.disabled = true;
        fetch(sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ body: body }),
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            if (data.error) { alert(data.error); return; }
            input.value = '';
        })
        .finally(function(){ btn.disabled = false; input.focus(); });
    }

    btn.addEventListener('click', send);
    input.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });

    scrollBottom();
    setTimeout(poll, 5000);
})();
</script>

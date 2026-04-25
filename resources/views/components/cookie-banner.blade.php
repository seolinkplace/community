<div id="cookie-banner" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:9999;background:#1a1a1a;border-top:2px solid #eab308;box-shadow:0 -4px 24px rgba(0,0,0,.6);">
    <div style="max-width:900px;margin:0 auto;padding:12px 16px;display:flex;align-items:center;gap:12px;justify-content:space-between;">
        <p style="font-size:12px;color:#9ca3af;margin:0;line-height:1.4;flex:1;min-width:0;">
            {!! __("auth.cookie_notice", ["url" => route("privacy")]) !!}
        </p>
        <div style="display:flex;gap:6px;flex-shrink:0;">
            <button onclick="cookieDecline()" style="padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;background:transparent;border:1px solid #374151;color:#9ca3af;cursor:pointer;white-space:nowrap;">
                {{ __("auth.cookie_decline") }}
            </button>
            <button onclick="cookieAccept()" style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;background:#eab308;color:#000;border:none;cursor:pointer;white-space:nowrap;">
                {{ __("auth.cookie_accept") }}
            </button>
        </div>
    </div>
</div>
<script>
(function(){
    if (!localStorage.getItem("cookie_consent")) {
        document.getElementById("cookie-banner").style.display = "block";
    }
})();
function cookieAccept() {
    localStorage.setItem("cookie_consent", "accepted");
    document.getElementById("cookie-banner").style.display = "none";
}
function cookieDecline() {
    localStorage.setItem("cookie_consent", "declined");
    document.getElementById("cookie-banner").style.display = "none";
    window["ga-disable-G-YJ6ZE1CCFQ"] = true;
}
</script>

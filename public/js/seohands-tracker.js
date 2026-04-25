(function(){
    var token = document.currentScript && document.currentScript.getAttribute('data-token');
    if(!token) return;
    var base = 'https://seolinkplace.com/api/v1/track';
    var page = encodeURIComponent(location.href);

    // Трекаємо всі зовнішні посилання на сторінці
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('a[href]').forEach(function(a){
            var href = a.getAttribute('href');
            if(!href || href.indexOf('http') !== 0) return;
            if(href.indexOf(location.hostname) !== -1) return; // пропускаємо внутрішні

            a.addEventListener('click', function(){
                fetch(base+'/anchor-click?token='+token+
                    '&href='+encodeURIComponent(href)+
                    '&text='+encodeURIComponent((a.textContent||'').trim().substring(0,100))+
                    '&page='+page,
                    {mode:'no-cors'}
                ).catch(function(){});
            });
        });
    });
})();

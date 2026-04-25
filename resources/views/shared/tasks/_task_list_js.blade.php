<script>
function toggleTaskForm(uuid) {
    const form = document.getElementById('tform-' + uuid);
    const btn  = document.getElementById('tbtn-' + uuid);
    const isHidden = form.classList.contains('hidden');
    document.querySelectorAll('[id^="tform-"]').forEach(f => f.classList.add('hidden'));
    document.querySelectorAll('[id^="tbtn-"]').forEach(b => {
        b.classList.remove('bg-gray-500', 'hover:bg-gray-600');
        b.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
    });
    if (isHidden) {
        form.classList.remove('hidden');
        btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
        btn.classList.add('bg-gray-500', 'hover:bg-gray-600');
        setTimeout(() => form.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
        const input = form.querySelector('input:not([type=hidden]), textarea');
        if (input) setTimeout(() => input.focus(), 100);
    }
}
</script>

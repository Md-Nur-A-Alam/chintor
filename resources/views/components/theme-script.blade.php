{{-- Apply the saved theme before first paint so the page never flashes. --}}
<script>
    (function () {
        var preference = 'system';
        try { preference = localStorage.getItem('chintor-theme') || 'system'; } catch (error) {}
        if (preference !== 'light' && preference !== 'dark') { preference = 'system'; }
        var isDark = preference === 'dark' || (preference === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.dataset.theme = isDark ? 'dark' : 'light';
        document.documentElement.dataset.themePreference = preference;
    })();
</script>

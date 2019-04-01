<script>
  if ('ontouchstart' in document.documentElement) {
    document.querySelector('body').classList.add('is-touch');
  }
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= option('googleAnaltics.trackingId') ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= option('googleAnaltics.trackingId') ?>', { 'anonymize_ip': true });
</script>
</html>

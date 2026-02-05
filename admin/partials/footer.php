    </div>
</main>
<script>
document.addEventListener('click', function (event) {
    var trigger = event.target.closest('[data-confirm]');
    if (!trigger) {
        return;
    }

    var message = trigger.getAttribute('data-confirm') || 'Opravdu chcete pokračovat?';
    if (!window.confirm(message)) {
        event.preventDefault();
    }
});
</script>
</body>
</html>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
// Confirm delete handlers
document.querySelectorAll('.confirm-delete').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>

</main>
</div>
</div>

<!-- Rich Text Editor Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // Initialize CKEditor for rich text areas
    document.addEventListener('DOMContentLoaded', function () {
        const editors = document.querySelectorAll('.rich-text-editor');
        editors.forEach(editor => {
            ClassicEditor
                .create(editor)
                .catch(error => {
                    console.error(error);
                });
        });
    });

    // Confirm delete actions
    function confirmDelete(message) {
        return confirm(message || 'Bạn có chắc chắn muốn xóa?');
    }
</script>
</body>

</html>
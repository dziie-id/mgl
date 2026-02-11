</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script>
    const getStoredTheme = () => localStorage.getItem('theme')
    const setStoredTheme = theme => localStorage.setItem('theme', theme)

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()
        if (storedTheme) {
            return storedTheme
        }
        return 'dark'
    }

    const setTheme = theme => {
        document.documentElement.setAttribute('data-bs-theme', theme)
    }

    setTheme(getPreferredTheme())

    document.querySelectorAll('.theme-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme')
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark'
            setTheme(newTheme)
            setStoredTheme(newTheme)
        })
    })

    Fancybox.bind("[data-fancybox]", {});
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
<script>
    function getSwalTheme() {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        return {
            bg: isDark ? '#1e1e1e' : '#fff',
            text: isDark ? '#fff' : '#000'
        };
    }

    <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $_SESSION['success'] ?>',
            timer: 2000,
            showConfirmButton: false,
            background: getSwalTheme().bg,
            color: getSwalTheme().text
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Waduh!',
            text: '<?= $_SESSION['error'] ?>',
            background: getSwalTheme().bg,
            color: getSwalTheme().text
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-hapus')) {
            e.preventDefault();
            const link = e.target.closest('.btn-hapus').getAttribute('href');

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data dan file fisik akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: getSwalTheme().bg,
                color: getSwalTheme().text
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        }
    });
</script>
</body>

</html>
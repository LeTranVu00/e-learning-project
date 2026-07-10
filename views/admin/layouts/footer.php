        </div> <!-- end of flex-1 content wrapper -->
    </main>

    <script>
        // Khởi tạo thư viện dùng chung cho admin
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 50 });
        }

        function showToast(message, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            // Thêm hiệu ứng trượt mượt mà bằng cubic-bezier
            toast.className = `px-6 py-4 rounded-xl shadow-xl font-medium text-sm flex items-center gap-3 transform transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] translate-x-full opacity-0 z-50 pointer-events-auto ${type === 'success' ? 'bg-white text-green-600 border-l-4 border-green-500' : 'bg-white text-red-600 border-l-4 border-red-500'}`;
            toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'} text-lg"></i> ${message}`;
            container.appendChild(toast);

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                });
            });

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        <?php if (isset($_SESSION['success'])): ?>
            showToast(<?= json_encode($_SESSION['success']) ?>, 'success');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            showToast(<?= json_encode($_SESSION['error']) ?>, 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>

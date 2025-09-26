        </div>
    </div>

    <script>
        // Admin JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm delete actions
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if(!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });

            // Image preview for file inputs
            const imageInputs = document.querySelectorAll('input[type="file"]');
            imageInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const previewId = this.getAttribute('data-preview');
                    if(previewId && this.files && this.files[0]) {
                        const reader = new FileReader();
                        const preview = document.getElementById(previewId);
                        
                        reader.onload = function(e) {
                            if(preview.tagName === 'IMG') {
                                preview.src = e.target.result;
                            } else {
                                preview.style.backgroundImage = `url(${e.target.result})`;
                            }
                            preview.style.display = 'block';
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            });

            // Form validation
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let valid = true;
                    
                    requiredFields.forEach(field => {
                        if(!field.value.trim()) {
                            valid = false;
                            field.style.borderColor = '#e74c3c';
                        } else {
                            field.style.borderColor = '#ddd';
                        }
                    });
                    
                    if(!valid) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                    }
                });
            });

            // Toggle password visibility
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const passwordInput = this.previousElementSibling;
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            });
        });

        // Function to preview image before upload
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if(file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = 'none';
            }
        }

        // Function to confirm actions
        function confirmAction(message) {
            return confirm(message || 'Are you sure you want to perform this action?');
        }

        // Function to show loading state
        function setLoadingState(button, isLoading) {
            if(isLoading) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            } else {
                button.disabled = false;
                button.innerHTML = button.getAttribute('data-original-text');
            }
        }
    </script>
</body>
</html>
// ==================== FORMULAIRE DE CONNEXION ====================
const loginForm = document.getElementById('loginForm');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const btnContent = document.querySelector('#submitBtn .btn-content');
    const btnLoader = document.querySelector('#submitBtn .btn-loader');

    btnContent.style.opacity = '0';
    btnLoader.style.display = 'block';

    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.success) {
            showMiniToast(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            btnContent.style.opacity = '1';
            btnLoader.style.display = 'none';
            showMiniToast(data.message, 'error');
        }
    } catch (error) {
        btnContent.style.opacity = '1';
        btnLoader.style.display = 'none';
        showMiniToast('Une erreur s\'est produite', 'error');
    }
});

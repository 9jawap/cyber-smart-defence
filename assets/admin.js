document.addEventListener('DOMContentLoaded', () => {
    console.log('Cyber Smart Defence admin loaded');

    // -------------------------------
    // One-Click Install & Activate
    // -------------------------------
    const installBtn = document.getElementById('csd_install_btn');
    if (installBtn) {
        installBtn.addEventListener('click', () => {
            const loader = document.getElementById('csd_loader');
            const bar = document.getElementById('csd_bar');
            loader.style.display = 'block';
            let progress = 0;

            const interval = setInterval(() => {
                progress = Math.min(progress + 10, 90);
                bar.style.width = progress + '%';
            }, 300);

            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=csd_fetch_security&nonce=' + csd_vars.nonce
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(interval);
                bar.style.width = '100%';
                if (data.success) {
                    window.open(data.data.installer_url, '_blank');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + (data.data || 'Unknown error'));
                    loader.style.display = 'none';
                    bar.style.width = '0%';
                }
            });
        });
    }

    // -------------------------------
    // Toggle Switches
    // -------------------------------
    const toggles = document.querySelectorAll('.csd-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            toggle.classList.toggle('on');
            const key = toggle.dataset.option;
            const value = toggle.classList.contains('on') ? 1 : 0;

            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=csd_toggle_option&option=${key}&value=${value}&nonce=${csd_vars.nonce}`
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) alert('Failed to update option');
            });
        });
    });

    // -------------------------------
    // Threat Logs Filtering (optional)
    // -------------------------------
    const filterInput = document.getElementById('csd-threat-filter');
    if (filterInput) {
        filterInput.addEventListener('input', () => {
            const val = filterInput.value.toLowerCase();
            const rows = document.querySelectorAll('.csd-table tbody tr');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    }
});

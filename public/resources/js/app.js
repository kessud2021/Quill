document.addEventListener('DOMContentLoaded', function () {
    console.log('Framework app loaded');

    initializeHoverEffects();
    initializeFormValidation();
});

function initializeHoverEffects() {
    const features = document.querySelectorAll('.feature');

    features.forEach(feature => {
        feature.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-4px)';
        });

        feature.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });
}

function initializeFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const csrfToken = this.querySelector('input[name="_token"]');

            if (!csrfToken) {
                console.warn('CSRF token not found');
            }
        });
    });
}

window.makeRequest = async function (method, url, data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('Request failed:', error);
        throw error;
    }
};

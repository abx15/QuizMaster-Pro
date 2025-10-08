document.addEventListener('DOMContentLoaded', function() {
    // Add click animation to options
    const options = document.querySelectorAll('.option-card');
    options.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            options.forEach(opt => opt.classList.remove('selected'));
            // Add selected class to clicked option
            this.classList.add('selected');
        });
    });

    // Auto-focus on first option if none selected
    const selectedOption = document.querySelector('.option-card.selected');
    if (!selectedOption && options.length > 0) {
        options[0].querySelector('input[type="radio"]').focus();
    }
});
// Function to toggle notification dropdown menus
function openNotimenu(element) {
    const notContent = element.nextElementSibling;
    
    // Close all other open notification menus
    document.querySelectorAll('.not-content').forEach(menu => {
        if (menu !== notContent) {
            menu.style.display = 'none';
        }
    });
    
    // Toggle current menu
    if (notContent.style.display === 'block') {
        notContent.style.display = 'none';
    } else {
        notContent.style.display = 'block';
    }
}

// Initialize dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize custom dropdowns
    initDropdowns();
    
    // Add active class to current tab
    const currentTab = new URLSearchParams(window.location.search).get('tab') || 'all';
    const tabButtons = document.querySelectorAll('#tabs a');
    
    tabButtons.forEach(button => {
        const tabType = button.getAttribute('href').split('=')[1]?.split('&')[0];
        if (tabType === currentTab) {
            button.classList.add('active');
        }
    });
});

// Function to initialize dropdown functionality
function initDropdowns() {
    document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
        dropdown.addEventListener('click', function() {
            const dropdownContent = this.nextElementSibling;
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-content').forEach(content => {
                if (content !== dropdownContent) {
                    content.style.display = 'none';
                }
            });
            
            // Toggle current dropdown
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });
    });
}

// Function to select option from dropdown
function selectOption(option) {
    const dropdownToggle = option.closest('.custom-dropdown').querySelector('.dropdown-toggle span');
    dropdownToggle.textContent = option.textContent;
    
    // Close dropdown after selection
    option.closest('.dropdown-content').style.display = 'none';
    
    // Handle filter changes
    const filterType = dropdownToggle.closest('.custom-dropdown').querySelector('.dropdown-toggle').textContent.trim().toLowerCase();
    const filterValue = option.textContent.trim().toLowerCase();
    
    // Build URL with selected filter
    const params = new URLSearchParams(window.location.search);
    
    if (filterType.includes('unread') || filterType.includes('read')) {
        params.set('status', filterValue);
    } else if (filterType.includes('date')) {
        if (filterValue === 'today') {
            params.set('date', 'today');
        } else if (filterValue === 'last 7 days') {
            params.set('date', 'week');
        } else if (filterValue === 'last 28 days') {
            params.set('date', 'month');
        } else if (filterValue === 'custom date') {
            params.set('date', 'custom');
        } else {
            params.delete('date');
        }
    }
    
    // Redirect with new params
    window.location.href = window.location.pathname + '?' + params.toString();
}

// Close notification menus when clicking outside
document.addEventListener('click', function(event) {
    // If click is not on an action button or inside a notification menu
    if (!event.target.matches('img[onclick="openNotimenu(this)"]') && !event.target.closest('.not-content')) {
        // Close all notification menus
        document.querySelectorAll('.not-content').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});
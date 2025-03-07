document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active classes from all tabs and contents
            tabs.forEach(t => {
                t.classList.remove('text-[#007F7F]', 'border-b-2', 'border-[#007F7F]');
                t.classList.add('text-gray-600');
            });
            contents.forEach(c => c.classList.add('hidden'));

            // Add active classes to clicked tab
            tab.classList.remove('text-gray-600');
            tab.classList.add('text-[#007F7F]', 'border-b-2', 'border-[#007F7F]');

            // Show corresponding content
            const contentId = tab.getAttribute('data-tab');
            const content = document.getElementById(contentId);
            content.classList.remove('hidden');
        });
    });
});

// Function to set active state when clicked
function setActive(element) {
  // This function now only saves the clicked link to localStorage
  // We don't need to modify classes here because the page will reload
  if (element.getAttribute('href')) {
    localStorage.setItem('clickedLink', element.getAttribute('href'));
  }
  
  // Allow the normal navigation to happen
  return true;
}

// Function to set the active navigation link based on current URL
function setActiveFromURL() {
  // Get current page filename (e.g., "overview.php", "products.php")
  const currentPage = window.location.pathname.split('/').pop();
  
  // Find all navigation links
  const navLinks = document.querySelectorAll(".sidenav .nav-link");
  
  // Loop through each link and check if its href matches the current page
  navLinks.forEach(link => {
    const linkPage = link.getAttribute('href').split('/').pop();
    
    // If the link matches the current page, set it as active
    if (linkPage === currentPage) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
  
  // Clear the localStorage item to prevent any issues
  localStorage.removeItem('clickedLink');
}

// Run when the page loads
document.addEventListener('DOMContentLoaded', setActiveFromURL);
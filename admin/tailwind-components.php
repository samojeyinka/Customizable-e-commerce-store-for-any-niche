<?php
/*
 * GLOREFY – shared styles for the admin panel.
 * Rendered as a <style type="text/tailwindcss"> so the Tailwind v4 browser CDN
 * compiles it with the rest of the page utilities. No external .css files.
 */
?>
<style type="text/tailwindcss">
@layer base {
    body {
        @apply overflow-x-hidden bg-[#FAFAFA];
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: gray;
        border-radius: 2rem;
    }
}

@layer components {
    /* ===== Carousel (admin home) ===== */
    .carousel-item.first,
    .carousel-item.second,
    .carousel-item.third {
        @apply w-full h-full bg-center bg-cover;
    }
    .carousel-item.first {
        background-image: url('https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1600&q=80');
    }
    .carousel-item.second {
        background-image: url('https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=1600&q=80');
    }
    .carousel-item.third {
        background-image: url('https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=1600&q=80');
    }
    .carousel-indicators .active {
        @apply w-[25px] h-[5px] rounded-[8px] bg-[#C2185B];
    }
    .carousel-indicators .dot {
        @apply w-[14px] h-[14px] rounded-full;
    }

    /* ===== Modal (admin overlay) ===== */
    .modal {
        @apply hidden fixed left-0 top-0 w-full h-full overflow-auto bg-black/40 pb-[5%];
        z-index: 100;
    }
    .modal.reg,
    .modal.cwyali,
    .modal.createCategory,
    .modal.verify-modal,
    .modal.cps,
    .modal.ticket,
    .modal.enabletwoFa {
        @apply pt-[10%];
    }
    .modal.change-pass,
    .modal.customeroverview {
        @apply pt-[5%];
    }
    .modal-content {
        @apply relative bg-white m-auto rounded-[16px] w-[90%] shadow-[0_4px_8px_rgba(0,0,0,0.2),0_6px_20px_rgba(0,0,0,0.19)] max-h-[85vh] overflow-y-auto;
        animation: animatetop 0.4s;
    }
    .modal.verify-modal .modal-content,
    .modal.change-pass .modal-content,
    .modal.cps .modal-content,
    .modal.cwyali .modal-content,
    .modal.ticket .modal-content {
        @apply w-[90%];
    }
    @media (max-width: 756px) {
        .modal-content {
            @apply w-[92%];
        }
        .modal.verify-modal .modal-content,
        .modal.change-pass .modal-content,
        .modal.cps .modal-content,
        .modal.cwyali .modal-content,
        .modal.ticket .modal-content {
            @apply w-[92%];
        }
    }

    .close {
        @apply text-white float-right text-[28px] font-bold;
    }
    .close:hover,
    .close:focus {
        @apply text-black no-underline cursor-pointer;
    }
    .modal-header {
        @apply p-[2px_16px] bg-[#5cb85c] text-white;
    }
    .modal-body {
        @apply p-[2px_16px];
    }
    .modal-footer {
        @apply p-[2px_16px] bg-[#5cb85c] text-white;
    }

    /* ===== Dropdown ===== */
    .custom-dropdown {
        @apply inline-flex items-center cursor-pointer relative;
    }
    .arrow-down {
        @apply ml-[5px] text-[14px] text-gray-500 transition-transform duration-300;
    }
    .dropdown-content {
        @apply hidden absolute w-fit whitespace-nowrap bg-white z-[10] border border-[#E1E1E1] rounded-[4px] shadow-[0_4px_6px_rgba(0,0,0,0.1)] p-[8px_10px];
        top: 40px;
        left: 0;
    }
    .dropdown-content div {
        @apply p-[5px_10px] cursor-pointer;
    }
    .open .arrow-down {
        @apply rotate-180;
    }
    .open .dropdown-content {
        @apply block;
    }

    /* ===== Dashboard layout ===== */
    .notification-content {
        @apply hidden absolute top-full right-0 w-[min(92vw,380px)] z-[50] mt-3;
        max-height: calc(100vh - 80px);
    }
    .shownotification {
        @apply block;
    }
    @media (max-width: 768px) {
        .notification-content {
            @apply fixed w-auto;
            top: 64px;
            left: 0.75rem;
            right: 0.75rem;
        }
    }

    .sidenav {
        @apply fixed bottom-0 left-0 w-[200px] bg-white overflow-x-hidden pt-[10px] border-r border-[#F0F0F0];
        height: calc(100vh - 64px);
        transition: width 0.5s ease;
    }
    .sidenav .nav-link {
        @apply flex items-center gap-3 px-3 py-2 rounded-lg no-underline text-[#4B5563] font-medium text-[14px];
        transition: background 0.2s ease, color 0.2s ease;
    }
    .sidenav .nav-link i {
        @apply w-5 text-center text-[#9CA3AF] text-[15px] leading-none;
        transition: color 0.2s ease;
    }
    .sidenav .nav-link:hover {
        @apply bg-gray-50 text-[#111827];
    }
    .sidenav .nav-link:hover i {
        @apply text-[#C2185B];
    }
    .sidenav .nav-link.active {
        @apply bg-[#FDECF2] text-[#C2185B] font-semibold;
    }
    .sidenav .nav-link.active i {
        @apply text-[#C2185B];
    }
    .sidenav .nav-link.nav-logout span,
    .sidenav .nav-link.nav-logout i {
        @apply text-[#D93939];
    }
    .sidenav .nav-link.nav-logout:hover {
        @apply bg-red-50;
    }
    .sidenav .closebtn {
        @apply absolute top-0 right-[25px] text-[36px] ml-[50px];
    }
    #main {
        @apply pt-16;
        height: 100%;
        width: calc(100vw - 200px);
        margin-left: 200px;
        transition: margin-left 0.5s;
    }
    @media (max-width: 768px) {
        #main {
            @apply w-full ml-0;
        }
    }
    @media (max-height: 450px) {
        .sidenav {
            @apply pt-[15px];
        }
        .sidenav a {
            font-size: 18px;
        }
    }

    .ordermenu-content {
        @apply hidden absolute min-w-[160px] min-h-[10rem] z-[5] bg-white border border-[#E1E1E1] rounded-[4px] p-[8px] shadow-[0_4px_6px_rgba(0,0,0,0.1)];
        top: 70%;
        right: 20px;
    }
    @media (max-width: 768px) {
        .ordermenu-content {
            top: 100%;
            right: 0%;
        }
    }
    .ordermenu-content.showom {
        @apply block;
        animation: fadeIn 0.2s ease-out;
    }

    .not-content {
        @apply hidden absolute min-w-[160px] h-[5rem] z-[5] bg-white border border-[#E1E1E1] rounded-[4px] p-[8px] shadow-[0_4px_6px_rgba(0,0,0,0.1)];
        top: 100%;
        right: 0;
    }
    @media (max-width: 768px) {
        .not-content {
            right: -50px;
        }
    }
    .not-content.active {
        @apply block;
        animation: fadeIn 0.2s ease-out;
    }
    .not-content a:hover {
        @apply opacity-80;
    }

    /* ===== Progress ring ===== */
    .progress-container {
        @apply relative flex items-center justify-center m-auto;
        width: 200px;
        height: 200px;
    }
    .progress-circle {
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    .progress-background {
        fill: none;
        stroke: #f0f0f0;
        stroke-width: 15;
    }
    .progress-arc {
        fill: none;
        stroke: #C2185B;
        stroke-width: 15;
        stroke-linecap: round;
    }
    .content {
        @apply absolute flex flex-col items-center justify-center;
    }
    .bucket-icon {
        @apply w-[24px] h-[24px] mb-[8px];
    }
    .value {
        font-family: Arial, sans-serif;
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    /* ===== Graph ===== */
    .axis-line {
        stroke: #e0e0e0;
        stroke-width: 1;
    }
    .axis-text {
        fill: #666;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }
    .area-path {
        fill: url(#greenGradient);
        stroke: #4ade80;
        stroke-width: 2;
    }

    /* ===== Tabs (admin auth) ===== */
    .tab {
        @apply overflow-hidden bg-white;
    }
    .tab button {
        @apply cursor-pointer;
    }
    .tab button.active {
        @apply text-[#C2185B] underline;
    }
    .tabcontent {
        @apply pt-[1.5rem] hidden;
    }

    /* ===== Notification transitions ===== */
    .w-full.flex.flex-col.gap-2.rounded-\[4px\] {
        transition: background-color 0.2s ease;
    }
    #tabs a.active {
        color: #C2185B;
        border-bottom: 2px solid #C2185B;
    }
    .rounded-\[50\%\].bg-\[#C2185B\] {
        transition: background-color 0.2s ease;
    }
    .cursor-pointer:hover {
        @apply opacity-80;
    }
    .bg-\[#EEEEEE\] {
        border-left: 3px solid #C2185B;
    }

    /* ===== Admin auth pages (login message, OTP, set password) ===== */
    .login-message {
        @apply fixed top-[20px] left-1/2 -translate-x-1/2 bg-[#d4edda] text-[#155724] p-[10px_20px] rounded-[5px] border border-[#c3e6cb] max-w-[90%] text-center shadow-[0_4px_6px_rgba(0,0,0,0.1)];
        z-index: 100;
    }
    .otp-input-group {
        @apply flex justify-center gap-[10px] my-[20px];
    }
    .otp-input {
        @apply text-center font-bold border border-[#E1E1E1] rounded-[8px] bg-transparent outline-none mx-[6px];
        width: 50px;
        height: 50px;
        font-size: 24px;
    }
    .otp-input:focus {
        border-color: #C2185B;
        box-shadow: 0 0 0 2px rgba(194, 24, 91, 0.2);
    }
    .timer {
        @apply text-[14px] text-[#777] text-center my-[15px];
    }
    .timer-highlight {
        color: #C2185B;
        font-weight: 600;
    }
    @media (max-width: 480px) {
        .otp-input {
            width: 40px;
            height: 40px;
            font-size: 20px;
            margin-left: 4px;
            margin-right: 4px;
        }
    }
    .container {
        @apply bg-white rounded-[24px] shadow-[0_2px_10px_rgba(0,0,0,0.1)] p-[30px] w-full;
        max-width: 500px;
    }
    @media (max-width: 480px) {
        .container {
            @apply p-[20px];
        }
    }
    .password-container {
        @apply relative;
    }
    .password-input {
        @apply w-full p-[12px_16px] border border-[#E1E1E1] rounded-[8px] text-[16px];
        transition: all 0.3s ease;
    }
    .password-input:focus {
        border-color: #C2185B;
        box-shadow: 0 0 0 2px rgba(194, 24, 91, 0.2);
        outline: none;
    }
    .toggle-password {
        @apply absolute right-[12px] top-1/2 -translate-y-1/2 cursor-pointer text-[#777777] bg-transparent border-none p-0 flex items-center justify-center;
    }
    .requirement {
        @apply flex items-center gap-[8px] mb-[8px];
        transition: color 0.3s ease;
    }
    .requirement.valid {
        color: #28C76F;
    }
    .requirement.invalid {
        color: #EE3F3F;
    }
    .requirement-icon {
        @apply w-[16px] h-[16px] rounded-full inline-flex items-center justify-center;
    }
    .valid .requirement-icon {
        @apply bg-[#E0F8E9] text-[#28C76F];
    }
    .invalid .requirement-icon {
        @apply bg-[#FDECEC] text-[#EE3F3F];
    }

    /* ===== Legacy dashboard page (dash.php) ===== */
    .sidebar {
        @apply fixed left-0 top-0 w-[250px] bg-[#C2185B] text-white overflow-y-auto;
        height: 100vh;
    }
    .main-content {
        @apply p-[20px];
        margin-left: 250px;
    }
    .sidebar-menu {
        @apply p-0 list-none;
    }
    .sidebar-menu li {
        @apply p-[10px_20px];
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .sidebar-menu li.active {
        background-color: rgba(255, 255, 255, 0.1);
    }
    .sidebar-menu li a {
        @apply block text-white no-underline;
    }
    .stats-container {
        @apply grid gap-[20px] mb-[30px];
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    .stat-card {
        @apply bg-white rounded-[8px] p-[20px] shadow-[0_2px_5px_rgba(0,0,0,0.1)];
    }
    .stat-value {
        @apply text-[28px] font-bold my-[10px];
    }
    .admin-profile {
        @apply bg-white rounded-[8px] p-[20px] shadow-[0_2px_5px_rgba(0,0,0,0.1)] mb-[30px];
    }
    .profile-header {
        @apply flex items-center mb-[20px];
    }
    .profile-image {
        @apply w-[80px] h-[80px] rounded-full object-cover mr-[20px];
    }
    .mobile-menu-toggle {
        @apply hidden;
    }
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .main-content {
            margin-left: 0;
        }
        .mobile-menu-toggle {
            @apply fixed top-[20px] left-[20px] bg-[#C2185B] text-white border-none w-[40px] h-[40px] rounded-[5px] cursor-pointer;
            display: block;
            z-index: 1001;
        }
    }

    /* ===== Row action menus (admin tables) ===== */
    .adminusersMenu {
        @apply hidden min-w-[10rem] z-[50];
    }
    .adminordersMenu {
        @apply hidden min-w-[10rem] z-[50];
    }

    /* ===== Pagination / issue list (issues.php) ===== */
    .pagination a.active {
        @apply bg-[#C2185B] text-white;
    }
    .issue-excerpt {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===== Modal layout tweaks (dashboard) ===== */
    .modal.logout {
        @apply pt-[10%];
    }
    .categoryLists,
    .editCategory,
    .createBrand {
        @apply pt-[5%];
    }
    .createCategory .modal-content,
    .createBrand .modal-content {
        @apply w-[40%];
    }
    .categoryLists .modal-content,
    .editCategory .modal-content {
        @apply w-[50%];
    }
    @media screen and (max-width: 765px) {
        .createCategory .modal-content,
        .categoryLists .modal-content,
        .editCategory .modal-content,
        .createBrand .modal-content {
            @apply w-[90%];
        }
    }

    /* ===== Order details tabs (order-details.php) ===== */
    .tab-content {
        @apply hidden;
    }
    .tab-content.active {
        @apply block;
    }
    .tab-button.active {
        border-bottom: 3px solid #C2185B;
        color: #C2185B;
        font-weight: 600;
    }

    /* ===== Design tokens – shared admin UI ===== */
    .admin-card {
        background-color: #fff;
        border: 1px solid #f3f4f6;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }
    .admin-page-title {
        font-family: 'Onest', sans-serif;
        font-size: 22px;
        line-height: 1.2;
        font-weight: 700;
        color: #111827;
    }
    @media (min-width: 768px) {
        .admin-page-title {
            font-size: 26px;
        }
    }
    .admin-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #C2185B;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .admin-btn-primary:hover {
        background-color: #A81450;
    }
    .admin-btn-dark {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #111827;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .admin-btn-dark:hover {
        background-color: #1F2937;
    }
    .admin-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #fff;
        border: 1px solid #e5e7eb;
        color: #262626;
        font-size: 14px;
        font-weight: 500;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .admin-btn-outline:hover {
        background-color: #f9fafb;
    }
    .admin-input {
        width: 100%;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 14px;
        font-family: 'Open Sans', sans-serif;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .admin-input:focus {
        border-color: #C2185B;
        box-shadow: 0 0 0 2px rgba(194, 24, 91, 0.1);
        outline: none;
    }
    .admin-select {
        border: 1px solid #e5e7eb;
        background-color: #fff;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 14px;
        font-family: 'Open Sans', sans-serif;
        color: #262626;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .admin-select:focus {
        border-color: #C2185B;
        box-shadow: 0 0 0 2px rgba(194, 24, 91, 0.1);
        outline: none;
    }
    .admin-table th {
        text-align: left;
        padding: 0.75rem 1rem;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        white-space: nowrap;
    }
    .admin-table td {
        padding: 1rem;
        font-size: 14px;
        white-space: nowrap;
    }
    .admin-table tbody tr {
        border-bottom: 1px solid #f9fafb;
        transition: background-color 0.2s ease;
    }
    .admin-table tbody tr:hover {
        background-color: #FBF5F8;
    }
}

@keyframes animatetop {
    from {
        top: -100px;
        opacity: 0;
    }
    to {
        top: 0;
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
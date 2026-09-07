<?php
/*
 * GLOREFY – shared styles for the storefront.
 * Rendered as a <style type="text/tailwindcss"> so the Tailwind v4 browser CDN
 * compiles it with the rest of the page utilities. No external .css files.
 */
if (!defined('ABSPATH') && !function_exists('glorefy_tailwind_components_guard')) {
    function glorefy_tailwind_components_guard() { return 1; }
}
?>
<style type="text/tailwindcss">
@layer base {
    body {
        @apply overflow-x-hidden;
    }
}

@layer components {
    /* ===== Hero carousel ===== */
    .carousel {
        @apply relative w-screen overflow-hidden;
    }
    .carousel-inner {
        @apply flex w-[400%] transition-transform duration-500 ease-in-out;
    }
    .carousel-item {
        @apply flex w-screen h-[510px] items-center justify-center bg-center bg-cover;
    }
    .slide1 {
        background-image: url('https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1600&q=80');
    }
    .slide2 {
        background-image: url('https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=1600&q=80');
    }
    .slide3 {
        background-image: url('https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=1600&q=80');
    }
    .slide4 {
        background-image: url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1600&q=80');
    }

    @media screen and (max-width: 765px) {
        .carousel-item {
            @apply h-[500px];
        }
    }

    .controls {
        @apply absolute top-1/2 w-full flex justify-between -translate-y-1/2;
    }
    .controls button {
        @apply bg-black/50 text-white p-[10px] cursor-pointer hidden;
    }
    .indicators {
        @apply absolute bottom-[25px] left-1/2 -translate-x-1/2 flex items-center;
    }
    .indicators div {
        @apply w-[16px] h-[16px] bg-white rounded-full mx-[5px] cursor-pointer;
    }
    .indicators .active {
        @apply w-[61px] h-[12px] rounded-[12px];
        background-color: var(--glor-primary, #C2185B);
    }

    /* ===== Navigation / sidemenu ===== */
    .sidemenu-content {
        @apply hidden absolute top-[70px] right-[10px] min-w-[160px] z-[1];
    }
    .dropdown-menu {
        @apply hidden fixed top-[125px] right-0 w-full h-full z-[100];
    }
    .sidemenu-content .menulink {
        @apply p-[10px_16px] no-underline cursor-pointer;
    }
    .showdd,
    .showmm,
    .showom {
        @apply block;
    }

    /* ===== Dropdowns ===== */
    .custom-dropdown {
        @apply inline-flex items-center cursor-pointer relative;
    }
    .arrow-down {
        @apply ml-[5px] text-[14px] text-gray-500 transition-transform duration-300;
    }
    .dropdown-content {
        @apply hidden absolute w-fit whitespace-nowrap bg-white z-[10] border border-[#E1E1E1] rounded-[4px] shadow-[0_4px_6px_rgba(0,0,0,0.1)] p-[10px];
        top: 40px;
        left: 0;
    }
    .dropdown-content div {
        @apply p-[10px] cursor-pointer;
    }
    .dropdown-content a {
        @apply block py-[4px];
        transition: color 0.2s ease;
    }
    .dropdown-content a:hover {
        color: var(--glor-primary, #C2185B);
    }
    .mobileOnlySearchResults {
        z-index: 100;
    }
    .open .arrow-down {
        @apply rotate-180;
    }
    .open .dropdown-content {
        @apply block;
    }
    .custom-dropdown.active .arrow-down {
        @apply rotate-180;
    }
    .custom-dropdown.active .dropdown-content {
        @apply block;
    }

    .ordermenu-content {
        @apply hidden absolute min-w-[160px] min-h-[10rem] z-[5] bg-white border border-[#E1E1E1] rounded-[4px] p-[8px] shadow-[0_4px_6px_rgba(0,0,0,0.1)];
        top: 70%;
        right: -50%;
    }

    .adminordersMenu {
        @apply absolute min-w-[10rem] min-h-[10rem] h-full z-[2];
        left: -10rem;
    }

    /* ===== Filter options (mobile) ===== */
    .filteroptions-content {
        @apply bg-white border border-[#E1E1E1] rounded-[8px] p-[15px];
        display: none;
        position: absolute;
        top: 240px;
        right: 10px;
        width: 70%;
    }
    .filteroptions-content.fixed-filter {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
    }
    .showfm {
        @apply block;
    }

    .filter-dropdown {
        @apply relative cursor-pointer;
    }
    .filter-menu {
        @apply absolute top-full left-0 z-[50] h-auto bg-white rounded-[6px] p-[8px_12px] min-w-[12rem] max-h-[300px] overflow-y-auto shadow-md;
        display: none;
    }
    .filter-dropdown.active .filter-menu {
        @apply block;
    }
    .filter-dropdown.active .arrow-down {
        @apply rotate-180;
    }
    .filter-menu a {
        @apply block py-[4px] transition-all duration-200;
    }
    .filter-menu a:hover {
        color: var(--glor-primary, #C2185B);
    }
    .has-filter .filter-toggle {
        border-color: var(--glor-primary, #C2185B);
    }

    /* ===== Modals ===== */
    .modal {
        @apply hidden fixed left-0 top-0 w-full h-full overflow-auto bg-black/40 pt-[8rem];
        z-index: 1000;
    }
    .modal.reg {
        @apply pt-[2%];
    }
    .modal.reg .modal-content {
        @apply w-[92%] md:w-[45%];
    }
    .modal.verify,
    .modal.regsuccess .ps {
        @apply pt-[10%];
    }
    .modal-content {
        @apply relative bg-white m-auto rounded-[16px] w-[92%] md:w-[60%] max-h-[80vh] overflow-y-auto shadow-[0_4px_8px_rgba(0,0,0,0.2),0_6px_20px_rgba(0,0,0,0.19)];
        animation: animatetop 0.4s;
    }
    .close {
        @apply text-[#aaa] float-right text-[28px] font-bold;
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

    /* ===== Payment modal (checkout) ===== */
    .payment {
        @apply hidden fixed left-0 top-0 w-full h-full overflow-auto bg-black/40;
        z-index: 1;
    }
    .payment-content {
        @apply relative bg-white rounded-[16px] w-[90%] sm:w-[70%] lg:w-[60%] shadow-[0_4px_8px_rgba(0,0,0,0.2),0_6px_20px_rgba(0,0,0,0.19)];
        margin: 15% auto;
        animation: animatetop 0.4s;
    }
    .payment-content.pss {
        margin: 5% auto !important;
    }

    /* ===== Tabs ===== */
    .tab {
        @apply overflow-hidden bg-white;
    }
    .tab button {
        @apply cursor-pointer;
    }
    .tab button.active {
        @apply underline;
        color: var(--glor-primary, #C2185B);
    }
    .tabcontent {
        @apply pt-[1.5rem] hidden;
    }

    /* ===== FAQ / accordion ===== */
    .faqext,
    .menufaqext {
        @apply max-h-0 overflow-hidden;
        transition: max-height 0.5s ease;
    }
    .faqext p {
        @apply text-left p-[10px] pl-[2%] pr-[2%];
    }
    .accordion:after,
    .menu-accordion:after {
        content: '\2039';
        font-size: 2.4rem;
        color: #9A9A9A;
        font-weight: 100;
        transform: rotate(-90deg);
    }

    /* ===== Product cards ===== */
    .actionstab {
        @apply hidden;
    }
    .productbox:hover .actionstab {
        @apply block;
    }

    /* ===== Reviews slider ===== */
    .reviews-container {
        @apply w-full max-w-full overflow-x-auto whitespace-nowrap scroll-smooth p-[10px_0] cursor-grab;
    }
    .reviews-wrapper {
        @apply inline-flex gap-[15px] p-[10px];
    }
    .review {
        @apply text-wrap select-none;
        min-width: 388px;
        max-width: 388px;
    }
    .reviews-container::-webkit-scrollbar {
        display: none;
    }

    .reviews-slider-container {
        @apply w-full relative;
    }
    .reviews-slider {
        @apply flex w-fit;
    }
    .reviews-slide {
        @apply flex;
    }
    .animate-scroll-right {
        animation: scroll-right 30s linear infinite;
    }
    .animate-scroll-left {
        animation: scroll-left 20s linear infinite;
    }
    .reviews-slider:hover {
        animation-play-state: paused;
    }
    @media (max-width: 768px) {
        .review {
            min-width: 260px;
        }
    }

    /* ===== Checkout sticky summary ===== */
    .mobile-order-summary {
        @apply relative w-full bg-white;
    }
    @media (max-width: 768px) {
        .mobile-order-summary {
            @apply fixed w-full z-[10] shadow-md;
            transition: top 0.3s ease-in-out;
        }
        #paysuccess {
            z-index: 40 !important;
        }
    }

    /* ===== Order tracking ===== */
    .status-icon {
        @apply transition-colors duration-300;
    }
    .status-line {
        @apply absolute left-[20px] top-[40px] w-[2px] bg-[#E1E1E1] z-0;
        height: calc(100% - 40px);
    }
    .status-line-active {
        background-color: var(--glor-primary, #C2185B);
    }
    .status-cancelled .status-icon {
        @apply bg-[#E1E1E1];
    }
    .status-cancelled .status-content h4,
    .status-cancelled .status-content p {
        color: #8F8F8F;
    }

    /* ===== Alerts ===== */
    .alert {
        @apply p-[10px_15px] rounded-[4px] mb-[15px];
    }
    .alert-success {
        @apply bg-[#d4edda] text-[#155724] border border-[#c3e6cb];
    }
    .alert-danger {
        @apply bg-[#f8d7da] text-[#721c24] border border-[#f5c6cb];
    }

    /* ===== Star rating (write review) ===== */
    .star-rating {
        @apply flex flex-row-reverse justify-end;
    }
    .star-rating input {
        @apply hidden;
    }
    .star-rating label {
        cursor: pointer;
        width: 36px;
        height: 36px;
        background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>');
        background-size: 36px;
        background-position: center;
        background-repeat: no-repeat;
        filter: grayscale(100%);
        opacity: 0.5;
        transition: all 0.2s ease;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        filter: grayscale(0);
        opacity: 1;
        color: #FFD700;
        fill: #FFD700;
        background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="%23FFD700" stroke="%23FFD700" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>');
    }
    .rating-display {
        @apply flex items-center;
    }
    .rating-display .star {
        @apply w-[20px] h-[20px] mr-[2px];
    }

    /* ===== Details pages hero banner ===== */
    .details-sec {
        @apply w-full h-[200px] bg-center bg-no-repeat bg-cover;
    }
    .details-sec.aboutus {
        background-image: url('https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1600&q=80');
    }
    .details-sec.refund {
        background-image: url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1600&q=80');
    }
    .details-sec.contactus {
        background-image: url('https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=1600&q=80');
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

@keyframes scroll-right {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

@keyframes scroll-left {
    0% {
        transform: translateX(-50%);
    }
    100% {
        transform: translateX(0);
    }
}
</style>
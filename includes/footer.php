<?php
// Newsletter subscription: handled only when the form is posted to the current page.
$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !empty($_POST['email'])) {
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Please enter a valid email address";
    } elseif (function_exists('pdo_db')) {
        try {
            $pdo = pdo_db();
            $pdo->exec("CREATE TABLE IF NOT EXISTS subscriptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('active', 'unsubscribed') DEFAULT 'active'
            )");

            $check = $pdo->prepare("SELECT id FROM subscriptions WHERE email = :email");
            $check->execute([':email' => $email]);

            if ($check->fetchColumn()) {
                $response['success'] = true;
                $response['message'] = "You are already subscribed!";
            } else {
                $insert = $pdo->prepare("INSERT INTO subscriptions (email) VALUES (:email)");
                $insert->execute([':email' => $email]);
                $response['success'] = true;
                $response['message'] = "Thank you for subscribing!";
            }
        } catch (PDOException $e) {
            error_log("Subscription error: " . $e->getMessage());
            $response['message'] = "Something went wrong. Please try again later.";
        }
    } else {
        $response['message'] = "Subscription is temporarily unavailable.";
    }

    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (!defined('DOMAIN')) {
    define('DOMAIN', '');
}
?>
<!-- ========================  The Footer section starts ======================== -->
<?php
$contactFooter = store('contact');
$socialFooter = store('social');
$newsTitle = store('news_title', 'Get on the List');
$newsText = store('news_text', '');
$storeName = store_escape(store('store_name', 'GLOREFY'));
$footLinks = [
    ['About Us', DOMAIN . '/details/about-us.php'],
    ['All Products', DOMAIN . '/products/index.php'],
    ['Return Policy', DOMAIN . '/details/refund-and-return-policy.php'],
    ['Contact Us', DOMAIN . '/details/contact-us.php'],
];
$contactItems = [
    ['fa-location-dot', $contactFooter['address'] ?? ''],
    ['fa-phone', $contactFooter['phone'] ?? ''],
    ['fa-envelope', $contactFooter['email'] ?? ''],
];
$socials = [
    'facebook' => 'fa-facebook-f', 'instagram' => 'fa-instagram', 'whatsapp' => 'fa-whatsapp',
    'pinterest' => 'fa-pinterest-p', 'youtube' => 'fa-youtube', 'x' => 'fa-x-twitter',
];
?>
<footer class="w-full mt-14 border-t border-[#262626]/[0.05]" style="background-color: var(--glor-tint, #F5EEF2)">
    <div class="w-[90%] mx-auto max-w-[1440px] pt-14 md:pt-20 pb-10">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-10 gap-y-12">
            <!-- Brand / contact -->
            <div class="lg:col-span-5 lg:pr-10">
                <a href="<?php echo DOMAIN; ?>/index.php" class="flex items-center gap-2.5 w-fit">
                    <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo $storeName; ?>" class="w-[42px] h-[42px]" />
                    <span class="text-[<?php echo store_color('color_heading'); ?>] text-[18px] tracking-[0.3em] uppercase font-['Montserrat'] font-semibold leading-none pt-[2px]"><?php echo $storeName; ?></span>
                </a>
                <p class="text-[14px] md:text-[15px] font-['Open_Sans'] text-[#6B6B6B] leading-relaxed mt-5 max-w-[36ch]">
                    <?php echo store_escape(store('foot_blurb', '')); ?>
                </p>
                <ul class="flex flex-col gap-3.5 mt-7">
                    <?php foreach ($contactItems as [$icon, $label]): if (empty($label)) continue; ?>
                    <li class="flex items-start gap-3">
                        <span class="shrink-0 w-9 h-9 rounded-full bg-white/80 border border-[#262626]/[0.06] flex items-center justify-center">
                            <i class="fa-solid <?php echo $icon; ?> text-[13px] text-[<?php echo store_color('color_heading'); ?>]/60 leading-none" aria-hidden="true"></i>
                        </span>
                        <span class="text-[14px] font-['Open_Sans'] text-[#6B6B6B] leading-relaxed pt-1.5"><?php echo store_escape($label); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Explore -->
            <div class="lg:col-span-3">
                <h4 class="text-[11px] tracking-[0.28em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]">Quick Links</h4>
                <ul class="flex flex-col gap-3 mt-6">
                    <?php foreach ($footLinks as [$label, $href]): ?>
                    <li>
                        <a href="<?php echo store_escape($href); ?>" class="text-[14px] md:text-[15px] font-['Open_Sans'] text-[#6B6B6B] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200"><?php echo store_escape($label); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="lg:col-span-4">
                <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-[30px] md:text-[34px] leading-[1.15] font-['Cormorant_Garamond'] font-medium"><?php echo store_escape($newsTitle); ?></h3>
                <p class="text-[#6B6B6B] text-[14px] md:text-[15px] font-['Open_Sans'] leading-relaxed mt-3"><?php echo store_escape($newsText); ?></p>
                <div id="response-message" class="text-[13px] font-['Open_Sans']"></div>
                <form id="subscription-form" class="mt-6">
                    <div class="flex items-center gap-2 bg-white rounded-full pl-5 pr-1.5 py-1.5 border border-[#262626]/[0.07] focus-within:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-300 shadow-[0_2px_14px_-8px_rgba(38,38,38,0.2)]">
                        <input type="email" placeholder="Your email address" name="email" autocomplete="email" class="flex-1 min-w-0 text-[14px] border-none outline-none placeholder:text-[#B8BBD7] bg-transparent font-['Open_Sans']" />
                        <button type="submit" class="shrink-0 px-5 py-2 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[11px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] transition-colors duration-300">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Socials + legal -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mt-14 pt-7 border-t border-[#262626]/[0.06]">
            <div class="flex flex-col items-center md:items-start gap-3">
                <span class="text-[11px] tracking-[0.28em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]">Connect with us</span>
                <div class="flex items-center gap-2.5">
                <?php foreach ($socials as $key => $icon): if (empty($socialFooter[$key])) continue; ?>
                <a href="<?php echo store_escape($socialFooter[$key]); ?>" target="_blank" aria-label="<?php echo ucfirst($key === 'x' ? 'X' : $key); ?>" class="w-9 h-9 rounded-full bg-white/70 border border-[#262626]/[0.06] flex items-center justify-center text-[#262626]/60 hover:text-white hover:bg-[<?php echo store_color('color_primary'); ?>] hover:border-[<?php echo store_color('color_primary'); ?>] transition-all duration-200">
                    <i class="fa-brands <?php echo $icon; ?> text-[14px] leading-none"></i>
                </a>
                <?php endforeach; ?>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <span class="text-[13px] font-['Open_Sans'] text-[#6B6B6B] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors cursor-pointer">Terms &amp; Conditions</span>
                <span class="text-[13px] font-['Open_Sans'] text-[#6B6B6B] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors cursor-pointer">Privacy Policy</span>
            </div>
        </div>
        <p class="text-center text-[12px] md:text-[13px] font-['Open_Sans'] text-[#8A8A8A] mt-8 tracking-[0.04em]">© <?php echo date('Y'); ?> <?php echo store_escape(store('copyright_name', $storeName)); ?> &middot; All Rights Reserved</p>
    </div>

    <?php $waNumber = store('whatsapp_number', '08004567339'); if (!empty($waNumber)): ?>
    <a href="<?php echo store_escape('https://wa.me/' . preg_replace('/\D+/', '', (string) $waNumber)); ?>" target="_blank" class="fixed top-[55%] md:top-[70%] right-5 md:right-10 z-40" aria-label="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp text-[48px] md:text-[58px] text-[#25D366] drop-shadow-lg hover:scale-105 transition-transform"></i>
    </a>
    <?php endif; ?>
</footer>
<!-- ========================  The Footer section ends ======================== -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('subscription-form');
        const responseMessage = document.getElementById('response-message');

        if (!form || !responseMessage) return;

        responseMessage.style.padding = '8px';
        responseMessage.style.marginTop = '10px';
        responseMessage.style.borderRadius = '6px';

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const emailInput = form.querySelector('input[name="email"]');
            const email = emailInput ? emailInput.value.trim() : '';

            if (!email) {
                responseMessage.textContent = 'Please enter an email address';
                responseMessage.style.color = '#B00020';
                responseMessage.style.backgroundColor = '#FDE8EC';
                return;
            }

            responseMessage.textContent = 'Processing...';
            responseMessage.style.color = '#666';
            responseMessage.style.backgroundColor = '#f8f8f8';

            const formData = new FormData();
            formData.append('email', email);

            fetch(window.location.href, { method: 'POST', body: formData })
                .then(response => response.text())
                .then(text => {
                    let jsonText = text;
                    const start = text.indexOf('{');
                    const end = text.lastIndexOf('}');
                    if (start >= 0 && end > start) {
                        jsonText = text.substring(start, end + 1);
                    }
                    const data = JSON.parse(jsonText);
                    if (data.success) {
                        responseMessage.style.color = '#1B7A3D';
                        responseMessage.style.backgroundColor = '#E7F6EC';
                        form.reset();
                    } else {
                        responseMessage.style.color = '#B00020';
                        responseMessage.style.backgroundColor = '#FDE8EC';
                    }
                    responseMessage.textContent = data.message;
                })
                .catch(() => {
                    responseMessage.style.color = '#B00020';
                    responseMessage.style.backgroundColor = '#FDE8EC';
                    responseMessage.textContent = 'Network error. Please try again later.';
                });
        });
    });
</script>
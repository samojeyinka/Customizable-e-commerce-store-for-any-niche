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
?>
<footer class="w-full pt-8 mt-10 border-t-[1px] border-[<?php echo store_color('color_tint'); ?>]" style="background-color: var(--glor-tint, #F5EEF2)">
    <div class="w-[90%] flex gap-6 flex-col md:flex-row justify-between mx-auto pb-8">
        <div class="flex flex-col gap-3 md:max-w-[20rem]">
            <a href="<?php echo DOMAIN; ?>/index.php" class="flex items-center gap-2">
                <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[46px] h-[46px]" />
            </a>
            <p class="text-[14px] md:text-[15px] font-['Open Sans'] text-[#777777] leading-relaxed">
                <?php echo store_escape(store('foot_blurb', '')); ?>
            </p>
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-[22px] text-[#777777]" alt="location"></i>
                    <p class="text-[14px] font-['Open Sans'] text-[#777777]"><?php echo store_escape($contactFooter['address'] ?? ''); ?></p>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-phone text-[22px] text-[#777777]" alt="phone"></i>
                    <p class="text-[14px] font-['Open Sans'] text-[#777777]"><?php echo store_escape($contactFooter['phone'] ?? ''); ?></p>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-[22px] text-[#777777]" alt="email"></i>
                    <p class="text-[14px] font-['Open Sans'] text-[#777777]"><?php echo store_escape($contactFooter['email'] ?? ''); ?></p>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <h1 class="text-[<?php echo store_color('color_heading'); ?>] text-[18px] md:text-[20px] font-['Montserrat'] font-semibold">Quick Links</h1>
            <ul class="flex flex-col gap-3">
                <li><a href="<?php echo DOMAIN; ?>/details/about-us.php" class="text-[14px] md:text-[15px] font-['Open Sans'] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">About Us</a></li>
                <li><a href="<?php echo DOMAIN; ?>/user/orders.php" class="text-[14px] md:text-[15px] font-['Open Sans'] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">Track Your Order</a></li>
                <li><a href="<?php echo DOMAIN; ?>/details/refund-and-return-policy.php" class="text-[14px] md:text-[15px] font-['Open Sans'] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">Return Policy</a></li>
                <li><a href="<?php echo DOMAIN; ?>/details/contact-us.php" class="text-[14px] md:text-[15px] font-['Open Sans'] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">Contact Us</a></li>
            </ul>
        </div>

        <div class="flex flex-col gap-3 md:max-w-[22rem]">
            <h1 class="text-[<?php echo store_color('color_heading'); ?>] text-[18px] md:text-[20px] font-['Montserrat'] font-semibold"><?php echo store_escape($newsTitle); ?></h1>
            <p class="text-[#777777] text-[14px] md:text-[15px] font-['Open Sans']"><?php echo store_escape($newsText); ?></p>
            <div id="response-message"></div>
            <form id="subscription-form" class="flex items-center gap-2 mt-1">
                <div class="flex items-center gap-2 border-[1px] border-[<?php echo store_color('color_tint'); ?>] rounded-[6px] px-3 py-2 bg-white flex-1">
                    <input type="email" placeholder="Enter your email address" name="email" class="w-full text-[14px] border-none outline-none placeholder:text-[#B8BBD7] bg-transparent" autocomplete="email" />
                </div>
                <button type="submit" class="py-2 px-5 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[15px] font-['Open Sans'] font-medium cursor-pointer rounded-[6px] hover:bg-[<?php echo store_color('color_primary_dark'); ?>] transition-colors whitespace-nowrap">
                    Subscribe
                </button>
            </form>
        </div>
    </div>

    <div class="w-[90%] mx-auto max-w-[1440px] border-t-[1px] border-[<?php echo store_color('color_tint'); ?>] py-5">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <h1 class="text-[<?php echo store_color('color_heading'); ?>] text-[16px] md:text-[18px] font-['Montserrat'] font-semibold">Connect with us:</h1>
            <div class="flex items-center gap-6">
                <?php if (!empty($socialFooter['facebook'])): ?><a href="<?php echo store_escape($socialFooter['facebook']); ?>" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f text-[20px] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors"></i></a><?php endif; ?>
                <?php if (!empty($socialFooter['instagram'])): ?><a href="<?php echo store_escape($socialFooter['instagram']); ?>" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram text-[20px] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors"></i></a><?php endif; ?>
                <?php if (!empty($socialFooter['whatsapp'])): ?><a href="<?php echo store_escape($socialFooter['whatsapp']); ?>" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp text-[20px] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors"></i></a><?php endif; ?>
                <?php if (!empty($socialFooter['pinterest'])): ?><a href="<?php echo store_escape($socialFooter['pinterest']); ?>" target="_blank" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p text-[20px] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors"></i></a><?php endif; ?>
                <?php if (!empty($socialFooter['youtube'])): ?><a href="<?php echo store_escape($socialFooter['youtube']); ?>" target="_blank" aria-label="YouTube"><i class="fa-brands fa-youtube text-[20px] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors"></i></a><?php endif; ?>
                <?php if (!empty($socialFooter['x'])): ?><a href="<?php echo store_escape($socialFooter['x']); ?>" target="_blank" aria-label="X (Twitter)"><i class="fa-brands fa-x-twitter text-[20px] text-[#777777] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors"></i></a><?php endif; ?>
            </div>
        </div>
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 mt-5">
            <div class="flex items-center gap-6">
                <span class="text-[14px] font-['Open Sans'] text-[#777777] cursor-pointer hover:text-[<?php echo store_color('color_primary'); ?>]">Terms &amp; Conditions</span>
                <span class="text-[14px] font-['Open Sans'] text-[#777777] cursor-pointer hover:text-[<?php echo store_color('color_primary'); ?>]">Privacy Policy</span>
            </div>
            <p class="text-[14px] font-['Open Sans'] font-medium text-[#777777]">© <?php echo date('Y'); ?> <?php echo store_escape(store('copyright_name', store('store_name'))); ?> | All Rights Reserved</p>
        </div>
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
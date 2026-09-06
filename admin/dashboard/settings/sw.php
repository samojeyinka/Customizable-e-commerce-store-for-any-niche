<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Two-Factor Authentication</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <?php include '../../tailwind-components.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100 p-4">
  <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm">
    <!-- Toggle Switch -->
    <div class="flex items-center justify-between px-4 py-2">
      <span class="text-[#2C2C2C]">Enable Two-factor Authentication</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" id="twoFAToggle">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>
    
    <!-- Demo Controls (for testing) -->
    <div class="p-4 border-t">
      <button id="showToggleState" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
        Show Current Toggle State
      </button>
    </div>
  </div>

  <!-- Enable 2FA Modal -->
  <div id="enableTwoFaModal" class="modal enabletwoFa">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
      <div class="w-full">
        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between cursor-pointer close-modal">
          <span class="flex items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-left">
              <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="#262626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3 class="text-[#262626] text-center text-[20px] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
          </span>
        </div>
        
        <p class="pl-[2.5%] font-['Open Sans'] text-[18px] md:text-[22px] font-medium text-left pt-5 text-[#C2185B]">
          Enable Two Factor Authentication (2FA)
        </p>
        
        <p class="pl-[2.5%] mr-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
          Enter the 4 digit code sent to miraclegift@gmail.com to enable your 2FA
        </p>
        
        <form class="w-full mt-[1rem] flex items-center flex-col">
          <p class="pl-[2.5%] mr-auto text-left font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
            Enter Code
          </p>
          
          <div class="pl-[2.5%] w-[fit-content] flex items-center gap-3 mr-auto pt-2">
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
          </div>
          
          <span
            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#C2185B] text-white rounded-[8px] mt-10 cursor-pointer verify-btn"
          >
            Verify
          </span>
        </form>
        
        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-left">
          Resend code in <span class="text-[#C2185B] countdown">23sec</span>
        </p>
      </div>
    </div>
  </div>

  <!-- Disable 2FA Modal -->
  <div id="disableTwoFaModal" class="modal enabletwoFa">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
      <div class="w-full">
        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between cursor-pointer close-modal">
          <span class="flex items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-left">
              <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="#262626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3 class="text-[#262626] text-center text-[20px] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
          </span>
        </div>
        
        <p class="pl-[2.5%] font-['Open Sans'] text-[18px] md:text-[22px] font-medium text-left pt-5 text-[#C2185B]">
          Disable Two Factor Authentication (2FA)
        </p>
        
        <p class="pl-[2.5%] mr-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
          Enter the 4 digit code sent to miraclegift@gmail.com to disable your 2FA
        </p>
        
        <form class="w-full mt-[1rem] flex items-center flex-col">
          <p class="pl-[2.5%] mr-auto text-left font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
            Enter Code
          </p>
          
          <div class="pl-[2.5%] w-[fit-content] flex items-center gap-3 mr-auto pt-2">
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
          </div>
          
          <span
            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#C2185B] text-white rounded-[8px] mt-10 cursor-pointer verify-btn"
          >
            Verify
          </span>
        </form>
        
        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-left">
          Resend code in <span class="text-[#C2185B] countdown">23sec</span>
        </p>
      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const twoFAToggle = document.getElementById('twoFAToggle');
    const enableTwoFaModal = document.getElementById('enableTwoFaModal');
    const disableTwoFaModal = document.getElementById('disableTwoFaModal');
    const closeModalButtons = document.querySelectorAll('.close-modal');
    const showToggleStateBtn = document.getElementById('showToggleState');
    const codeInputs = document.querySelectorAll('.code-input');
    const verifyButtons = document.querySelectorAll('.verify-btn');
    const countdowns = document.querySelectorAll('.countdown');
    
    // Toggle state (local storage could be used for persistence)
    let isTwoFAEnabled = false;
    
    // Event Listeners
    twoFAToggle.addEventListener('change', handleToggleChange);
    closeModalButtons.forEach(button => {
      button.addEventListener('click', closeAllModals);
    });
    showToggleStateBtn.addEventListener('click', showToggleState);
    verifyButtons.forEach(button => {
      button.addEventListener('click', handleVerification);
    });
    
    // Code input auto-focus functionality
    codeInputs.forEach((input, index) => {
      // Auto-focus to next input after entering a digit
      input.addEventListener('input', function() {
        if (this.value.length === 1) {
          if (index < codeInputs.length - 1) {
            codeInputs[index + 1].focus();
          }
        }
      });
      
      // Handle backspace to go to previous input
      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && this.value.length === 0) {
          if (index > 0) {
            codeInputs[index - 1].focus();
          }
        }
      });
    });
    
    // Functions
    function handleToggleChange(e) {
      isTwoFAEnabled = e.target.checked;
      
      if (isTwoFAEnabled) {
        // Show Enable 2FA Modal
        enableTwoFaModal.style.display = 'block';
        startCountdown(countdowns[0]);
      } else {
        // Show Disable 2FA Modal
        disableTwoFaModal.style.display = 'block';
        startCountdown(countdowns[1]);
      }
    }
    
    function closeAllModals() {
      enableTwoFaModal.style.display = 'none';
      disableTwoFaModal.style.display = 'none';
      
      // Reset toggle if modal is closed without verification
      twoFAToggle.checked = isTwoFAEnabled;
      
      // Reset inputs
      codeInputs.forEach(input => {
        input.value = '';
      });
    }
    
    function showToggleState() {
      alert(`Two-Factor Authentication is currently ${isTwoFAEnabled ? 'enabled' : 'disabled'}`);
    }
    
    function handleVerification() {
      // Check if all code inputs are filled
      let allFilled = true;
      let code = '';
      
      codeInputs.forEach(input => {
        if (input.value.length === 0) {
          allFilled = false;
        }
        code += input.value;
      });
      
      if (allFilled) {
        // In a real app, you would validate the code with the server
        alert(`Verification code submitted: ${code}`);
        
        // Update the 2FA state and close modal
        isTwoFAEnabled = twoFAToggle.checked;
        closeAllModals();
      } else {
        alert('Please enter the complete 4-digit code');
      }
    }
    
    function startCountdown(element) {
      let seconds = 23;
      element.textContent = `${seconds}sec`;
      
      const timer = setInterval(() => {
        seconds--;
        element.textContent = `${seconds}sec`;
        
        if (seconds <= 0) {
          clearInterval(timer);
          element.textContent = 'Resend';
          element.parentElement.innerHTML = 'Code expired. <span class="text-[#C2185B] cursor-pointer">Resend code</span>';
        }
      }, 1000);
    }
    
 
  </script>
</body>
</html>
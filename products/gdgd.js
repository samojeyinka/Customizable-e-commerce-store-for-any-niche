    
    function processOrder(paymentRef, transactionId) {
        // Create comprehensive FormData
        const formData = new FormData();
        
        // Payment details
        formData.append('payment_reference', paymentRef);
        formData.append('transaction_id', transactionId);
        
        // Collect delivery method
        const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked').value;
        formData.append('delivery_method', deliveryMethod);
        
        // Basic order details
        formData.append('email', document.getElementById('email').value);
        formData.append('note', document.getElementById('note').value || '');
        
        // Pickup or delivery specifics
        if (deliveryMethod === 'pickup') {
            formData.append('pickup_location', 
                document.querySelector('input[name="pickup_location"]:checked').value
            );
        } else {
            // Shipping fields collection
            const shippingFields = [
                'country', 'first_name', 'last_name', 'phone', 
                'address', 'state', 'city', 'zip_code'
            ];
            
            shippingFields.forEach(field => {
                const element = document.getElementById(field);
                formData.append(field, element ? element.value : '');
            });
            
            // Billing details handling
            const billingCheckbox = document.getElementById('billing_same');
            formData.append('billing_same', billingCheckbox && billingCheckbox.checked ? '1' : '0');
            
            // If billing is different
            if (!billingCheckbox || !billingCheckbox.checked) {
                const billingFields = [
                    'billing_country', 'billing_first_name', 'billing_last_name', 
                    'billing_phone', 'billing_address', 'billing_state', 
                    'billing_city', 'billing_zip_code'
                ];
                
                billingFields.forEach(field => {
                    const element = document.getElementById(field);
                    formData.append(field, element ? element.value : '');
                });
            }
        }
        
        // Enhanced fetch with comprehensive error handling
        fetch('process-order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Log response details for debugging
            console.log('Response Status:', response.status);
            
            // Try to parse response as JSON
            return response.json().then(data => {
                if (!response.ok) {
                    // Throw error with message from server
                    throw new Error(data.message || 'Order processing failed');
                }
                return data;
            });
        })
        .then(data => {
            console.log('Order processed successfully:', data);
            
            // Show success modal
            const successModal = document.getElementById('paysuccess');
            if (successModal) {
                successModal.style.display = 'block';
            }
            
            // Redirect to order success page
            setTimeout(() => {
                window.location.href = 'order-success.php?ref=' + paymentRef;
            }, 3000);
        })
        .catch(error => {
            console.error('Order Processing Error:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            
            // Reset all button texts in case of error
            resetButtonTexts();
            
            // User-friendly error notification
            alert('Order Processing Failed: ' + error.message);
        });
    }
    
    // Function to update all pay buttons when total changes
    function updatePayButtons(total) {
        const formattedTotal = total.toLocaleString();
        const buttonText = `Pay Now ₦${formattedTotal}`;
        
        payButtons.forEach(button => {
            button.innerHTML = buttonText;
        });
    }
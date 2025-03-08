
// The payment

document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');
    
    payButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Validate form fields first
        const requiredFields = [
            { id: 'email', name: 'Email Address' },
            { id: 'first_name', name: 'First Name' },
            { id: 'last_name', name: 'Last Name' }
        ];
        
        // Add delivery fields if express delivery is selected
        if (document.querySelector('input[name="delivery_method"]:checked').value === 'express') {
            requiredFields.push(
                { id: 'address', name: 'Address' },
                { id: 'state', name: 'State' },
                { id: 'city', name: 'City' },
                { id: 'phone', name: 'Phone Number' }
            );
        }
        
        let isValid = true;
        for (const field of requiredFields) {
            const fieldElement = document.getElementById(field.id);
            if (fieldElement && !fieldElement.value.trim()) {
                alert(`${field.name} is required`);
                fieldElement.focus();
                isValid = false;
                break;
            }
        }
        
        if (!isValid) return;
        
        // Form is valid, proceed with payment
        const email = document.getElementById('email-address').value;
        const amount = document.getElementById('amount').value;
        const firstName = document.getElementById('first-name').value;
        const lastName = document.getElementById('last-name').value;
        const orderRef = document.getElementById('order-ref').value;
        
        let handler = PaystackPop.setup({
            key: 'pk_test_ed99e88c9f3e1caf961089161641b23813a8fc41', // Replace with your public key
            email: email,
            amount: amount * 100, // Convert to kobo
            currency: "NGN",
            ref: orderRef,
            metadata: {
                custom_fields: [
                    {
                        display_name: "First Name",
                        variable_name: "first_name",
                        value: firstName
                    },
                    {
                        display_name: "Last Name",
                        variable_name: "last_name",
                        value: lastName
                    }
                ]
            },
            onClose: function() {
                // Handle when user closes the payment modal
                console.log('Payment window closed');
            },
            callback: function(response) {
                // Payment was successful
                console.log('Payment complete! Reference: ' + response.reference);
                
                // Create a form with all checkout data plus payment reference
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process-order.php'; // Create this file to handle the order
                
                // Add all form fields
                const formData = new FormData(document.querySelector('form'));
                for (const [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
                
                // Add payment reference
                const paymentRef = document.createElement('input');
                paymentRef.type = 'hidden';
                paymentRef.name = 'payment_reference';
                paymentRef.value = response.reference;
                form.appendChild(paymentRef);
                
                // Add transaction ID
                const transactionId = document.createElement('input');
                transactionId.type = 'hidden';
                transactionId.name = 'transaction_id';
                transactionId.value = response.transaction;
                form.appendChild(transactionId);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        });
        
        handler.openIframe();
    });
});


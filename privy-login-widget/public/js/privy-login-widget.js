(function($) {
    'use strict';

    // Initialize Privy when the document is ready
    $(document).ready(function() {
        // Check if Privy configuration is available
        if (typeof privyConfig === 'undefined' || !privyConfig.appId) {
            console.error('Privy configuration is missing. Please check your WordPress settings.');
            return;
        }

        // Initialize Privy
        const privy = new window.Privy({
            appId: privyConfig.appId,
            config: {
                loginMethods: privyConfig.loginMethods || ['email', 'wallet'],
                appearance: {
                    theme: 'dark',
                    accentColor: '#0f62fe',
                },
            },
        });

        // Handle login button click
        $('#privy-login-button').on('click', function() {
            privy.login();
        });

        // Handle authentication state changes
        privy.on('auth', (user) => {
            if (user) {
                // User is authenticated
                $('#privy-login-button').text('Logout');
                // You can add additional UI updates here
            } else {
                // User is logged out
                $('#privy-login-button').text('Login with Privy');
                // You can add additional UI updates here
            }
        });

        // Handle logout
        $('#privy-login-button').on('click', function() {
            if (privy.isAuthenticated()) {
                privy.logout();
            }
        });
    });
})(jQuery); 
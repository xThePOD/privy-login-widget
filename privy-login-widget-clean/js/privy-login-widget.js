(function() {
    // Inject required CSS
    const style = document.createElement('style');
    style.textContent = `
        .privy-login-widget-container {
            text-align: center;
            padding: 1rem;
        }

        .privy-login-widget-button {
            display: inline-block;
            background: #0f62fe;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(15, 98, 254, 0.2);
            text-decoration: none;
        }

        .privy-login-widget-button:hover {
            background: #0051e6;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(15, 98, 254, 0.3);
        }

        .privy-login-widget-button:active {
            transform: translateY(0);
        }

        @media (prefers-color-scheme: dark) {
            .privy-login-widget-button {
                background: #0f62fe;
                color: white;
            }

            .privy-login-widget-button:hover {
                background: #0051e6;
            }
        }

        @media screen and (max-width: 600px) {
            .privy-login-widget-button {
                padding: 12px 24px;
                font-size: 1rem;
            }
        }
    `;
    document.head.appendChild(style);

    class PrivyLoginWidget {
        constructor(config) {
            if (!config || !config.appId) {
                console.error('Privy App ID is required');
                return;
            }

            this.appId = config.appId;
            this.containerId = config.containerId || 'privy-login-widget';
            this.buttonText = config.buttonText || 'Login with Privy';
            this.theme = config.theme || 'dark';
            this.loginMethods = config.loginMethods || ['email', 'wallet'];
            this.onAuthChange = config.onAuthChange || null;
            
            this.initialize();
        }

        async initialize() {
            try {
                // Create container if it doesn't exist
                if (!document.getElementById(this.containerId)) {
                    const container = document.createElement('div');
                    container.id = this.containerId;
                    document.body.appendChild(container);
                }

                // Load Privy SDK
                await this.loadPrivySDK();
                
                // Initialize Privy
                this.privy = new window.Privy({
                    appId: this.appId,
                    config: {
                        loginMethods: this.loginMethods,
                        appearance: {
                            theme: this.theme,
                            accentColor: '#0f62fe',
                        },
                    },
                });

                this.render();
                this.setupEventListeners();
            } catch (error) {
                console.error('Failed to initialize Privy Login Widget:', error);
            }
        }

        async loadPrivySDK() {
            if (window.Privy) return;

            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://privy.io/sdk.js';
                script.async = true;
                script.onload = resolve;
                script.onerror = (error) => reject(new Error('Failed to load Privy SDK: ' + error));
                document.head.appendChild(script);
            });
        }

        render() {
            const container = document.getElementById(this.containerId);
            if (!container) return;

            container.className = 'privy-login-widget-container';
            container.innerHTML = `
                <button class="privy-login-widget-button" id="privy-login-button">
                    ${this.buttonText}
                </button>
            `;
        }

        setupEventListeners() {
            const button = document.getElementById('privy-login-button');
            if (!button) return;
            
            // Store reference for cleanup
            this.clickHandler = async () => {
                try {
                    if (this.privy.isAuthenticated()) {
                        await this.privy.logout();
                    } else {
                        await this.privy.login();
                    }
                } catch (error) {
                    console.error('Privy authentication error:', error);
                }
            };

            button.addEventListener('click', this.clickHandler);

            // Store reference for cleanup
            this.authHandler = (user) => {
                const button = document.getElementById('privy-login-button');
                if (!button) return;

                button.textContent = user ? 'Logout' : this.buttonText;
                
                // Call onAuthChange callback if provided
                if (this.onAuthChange) {
                    this.onAuthChange(user, !!user);
                }

                // Dispatch custom event
                const event = new CustomEvent('privyAuthChange', { 
                    detail: { user, authenticated: !!user } 
                });
                window.dispatchEvent(event);
            };

            this.privy.on('auth', this.authHandler);
        }

        cleanup() {
            const button = document.getElementById('privy-login-button');
            if (button && this.clickHandler) {
                button.removeEventListener('click', this.clickHandler);
            }
            if (this.privy && this.authHandler) {
                this.privy.off('auth', this.authHandler);
            }
        }

        // Public methods
        getPrivy() {
            return this.privy;
        }

        isAuthenticated() {
            return this.privy ? this.privy.isAuthenticated() : false;
        }

        getUser() {
            return this.privy ? this.privy.getUser() : null;
        }
    }

    // Export to window
    window.PrivyLoginWidget = PrivyLoginWidget;
})(); 
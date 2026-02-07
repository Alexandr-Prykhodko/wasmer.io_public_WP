<?php
/**
 * Plugin Name: Awelytics Custom Login
 * Description: Replaces the default WordPress login page with a high-end, elegant glassmorphism design.
 * Version: 1.1.0
 * Author: Awelytics Development
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Modern_Glass_Login {

    public function __construct() {
        add_action( 'login_enqueue_scripts', array( $this, 'load_assets' ) );
        add_action( 'login_head', array( $this, 'custom_login_css' ) );
        // Inject our HTML at the very start of the body
        add_action( 'login_header', array( $this, 'render_custom_ui' ) );
        
        add_filter( 'login_headerurl', array( $this, 'custom_login_url' ) );
        add_filter( 'login_headertext', array( $this, 'custom_login_title' ) );
    }

    public function custom_login_url() {
        return home_url();
    }

    public function custom_login_title() {
        return get_bloginfo( 'name' );
    }

    public function load_assets() {
        // Deregister standard WP login styles to prevent conflicts/overrides
        wp_dequeue_style( 'login' );
        wp_deregister_style( 'login' );
        wp_dequeue_style( 'buttons' );
        wp_deregister_style( 'buttons' );
        wp_dequeue_style( 'dashicons' );
        wp_deregister_style( 'dashicons' );

        // We use Tailwind via CDN for this standalone example
        wp_register_script( 'tailwindcss', 'https://cdn.tailwindcss.com', array(), null, false );
        wp_enqueue_script( 'tailwindcss' );
    }

    public function custom_login_css() {
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: { sans: ['Inter', 'sans-serif'] },
                        animation: {
                            'fade-in': 'fadeIn 0.5s ease-out',
                            'slide-up': 'slideUp 0.5s ease-out',
                            'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        },
                        keyframes: {
                            fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                            slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } }
                        }
                    },
                },
            }
        </script>
        <style>
            /* Reset body to ensure Tailwind controls the background */
            body { 
                margin: 0 !important; 
                padding: 0 !important;
                background: #0a0a0a !important; 
                font-family: 'Inter', sans-serif !important; 
                height: 100vh;
                width: 100vw;
                overflow-x: hidden;
            }

            /* Hide the standard WordPress login form container visually, 
               but keep it in DOM for JS interactions if needed. 
               We position it off-screen to ensure it doesn't affect layout. */
            #login {
                position: fixed !important;
                left: -9999px !important;
                top: -9999px !important;
                opacity: 0 !important;
                pointer-events: none !important;
                z-index: -1 !important;
                width: 1px !important;
                height: 1px !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Override WebKit autofill to match the dark theme */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus, 
            input:-webkit-autofill:active {
                -webkit-box-shadow: 0 0 0 30px #1e1e1e inset !important;
                -webkit-text-fill-color: white !important;
                transition: background-color 5000s ease-in-out 0s;
            }
        </style>
        <?php
    }

    public function render_custom_ui() {
        // Output the Background Component HTML
        ?>
        <div class="fixed inset-0 z-0 w-full h-full overflow-hidden bg-[#0a0a0a]">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-[#0f172a] to-black opacity-90"></div>
            <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full bg-blue-600/20 blur-[100px] animate-pulse-slow"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-indigo-600/10 blur-[120px] animate-pulse-slow" style="animation-delay: 2s"></div>
            <div class="absolute top-[40%] left-[50%] transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] rounded-full bg-violet-900/10 blur-[150px]"></div>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150 mix-blend-overlay pointer-events-none"></div>
        </div>
        
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative z-10 w-full max-w-md animate-slide-up">
                <!-- Glass Card -->
                <div class="relative overflow-hidden bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.5)] p-8 md:p-10">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-500/50 to-transparent opacity-50"></div>
                    
                    <!-- Header -->
                    <div class="flex flex-col items-center mb-8 space-y-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 p-[1px] shadow-lg shadow-blue-900/40">
                            <div class="w-full h-full rounded-2xl bg-[#0a0a0a] flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white fill-current" viewBox="0 0 24 24">
                                    <path d="M12.0006 1.15723C13.6702 1.15723 15.3149 1.53588 16.8123 2.26189C18.1566 2.91355 19.3499 3.84074 20.3168 4.97805C22.6105 7.67499 23.4682 11.4518 22.5029 14.8631C21.9961 16.6543 21.0336 18.2524 19.7408 19.5444C17.6738 21.6106 14.9318 22.7937 12.0006 22.8428H11.8906C9.17296 22.7758 6.61109 21.6882 4.63959 19.8214C3.25055 18.5061 2.21528 16.8529 1.63672 15.029C0.605476 11.7779 1.35336 8.16914 3.52841 5.61174C4.54226 4.41984 5.79532 3.44754 7.2023 2.76678C8.68337 2.05031 10.3179 1.6703 11.9796 1.65775L12.0006 1.15723ZM12.0006 1.65775C10.4284 1.65775 8.88246 2.01633 7.4784 2.69532L17.5118 19.5332C18.6672 18.2831 19.5273 16.8242 19.9926 15.213C20.4239 13.7196 20.4851 12.1554 20.1706 10.6429L16.6713 20.1738H16.1423L13.7844 13.1952L11.8596 18.8828L12.6074 21.1685C14.7077 21.1215 16.6806 20.4729 18.3618 19.3496L8.43407 2.68449C9.57688 2.01526 10.8717 1.65775 12.0006 1.65775ZM2.70992 13.8826C3.06456 15.4262 3.82912 16.8407 4.90823 18.0069L10.8687 3.39088C9.55403 4.14811 8.44185 5.22855 7.64368 6.52972L7.6967 6.68779L10.9577 16.4265L8.74921 9.87979L4.85221 20.4139C3.39958 18.6572 2.53696 16.3986 2.50294 14.0487L2.70992 13.8826ZM15.0119 9.53862L12.8715 3.19882C12.5855 3.16781 12.2964 3.1533 12.0051 3.1533C11.666 3.1533 11.3308 3.17031 11.0007 3.20882L13.5744 10.8541L15.0119 9.53862Z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-center">
                            <h1 class="text-2xl font-bold text-white tracking-tight">Admin Console</h1>
                            <p class="text-sm text-gray-400 mt-1">Sign in to your account</p>
                        </div>
                    </div>
                    
                    <!-- We inject the WP Form Logic here by moving the original form elements via JS or styling them -->
                    <div id="modern-login-form-container"></div>

                </div>

                <div class="mt-8 text-center animate-fade-in" style="animation-delay: 0.2s">
                    <p class="text-xs text-gray-500 font-medium">
                        &copy; <?php echo date("Y"); ?> Awelytics Development. <span class="hidden md:inline"> • </span>
                        <a href="#" class="hover:text-gray-400 transition-colors">Privacy</a>
                    </p>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get original form
                const wpForm = document.querySelector('#loginform');
                const container = document.getElementById('modern-login-form-container');
                
                if (!wpForm || !container) return;

                // Create wrapper for Email/Username
                const emailWrapper = createInputWrapper('user_login', 'text', 'Username or Email', 'admin');
                const pwdWrapper = createInputWrapper('user_pass', 'password', 'Password', '••••••••', true);

                // Move original inputs to hidden or replace them
                const originalUser = document.getElementById('user_login');
                const originalPass = document.getElementById('user_pass');
                const originalRemember = document.getElementById('rememberme');
                
                // Helper to create modern markup
                function createInputWrapper(id, type, labelText, placeholder, isPwd = false) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'w-full space-y-1.5 group mb-4';
                    
                    const label = document.createElement('label');
                    label.className = 'text-xs font-medium tracking-wide text-gray-400 transition-colors duration-200';
                    label.innerText = labelText;
                    label.setAttribute('for', 'modern_' + id);
                    
                    const relative = document.createElement('div');
                    relative.className = 'relative';

                    // Icon
                    const iconDiv = document.createElement('div');
                    iconDiv.className = 'absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 transition-colors duration-200 group-focus-within:text-blue-400';
                    if (isPwd) {
                         iconDiv.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>';
                    } else {
                         iconDiv.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>';
                    }

                    const input = document.createElement('input');
                    input.id = 'modern_' + id;
                    input.type = type;
                    input.className = 'w-full bg-[#1e1e1e]/50 border border-white/10 backdrop-blur-sm rounded-xl py-3 pl-10 pr-4 text-sm text-white placeholder-gray-600 outline-none transition-all duration-300 focus:border-blue-500/50 focus:shadow-[0_0_20px_rgba(59,130,246,0.15)] hover:border-white/20';
                    input.placeholder = placeholder;
                    
                    // Sync with original
                    input.addEventListener('input', (e) => {
                        const original = document.getElementById(id);
                        if(original) original.value = e.target.value;
                    });

                    // Initial Value
                    if (document.getElementById(id)) {
                        input.value = document.getElementById(id).value;
                    }

                    relative.appendChild(iconDiv);
                    relative.appendChild(input);
                    
                    if (isPwd) {
                        const toggleBtn = document.createElement('button');
                        toggleBtn.type = 'button';
                        toggleBtn.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors focus:outline-none';
                        toggleBtn.innerHTML = '<svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                        
                        toggleBtn.onclick = function() {
                            if (input.type === 'password') {
                                input.type = 'text';
                                toggleBtn.innerHTML = '<svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07-2.3 2.3"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                            } else {
                                input.type = 'password';
                                toggleBtn.innerHTML = '<svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                            }
                        };
                        relative.appendChild(toggleBtn);
                    }

                    wrapper.appendChild(label);
                    wrapper.appendChild(relative);
                    return wrapper;
                }

                // Append new inputs
                container.appendChild(emailWrapper);
                container.appendChild(pwdWrapper);

                // Checkbox & Forgot Password row
                const row = document.createElement('div');
                row.className = 'flex items-center justify-between pt-1 mb-6';
                row.innerHTML = `
                    <label class="flex items-center space-x-2 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" id="modern_remember" class="sr-only">
                            <div id="custom_check" class="w-4 h-4 rounded border border-gray-600 bg-transparent transition-all duration-200 group-hover:border-gray-500"></div>
                        </div>
                        <span class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors">Remember me</span>
                    </label>
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-xs text-blue-400 hover:text-blue-300 transition-colors font-medium">Forgot password?</a>
                `;
                container.appendChild(row);

                // Handle Checkbox Sync
                const modernCheck = row.querySelector('#modern_remember');
                const customCheck = row.querySelector('#custom_check');
                
                // Init state
                if (originalRemember && originalRemember.checked) {
                     modernCheck.checked = true;
                     updateCheckUI(true);
                }

                modernCheck.addEventListener('change', (e) => {
                    if (originalRemember) originalRemember.checked = e.target.checked;
                    updateCheckUI(e.target.checked);
                });
                
                function updateCheckUI(checked) {
                     if (checked) {
                        customCheck.className = 'w-4 h-4 rounded border border-blue-600 bg-blue-600 transition-all duration-200 flex items-center justify-center';
                        customCheck.innerHTML = '<svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>';
                    } else {
                        customCheck.className = 'w-4 h-4 rounded border border-gray-600 bg-transparent transition-all duration-200 group-hover:border-gray-500';
                        customCheck.innerHTML = '';
                    }
                }

                // Submit Button
                const submitBtn = document.createElement('button');
                submitBtn.type = 'button'; // We trigger form submit programmatically
                submitBtn.className = 'group relative w-full flex items-center justify-center py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform active:scale-[0.98] shadow-[0_0_20px_rgba(79,70,229,0.3)] hover:shadow-[0_0_30px_rgba(79,70,229,0.5)]';
                submitBtn.innerHTML = `
                    <span>Log In</span>
                    <svg class="ml-2 transition-transform duration-300 group-hover:translate-x-1" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                `;
                
                submitBtn.addEventListener('click', () => {
                   wpForm.submit(); 
                });
                
                // Allow Enter key to submit
                container.addEventListener('keydown', (e) => {
                   if (e.key === 'Enter') wpForm.submit(); 
                });

                container.appendChild(submitBtn);

                // Back to site link
                const backLink = document.createElement('div');
                backLink.className = 'pt-4 text-center';
                const backUrl = document.querySelector('.login #backtoblog a')?.href || '/';
                backLink.innerHTML = `
                    <a href="${backUrl}" class="text-xs text-gray-500 hover:text-white transition-colors flex items-center justify-center gap-2 group">
                        <span class="w-1 h-1 rounded-full bg-gray-600 group-hover:bg-white transition-colors"></span>
                        ← Back to website
                    </a>
                `;
                container.appendChild(backLink);
                
                // Handle Errors
                const wpError = document.querySelector('.login .message, .login .error, .login #login_error');
                if (wpError) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-200 text-xs text-center animate-fade-in';
                    errorDiv.innerHTML = wpError.innerText; 
                    container.insertBefore(errorDiv, container.firstChild);
                }
            });
        </script>
        <?php
    }
}

new Modern_Glass_Login();

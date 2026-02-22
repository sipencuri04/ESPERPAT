<!-- Ultra Modern 2026 Split Auth Login -->
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <!-- Left Side: Interactive Illustration & Branding -->
        <div class="col-lg-6 d-none d-lg-flex bg-dark position-relative overflow-hidden align-items-center justify-content-center">
            <!-- Dynamic Pattern Background -->
            <div class="position-absolute bg-primary rounded-circle blur-shadow" style="width: 400px; height: 400px; top: -10%; left: -10%; opacity: 0.2;"></div>
            <div class="position-absolute bg-purple rounded-circle blur-shadow" style="width: 300px; height: 300px; bottom: -5%; right: -5%; opacity: 0.1;"></div>
            
            <div class="position-relative z-index-1 text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-gear-wide-connected text-primary display-1 floating-animation"></i>
                </div>
                <h1 class="text-white fw-900 display-4 mb-3">ESPER<span class="text-primary border-bottom border-primary border-4">PAT</span></h1>
                <p class="text-white-50 fs-5 mb-5 px-5">Your ultra-premium destination for high-performance automotive spare parts.</p>
                
                <div class="d-flex justify-content-center gap-4 text-white opacity-50">
                    <div class="text-center">
                        <div class="fw-800 h4 mb-0">20k+</div>
                        <div class="small text-uppercase">Items</div>
                    </div>
                    <div class="border-start border-white opacity-25"></div>
                    <div class="text-center">
                        <div class="fw-800 h4 mb-0">4.9/5</div>
                        <div class="small text-uppercase">Rating</div>
                    </div>
                    <div class="border-start border-white opacity-25"></div>
                    <div class="text-center">
                        <div class="fw-800 h4 mb-0">24h</div>
                        <div class="small text-uppercase">Support</div>
                    </div>
                </div>
            </div>
            
            <!-- Modern Grid Overlay -->
            <div class="hero-grid-overlay opacity-10"></div>
        </div>

        <!-- Right Side: Glass Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5 bg-modern-soft">
            <div class="auth-card-glass p-5 rounded-xl shadow-lg border border-white position-relative overflow-hidden">
                <!-- Branding for Mobile -->
                <div class="d-lg-none text-center mb-5">
                     <h2 class="fw-900 text-dark mb-0">ESPER<span class="text-primary">PAT</span></h2>
                </div>

                <div class="mb-5">
                    <h3 class="fw-900 text-dark mb-2">Welcome Back</h3>
                    <p class="text-muted fw-500">Log in to manage your high-performance garage.</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert-modern-error mb-4">
                        <i class="bi bi-shield-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/login') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <?php if (!empty($redirect)): ?>
                        <input type="hidden" name="redirect" value="<?= esc($redirect) ?>">
                    <?php endif; ?>

                    <div class="form-group-modern mb-4">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block letter-spacing-1">Email Terminal</label>
                        <div class="input-modern-wrap">
                            <i class="bi bi-envelope-at text-muted"></i>
                            <input type="email" name="email" value="<?= old('email') ?>" placeholder="name@terminal.com" required>
                        </div>
                    </div>

                    <div class="form-group-modern mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="small fw-800 text-muted text-uppercase mb-0 letter-spacing-1">Access Pin</label>
                            <a href="#" class="small text-primary text-decoration-none fw-800">Forgot Access?</a>
                        </div>
                        <div class="input-modern-wrap">
                            <i class="bi bi-shield-lock text-muted"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-ultra w-100 py-3 fw-900 rounded-lg shadow-primary mb-4 transition-all">
                        AUTHORIZE ACCESS
                    </button>

                    <div class="text-center pt-2">
                        <span class="small text-muted fw-600">New around here?</span> 
                        <a href="<?= base_url('/register') ?>" class="small text-primary fw-900 text-decoration-none ms-1">Create Account</a>
                    </div>
                </form>

                <!-- Footer Text inside Card -->
                <div class="mt-5 text-center opacity-50 fs-x-small fw-600">
                    SENSITIVE DATA PROTECTION ENABLED <br>
                    &copy; <?= date('Y') ?> ESPERPAT TERMINAL V3.0
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* 2026 Auth Page Specific Styles */
.bg-modern-soft { background: radial-gradient(at center bottom, #f0f2f5, #ffffff); }
.auth-card-glass { max-width: 480px; width: 100%; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border: 1.5px solid rgba(255,255,255,1); }
.hero-grid-overlay { position: absolute; inset: 0; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M0 0h1v1H0z" fill="white"/></svg>'); }
.blur-shadow { filter: blur(80px); }
.bg-purple { background: #764ba2; }
.z-index-1 { z-index: 1; }
.fs-x-small { font-size: 0.65rem; letter-spacing: 1.5px; }

.input-modern-wrap { display: flex; align-items: center; gap: 12px; background: rgba(255,255,255,0.8); border: 1px solid #eee; padding: 12px 16px; border-radius: 12px; transition: var(--transition); }
.input-modern-wrap:focus-within { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px var(--primary-glow); }
.input-modern-wrap input { border: none; background: transparent; width: 100%; outline: none; font-weight: 600; color: #000; font-size: 0.95rem; }
.input-modern-wrap input::placeholder { color: #aaa; }

.btn-primary-ultra { background: var(--primary); color: #fff; border: none; box-shadow: 0 10px 25px var(--primary-glow); opacity: 1; }
.btn-primary-ultra:hover { transform: translateY(-3px); box-shadow: 0 15px 30px var(--primary-glow); background: var(--primary-dark); }
.btn-primary-ultra:active { transform: scale(0.97); }

.alert-modern-error { background: #fff1f0; border: 1px solid #ffccc7; color: #ff4d4f; padding: 12px 16px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; transform: translateY(0); animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }

@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}

@keyframes floating-animation {
    0%, 100% { transform: translateY(0) rotate(0); }
    50% { transform: translateY(-15px) rotate(5deg); }
}
.floating-animation { display: inline-block; animation: floating-animation 4s ease-in-out infinite; }
</style>

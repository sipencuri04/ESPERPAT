<!-- Ultra Modern 2026 Split Auth Register -->
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <!-- Left Side: Interactive Branding (Shared with Login) -->
        <div class="col-lg-6 d-none d-lg-flex bg-dark position-relative overflow-hidden align-items-center justify-content-center">
            <div class="position-absolute bg-primary rounded-circle blur-shadow" style="width: 400px; height: 400px; top: -10%; left: -10%; opacity: 0.2;"></div>
            <div class="position-absolute bg-purple rounded-circle blur-shadow" style="width: 300px; height: 300px; bottom: -5%; right: -5%; opacity: 0.1;"></div>
            
            <div class="position-relative z-index-1 text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-shield-lock-fill text-primary display-1 floating-animation"></i>
                </div>
                <h1 class="text-white fw-900 display-4 mb-3">ESPER<span class="text-primary border-bottom border-primary border-4">PAT</span></h1>
                <p class="text-white-50 fs-5 mb-5 px-5">Join the network of elite mechanical engineers and performance enthusiasts.</p>
                
                <div class="glass-mini-card p-4 rounded-lg border-white border text-white text-start shadow-lg">
                    <div class="small fw-800 opacity-50 mb-2 uppercase letter-spacing-1">MEMBERSHIP PRIVILEGES</div>
                    <ul class="list-unstyled small fw-600 opacity-75 mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Priority Logistics Hub</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Industrial Tech Support</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Premium Asset Reservations</li>
                    </ul>
                </div>
            </div>
            <div class="hero-grid-overlay opacity-10"></div>
        </div>

        <!-- Right Side: Register Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5 bg-modern-soft">
            <div class="auth-card-glass p-5 rounded-xl shadow-lg border border-white">
                <div class="d-lg-none text-center mb-5">
                     <h2 class="fw-900 text-dark mb-0">ESPER<span class="text-primary">PAT</span></h2>
                </div>

                <div class="mb-5">
                    <h3 class="fw-900 text-dark mb-2">New Identity</h3>
                    <p class="text-muted fw-500">Create your terminal access credentials.</p>
                </div>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert-modern-error mb-4">
                        <ul class="mb-0 ps-3 small fw-700">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/register') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-group-modern">
                                <label class="small fw-800 text-muted text-uppercase mb-2 d-block letter-spacing-1">Full Name</label>
                                <div class="input-modern-wrap">
                                    <i class="bi bi-person text-muted"></i>
                                    <input type="text" name="name" value="<?= old('name') ?>" placeholder="John Doe" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="small fw-800 text-muted text-uppercase mb-2 d-block letter-spacing-1">Email Terminal</label>
                                <div class="input-modern-wrap">
                                    <i class="bi bi-envelope-at text-muted"></i>
                                    <input type="email" name="email" value="<?= old('email') ?>" placeholder="name@terminal.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="small fw-800 text-muted text-uppercase mb-2 d-block letter-spacing-1">WhatsApp Hub</label>
                                <div class="input-modern-wrap">
                                    <i class="bi bi-whatsapp text-muted"></i>
                                    <input type="text" name="phone" value="<?= old('phone') ?>" placeholder="08xxxxxxxx">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-modern">
                                <label class="small fw-800 text-muted text-uppercase mb-2 d-block letter-spacing-1">Access Pin</label>
                                <div class="input-modern-wrap">
                                    <i class="bi bi-shield-lock text-muted"></i>
                                    <input type="password" name="password" placeholder="Min 6 characters" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check small mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required checked>
                                <label class="form-check-label text-muted fw-600" for="terms">
                                    I authorize the <a href="#" class="text-primary text-decoration-none fw-800">Operational Protocols</a>.
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary-ultra w-100 py-3 fw-900 rounded-lg shadow-primary mb-4 transition-all">
                                INITIALIZE REGISTRATION
                            </button>

                            <div class="text-center">
                                <span class="small text-muted fw-600">Active member?</span> 
                                <a href="<?= base_url('/login') ?>" class="small text-primary fw-900 text-decoration-none ms-1">Authorize Access</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* 2026 Auth Page Specific Styles (Shared) */
.bg-modern-soft { background: radial-gradient(at center bottom, #f0f2f5, #ffffff); }
.auth-card-glass { max-width: 600px; width: 100%; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border: 1.5px solid rgba(255,255,255,1); }
.hero-grid-overlay { position: absolute; inset: 0; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M0 0h1v1H0z" fill="white"/></svg>'); }
.glass-mini-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); }
.blur-shadow { filter: blur(80px); }
.bg-purple { background: #764ba2; }

.input-modern-wrap { display: flex; align-items: center; gap: 12px; background: rgba(255,255,255,0.8); border: 1px solid #eee; padding: 12px 16px; border-radius: 12px; transition: var(--transition); }
.input-modern-wrap:focus-within { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px var(--primary-glow); }
.input-modern-wrap input { border: none; background: transparent; width: 100%; outline: none; font-weight: 600; color: #000; font-size: 0.95rem; }

.alert-modern-error { background: #fff1f0; border: 1px solid #ffccc7; color: #ff4d4f; padding: 12px 16px; border-radius: 12px; }

@keyframes floating-animation {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}
.floating-animation { display: inline-block; animation: floating-animation 4s ease-in-out infinite; }
</style>

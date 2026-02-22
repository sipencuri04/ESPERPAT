<?php
// Profile Edit - DROPS 2026 Aesthetic Refined
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5 pt-2 px-1">
        <button class="btn-icon-circle" onclick="history.back()"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0">Edit Profile</h6>
        <button class="btn-icon-circle" onclick="toggleSystemMenu(true)"><i class="bi bi-list"></i></button>
    </div>

    <!-- Avatar Section -->
    <div class="text-center mb-5">
        <div class="position-relative d-inline-block">
            <div class="profile-avatar-drops mx-auto mb-3">
                <img src="https://i.pravatar.cc/150?u=rex" alt="Rex">
            </div>
            <button class="btn-avatar-edit"><i class="bi bi-camera-fill text-white"></i></button>
        </div>
    </div>

    <!-- Profile Form -->
    <form action="<?= base_url('/customer/profile/update') ?>" method="post" class="px-1">
        <?= csrf_field() ?>

        <div class="form-group-drops mb-4">
            <label class="text-muted extra-small fw-700 uppercase mb-2 px-1">Full Name</label>
            <div class="pill-input-wrap">
                <i class="bi bi-person text-muted"></i>
                <input type="text" name="name" value="<?= esc($user['name']) ?>" required>
            </div>
        </div>

        <div class="form-group-drops mb-4">
            <label class="text-muted extra-small fw-700 uppercase mb-2 px-1">Email Address</label>
            <div class="pill-input-wrap opacity-75">
                <i class="bi bi-envelope text-muted"></i>
                <input type="email" value="<?= esc($user['email']) ?>" disabled>
            </div>
        </div>

        <div class="form-group-drops mb-4">
            <label class="text-muted extra-small fw-700 uppercase mb-2 px-1">WhatsApp Number</label>
            <div class="pill-input-wrap">
                <span class="text-muted small fw-700">+62</span>
                <input type="text" name="phone" value="<?= esc($user['phone'] ?? '') ?>" placeholder="8123xxxxxxx">
            </div>
        </div>

        <div class="form-group-drops mb-5">
            <label class="text-muted extra-small fw-700 uppercase mb-2 px-1">Shipping Address</label>
            <div class="pill-textarea-wrap">
                <textarea name="address" rows="4" placeholder="Street name, City, Postcode..."><?= esc($user['address'] ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-drops w-100 py-3 mt-2 mb-5">
            Save Changes
        </button>
    </form>
</div>

<style>
.profile-avatar-drops { width: 110px; height: 110px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
.profile-avatar-drops img { width: 100%; height: 100%; object-fit: cover; }
.btn-avatar-edit { 
    position: absolute; bottom: 10px; right: 0; 
    background: var(--primary); border: 3px solid #fff; width: 36px; height: 36px; 
    border-radius: 50%; display: flex; align-items: center; justify-content: center; 
}

.pill-input-wrap {
    background: #f8f8f8; border: 1.5px solid #f1f1f1; border-radius: 20px;
    padding: 12px 20px; display: flex; align-items: center; gap: 12px;
}
.pill-input-wrap input { border: none; background: transparent; width: 100%; font-weight: 700; color: #000; font-size: 0.95rem; outline: none; }

.pill-textarea-wrap {
    background: #f8f8f8; border: 1.5px solid #f1f1f1; border-radius: 24px;
    padding: 15px 20px;
}
.pill-textarea-wrap textarea { border: none; background: transparent; width: 100%; font-weight: 700; color: #000; font-size: 0.95rem; outline: none; resize: none; }

.btn-primary-drops { background: var(--primary); color: #fff; border: none; border-radius: 24px; font-weight: 800; font-size: 1.1rem; transition: 0.4s; }

.extra-small { font-size: 10px; }
.uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
</style>

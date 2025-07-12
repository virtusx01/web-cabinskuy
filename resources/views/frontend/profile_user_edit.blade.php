{{-- resources/views/frontend/profile_user_edit.blade.php --}}
@extends('backend.user_layout')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ $judul }}</h1>
        <p>Perbarui informasi profil dan keamanan akun Anda</p>
    </div>
</div>

<div class="container" style="margin-bottom: 40px;">
    <div class="edit-profile-container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Error!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="edit-profile-card">
            <form action="{{ route('profile.user.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf
                
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-camera"></i> Foto Profil</h3>
                        <p>Unggah foto profil untuk personalisasi akun Anda</p>
                    </div>
                    
                    <div class="photo-upload-section">
                        <div class="current-photo">
                            {{-- Logic to display current profile photo --}}
                            @php
                                $photoUrl = asset('backend/images/default-avatar.png'); // Default fallback
                                if ($user->profile_photo_path) {
                                    if (Str::startsWith($user->profile_photo_path, 'http')) {
                                        $photoUrl = $user->profile_photo_path; // Google or external URL
                                    } else {
                                        $photoUrl = asset('storage/' . $user->profile_photo_path); // Local storage
                                    }
                                } elseif ($user->google_avatar_url) {
                                    $photoUrl = $user->google_avatar_url; // Use Google avatar if no custom one
                                }
                            @endphp
                            <img src="{{ $photoUrl }}" 
                                alt="Current Profile Photo" 
                                class="photo-preview" 
                                id="photoPreview">
                        </div>
                        
                        <div class="photo-upload-controls">
                            <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload" onclick="document.getElementById('profilePhotoInput').click()">
                                <i class="fas fa-upload"></i>
                                Pilih Foto Baru
                            </button>
                            <button type="button" class="btn-remove" id="removePhotoBtn">
                                <i class="fas fa-trash"></i>
                                Hapus Foto
                            </button>
                            <input type="hidden" name="remove_photo" id="removePhotoHiddenInput" value="0"> {{-- Hidden input to signal photo removal --}}
                            <p class="upload-note">Format: JPG, PNG, maksimal 2MB</p>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-user"></i> Informasi Personal</h3>
                        <p>Kelola informasi dasar akun Anda</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user"></i>
                                Nama Lengkap
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                            <span class="form-helper">Nama yang akan ditampilkan di profil Anda</span>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                Alamat Email
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                            <span class="form-helper">Email untuk login dan notifikasi</span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-shield-alt"></i> Keamanan Akun</h3>
                        <p>Ubah kata sandi untuk menjaga keamanan akun</p>
                    </div>
                    
                    <div class="security-notice">
                        <div class="notice-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="notice-content">
                            <strong>Catatan Keamanan:</strong>
                            {{-- Conditional message for password --}}
                            @if ($user->password === null)
                                <p>Anda belum memiliki kata sandi lokal. Anda dapat membuat kata sandi baru di sini.</p>
                            @else
                                <p>Kosongkan bagian kata sandi jika tidak ingin mengubahnya. Untuk mengubah kata sandi, masukkan kata sandi saat ini terlebih dahulu.</p>
                            @endif
                        </div>
                    </div>

                    <div class="form-grid">
                        {{-- Show "Current Password" only if the user has a password set --}}
                        @if ($user->password !== null)
                        <div class="form-group password-group">
                            <label for="current_password">
                                <i class="fas fa-lock"></i>
                                Kata Sandi Saat Ini
                            </label>
                            <div class="password-input">
                                <input type="password" name="current_password" id="current_password">
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            <span class="form-helper">Diperlukan hanya jika ingin mengubah kata sandi</span>
                        </div>
                        @endif

                        <div class="form-group password-group">
                            <label for="password">
                                <i class="fas fa-key"></i>
                                @if ($user->password === null)
                                    Buat Kata Sandi Baru
                                @else
                                    Kata Sandi Baru
                                @endif
                            </label>
                            <div class="password-input">
                                <input type="password" name="password" id="password">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password_icon"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>

                        <div class="form-group password-group">
                            <label for="password_confirmation">
                                <i class="fas fa-check-double"></i>
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <div class="password-input">
                                <input type="password" name="password_confirmation" id="password_confirmation">
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                            <span class="form-helper">Ulangi kata sandi baru untuk konfirmasi</span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('profile.user.show') }}" class="btn-secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>

.page-header{
    text-align: center;
    padding-block: 30px;
}


.edit-profile-container {
    max-width: 800px;
    margin: 0 auto;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert ul {
    margin: 5px 0 0 0;
    padding-left: 20px;
}

.edit-profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    overflow: hidden;
}

.form-section {
    padding: 30px;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
}

.section-header {
    margin-bottom: 25px;
}

.section-header h3 {
    color: #223324;
    font-size: 1.3em;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header p {
    color: #666;
    margin: 0;
    font-size: 0.95em;
}

.photo-upload-section {
    display: flex;
    align-items: center;
    gap: 30px;
}

.current-photo {
    flex-shrink: 0;
}

.photo-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.photo-preview:hover {
    border-color: #229954; /* Highlight on hover */
}

.photo-upload-controls {
    flex: 1;
}

.btn-upload, .btn-remove {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9em;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-right: 10px;
    margin-bottom: 10px;
}

.btn-upload {
    background: #229954;
    color: white;
}

.btn-upload:hover {
    background: #1c7d43;
    transform: translateY(-1px);
}

.btn-remove {
    background: #dc3545;
    color: white;
}

.btn-remove:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.upload-note {
    color: #666;
    font-size: 0.85em;
    margin: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.95em;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    padding: 12px 15px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    font-size: 1em;
    color: #333;
    transition: border-color 0.3s, box-shadow 0.3s;
    width: 100%;
    box-sizing: border-box; /* Include padding and border in the element's total width and height */
}

.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus,
.form-group input[type="password"]:focus {
    border-color: #229954;
    box-shadow: 0 0 0 3px rgba(34, 153, 84, 0.2);
    outline: none;
}

.form-helper {
    font-size: 0.85em;
    color: #6c757d;
    margin-top: 5px;
}

.password-group .password-input {
    position: relative;
    width: 100%;
}

.password-group .password-input input {
    padding-right: 45px; /* Space for the toggle button */
}

.password-group .password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    font-size: 1.1em;
    padding: 5px;
    border-radius: 50%;
    transition: color 0.2s, background-color 0.2s;
}

.password-group .password-toggle:hover {
    color: #333;
    background-color: #f0f0f0;
}

.password-strength {
    margin-top: 10px;
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.password-strength::before {
    content: '';
    display: block;
    height: 100%;
    width: 0;
    background-color: transparent;
    transition: width 0.3s ease-in-out, background-color 0.3s ease-in-out;
}

/* Password strength indicators */
.password-strength.weak::before {
    width: 33%;
    background-color: #dc3545;
}
.password-strength.medium::before {
    width: 66%;
    background-color: #ffc107;
}
.password-strength.strong::before {
    width: 100%;
    background-color: #28a745;
}

.security-notice {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    background-color: #e6f7ff;
    border: 1px solid #91d5ff;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    color: #005080;
}

.security-notice .notice-icon {
    font-size: 1.5em;
    color: #1890ff;
    flex-shrink: 0;
}

.security-notice .notice-content strong {
    display: block;
    margin-bottom: 5px;
}

.security-notice .notice-content p {
    margin: 0;
    font-size: 0.9em;
    line-height: 1.4;
}

.form-actions {
    padding: 30px;
    background: #f8f9fa;
    display: flex;
    gap: 15px;
    justify-content: center;
    border-top: 1px solid #e9ecef;
}

.btn-primary, .btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.95em;
    cursor: pointer;
}

.btn-primary {
    background: #229954;
    color: white;
    border: 2px solid #229954;
}

.btn-primary:hover {
    background: #1c7d43;
    border-color: #1c7d43;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(34, 153, 84, 0.3);
}

.btn-secondary {
    background: white;
    color: #333;
    border: 2px solid #dee2e6;
}

.btn-secondary:hover {
    background: #f8f9fa;
    border-color: #229954;
    color: #229954;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .photo-upload-section {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    .section-header{
        text-align: center;
        align-items: center;
        justify-content: center;
        justify-items: center;
    }
    .btn-primary{
        text-align: center;
        align-items: center;
        justify-content: center
    }

    .btn-secondary{
        text-align: center;
        align-items: center;
        justify-content: center;
    }

    .photo-upload-controls {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .btn-upload, .btn-remove {
        width: 100%;
        max-width: 250px;
        justify-content: center;
        margin-right: 0;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
        align-items: center;
    }

    .btn-primary, .btn-secondary {
        width: 100%;
        max-width: 250px;
    }

    .security-notice {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profilePhotoInput = document.getElementById('profilePhotoInput');
        const photoPreview = document.getElementById('photoPreview');
        const removePhotoBtn = document.getElementById('removePhotoBtn');
        const removePhotoHiddenInput = document.getElementById('removePhotoHiddenInput'); // Get the hidden input
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('passwordStrength');

        // Initial check for current photo to enable/disable remove button
        function checkPhotoStatus() {
            const currentPhotoSrc = photoPreview.src;
            // Assuming default avatar URL contains 'default-avatar.png'
            if (currentPhotoSrc.includes('default-avatar.png')) {
                removePhotoBtn.disabled = true;
                removePhotoBtn.style.opacity = '0.5';
                removePhotoBtn.style.cursor = 'not-allowed';
            } else {
                removePhotoBtn.disabled = false;
                removePhotoBtn.style.opacity = '1';
                removePhotoBtn.style.cursor = 'pointer';
            }
        }
        checkPhotoStatus(); // Call on page load

        // Photo Preview Logic
        profilePhotoInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    removePhotoHiddenInput.value = '0'; // If a new photo is selected, don't remove
                    checkPhotoStatus(); // Update button status
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        });

        // Remove Photo Logic
        removePhotoBtn.addEventListener('click', function() {
            // Set the preview to the default avatar
            photoPreview.src = "{{ asset('backend/images/default-avatar.png') }}";
            // Clear the file input
            profilePhotoInput.value = ''; 
            // Set the hidden input to 1 to tell the backend to remove the photo
            removePhotoHiddenInput.value = '1';
            checkPhotoStatus(); // Update button status
        });

        // Password Strength Indicator (basic example)
        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            let strength = 0;

            if (password.length > 0) {
                if (password.length >= 8) {
                    strength += 1;
                }
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
                    strength += 1;
                }
                if (password.match(/\d/)) {
                    strength += 1;
                }
                if (password.match(/[^a-zA-Z0-9]/)) {
                    strength += 1;
                }
            }

            passwordStrength.className = 'password-strength'; // Reset classes
            if (strength === 0) {
                // No password entered - do nothing or set to a neutral state
            } else if (strength < 2) {
                passwordStrength.classList.add('weak');
            } else if (strength < 4) {
                passwordStrength.classList.add('medium');
            } else {
                passwordStrength.classList.add('strong');
            }
        });
    });

    // Toggle Password Visibility
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById(id + '_icon');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
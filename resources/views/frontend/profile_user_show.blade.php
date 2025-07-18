{{-- resources/views/frontend/profile_user_show.blade.php --}}
@extends('backend.user_layout')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ $judul }}</h1>
    </div>
</div>

<div class="container" style="margin-bottom: 40px;">
    <div class="profile-container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        @endif

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar-section">
                    <div class="profile-avatar-container">
                        @php
                            $photoUrl = asset('backend/images/default-avatar.png'); // Default fallback
                            if ($user->profile_photo_path) {
                                if (Str::startsWith($user->profile_photo_path, 'http')) {
                                    $photoUrl = $user->profile_photo_path; // Google or external URL
                                } else {
                                    $photoUrl = Storage::disk('s3')->url($user->profile_photo_path); // Local storage
                                }
                            } elseif ($user->google_avatar_url) {
                                $photoUrl = $user->google_avatar_url; // Use Google avatar if no custom one
                            }
                        @endphp
                        <img src="{{ $photoUrl }}"
                             alt="Profile Picture"
                             class="profile-avatar"
                             id="profileImage">
                            
                        <div class="avatar-overlay">
                            <a href="{{ route('profile.user.edit') }}"><i class="fas fa-camera" style="color: white"></i></a>
                        </div>
                    </div>
                    <div class="profile-status">
                        <div class="status-indicator"></div>
                        <span>Online</span>
                    </div>
                </div>
                <div class="profile-info-header">
                    <h2>{{ $user->name }}</h2>
                    {{-- Display the dynamic member role --}}
                    <p class="profile-role">{{ $memberRole }}</p>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ (int)$daysJoined }}</span>
                            <span class="stat-label">Hari Bergabung</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $totalSuccessBookings }}</span> {{-- Use the new variable --}}
                            <span class="stat-label">Booking Berhasil</span> {{-- Optional: changed label to be more specific --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-section">
                    <h3><i class="fas fa-user"></i> Informasi Personal</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Nama Lengkap</label>
                            <span>{{ $user->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Bergabung Sejak</label>
                            <span>{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Status Akun</label>
                            <span class="status-badge active">Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3><i class="fas fa-shield-alt"></i> Keamanan Akun</h3>
                    <div class="security-items">
                        <div class="security-item">
                            <div class="security-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="security-info">
                                <h4>Kata Sandi</h4>
                                <p>Terakhir diubah {{ $user->updated_at->diffForHumans() }}</p>
                            </div>
                            <div class="security-status">
                                <span class="status-badge secure">Aman</span>
                            </div>
                        </div>
                        <div class="security-item">
                            <div class="security-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="security-info">
                                <h4>Email Verification</h4>
                                <p>Email telah diverifikasi</p>
                            </div>
                            <div class="security-status">
                                <span class="status-badge verified">Terverifikasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <a href="{{ route('profile.user.edit') }}" class="btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Profil
                </a>
                <a href="{{ route('frontend.booking.index') }}" class="btn-secondary">
                    <i class="fas fa-calendar-alt"></i>
                    My Bookings
                </a>
            </div>
        </div>
    </div>
</div>

<style>

.page-header{
    text-align: center;
    padding-block: 30px;
}
/* Your existing CSS styles go here. No changes needed in CSS for these specific updates. */
.profile-container {
    max-width: 900px;
    margin: 0 auto;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.profile-header {
    background: linear-gradient(135deg, #229954 0%, #1c7d43 100%);
    padding: 40px 30px;
    display: flex;
    align-items: center;
    gap: 30px;
    color: white;
    position: relative;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.profile-avatar-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    position: relative;
    z-index: 1;
}

.profile-avatar-container {
    position: relative;
    cursor: pointer;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.3);
    transition: transform 0.3s ease, border-color 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
    border-color: rgba(255,255,255,0.8);
}

.avatar-overlay {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-avatar-container:hover .avatar-overlay {
    opacity: 1;
}

.profile-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9em;
}

.status-indicator {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.profile-info-header {
    flex: 1;
    position: relative;
    z-index: 1;
}

.profile-info-header h2 {
    font-size: 2.2em;
    margin: 0 0 8px 0;
    font-weight: 700;
}

.profile-role {
    font-size: 1.1em;
    opacity: 0.9;
    margin: 0 0 20px 0;
}

.profile-stats {
    display: flex;
    gap: 30px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.8em;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    font-size: 0.9em;
    opacity: 0.8;
}

.profile-details {
    padding: 40px 30px;
}

.detail-section {
    margin-bottom: 35px;
}

.detail-section:last-child {
    margin-bottom: 0;
}

.detail-section h3 {
    color: #223324;
    font-size: 1.3em;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9f5e9;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.detail-item label {
    font-size: 0.9em;
    color: #666;
    font-weight: 500;
}

.detail-item span {
    font-size: 1.1em;
    color: #333;
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.active {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.status-badge.secure {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.status-badge.verified {
    background: rgba(139, 69, 19, 0.1);
    color: #a16207;
    border: 1px solid rgba(139, 69, 19, 0.3);
}

.security-items {
    display: flex;
    flex-direction: column;
    gap: 20px;
}


.security-item {
    display: flex;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.security-icon {
    width: 50px;
    height: 50px;
    background: rgba(34, 153, 84, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #229954;
    margin-right: 15px;
}

.security-info {
    flex: 1;
}

.security-info h4 {
    margin: 0 0 5px 0;
    color: #333;
    font-size: 1.1em;
}

.security-info p {
    margin: 0;
    color: #666;
    font-size: 0.9em;
}

.profile-actions {
    padding: 30px;
    display: flex;
    gap: 15px;
    justify-content: center;
}

.profile-actions a {
    color: black;
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
    background: none !important;
}

.btn-primary:hover, .btn-secondary:hover {
    background: #f8f9fa;
    border-color: #229954;
    color: #229954;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}


@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }
    
    .profile-details {
        padding: 30px 20px;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .profile-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
        max-width: 250px;
    }
    
    .profile-stats {
        justify-content: center;
    }
}
</style>
@endsection
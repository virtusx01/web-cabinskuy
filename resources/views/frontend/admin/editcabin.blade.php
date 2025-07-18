@extends('backend.admin_layout')

@section('title', 'Edit Kabin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    :root {
        --primary-green: #008272;
        --light-green: #E6F3F2;
        --dark-text: #333;
        --light-text: #6c757d;
        --border-color: #E0E0E0;
        --background-color: #F9FAFB;
        --white: #FFFFFF;
        --danger: #e53e3e;
        --danger-light: #FEE2E2;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--background-color);
        color: var(--dark-text);
        line-height: 1.6;
    }

    .cabin-form-container {
        padding: 1.5rem 1rem; /* Adjust padding for mobile */
        max-width: 1200px;
        margin: auto;
    }

    .cabin-form-header {
        display: flex;
        flex-direction: column; /* Stack on mobile */
        justify-content: space-between;
        align-items: flex-start; /* Align items to start on mobile */
        margin-bottom: 2rem;
        gap: 1rem;
    }

    @media (min-width: 768px) { /* Adjust for tablet and desktop */
        .cabin-form-header {
            flex-direction: row; /* Row on larger screens */
            align-items: center;
        }
    }

    .cabin-form-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--primary-green);
        margin: 0;
    }
    .cabin-form-header p {
        font-size: 0.95rem; /* Slightly smaller text for description */
        margin-top: 0.25rem;
        color: var(--light-text);
    }

    .header-buttons {
        display: flex;
        gap: 0.75rem; /* Closer buttons */
        flex-wrap: wrap; /* Allow buttons to wrap on smaller screens */
    }

    .header-buttons .btn {
        text-decoration: none;
        padding: 0.6rem 1rem; /* Slightly smaller padding for buttons */
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid var(--primary-green);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem; /* Smaller font size for buttons */
    }

    .header-buttons .btn-secondary {
        background-color: var(--white);
        color: var(--primary-green);
    }
    .header-buttons .btn-secondary:hover {
        background-color: var(--light-green);
    }
    .header-buttons .btn-dashboard {
        background-color: var(--primary-green);
        color: var(--white);
    }
    .header-buttons .btn-dashboard:hover {
        opacity: 0.9;
    }

    .main-content-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    @media (min-width: 992px) {
        .main-content-grid {
            grid-template-columns: 2fr 1fr;
        }
    }
    
    .form-card {
        background-color: var(--white);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .form-card h2 {
        font-size: 1.5rem; /* Slightly larger heading for form sections */
        font-weight: 600;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-green);
        padding-left: 1rem;
        color: var(--dark-text);
    }
    .form-group {
        margin-bottom: 1.25rem; /* Slightly reduced margin for tighter spacing */
    }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--dark-text);
        font-size: 0.9rem; /* Smaller label font size */
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.9rem; /* Slightly smaller font size for inputs */
        transition: border-color 0.3s, box-shadow 0.3s;
        box-sizing: border-box; /* Include padding and border in the element's total width and height */
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px var(--light-green);
    }
    .form-control[disabled] {
        background-color: #f1f1f1;
        cursor: not-allowed;
    }
    textarea.form-control {
        min-height: 100px; /* Reduced min-height for textarea */
        resize: vertical;
    }

    .current-photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); /* Adjusted for mobile */
        gap: 0.75rem; /* Smaller gap */
    }
    .current-photo-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: all 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Push actions to bottom */
    }
    .current-photo-item.marked-for-delete {
        opacity: 0.6;
        border-color: var(--danger);
    }
    .current-photo-item img {
        display: block;
        width: 100%;
        height: 90px; /* Adjusted image height */
        object-fit: cover;
    }
    .photo-actions {
        font-size: 0.75rem; /* Smaller font size for actions */
        padding: 0.4rem; /* Smaller padding */
        background-color: #f8f9fa;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.3rem; /* Smaller gap between items */
    }
    .photo-actions label {
        display: flex;
        align-items: center;
        color: var(--danger);
        cursor: pointer;
        margin-bottom: 0; /* No margin bottom */
        font-size: 0.75rem;
    }
    .photo-actions input[type="checkbox"] {
        margin-right: 0.4rem; /* Smaller margin */
        transform: scale(0.9); /* Slightly smaller checkbox */
    }
    .set-main-btn {
        font-size: 0.75rem; /* Smaller font size */
        padding: 0.15rem 0.4rem; /* Smaller padding */
        border-radius: 4px;
        border: 1px solid var(--primary-green);
        background: transparent;
        color: var(--primary-green);
        cursor: pointer;
        display: block;
        width: 100%;
        text-align: center;
        margin-top: 0.3rem; /* Small margin top */
    }
    .set-main-btn:disabled {
        background: var(--light-green);
        cursor: not-allowed;
        opacity: 0.7;
    }

    .file-input-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 1.5rem; /* Adjusted padding */
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }
    .file-input-wrapper:hover { border-color: var(--primary-green); }
    .file-input-wrapper input[type="file"] { display: none; }
    .file-input-wrapper .file-input-label {
        color: var(--light-text);
        font-weight: 500;
        font-size: 0.9rem; /* Smaller font size */
    }
    .file-input-wrapper .file-input-label svg {
        width: 30px; height: 30px; margin-bottom: 0.5rem; color: var(--primary-green); /* Smaller icon */
    }
    .file-input-wrapper .preview-image {
        max-width: 100%;
        max-height: 120px; /* Adjusted height for preview image */
        object-fit: contain;
        display: block;
        margin: 0 auto 10px;
        border-radius: 4px;
    }

    .preview-card {
        background-color: var(--white);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }
    .preview-card h3 {
        padding: 1rem 1.5rem; margin: 0; font-size: 1.1rem; font-weight: 600; /* Smaller heading */
        background-color: var(--light-green); color: var(--primary-green);
    }
    .preview-gallery {
        background-color: #f0f0f0;
        padding: 0.75rem; /* Smaller padding */
    }
    .main-preview-image {
        width: 100%;
        height: 180px; /* Adjusted height */
        background-color: #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 0.75rem; /* Smaller margin */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .main-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-placeholder {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        width: 100%; height: 100%; color: var(--light-text); font-size: 0.85rem; /* Smaller font */
    }
    .image-placeholder svg { width: 35px; height: 35px; margin-bottom: 0.4rem; } /* Smaller icon */

    #thumbnail-preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); /* Smaller thumbnails for mobile */
        gap: 0.4rem; /* Smaller gap */
        margin-top: 0.75rem; /* Smaller margin */
    }
    .thumbnail-item {
        width: 100%;
        height: 50px; /* Smaller height */
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.3s;
    }
    .thumbnail-item:hover, .thumbnail-item.active {
        border-color: var(--primary-green);
    }
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .preview-content { padding: 1.25rem; } /* Smaller padding */
    .preview-name {
        font-size: 1.3rem; font-weight: 600; margin-top: 0; margin-bottom: 0.4rem; word-wrap: break-word; /* Smaller font */
    }
    .preview-location {
        font-weight: 500; color: var(--light-text); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.4rem; /* Smaller gap */
        font-size: 0.9rem; /* Smaller font */
    }
    .preview-description {
        font-size: 0.85rem; color: var(--light-text); margin-bottom: 1.25rem; max-height: 90px; overflow-y: auto; /* Smaller font and height */
    }
    .preview-status {
        display: inline-block; padding: 0.3rem 0.7rem; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600; text-transform: uppercase; /* Smaller font */
    }
    .status-available { background-color: var(--light-green); color: var(--primary-green); }
    .status-unavailable { background-color: var(--danger-light); color: var(--danger); }
    
    .btn-primary {
        width: 100%; background-color: var(--primary-green); color: var(--white);
        padding: 0.8rem; font-size: 1rem; font-weight: 600; border: none;
        border-radius: 8px; cursor: pointer; transition: background-color 0.3s;
        margin-top: 1.5rem; /* Added margin-top */
    }
    .btn-primary:hover { background-color: #006a5f; }
    .alert-danger {
        background-color: var(--danger-light); border-left: 4px solid var(--danger); color: #C53030;
        padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px;
        font-size: 0.9rem; /* Smaller font for alerts */
    }
    .alert-danger ul { margin: 0; padding-left: 1.2rem; }
    .alert-danger li { margin-bottom: 0.2rem; } /* Spacing for list items */
</style>
@endpush

@section('admin_content')
<div class="cabin-form-container">
    <div class="cabin-form-header">
        <div>
            <h1>Edit Kabin</h1>
            <p>Perbarui detail untuk kabin: <strong>{{ $cabin->name }}</strong></p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('admin.cabins.show', $cabin) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <a href="{{ route('admin.cabins.index') }}" class="btn btn-dashboard">
                <i class="fas fa-list-ul"></i>
                Daftar Kabin
            </a>
        </div>
    </div>

    <div class="main-content-grid">
        <div class="form-card">
            <form action="{{ route('admin.cabins.update', $cabin->id_cabin) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="photo_order" id="photo_order">
                <input type="hidden" name="province_name" id="province_name_hidden" value="{{ old('province_name', $cabin->province) }}">
                <input type="hidden" name="regency_name" id="regency_name_hidden" value="{{ old('regency_name', $cabin->regency) }}">
                <input type="hidden" name="location" id="location_hidden" value="{{ old('location', $cabin->location) }}">
                
                <h2>Informasi Kabin</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="id_cabin">ID Kabin</label>
                    <input type="text" id="id_cabin" class="form-control" value="{{ $cabin->id_cabin }}" disabled>
                    <small style="color: var(--light-text); font-size: 0.8rem;">ID Kabin tidak dapat diubah.</small>
                </div>

                <div class="form-group">
                    <label for="name">Nama Kabin</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $cabin->name) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="province_select">Provinsi</label>
                    <select name="province" id="province_select" class="form-control" required>
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="regency_select">Kabupaten/Kota</label>
                    <select name="regency" id="regency_select" class="form-control" required>
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location_address">Alamat Detail Lokasi</label>
                    <input type="text" name="location_address" id="location_address" class="form-control" value="{{ old('location_address', $cabin->location_address) }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" required>{{ old('description', $cabin->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Kelola Foto Saat Ini</label>
                    <div class="current-photos-grid" id="current-photos-container">
                        @if($cabin->cabin_photos && count($cabin->cabin_photos) > 0)
                            @foreach($cabin->cabin_photos as $photo)
                                <div class="current-photo-item" data-photo-path="{{ $photo }}">
                                    <img src="{{ Storage::disk('s3')->url($photo)" alt="Foto Kabin">
                                    <div class="photo-actions">
                                        <label>
                                            <input type="checkbox" name="delete_photos[]" value="{{ $photo }}" class="delete-photo-checkbox"> Hapus
                                        </label>
                                        <button type="button" class="set-main-btn">Jadikan Utama</button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p style="color: var(--light-text); font-size: 0.9rem;" id="no-current-photos">Tidak ada foto saat ini.</p>
                        @endif
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cabin_photos_input">Tambah Foto Baru</label>
                    <label for="cabin_photos_input" class="file-input-wrapper">
                        <img class="preview-image" style="display: none;">
                        <span class="file-input-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-cloud-arrow-up-fill">
                                <path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z"></path>
                            </svg>
                            <br>
                            Klik untuk unggah foto baru (opsional)
                        </span>
                    </label>
                    <input type="file" name="cabin_photos[]" id="cabin_photos_input" class="form-control" multiple accept="image/*">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="1" {{ old('status', $cabin->status) == 1 ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="0" {{ old('status', $cabin->status) == 0 ? 'selected' : '' }}>Tidak Tersedia (Not Available)</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Perbarui Kabin</button>
            </form>
        </div>

        <div class="preview-card">
            <h3>Live Preview</h3>
            <div class="preview-gallery">
                <div class="main-preview-image" id="main-preview-image">
                    {{-- Initial image will be set by JS --}}
                </div>
                <div id="thumbnail-preview-container">
                    {{-- Thumbnails will be generated by JS --}}
                </div>
            </div>
            <div class="preview-content">
                <h4 id="preview-name" class="preview-name"></h4>
                <p id="preview-location" class="preview-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span></span>
                </p>
                <p id="preview-description" class="preview-description"></p>
                <div id="preview-status" class="preview-status"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPhotoPaths = @json($cabin->cabin_photos ?? []);
    let newPhotoFiles = [];
    let photosToDelete = new Set();

    const elements = {
        nameInput: document.getElementById('name'),
        provinceSelect: document.getElementById('province_select'),
        regencySelect: document.getElementById('regency_select'),
        locationAddressInput: document.getElementById('location_address'),
        descriptionInput: document.getElementById('description'),
        statusSelect: document.getElementById('status'),
        photoOrderInput: document.getElementById('photo_order'),
        newPhotoInput: document.getElementById('cabin_photos_input'),
        currentPhotosContainer: document.getElementById('current-photos-container'),
        noCurrentPhotosPlaceholder: document.getElementById('no-current-photos'),
        
        provinceNameHidden: document.getElementById('province_name_hidden'),
        regencyNameHidden: document.getElementById('regency_name_hidden'),
        locationHidden: document.getElementById('location_hidden'),

        mainPreview: document.getElementById('main-preview-image'),
        thumbContainer: document.getElementById('thumbnail-preview-container'),
        previewName: document.getElementById('preview-name'),
        previewLocation: document.getElementById('preview-location').querySelector('span'),
        previewDescription: document.getElementById('preview-description'),
        previewStatus: document.getElementById('preview-status'),
        newPhotoPreviewImage: document.querySelector('.file-input-wrapper .preview-image'),
        newPhotoInputLabel: document.querySelector('.file-input-wrapper .file-input-label')
    };
    const assetRoot = "{{ rtrim(Storage::disk('s3'))) }}";

    const initialProvinceName = elements.provinceNameHidden.value;
    const initialRegencyName = elements.regencyNameHidden.value;

    function updateTextPreview() {
        elements.previewName.innerText = elements.nameInput.value || 'Nama Kabin Anda';
        
        let selectedProvinceName = elements.provinceSelect.options[elements.provinceSelect.selectedIndex]?.textContent || '';
        let selectedRegencyName = elements.regencySelect.options[elements.regencySelect.selectedIndex]?.textContent || '';

        elements.provinceNameHidden.value = selectedProvinceName;
        elements.regencyNameHidden.value = selectedRegencyName;
        
        if (selectedRegencyName && selectedProvinceName) {
            elements.previewLocation.innerText = `${selectedRegencyName}, ${selectedProvinceName}`;
            elements.locationHidden.value = `${selectedRegencyName}, ${selectedProvinceName}`;
        } else if (selectedProvinceName) {
            elements.previewLocation.innerText = selectedProvinceName;
            elements.locationHidden.value = selectedProvinceName;
        } else {
            elements.previewLocation.innerText = 'Lokasi Kabin';
            elements.locationHidden.value = '';
        }

        elements.previewDescription.innerText = elements.descriptionInput.value || 'Deskripsi kabin akan muncul di sini...';
        
        const isAvailable = elements.statusSelect.value == '1';
        elements.previewStatus.innerText = isAvailable ? 'Tersedia' : 'Tidak Tersedia';
        elements.previewStatus.className = `preview-status ${isAvailable ? 'status-available' : 'status-unavailable'}`;
    }

    function renderLivePreview() {
        elements.mainPreview.innerHTML = '';
        elements.thumbContainer.innerHTML = '';

        const existingPhotosForPreview = currentPhotoPaths
            .filter(path => !photosToDelete.has(path))
            .map(path => ({ src: `${assetRoot}/${path}`, type: 'existing' }));
            
        const newPhotosForPreview = newPhotoFiles
            .map(file => ({ src: URL.createObjectURL(file), type: 'new' }));

        const combinedPhotos = [...existingPhotosForPreview, ...newPhotosForPreview];
        
        if (combinedPhotos.length === 0) {
            elements.mainPreview.innerHTML = `<div class="image-placeholder"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/></svg><span>Tidak ada foto</span></div>`;
            return;
        }

        const setMainImage = (src) => {
            elements.mainPreview.innerHTML = `<img src="${src}" alt="Main preview">`;
        };
        
        setMainImage(combinedPhotos[0].src);

        combinedPhotos.forEach((photo, index) => {
            const thumbDiv = document.createElement('div');
            thumbDiv.className = 'thumbnail-item';
            if (index === 0) thumbDiv.classList.add('active');
            
            thumbDiv.innerHTML = `<img src="${photo.src}" alt="Thumbnail ${index + 1}">`;
            
            thumbDiv.onclick = () => {
                setMainImage(photo.src);
                elements.thumbContainer.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('active'));
                thumbDiv.classList.add('active');
            };
            elements.thumbContainer.appendChild(thumbDiv);
        });
    }
    
    function updatePhotoOrderInput() {
        const items = elements.currentPhotosContainer.querySelectorAll('.current-photo-item');
        const orderedPaths = Array.from(items)
            .filter(item => !item.classList.contains('marked-for-delete'))
            .map(item => item.dataset.photoPath);
        
        elements.photoOrderInput.value = JSON.stringify(orderedPaths);
        
        items.forEach((item, index) => {
            const btn = item.querySelector('.set-main-btn');
            if (btn) btn.disabled = (index === 0);
        });

        renderLivePreview();
    }

    function handleFileSelection(event) {
        newPhotoFiles = Array.from(event.target.files);
        if (newPhotoFiles.length > 0) {
            elements.newPhotoPreviewImage.src = URL.createObjectURL(newPhotoFiles[0]);
            elements.newPhotoPreviewImage.style.display = 'block';
            elements.newPhotoInputLabel.style.display = 'none';
        } else {
            elements.newPhotoPreviewImage.src = '';
            elements.newPhotoPreviewImage.style.display = 'none';
            elements.newPhotoInputLabel.style.display = 'block';
        }
        photosToDelete.clear();
        elements.currentPhotosContainer.querySelectorAll('.current-photo-item').forEach(item => {
            item.classList.remove('marked-for-delete');
            item.querySelector('input[type="checkbox"]').checked = false;
        });

        renderLivePreview();
    }

    function handleSetMain(button) {
        const itemToMove = button.closest('.current-photo-item');
        if (itemToMove && itemToMove !== elements.currentPhotosContainer.firstChild && !itemToMove.classList.contains('marked-for-delete')) {
            elements.currentPhotosContainer.insertBefore(itemToMove, elements.currentPhotosContainer.firstChild);
            updatePhotoOrderInput();
        }
    }
    
    function handleDeleteToggle(checkbox) {
        const photoPath = checkbox.value;
        const photoItem = checkbox.closest('.current-photo-item');
        if (checkbox.checked) {
            photosToDelete.add(photoPath);
            photoItem.classList.add('marked-for-delete');
        } else {
            photosToDelete.delete(photoPath);
            photoItem.classList.remove('marked-for-delete');
        }
        elements.newPhotoInput.value = ''; 
        newPhotoFiles = [];
        elements.newPhotoPreviewImage.src = '';
        elements.newPhotoPreviewImage.style.display = 'none';
        elements.newPhotoInputLabel.style.display = 'block';

        updatePhotoOrderInput();
    }

    elements.currentPhotosContainer.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        if (checkbox.checked) {
            photosToDelete.add(checkbox.value);
            checkbox.closest('.current-photo-item').classList.add('marked-for-delete');
        }
        checkbox.addEventListener('change', () => handleDeleteToggle(checkbox));
    });

    elements.currentPhotosContainer.querySelectorAll('.set-main-btn').forEach(button => {
        button.addEventListener('click', () => handleSetMain(button));
    });

    elements.newPhotoInput.addEventListener('change', handleFileSelection);
    
    ['name', 'description', 'status'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('keyup', updateTextPreview);
        el.addEventListener('change', updateTextPreview);
    });
    
    const API_URL_PROVINCES = "https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json";
    const API_URL_REGENCIES = "https://www.emsifa.com/api-wilayah-indonesia/api/regencies/";

    async function fetchProvinces() {
        try {
            const response = await fetch(API_URL_PROVINCES);
            const data = await response.json();
            elements.provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.id;
                option.textContent = province.name;
                elements.provinceSelect.appendChild(option);
            });
            if (initialProvinceName) {
                const existingOption = Array.from(elements.provinceSelect.options).find(opt => opt.textContent === initialProvinceName);
                if (existingOption) {
                    elements.provinceSelect.value = existingOption.value;
                    fetchRegencies(existingOption.value);
                }
            }
            updateTextPreview();
        } catch (error) {
            console.error('Error fetching provinces:', error);
            elements.provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
        }
    }

    async function fetchRegencies(provinceId) {
        elements.regencySelect.innerHTML = '<option value="">Memuat...</option>';
        elements.regencySelect.disabled = true;
        try {
            const response = await fetch(`${API_URL_REGENCIES}${provinceId}.json`);
            const data = await response.json();
            elements.regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            data.forEach(regency => {
                const option = document.createElement('option');
                option.value = regency.id;
                option.textContent = regency.name;
                elements.regencySelect.appendChild(option);
            });
            elements.regencySelect.disabled = false;
            if (initialRegencyName) {
                const existingOption = Array.from(elements.regencySelect.options).find(opt => opt.textContent === initialRegencyName);
                if (existingOption) {
                    elements.regencySelect.value = existingOption.value;
                }
            }
            updateTextPreview();
        } catch (error) {
            console.error('Error fetching regencies:', error);
            elements.regencySelect.innerHTML = '<option value="">Gagal memuat kabupaten/kota</option>';
        }
    }

    elements.provinceSelect.addEventListener('change', function() {
        const selectedProvinceId = this.value;
        if (selectedProvinceId) {
            fetchRegencies(selectedProvinceId);
        } else {
            elements.regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            elements.regencySelect.disabled = true;
        }
        updateTextPreview();
    });

    elements.regencySelect.addEventListener('change', updateTextPreview);
    elements.locationAddressInput.addEventListener('input', updateTextPreview);

    fetchProvinces();
    updateTextPreview();
    updatePhotoOrderInput();
});
</script>
@endpush
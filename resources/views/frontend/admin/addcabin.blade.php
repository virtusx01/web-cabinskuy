@extends('backend.admin_layout')

@section('title', 'Tambah Kabin Baru')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> {{-- Add Font Awesome for icons --}}
<style>
    :root {
        --primary-green: #008272;
        --light-green: #E6F3F2;
        --dark-text: #333;
        --light-text: #666;
        --border-color: #E0E0E0;
        --background-color: #F9FAFB;
        --white: #FFFFFF;
        --danger: #e53e3e;
        --danger-light: #FEE2E2; /* Added for consistency with the room page */
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--background-color);
        color: var(--dark-text);
    }

    .cabin-form-container {
        padding: 2rem 1.5rem;
        max-width: 1200px;
        margin: auto;
    }

    .cabin-form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .cabin-form-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--primary-green);
        margin: 0;
    }

    .header-buttons .btn {
        text-decoration: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid var(--primary-green);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .header-buttons .btn-secondary {
        background-color: var(--primary-green);
        color: var(--white);
    }

    .header-buttons .btn-secondary:hover {
        opacity: 0.9;
        /* padding-block: 15px; */ /* Removed this as it causes layout shifts */
        /* border-radius: 15px; */ /* Removed this as it causes layout shifts */
    }

    .header-buttons .btn-dashboard {
        background-color: var(--white);
        color: var(--primary-green);
    }

    .header-buttons .btn-dashboard:hover {
        /* padding-block: 15px; */ /* Removed this as it causes layout shifts */
        /* border-radius: 15px; */ /* Removed this as it causes layout shifts */
        background-color: var(--light-green);
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
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-green);
        padding-left: 1rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--dark-text);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px var(--light-green);
    }

    .form-control::placeholder {
        color: #aaa;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .file-input-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s ease;
        position: relative; /* Added for positioning the remove button */
        display: block; /* Make it a block element to take full width */
    }
    .file-input-wrapper:hover {
        border-color: var(--primary-green);
    }
    .file-input-wrapper input[type="file"] {
        display: none;
    }
    .file-input-wrapper .file-input-label {
        color: var(--light-text);
        font-weight: 500;
    }
    .file-input-wrapper .file-input-label svg {
        width: 40px;
        height: 40px;
        margin-bottom: 0.5rem;
        color: var(--primary-green);
    }
    .file-input-wrapper img.preview-image {
        max-width: 100%;
        max-height: 150px;
        display: block;
        margin: 0 auto 10px;
        border-radius: 4px;
        object-fit: contain;
    }

    /* Styles for dynamic photo input */
    .photo-input-group {
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
        padding: 1rem;
        border-radius: 8px;
        background-color: var(--white);
        position: relative;
    }

    .photo-input-group .remove-photo-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: var(--danger);
        color: var(--white);
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.9rem;
        line-height: 1;
        transition: background-color 0.3s ease;
        z-index: 10;
    }

    .photo-input-group .remove-photo-btn:hover {
        background-color: #c03030;
    }

    .add-photo-btn {
        background-color: var(--primary-green);
        color: var(--white);
        padding: 0.6rem 1rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
    }
    .add-photo-btn:hover {
        background-color: #006a5f;
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
        padding: 1rem 1.5rem;
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        background-color: var(--light-green);
        color: var(--primary-green);
    }

    .preview-gallery { /* New wrapper for image previews */
        padding: 1rem;
        background-color: #f0f0f0;
    }
    .main-preview-image { /* New element for the main image */
        width: 100%;
        height: 250px;
        background-color: var(--border-color);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .main-preview-image img, .main-preview-image .image-placeholder {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Changed to contain */
    }

    .thumbnail-preview-container { /* New container for thumbnails */
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
        gap: 0.5rem;
        min-height: 64px;
    }
    .thumbnail-item {
        width: 100%;
        height: 60px;
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

    .image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: var(--light-text);
        font-size: 0.9rem;
        /* grid-column: 1 / -1; This was for the old grid, not needed for new main preview */
    }
    .image-placeholder svg {
        width: 30px;
        height: 30px;
        margin-bottom: 0.5rem;
        /* color: var(--primary-green); Not needed as it's already light-text */
    }


    .preview-content {
        padding: 1.5rem;
    }

    .preview-name {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 0.5rem;
        word-wrap: break-word;
    }

    .preview-location {
        font-weight: 500;
        color: var(--light-text);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .preview-location svg {
        flex-shrink: 0;
    }

    .preview-description {
        font-size: 0.95rem;
        color: var(--light-text);
        margin-bottom: 1.5rem;
        max-height: 100px;
        overflow-y: auto;
    }

    .preview-status {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-available {
        background-color: var(--light-green);
        color: var(--primary-green);
    }

    .status-unavailable {
        background-color: #FEE2E2;
        color: var(--danger);
    }

    .btn-primary {
        width: 100%;
        background-color: var(--primary-green);
        color: var(--white);
        padding: 0.8rem;
        font-size: 1.1rem;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #006a5f;
    }

    .alert-danger {
        background-color: #FFF5F5;
        border-left: 4px solid #E53E3E;
        color: #C53030;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
    }
    .alert-danger ul {
        margin: 0;
        padding-left: 1.2rem;
    }

</style>
@endpush

@section('admin_content')
<div class="cabin-form-container">
    <div class="cabin-form-header">
        <div>
            <h1>Tambah Kabin Baru</h1>
            <p style="color: var(--light-text); margin-top: 0.25rem;">Isi formulir untuk menambahkan kabin baru.</p>
        </div>
        <div class="header-buttons">
            <a href="{{ url('/admin/dashboard') }}" class="btn btn-dashboard">
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                     <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                   </svg>
                   Back to Dashboard
            </a>

            <a href="{{ route('admin.cabins.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-ul" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                </svg>
                List Cabin
            </a>
        </div>
    </div>

    <div class="main-content-grid">
        <div class="form-card">
            <form action="{{ route('admin.cabins.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h2>Detail Informasi Kabin</h2>

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
                    <label for="name">Nama Kabin</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Villa Pinus Sejuk" onkeyup="updatePreview()">
                </div>

                <div class="form-group">
                    <label for="province">Provinsi</label>
                    <select name="province" id="province" class="form-control" required onchange="fetchRegencies()">
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                    <input type="hidden" name="location" id="hidden_location_input" value="{{ old('location') }}">
                </div>

                <div class="form-group">
                    <label for="regency">Kabupaten/Kota</label>
                    <select name="regency" id="regency" class="form-control" required onchange="updatePreview()">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location_address">Alamat Detail Lokasi</label>
                    <input type="text" name="location_address" id="location_address" class="form-control" value="{{ old('location_address') }}" required placeholder="Contoh: Jl. Raya Puncak KM. 85, Tugu Selatan">
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" required onkeyup="updatePreview()">{{ old('description') }}</textarea>
                </div>

                {{-- NEW PHOTO INPUT SECTION --}}
                <div class="form-group">
                    <label>Foto Kabin</label>
                    {{-- Initial photo input --}}
                    <div class="photo-input-group" id="initial-photo-input">
                        <label for="cabin_photos_input_0" class="file-input-wrapper">
                            <img class="preview-image" style="display: none;">
                            <span class="file-input-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-arrow-up-fill" viewBox="0 0 16 16">
                                    <path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z"/>
                                </svg>
                                <br>Klik untuk memilih file atau drag & drop
                            </span>
                            <input type="file" name="cabin_photos[]" id="cabin_photos_input_0" accept="image/*" class="cabin-photo-input">
                        </label>
                    </div>

                    {{-- Container for dynamically added photo inputs --}}
                    <div id="photo-inputs-container">
                        {{-- Dynamic photo inputs will be added here --}}
                    </div>
                    <button type="button" id="add-photo-button" class="add-photo-btn mt-3">
                        <i class="fas fa-plus"></i> Tambah Foto Lain
                    </button>
                </div>
                {{-- END NEW PHOTO INPUT SECTION --}}


                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required onchange="updatePreview()">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Tersedia (Not Available)</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Simpan Kabin</button>
            </form>
        </div>

        <div class="preview-card">
            <h3>Live Preview</h3>
            <div class="preview-gallery"> {{-- Wrap image previews --}}
                <div class="main-preview-image" id="main-preview-image"> {{-- Main image display --}}
                    <div class="image-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                           <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                           <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                        </svg>
                        <span>Foto akan tampil di sini</span>
                    </div>
                </div>
                <div class="thumbnail-preview-container" id="thumbnail-preview-container"> {{-- Thumbnails container --}}
                </div>
            </div>
            <div class="preview-content">
                <h4 id="preview-name" class="preview-name">Nama Kabin Anda</h4>
                <p id="preview-location" class="preview-location">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                      <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    <span>Lokasi Kabin</span>
                </p>
                <p id="preview-description" class="preview-description">Deskripsi kabin akan muncul di sini saat Anda mengetik...</p>
                <div id="preview-status" class="preview-status status-available">Tersedia</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const elements = {
            nameInput: document.getElementById('name'),
            provinceSelect: document.getElementById('province'),
            regencySelect: document.getElementById('regency'),
            hiddenLocationInput: document.getElementById('hidden_location_input'),
            descriptionInput: document.getElementById('description'),
            statusInput: document.getElementById('status'),

            mainPreview: document.getElementById('main-preview-image'),
            thumbContainer: document.getElementById('thumbnail-preview-container'),
            previewName: document.getElementById('preview-name'),
            previewLocation: document.getElementById('preview-location').querySelector('span'),
            previewDescription: document.getElementById('preview-description'),
            previewStatus: document.getElementById('preview-status'),

            initialPhotoInputGroup: document.getElementById('initial-photo-input'),
            photoInputsContainer: document.getElementById('photo-inputs-container'),
            addPhotoButton: document.getElementById('add-photo-button'),
        };

        let allCabinFiles = []; // To store all files from dynamic inputs and the initial input

        // Function to update textual previews (name, location, description, status)
        function updateTextualPreviews() {
            elements.previewName.innerText = elements.nameInput.value || 'Nama Kabin Anda';

            const selectedProvinceName = elements.provinceSelect.options[elements.provinceSelect.selectedIndex].text;
            const selectedRegencyName = elements.regencySelect.options[elements.regencySelect.selectedIndex].text;

            let combinedLocation = '';
            if (selectedRegencyName && selectedRegencyName !== '-- Pilih Kabupaten/Kota --') {
                combinedLocation += selectedRegencyName;
            }
            if (selectedProvinceName && selectedProvinceName !== '-- Pilih Provinsi --') {
                if (combinedLocation) {
                    combinedLocation += ', ';
                }
                combinedLocation += selectedProvinceName;
            }
            elements.previewLocation.innerText = combinedLocation || 'Lokasi Kabin';
            elements.hiddenLocationInput.value = combinedLocation; // Update hidden input for form submission

            elements.previewDescription.innerText = elements.descriptionInput.value || 'Deskripsi kabin akan muncul di sini saat Anda mengetik...';

            const isAvailable = elements.statusInput.value == '1';
            elements.previewStatus.innerText = isAvailable ? 'Tersedia' : 'Tidak Tersedia';
            elements.previewStatus.className = `preview-status ${isAvailable ? 'status-available' : 'status-unavailable'}`;
        }

        // Function to set the main preview image
        const setMainImage = (src) => {
            if (src) {
                elements.mainPreview.innerHTML = `<img src="${src}" alt="Main preview">`;
            } else {
                elements.mainPreview.innerHTML = `<div class="image-placeholder"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/></svg><span>Foto akan tampil di sini</span></div>`;
            }
        };

        // Renders the photo thumbnails and sets the main preview
        function renderPhotoPreview() {
            elements.thumbContainer.innerHTML = ''; // Clear existing thumbnails

            // Filter out nulls from allCabinFiles before rendering
            const validFiles = allCabinFiles.filter(file => file !== null);

            if (validFiles.length === 0) {
                setMainImage(null); // Show placeholder if no photos
                return;
            }

            // Set the first valid image as the main preview by default
            setMainImage(URL.createObjectURL(validFiles[0]));

            validFiles.forEach((file, index) => {
                const thumbDiv = document.createElement('div');
                thumbDiv.className = 'thumbnail-item';
                if (index === 0) thumbDiv.classList.add('active'); // Highlight the first thumbnail

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = `Thumbnail ${index + 1}`;

                thumbDiv.appendChild(img);

                thumbDiv.onclick = () => {
                    setMainImage(URL.createObjectURL(file));
                    elements.thumbContainer.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('active'));
                    thumbDiv.classList.add('active');
                };

                elements.thumbContainer.appendChild(thumbDiv);
            });
        }

        // Function to handle file input change for a specific index
        function handleFileInputChange(event, index) {
            const fileInput = event.target;
            const photoInputGroup = fileInput.closest('.photo-input-group');
            const previewImage = photoInputGroup.querySelector('.preview-image');
            const fileInputLabel = photoInputGroup.querySelector('.file-input-label');

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const fileUrl = URL.createObjectURL(file);
                previewImage.src = fileUrl;
                previewImage.style.display = 'block';
                fileInputLabel.style.display = 'none';

                // Update allCabinFiles array at the correct index
                allCabinFiles[index] = file;

            } else {
                previewImage.src = '';
                previewImage.style.display = 'none';
                fileInputLabel.style.display = 'block';

                // Set file to null in allCabinFiles if cleared
                allCabinFiles[index] = null;
            }
            renderPhotoPreview(); // Update main preview and thumbnails
        }

        // Function to create a new photo input group (for "Tambah Foto Lain")
        function createPhotoInputGroup() {
            const index = allCabinFiles.length; // Get the next available index
            const photoInputGroup = document.createElement('div');
            photoInputGroup.className = 'photo-input-group';
            photoInputGroup.dataset.index = index; // Store index for easy lookup

            photoInputGroup.innerHTML = `
                <label for="cabin_photos_input_${index}" class="file-input-wrapper">
                    <img class="preview-image" style="display: none;">
                    <span class="file-input-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-arrow-up-fill" viewBox="0 0 16 16">
                            <path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z"/>
                        </svg>
                        <br>Klik untuk memilih file atau drag & drop
                    </span>
                    <input type="file" name="cabin_photos[]" id="cabin_photos_input_${index}" accept="image/*" class="cabin-photo-input">
                </label>
                <button type="button" class="remove-photo-btn">&times;</button>
            `;

            const fileInput = photoInputGroup.querySelector('.cabin-photo-input');
            const newListener = (event) => handleFileInputChange(event, index);
            fileInput.addEventListener('change', newListener);
            fileInput._changeListener = newListener; // Store listener for future removal

            const removeButton = photoInputGroup.querySelector('.remove-photo-btn');
            removeButton.onclick = () => {
                const groupIndexToRemove = parseInt(photoInputGroup.dataset.index);
                if (!isNaN(groupIndexToRemove)) {
                    allCabinFiles.splice(groupIndexToRemove, 1);
                }
                photoInputGroup.remove();
                reindexPhotoInputs(); // Re-index inputs after removal
                renderPhotoPreview(); // Update main preview and thumbnails
            };

            elements.photoInputsContainer.appendChild(photoInputGroup);
            allCabinFiles.push(null); // Add a placeholder for the new file
        }

        // Function to re-index dynamic photo inputs after one is removed
        function reindexPhotoInputs() {
            const allInputGroups = [elements.initialPhotoInputGroup, ...elements.photoInputsContainer.querySelectorAll('.photo-input-group')];
            allInputGroups.forEach((group, i) => {
                group.dataset.index = i;
                const fileInput = group.querySelector('.cabin-photo-input');
                fileInput.id = `cabin_photos_input_${i}`;
                group.querySelector('label').setAttribute('for', `cabin_photos_input_${i}`);

                // Update the event listener to use the new index
                if (fileInput._changeListener) {
                    fileInput.removeEventListener('change', fileInput._changeListener);
                }
                const newListener = (event) => handleFileInputChange(event, i);
                fileInput.addEventListener('change', newListener);
                fileInput._changeListener = newListener;
            });
        }


        // Event listener for the initial photo input
        const initialFileInput = document.getElementById('cabin_photos_input_0');
        if (initialFileInput) {
            allCabinFiles[0] = null; // Initialize first slot as null
            const initialListener = (event) => handleFileInputChange(event, 0);
            initialFileInput.addEventListener('change', initialListener);
            initialFileInput._changeListener = initialListener; // Store reference
        }

        // Event listener for adding more photo inputs
        elements.addPhotoButton.addEventListener('click', createPhotoInputGroup);

        // Initial updates and event listeners for textual fields
        [elements.nameInput, elements.descriptionInput, elements.statusInput].forEach(input => {
            input.addEventListener('input', updateTextualPreviews);
            input.addEventListener('change', updateTextualPreviews);
        });
        elements.provinceSelect.addEventListener('change', updateTextualPreviews);
        elements.regencySelect.addEventListener('change', updateTextualPreviews);


        // --- Province and Regency Logic ---
        async function fetchProvinces() {
            try {
                const response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                const provinces = await response.json();

                if (elements.provinceSelect) {
                    elements.provinceSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                    provinces.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.id;
                        option.textContent = province.name;
                        elements.provinceSelect.appendChild(option);
                    });
                    // For old values (if a submission failed and redirected back with old data)
                    const oldProvince = "{{ old('province') }}";
                    if (oldProvince) {
                        elements.provinceSelect.value = oldProvince;
                        fetchRegencies(oldProvince, "{{ old('regency') }}"); // Pass old regency to pre-select
                    }
                    updateTextualPreviews(); // Update preview after provinces are loaded
                }
            } catch (error) {
                console.error('Error fetching provinces:', error);
            }
        }

        async function fetchRegencies(provinceId, oldRegencyId = null) {
            if (elements.regencySelect) {
                elements.regencySelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                if (!provinceId) {
                    elements.regencySelect.disabled = true;
                    updateTextualPreviews();
                    return;
                }
                elements.regencySelect.disabled = false;
                try {
                    const response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`);
                    const regencies = await response.json();

                    regencies.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.id;
                        option.textContent = regency.name;
                        elements.regencySelect.appendChild(option);
                    });

                    if (oldRegencyId) {
                        elements.regencySelect.value = oldRegencyId;
                    }
                    updateTextualPreviews(); // Update preview after regencies are loaded
                } catch (error) {
                    console.error('Error fetching regencies:', error);
                    elements.regencySelect.disabled = false;
                }
            }
        }

        // Event listener for province change
        if (elements.provinceSelect) {
            elements.provinceSelect.addEventListener('change', function() {
                fetchRegencies(this.value);
            });
        }

        // Initial calls on page load
        fetchProvinces(); // Load provinces
        updateTextualPreviews(); // Call initially to set text placeholders
        renderPhotoPreview(); // Call initially to set photo placeholder
    });
</script>
@endpush
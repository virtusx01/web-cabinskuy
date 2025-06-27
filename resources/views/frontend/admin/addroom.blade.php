@extends('backend.admin_layout')

@section('title', 'Tambah Ruangan Baru')

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
    }

    .room-form-container {
        padding: 2rem 1.5rem;
        max-width: 1200px;
        margin: auto;
    }

    .room-form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .room-form-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--primary-green);
        margin: 0;
    }
    .room-form-header h1 span {
        font-weight: 400;
        color: var(--light-text);
        font-size: 1.2rem;
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
        background-color: var(--white);
        color: var(--primary-green);
    }
    .header-buttons .btn:hover {
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
    .file-input-wrapper:hover { border-color: var(--primary-green); }
    .file-input-wrapper input[type="file"] { display: none; }
    .file-input-wrapper .file-input-label svg {
        width: 40px; height: 40px; margin-bottom: 0.5rem; color: var(--primary-green);
    }
    .file-input-wrapper img.preview-image {
        max-width: 100%;
        max-height: 150px;
        display: block;
        margin: 0 auto 10px;
        border-radius: 4px;
        object-fit: contain; /* Changed to contain to show full image */
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
        z-index: 10; /* Ensure button is clickable */
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

    /* Preview card styles */
    .preview-card {
        background-color: var(--white);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }
    .preview-card h3 {
        padding: 1rem 1.5rem; margin: 0; font-size: 1.25rem;
        background-color: var(--light-green); color: var(--primary-green);
    }
    .preview-gallery {
        padding: 1rem;
        background-color: #f0f0f0;
    }
    .main-preview-image {
        width: 100%; height: 250px; /* Increased height for master photo */
        background-color: var(--border-color);
        border-radius: 8px; overflow: hidden; margin-bottom: 1rem;
        display: flex; align-items: center; justify-content: center;
    }
    .main-preview-image img, .main-preview-image .image-placeholder {
        width: 100%; height: 100%; object-fit: contain; /* Changed to contain */
    }

    .thumbnail-preview-container {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); /* Slightly larger thumbnails */
        gap: 0.5rem; min-height: 64px; /* Adjusted height */
    }
    .thumbnail-item {
        width: 100%; height: 60px; border-radius: 6px; /* Adjusted height */
        overflow: hidden; cursor: pointer; border: 2px solid transparent;
        transition: border-color 0.3s;
    }
    .thumbnail-item:hover, .thumbnail-item.active {
        border-color: var(--primary-green);
    }
    .thumbnail-item img {
        width: 100%; height: 100%; object-fit: cover;
    }

    .image-placeholder {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        color: var(--light-text); font-size: 0.9rem;
    }
    .image-placeholder svg { width: 40px; height: 40px; margin-bottom: 0.5rem; }

    .preview-content { padding: 1.5rem; }
    .preview-typeroom {
        font-size: 1.5rem; font-weight: 600; margin: 0;
    }
    .preview-price {
        font-size: 1.25rem; color: var(--primary-green); font-weight: 500; margin: 0.25rem 0 1rem 0;
    }
    .preview-description {
        font-size: 0.95rem; color: var(--light-text); margin-bottom: 1.5rem; max-height: 120px; overflow-y: auto;
    }
    .preview-status {
        display: inline-block; padding: 0.4rem 0.8rem; border-radius: 20px;
        font-size: 0.8rem; font-weight: 600; text-transform: uppercase;
    }
    .status-available { background-color: var(--light-green); color: var(--primary-green); }
    .status-unavailable { background-color: var(--danger-light); color: var(--danger); }

    .btn-submit {
        background-color: var(--primary-green); color: var(--white);
        padding: 0.8rem 1.5rem; font-size: 1rem; font-weight: 600; border: none;
        border-radius: 8px; cursor: pointer; transition: background-color 0.3s;
    }
    .btn-submit:hover { background-color: #006a5f; }
</style>
@endpush

@section('admin_content')
<div class="room-form-container">
    <div class="room-form-header">
        <div>
            <h1>Tambah Ruangan Baru</h1>
            <p style="color: var(--light-text); margin-top: 0.25rem;">Kabin: <strong>{{ $cabin->name }}</strong></p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('admin.cabins.show', $cabin->id_cabin) }}" class="btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Detail Kabin
            </a>
        </div>
    </div>

    <div class="main-content-grid">
        <div class="form-card">
            <form action="{{ route('admin.cabins.rooms.store', $cabin->id_cabin) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h2>Detail Ruangan</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- The id_room input field is removed as it's now auto-generated --}}

                <div class="form-group">
                    <label for="typeroom">Tipe Ruangan</label>
                    <select name="typeroom" id="typeroom" class="form-control" required>
                        <option value="" disabled selected>Pilih Tipe</option>
                        <option value="Standard" {{ old('typeroom') == 'Standard' ? 'selected' : '' }}>Standard</option>
                        <option value="Deluxe" {{ old('typeroom') == 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                        <option value="Executive" {{ old('typeroom') == 'Executive' ? 'selected' : '' }}>Executive</option>
                        <option value="Family Suite" {{ old('typeroom') == 'Family Suite' ? 'selected' : '' }}>Family Suite</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Harga per Malam (Rp)</label>
                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required placeholder="e.g., 500000">
                </div>

                <div class="form-group">
                    <label for="max_guests">Maksimal Tamu</label>
                    <input type="number" name="max_guests" id="max_guests" class="form-control" value="{{ old('max_guests') }}" required placeholder="e.g., 2">
                </div>

                <div class="form-group">
                    <label for="slot_room">Jumlah Kamar</label>
                    <input type="number" name="slot_room" id="slot_room" class="form-control" value="{{ old('slot_room') }}" required placeholder="e.g., 5">
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Ruangan</label>
                    <textarea name="description" id="description" class="form-control" required>{{ old('description') }}</textarea>
                </div>

                {{-- CORRECTED PHOTO INPUT SECTION --}}
                <div class="form-group">
                    <label>Foto Ruangan</label>
                    {{-- Initial photo input --}}
                    <div class="photo-input-group" id="initial-photo-input">
                        <label for="room_photos_input_0" class="file-input-wrapper">
                            <img class="preview-image" style="display: none;">
                            <span class="file-input-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-arrow-up-fill" viewBox="0 0 16 16">
                                    <path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z"/>
                                </svg>
                                <br>Klik untuk memilih file atau drag & drop
                            </span>
                            <input type="file" name="room_photos[]" id="room_photos_input_0" accept="image/*" class="room-photo-input">
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
                {{-- END CORRECTED PHOTO INPUT SECTION --}}

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Simpan Ruangan</button>
                <a href="{{ route('admin.cabins.show', $cabin->id_cabin) }}" style="margin-left: 10px; color: var(--light-text);">Batal</a>
            </form>
        </div>

        <div class="preview-card">
            <h3>Live Preview</h3>
            <div class="preview-gallery">
                <div class="main-preview-image" id="main-preview-image">
                    <div class="image-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/></svg>
                        <span>Foto akan tampil di sini</span>
                    </div>
                </div>
                <div class="thumbnail-preview-container" id="thumbnail-preview-container">
                </div>
            </div>
            <div class="preview-content">
                <h4 id="preview-typeroom" class="preview-typeroom">Pilih Tipe Ruangan</h4>
                <p id="preview-price" class="preview-price">Rp 0</p>
                <p id="preview-description" class="preview-description">Deskripsi ruangan akan muncul di sini...</p>
                <div id="preview-status" class="preview-status"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinceSelect = document.getElementById('province');
        const regencySelect = document.getElementById('regency');

        // Function to fetch and populate provinces
        async function fetchProvinces() {
            try {
                const response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                const provinces = await response.json();

                // Check if provinceSelect exists before trying to modify it
                if (provinceSelect) {
                    provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                    provinces.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.id; // Send ID to backend
                        option.textContent = province.name;
                        provinceSelect.appendChild(option);
                    });

                    // If editing, pre-select the province
                    @if(isset($cabin) && $cabin->province)
                        // You'll need a way to get the province ID here based on the stored name.
                        // This is usually done by passing the ID from the backend if available,
                        // or by iterating through provinces to find a match by name.
                        // For simplicity, let's assume we pass the old ID if available or find it.
                        const currentProvinceName = "{{ $cabin->province ?? '' }}";
                        const selectedProvince = provinces.find(p => p.name === currentProvinceName);
                        if (selectedProvince) {
                            provinceSelect.value = selectedProvince.id;
                            fetchRegencies(selectedProvince.id); // Fetch regencies for pre-selected province
                        }
                    @endif
                }

            } catch (error) {
                console.error('Error fetching provinces:', error);
                // alert('Gagal mengambil data provinsi. Silakan coba lagi.'); // Removed alert for cleaner UX
            }
        }

        // Function to fetch and populate regencies based on province ID
        async function fetchRegencies(provinceId) {
            // Check if regencySelect exists before trying to modify it
            if (regencySelect) {
                regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                if (!provinceId) {
                    regencySelect.disabled = true;
                    return;
                }
                regencySelect.disabled = false;
                try {
                    const response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`);
                    const regencies = await response.json();

                    regencies.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.id; // Send ID to backend
                        option.textContent = regency.name;
                        regencySelect.appendChild(option);
                    });

                    // If editing, pre-select the regency
                    @if(isset($cabin) && $cabin->regency)
                        const currentRegencyName = "{{ $cabin->regency ?? '' }}";
                        const selectedRegency = regencies.find(r => r.name === currentRegencyName);
                        if (selectedRegency) {
                            regencySelect.value = selectedRegency.id;
                        }
                    @endif

                } catch (error) {
                    console.error('Error fetching regencies:', error);
                    // alert('Gagal mengambil data kabupaten/kota. Silakan coba lagi.'); // Removed alert for cleaner UX
                }
            }
        }

        // Event listener for province change
        if (provinceSelect) {
            provinceSelect.addEventListener('change', function() {
                fetchRegencies(this.value);
            });
        }

        // Initial fetch for provinces when the page loads
        fetchProvinces();


        // --- Room Photo Preview Logic ---
        const elements = {
            typeroomInput: document.getElementById('typeroom'),
            priceInput: document.getElementById('price'),
            descriptionInput: document.getElementById('description'),
            statusInput: document.getElementById('status'),

            mainPreview: document.getElementById('main-preview-image'),
            thumbContainer: document.getElementById('thumbnail-preview-container'),
            previewTyperoom: document.getElementById('preview-typeroom'),
            previewPrice: document.getElementById('preview-price'), // Corrected to previewPrice
            previewDescription: document.getElementById('preview-description'),
            previewStatus: document.getElementById('preview-status'),

            initialPhotoInputGroup: document.getElementById('initial-photo-input'), // New element for the initial input
            photoInputsContainer: document.getElementById('photo-inputs-container'),
            addPhotoButton: document.getElementById('add-photo-button'),
        };

        let allRoomFiles = []; // To store all files from dynamic inputs and the initial input

        function formatPrice(value) { // Corrected function name to formatPrice
            const number = parseInt(value, 10) || 0;
            return 'Rp ' + number.toLocaleString('id-ID');
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

            if (allRoomFiles.length === 0) {
                setMainImage(null); // Show placeholder if no photos
                return;
            }

            // Set the first image as the main preview by default
            setMainImage(URL.createObjectURL(allRoomFiles[0]));

            allRoomFiles.forEach((file, index) => {
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

        // Updates the textual and status previews
        function updateFullPreview() {
            elements.previewTyperoom.textContent = elements.typeroomInput.value || 'Pilih Tipe Ruangan';
            elements.previewPrice.textContent = formatPrice(elements.priceInput.value); // Corrected to previewPrice
            elements.previewDescription.textContent = elements.descriptionInput.value || 'Deskripsi ruangan akan muncul di sini...';

            const isAvailable = elements.statusInput.value == '1';
            elements.previewStatus.textContent = isAvailable ? 'Tersedia' : 'Tidak Tersedia';
            elements.previewStatus.className = `preview-status ${isAvailable ? 'status-available' : 'status-unavailable'}`;

            // Photo preview is handled by renderPhotoPreview()
        }

        // Function to handle file input change
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

                // Update allRoomFiles array at the correct index
                allRoomFiles[index] = file;

            } else {
                previewImage.src = '';
                previewImage.style.display = 'none';
                fileInputLabel.style.display = 'block';

                // Remove file from allRoomFiles if cleared
                allRoomFiles.splice(index, 1);
            }
            renderPhotoPreview(); // Update main preview and thumbnails
        }

        // Function to create a new photo input group (for "Tambah Foto Lain")
        function createPhotoInputGroup() {
            const index = allRoomFiles.length; // Get the next available index
            const photoInputGroup = document.createElement('div');
            photoInputGroup.className = 'photo-input-group';
            photoInputGroup.dataset.index = index; // Store index for easy lookup

            photoInputGroup.innerHTML = `
                <label for="room_photos_input_${index}" class="file-input-wrapper">
                    <img class="preview-image" style="display: none;">
                    <span class="file-input-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-arrow-up-fill" viewBox="0 0 16 16">
                            <path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z"/>
                        </svg>
                        <br>Klik untuk memilih file atau drag & drop
                    </span>
                    <input type="file" name="room_photos[]" id="room_photos_input_${index}" accept="image/*" class="room-photo-input">
                </label>
                <button type="button" class="remove-photo-btn">&times;</button>
            `;

            const fileInput = photoInputGroup.querySelector('.room-photo-input');
            fileInput.addEventListener('change', (event) => handleFileInputChange(event, index));

            const removeButton = photoInputGroup.querySelector('.remove-photo-btn');
            removeButton.onclick = () => {
                const groupIndexToRemove = Array.from(elements.photoInputsContainer.children).indexOf(photoInputGroup);
                if (groupIndexToRemove !== -1) {
                    allRoomFiles.splice(groupIndexToRemove + 1, 1); // +1 because initial input is at index 0
                }
                photoInputGroup.remove();
                renderPhotoPreview();
                reindexPhotoInputs(); // Re-index inputs after removal
            };

            elements.photoInputsContainer.appendChild(photoInputGroup);
            allRoomFiles.push(null); // Add a placeholder for the new file
        }

        // Function to re-index dynamic photo inputs after one is removed
        function reindexPhotoInputs() {
            const dynamicInputGroups = elements.photoInputsContainer.querySelectorAll('.photo-input-group');
            dynamicInputGroups.forEach((group, i) => {
                const newIndex = i + 1; // Start indexing from 1 for dynamic inputs
                group.dataset.index = newIndex;
                const fileInput = group.querySelector('.room-photo-input');
                fileInput.id = `room_photos_input_${newIndex}`;
                fileInput.name = `room_photos[]`; // Ensure correct name for submission
                group.querySelector('label').setAttribute('for', `room_photos_input_${newIndex}`);

                // Update the event listener to use the new index
                fileInput.removeEventListener('change', fileInput._changeListener); // Remove old listener
                const newListener = (event) => handleFileInputChange(event, newIndex);
                fileInput.addEventListener('change', newListener);
                fileInput._changeListener = newListener; // Store listener for future removal
            });
        }


        // Event listener for the initial photo input
        const initialFileInput = document.getElementById('room_photos_input_0');
        if (initialFileInput) {
            allRoomFiles[0] = null; // Initialize first slot as null
            const initialListener = (event) => handleFileInputChange(event, 0);
            initialFileInput.addEventListener('change', initialListener);
            initialFileInput._changeListener = initialListener; // Store reference
        }


        // Event listener for adding more photo inputs
        elements.addPhotoButton.addEventListener('click', createPhotoInputGroup);

        // Initial updates on page load
        [elements.typeroomInput, elements.priceInput, elements.descriptionInput, elements.statusInput].forEach(input => {
            input.addEventListener('input', updateFullPreview);
            input.addEventListener('change', updateFullPreview);
        });

        updateFullPreview(); // Call initially to set placeholders
        renderPhotoPreview(); // Call initially to set photo placeholder
    });
</script>
@endpush
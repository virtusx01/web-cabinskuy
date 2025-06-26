@extends('backend.admin_layout')

@section('title', 'Edit Ruangan')

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
    .form-control[disabled] {
        background-color: #f1f1f1;
        cursor: not-allowed;
    }
    
    /* Styles for current photos and delete functionality */
    .current-photos-section {
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        border-radius: 12px;
        background-color: var(--background-color);
    }
    .current-photos-section label {
        font-weight: 600;
        margin-bottom: 1rem;
        display: block;
    }
    .current-photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); /* Adjusted for better visibility */
        gap: 1rem;
        padding-bottom: 0.5rem; /* Space for potential scrollbar */
    }
    .current-photo-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.2s ease-in-out;
    }
    .current-photo-item.marked-for-delete {
        opacity: 0.6; /* Reduced opacity */
        border-color: var(--danger); /* Highlight with danger color */
    }
    .current-photo-item img {
        display: block;
        width: 100%;
        height: 100px;
        object-fit: cover;
    }
    .delete-photo-overlay {
        position: absolute;
        top: 0; /* Changed to top */
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        color: white;
        text-align: center;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translateY(-100%); /* Start hidden above */
        transition: transform 0.3s ease-out;
        height: 100%; /* Cover entire image */
    }
    .current-photo-item:hover .delete-photo-overlay {
        transform: translateY(0); /* Slide down on hover */
    }
    .delete-photo-overlay label {
        font-size: 0.9rem;
        display: flex;
        flex-direction: column; /* Stack checkbox and text */
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin: 0;
        font-weight: 500;
    }
    .delete-photo-overlay input {
        margin-bottom: 0.3rem; /* Space between checkbox and text */
        transform: scale(1.2); /* Slightly larger checkbox */
    }


    /* Styles for new photo input */
    .file-input-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s ease;
        margin-top: 1rem;
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
        object-fit: contain;
    }
    .add-new-photo-area {
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        border-radius: 12px;
        background-color: var(--background-color);
    }

    /* Preview card styles - copied from addroom for consistency */
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

    .image-placeholder {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        color: var(--light-text); font-size: 0.9rem;
    }
    .image-placeholder svg { width: 40px; height: 40px; margin-bottom: 0.5rem; }

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
    
    .form-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-top: 2rem;
    }
    .btn-primary {
        background-color: var(--primary-green); color: var(--white);
        padding: 0.8rem 1.5rem; font-size: 1rem; font-weight: 600; border: none;
        border-radius: 8px; cursor: pointer; transition: background-color 0.3s;
    }
    .btn-primary:hover { background-color: #006a5f; }
    .btn-cancel { color: var(--light-text); text-decoration: none; font-weight: 500;}
</style>
@endpush

@section('admin_content')
<div class="room-form-container">
    <div class="room-form-header">
        <div>
            <h1>Edit Ruangan: <span>{{ $room->typeroom }}</span></h1>
            <p style="color: var(--light-text); margin-top: 0.25rem;">Kabin: <strong>{{ $room->cabin->name ?? 'N/A' }}</strong></p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('admin.cabins.show', $room->id_cabin) }}" class="btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Detail Kabin
            </a>
        </div>
    </div>

    @php
        // Start with the raw room photos, which should be an array due to model casting
        $initialRoomPhotos = (array)($room->room_photos ?? []);
        
        // Clean up each path for consistent URL generation
        $cleanedCurrentPhotos = [];
        foreach ($initialRoomPhotos as $path) {
            if (is_string($path)) {
                // Ensure path starts with 'images/' and uses forward slashes
                $cleanedPath = str_replace(['\\', '"'], ['/', ''], $path);
                // Remove leading slash if it exists, to prevent double slashes when concatenating with asset('storage/')
                $cleanedPath = ltrim($cleanedPath, '/');
                // Ensure it starts with 'images/cabin_rooms/'
                if (!Str::startsWith($cleanedPath, 'images/cabin_rooms/')) {
                    // This case should ideally not happen if paths are stored correctly from upload
                    // But as a safeguard, try to append it if it's just the filename
                    $cleanedPath = 'images/cabin_rooms/' . basename($cleanedPath);
                }
                $cleanedCurrentPhotos[] = $cleanedPath;
            }
        }
        $currentPhotos = $cleanedCurrentPhotos; // This will be passed to JavaScript
    @endphp

    <div class="main-content-grid">
        <div class="form-card">
            <form action="{{ route('admin.rooms.update', $room->id_room) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
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

                <div class="form-group">
                    <label for="id_room">ID Ruangan</label>
                    <input type="text" class="form-control" value="{{ $room->id_room }}" disabled>
                    {{-- Hidden input to ensure id_room is sent with the form if needed for custom logic --}}
                    {{-- <input type="hidden" name="id_room" value="{{ $room->id_room }}"> --}}
                </div>

                <div class="form-group">
                    <label for="typeroom">Tipe Ruangan</label>
                    <select name="typeroom" id="typeroom" class="form-control" required>
                        <option value="Standard" {{ old('typeroom', $room->typeroom) == 'Standard' ? 'selected' : '' }}>Standard</option>
                        <option value="Deluxe" {{ old('typeroom', $room->typeroom) == 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                        <option value="Executive" {{ old('typeroom', $room->typeroom) == 'Executive' ? 'selected' : '' }}>Executive</option>
                        <option value="Family Suite" {{ old('typeroom', $room->typeroom) == 'Family Suite' ? 'selected' : '' }}>Family Suite</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Harga per Malam (Rp)</label>
                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $room->price) }}" required placeholder="e.g., 500000">
                </div>

                <div class="form-group">
                    <label for="max_guests">Maksimal Tamu</label>
                    <input type="number" name="max_guests" id="max_guests" class="form-control" value="{{ old('max_guests', $room->max_guests) }}" required placeholder="e.g., 2">
                </div>

                <div class="form-group">
                    <label for="slot_room">Jumlah Kamar</label>
                    <input type="number" name="slot_room" id="slot_room" class="form-control" value="{{ old('slot_room', $room->slot_room) }}" required placeholder="e.g., 5">
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Ruangan</label>
                    <textarea name="description" id="description" class="form-control" required>{{ old('description', $room->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="1" {{ old('status', $room->status) == 1 ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ old('status', $room->status) == 0 ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>
                
                <h2>Kelola Foto</h2>
                
                <div class="current-photos-section">
                    <label>Foto Saat Ini</label>
                    @if(!empty($currentPhotos))
                        <div class="current-photos-grid">
                            @foreach($currentPhotos as $photoPath)
                                @php
                                    $photoUrl = asset('images/cabinskuy_placeholder.jpg'); // Default placeholder
                                    if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                                        $photoUrl = asset('storage/' . $photoPath);
                                    }
                                @endphp
                                <div class="current-photo-item">
                                    <img src="{{ $photoUrl }}" alt="Foto Ruangan">
                                    <div class="delete-photo-overlay">
                                        <label>
                                            <input type="checkbox" name="delete_photos[]" value="{{ $photoPath }}" class="delete-photo-checkbox">
                                            Hapus
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color: var(--light-text); text-align: center;">Tidak ada foto saat ini untuk ruangan ini.</p>
                    @endif
                </div>

                <div class="form-group add-new-photo-area">
                    <label for="room_photos_input">Tambah Foto Baru</label>
                    <label for="room_photos_input" class="file-input-wrapper">
                        <img class="preview-image" style="display: none;">
                        <span class="file-input-label">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z"></path></svg>
                            <br>Klik untuk unggah foto baru (opsional)
                        </span>
                    </label>
                    <input type="file" name="room_photos[]" id="room_photos_input" accept="image/*" multiple style="display:none;">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Perbarui Ruangan</button>
                    <a href="{{ route('admin.cabins.show', $room->id_cabin) }}" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>

        <div class="preview-card">
            <h3>Live Preview</h3>
            <div class="preview-gallery">
                <div class="main-preview-image" id="main-preview-image">
                    {{-- Initial image will be set by JS or remain placeholder if no photos --}}
                </div>
                <div class="thumbnail-preview-container" id="thumbnail-preview-container">
                    {{-- Thumbnails will be generated by JS --}}
                </div>
            </div>
            <div class="preview-content">
                <h4 id="preview-typeroom" class="preview-typeroom"></h4>
                <p id="preview-price" class="preview-price"></p>
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
    // Current photo paths passed from Blade, cleaned and ready for use
    const currentPhotoPaths = @json($currentPhotos ?? []); 
    let newPhotoFiles = []; // Files selected via the new photo input

    const elements = {
        typeroomInput: document.getElementById('typeroom'),
        priceInput: document.getElementById('price'),
        maxGuestsInput: document.getElementById('max_guests'),
        slotRoomInput: document.getElementById('slot_room'),
        descriptionInput: document.getElementById('description'),
        statusInput: document.getElementById('status'),
        newPhotoInput: document.getElementById('room_photos_input'),
        deleteCheckboxes: document.querySelectorAll('.delete-photo-checkbox'),
        currentPhotoItems: document.querySelectorAll('.current-photo-item'), // For adding/removing class

        mainPreview: document.getElementById('main-preview-image'),
        thumbContainer: document.getElementById('thumbnail-preview-container'),
        previewTyperoom: document.getElementById('preview-typeroom'),
        previewprice: document.getElementById('preview-price'),
        previewDescription: document.getElementById('preview-description'),
        previewStatus: document.getElementById('preview-status'),
        newPhotoPreviewImage: document.querySelector('.file-input-wrapper .preview-image'),
        newPhotoInputLabel: document.querySelector('.file-input-wrapper .file-input-label')
    };

    function formatprice(value) {
        const number = parseInt(value, 10) || 0;
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function updateTextPreview() {
        elements.previewTyperoom.textContent = elements.typeroomInput.value || 'Tipe Ruangan';
        elements.previewprice.textContent = formatprice(elements.priceInput.value);
        elements.previewDescription.textContent = elements.descriptionInput.value || 'Deskripsi ruangan akan muncul di sini...';

        const isAvailable = elements.statusInput.value == '1';
        elements.previewStatus.textContent = isAvailable ? 'Tersedia' : 'Tidak Tersedia';
        elements.previewStatus.className = `preview-status ${isAvailable ? 'status-available' : 'status-unavailable'}`;
    }

    // Function to set the main preview image
    const setMainImage = (src) => {
        if (src) {
            elements.mainPreview.innerHTML = `<img src="${src}" alt="Main preview">`;
        } else {
            elements.mainPreview.innerHTML = `
                <div class="image-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                    </svg>
                    <span>Foto akan tampil di sini</span>
                </div>`;
        }
    };

    function renderPhotoPreview() {
        elements.thumbContainer.innerHTML = '';
        let photosForPreview = [];
        const assetBaseUrl = "{{ asset('storage/') }}"; 

        // Collect existing photos that are NOT marked for deletion
        const nonDeletedCurrentPhotos = currentPhotoPaths.filter(path => {
            const checkbox = document.querySelector(`.delete-photo-checkbox[value="${path}"]`);
            return !checkbox || !checkbox.checked;
        });

        // Add non-deleted current photos to the preview array
        nonDeletedCurrentPhotos.forEach(path => {
            photosForPreview.push(`${assetBaseUrl}${path}`);
        });

        // Add newly selected files to the preview array
        newPhotoFiles.forEach(file => {
            photosForPreview.push(URL.createObjectURL(file));
        });
        
        if (photosForPreview.length === 0) {
            setMainImage(null); // Show placeholder if no photos
            return;
        }
        
        // Set the first available photo as the main preview
        setMainImage(photosForPreview[0]);

        // Render thumbnails
        photosForPreview.forEach((photoSrc, index) => {
            const thumbDiv = document.createElement('div');
            thumbDiv.className = 'thumbnail-item';
            if (index === 0) thumbDiv.classList.add('active'); // First thumbnail is active by default
            
            thumbDiv.innerHTML = `<img src="${photoSrc}" alt="Thumbnail ${index + 1}">`;
            
            thumbDiv.onclick = () => {
                setMainImage(photoSrc);
                elements.thumbContainer.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('active'));
                thumbDiv.classList.add('active');
            };
            
            elements.thumbContainer.appendChild(thumbDiv);
        });
    }
    
    function handleNewFileSelection(event) {
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

        // Deselect all delete checkboxes when new files are chosen
        elements.deleteCheckboxes.forEach(cb => {
            cb.checked = false;
            const parentItem = cb.closest('.current-photo-item');
            if (parentItem) {
                parentItem.classList.remove('marked-for-delete');
            }
        });
        renderPhotoPreview();
    }
    
    // Event listener for delete checkboxes
    elements.deleteCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', () => {
            // Clear new file input and files when a delete checkbox is checked
            elements.newPhotoInput.value = ''; 
            newPhotoFiles = [];
            elements.newPhotoPreviewImage.src = '';
            elements.newPhotoPreviewImage.style.display = 'none';
            elements.newPhotoInputLabel.style.display = 'block';

            // Update the visual state of the current photo item
            const parentItem = elements.currentPhotoItems[index];
            if (parentItem) {
                parentItem.classList.toggle('marked-for-delete', checkbox.checked);
            }
            renderPhotoPreview();
        });

        // Set initial state for checkboxes based on old input (if a form validation error occurred)
        if (checkbox.checked) {
             const parentItem = elements.currentPhotoItems[index];
             if (parentItem) {
                parentItem.classList.add('marked-for-delete');
             }
        }
    });

    // Event listeners for text/select inputs to update live preview
    [elements.typeroomInput, elements.priceInput, elements.descriptionInput, elements.statusInput, elements.maxGuestsInput, elements.slotRoomInput].forEach(input => {
        input.addEventListener('input', updateTextPreview);
        input.addEventListener('change', updateTextPreview);
    });
    
    // Event listener for new photo input
    elements.newPhotoInput.addEventListener('change', handleNewFileSelection);

    // Initial setup for the form and preview
    updateTextPreview();
    renderPhotoPreview(); // Call initially to show existing photos
});
</script>
@endpush
@extends('backend.admin_layout')

@push('styles')
<style>
    /* === DETAIL CABIN LAYOUT STYLES === */
    .detail-cabin-container {
        background-color: #f8f9fa;
        min-height: calc(100vh - 160px);
        padding: 30px 0;
    }
    
    .breadcrumb-nav {
        margin-bottom: 25px;
    }
    
    .breadcrumb-nav a {
        color: #229954;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s;
    }
    
    .breadcrumb-nav a:hover {
        color: #1c7d43;
    }
    
    /* === CABIN HEADER CARD === */
    .cabin-header-card {
        background: linear-gradient(135deg, #fff 0%, #f8fffe 100%);
        border-radius: 16px;
        padding: 35px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(34, 153, 84, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .cabin-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #229954, #1c7d43);
    }
    
    .cabin-header-info {
        display: flex;
        align-items: flex-start;
        gap: 25px;
    }
    
    .cabin-main-photo {
        width: 180px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 3px solid #fff;
    }
    
    .cabin-details-text {
        flex: 1;
    }
    
    .cabin-details-text h1 {
        font-size: 2.2em;
        color: #223324;
        margin: 0 0 8px 0;
        font-weight: 700;
    }
    
    .cabin-location {
        color: #6c757d;
        font-size: 1.1em;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cabin-meta-tags {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .meta-tag {
        background-color: rgba(34, 153, 84, 0.1);
        color: #1c7d43;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: 500;
        border: 1px solid rgba(34, 153, 84, 0.2);
    }
    
    /* === CONTENT SECTION === */
    .content-section {
        background-color: #fff;
        border-radius: 16px;
        padding: 35px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f3f5;
    }
    
    .section-title {
        font-size: 1.8em;
        color: #223324;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .section-title i {
        color: #229954;
        font-size: 0.9em;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #229954, #1c7d43);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 0.95em;
        box-shadow: 0 4px 12px rgba(34, 153, 84, 0.3);
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(34, 153, 84, 0.4);
    }
    
    /* === ROOMS GRID === */
    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
    
    .room-card {
        background: linear-gradient(145deg, #fff, #f8f9fa);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
    }
    
    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    
    .room-image-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .room-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .room-card:hover .room-image {
        transform: scale(1.05);
    }
    
    .room-status-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8em;
        font-weight: 600;
        color: #fff;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .status-available {
        background-color: rgba(40, 167, 69, 0.9);
    }
    
    .status-unavailable {
        background-color: rgba(220, 53, 69, 0.9);
    }
    
    .room-content {
        padding: 20px;
    }
    
    .room-id {
        color: #6c757d;
        font-size: 0.85em;
        font-weight: 500;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .room-type {
        font-size: 1.3em;
        color: #223324;
        font-weight: 600;
        margin-bottom: 12px;
    }
    
    .room-price {
        font-size: 1.4em;
        color: #229954;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .room-price-unit {
        font-size: 0.7em;
        color: #6c757d;
        font-weight: 400;
    }
    
    .room-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .btn-action {
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9em;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-edit {
        background-color: #007bff;
        color: white;
    }
    
    .btn-edit:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
    }
    
    .btn-delete {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
        transform: translateY(-1px);
    }
    
    /* === EMPTY STATE === */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state-icon {
        font-size: 4em;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        font-size: 1.4em;
        color: #495057;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        font-size: 1.1em;
        margin-bottom: 25px;
    }
    
    /* === ALERT STYLES === */
    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        border: 1px solid;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .alert-success {
        background-color: #d1f2d1;
        color: #155724;
        border-color: #c3e6c3;
    }
    
    .alert-success i {
        color: #28a745;
    }
    
    /* === RESPONSIVE DESIGN === */
    @media (max-width: 768px) {
        .cabin-header-info {
            flex-direction: column;
            gap: 20px;
        }
        
        .cabin-main-photo {
            width: 100%;
            max-width: 250px;
            height: 160px;
            align-self: center;
        }
        
        .rooms-grid {
            grid-template-columns: 1fr;
        }
        
        .section-header {
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }
        
        .room-actions {
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .cabin-header-card,
        .content-section {
            padding: 25px 20px;
        }
        
        .cabin-details-text h1 {
            font-size: 1.8em;
        }
        
        .meta-tag {
            font-size: 0.8em;
            padding: 4px 10px;
        }
    }
</style>
@endpush

@section('admin_content')
<div class="detail-cabin-container">
    <div class="container">
        @yield('detail_content')
    </div>
</div>
@endsection
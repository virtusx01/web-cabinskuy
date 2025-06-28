@extends('backend.admin_layout')

@section('title', 'Manajemen Admin')

@push('styles')
<style>
    /* Tambahan CSS untuk tabel dan modal */
    .card {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .table th, .table td {
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        text-align: left;
    }
    .table th {
        background-color: var(--light-green-bg);
        font-weight: 600;
        color: var(--primary-dark);
    }
    .table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn {
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
        border: none;
    }
    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }
    .btn-primary:hover {
        background-color: var(--primary-dark);
    }
    .btn-warning {
        background-color: #f39c12;
        color: white;
    }
    .btn-warning:hover {
        background-color: #e67e22;
    }
    .btn-danger {
        background-color: #e74c3c;
        color: white;
    }
    .btn-danger:hover {
        background-color: #c0392b;
    }
    .btn-sm {
        padding: 6px 10px;
        font-size: 0.85em;
    }
    .flex-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-dark);
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 1em;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(34, 153, 84, 0.2);
    }
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }
    .modal-content {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        position: relative;
        animation: fadeInScale 0.3s ease-out;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 15px;
    }
    .modal-header h3 {
        margin: 0;
        color: var(--primary-dark);
    }
    .modal-close-btn {
        background: none;
        border: none;
        font-size: 1.8em;
        cursor: pointer;
        color: #666;
    }
    .modal-close-btn:hover {
        color: var(--primary-color);
    }
    .modal-footer {
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
        margin-top: 20px;
        text-align: right;
    }
    .alert {
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 0.95em;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .error-message {
        color: #e74c3c;
        font-size: 0.85em;
        margin-top: 5px;
    }
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .form-check input[type="checkbox"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .form-check label {
        margin-bottom: 0;
    }
</style>
@endpush

@section('admin_content')
<div class="container" x-data="employeeManagement()">
    <div class="admin-header">
        <h1>Kelola Data Admin</h1>
        <p>Kelola daftar Admin dengan peran Super Admin.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="flex-between">
            <h2>Daftar Admin</h2>
            <button @click="openModal()" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Admin Baru
            </button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                    <tr>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->hp }}</td>
                        <td>
                            @if ($employee->status)
                                <span style="color: green;">Aktif</span>
                            @else
                                <span style="color: red;">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <button @click="openModal({{ $employee }})" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Admin ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data admin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showModal" class="modal-overlay" style="display: none;" @click.away="closeModal()">
        <div class="modal-content" @click.stop>
            <div class="modal-header">
                <h3 x-text="isEditMode ? 'Edit Admin' : 'Tambah Admin Baru'"></h3>
                <button @click="closeModal()" class="modal-close-btn">&times;</button>
            </div>
            <form @submit.prevent="saveEmployee()">
                <div class="form-group">
                    <label for="name">Nama Admin</label>
                    <input type="text" id="name" x-model="form.name" class="form-control" required>
                    <template x-if="errors.name"><p class="error-message" x-text="errors.name[0]"></p></template>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" x-model="form.email" class="form-control" required>
                    <template x-if="errors.email"><p class="error-message" x-text="errors.email[0]"></p></template>
                </div>
                <div class="form-group">
                    <label for="hp">Nomor HP</label>
                    <input type="text" id="hp" x-model="form.hp" class="form-control">
                    <template x-if="errors.hp"><p class="error-message" x-text="errors.hp[0]"></p></template>
                </div>
                <div class="form-group">
                    <label for="password">Password <span x-show="isEditMode">(Biarkan kosong jika tidak ingin mengubah)</span></label>
                    <input type="password" id="password" x-model="form.password" class="form-control" :required="!isEditMode">
                    <template x-if="errors.password"><p class="error-message" x-text="errors.password[0]"></p></template>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" x-model="form.password_confirmation" class="form-control" :required="!isEditMode">
                </div>
                <div class="form-check">
                    <input type="checkbox" id="status" x-model="form.status" class="form-check-input">
                    <label for="status" class="form-check-label">Aktif</label>
                </div>
                
                <div class="modal-footer">
                    <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary" x-text="isEditMode ? 'Update' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script>
    function employeeManagement() {
        return {
            showModal: false,
            isEditMode: false,
            form: {
                id: null,
                name: '',
                email: '',
                hp: '',
                password: '',
                password_confirmation: '',
                status: true,
                _method: 'POST' // Default for POST
            },
            errors: {},
            openModal(employee = null) {
                this.errors = {}; // Clear previous errors
                if (employee) {
                    this.isEditMode = true;
                    this.form.id = employee.id;
                    this.form.name = employee.name;
                    this.form.email = employee.email;
                    this.form.hp = employee.hp;
                    this.form.status = employee.status;
                    this.form.password = ''; // Clear password for security
                    this.form.password_confirmation = '';
                    this.form._method = 'PUT'; // For PUT request
                } else {
                    this.isEditMode = false;
                    this.resetForm();
                    this.form._method = 'POST'; // For POST request
                }
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
                this.resetForm();
            },
            resetForm() {
                this.form.id = null;
                this.form.name = '';
                this.form.email = '';
                this.form.hp = '';
                this.form.password = '';
                this.form.password_confirmation = '';
                this.form.status = true;
                this.form._method = 'POST';
            },
            async saveEmployee() {
                this.errors = {}; // Clear errors before new submission
                let url = this.isEditMode ? `/admin/employees/${this.form.id}` : '/admin/employees';
                let method = this.form._method;

                try {
                    const response = await fetch(url, {
                        method: 'POST', // Always POST for Laravel with _method spoofing
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422) { // Validation error
                            this.errors = data.errors;
                        } else {
                            alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                        }
                        return;
                    }

                    // Success handling
                    if (data.success) {
                        alert(data.message);
                        this.closeModal();
                        window.location.reload(); // Reload page to show updated list
                    } else {
                        alert(data.message || 'Gagal menyimpan data.');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan jaringan atau server.');
                }
            }
        }
    }
</script>
@endpush
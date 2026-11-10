@extends('layouts.app')

@section('title', 'Users & Staff - Hashmi Grains')
@section('header_title', 'Users & Role Credentials')

@section('content')
<div x-data="{
    showAddUserModal: false,
    showEditUserModal: false,
    editUser: { id: '', name: '', email: '', phone: '', role: 'cashier', is_active: 1, password: '' },
    openEdit(id, name, email, phone, role, is_active) {
        this.editUser = { id, name, email, phone: phone || '', role, is_active: is_active ? 1 : 0, password: '' };
        this.showEditUserModal = true;
    }
}">
    <!-- Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <p style="color: var(--gray-600); font-size: 0.9rem;">Manage store cashiers and manager accounts with secure login access.</p>
        </div>
        <button @click="showAddUserModal = true" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Add New User
        </button>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: {{ $u->role === 'admin' ? '#16a34a' : '#d97706' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div style="font-weight: 700; color: var(--gray-900);">{{ $u->name }}</div>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->phone ?: '-' }}</td>
                            <td>
                                <span class="status-badge {{ $u->role === 'admin' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $u->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--gray-600);">
                                {{ $u->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.35rem;">
                                    <button 
                                        type="button"
                                        @click.stop="openEdit({{ $u->id }}, @js($u->name), @js($u->email), @js($u->phone ?? ''), @js($u->role), {{ $u->is_active ? 1 : 0 }})" 
                                        class="btn btn-outline btn-sm" 
                                        title="Edit"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if($users->count() > 1 && $u->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Delete this user?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444;" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Add User -->
    <div class="modal-overlay" x-show="showAddUserModal" style="display: none;" @click.self="showAddUserModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-user-plus" style="color: #16a34a;"></i>
                    <span>Create User Account</span>
                </h3>
                <button type="button" @click="showAddUserModal = false" style="background:none;border:none;font-size:1.2rem;cursor:pointer;">&times;</button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Ali Ahmed" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. ali@hashmigrains.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="03001234567">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-control" required>
                                <option value="cashier">Store Cashier</option>
                                <option value="admin">Admin Manager</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showAddUserModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit User -->
    <div class="modal-overlay" x-show="showEditUserModal" style="display: none;" @click.self="showEditUserModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-user-pen" style="color: #0284c7;"></i>
                    <span>Edit User Account</span>
                </h3>
                <button type="button" @click="showEditUserModal = false" style="background:none;border:none;font-size:1.2rem;cursor:pointer;">&times;</button>
            </div>
            <form :action="'{{ url('/users') }}/' + editUser.id" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" x-model="editUser.name" class="form-control" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" x-model="editUser.email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" x-model="editUser.phone" class="form-control">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Role *</label>
                            <select name="role" x-model="editUser.role" class="form-control" required>
                                <option value="cashier">Store Cashier</option>
                                <option value="admin">Admin Manager</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status *</label>
                            <select name="is_active" x-model="editUser.is_active" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Disabled</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave empty if not changing">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showEditUserModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

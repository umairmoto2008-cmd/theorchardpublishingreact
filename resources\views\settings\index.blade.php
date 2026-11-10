@extends('layouts.app')

@section('title', 'Shop Settings - Hashmi Grains')
@section('header_title', 'Store & Receipt Configurations')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header" style="background: #ecfdf5;">
            <h3 class="card-title">
                <i class="fa-solid fa-store" style="color: #0f5132;"></i>
                <span>Hashmi Grains Store Settings</span>
            </h3>
        </div>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Store Name *</label>
                        <input type="text" name="store_name" value="{{ $settings['store_name'] }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Store Tagline / Slogan</label>
                        <input type="text" name="store_tagline" value="{{ $settings['store_tagline'] }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Shop Address (Bahria Town Lahore) *</label>
                    <input type="text" name="store_address" value="{{ $settings['store_address'] }}" class="form-control" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Contact Phone Number</label>
                        <input type="text" name="store_phone" value="{{ $settings['store_phone'] }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Official WhatsApp Number (without + or 0)</label>
                        <input type="text" name="store_whatsapp" value="{{ $settings['store_whatsapp'] }}" class="form-control" placeholder="e.g. 923001234567">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Currency Symbol *</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Default Low Stock Alert Quantity *</label>
                        <input type="number" step="any" min="1" name="default_alert_quantity" value="{{ $settings['default_alert_quantity'] }}" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Thermal Receipt Header Information</label>
                    <textarea name="receipt_header" rows="3" class="form-control">{{ $settings['receipt_header'] }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Thermal Receipt Footer Policy & Thank You Note</label>
                    <textarea name="receipt_footer" rows="3" class="form-control">{{ $settings['receipt_footer'] }}</textarea>
                </div>
            </div>

            <div class="card-footer" style="padding: 1.25rem; display: flex; justify-content: flex-end; background: #fafafa; border-top: 1px solid var(--gray-200);">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Shop Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

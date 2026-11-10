@extends('layouts.app')

@section('title', 'Bulk Add Products - Hashmi Grains')
@section('header_title', '⚡ Fast Bulk Product Entry')

@section('content')
<div class="card" x-data="{
    rows: [
        { name: '', category_id: '{{ $categories->first()->id ?? 1 }}', unit: 'Kg', purchase_price: '', sale_price: '', stock_quantity: 10, alert_quantity: 5, barcode: '' },
        { name: '', category_id: '{{ $categories->first()->id ?? 1 }}', unit: 'Kg', purchase_price: '', sale_price: '', stock_quantity: 10, alert_quantity: 5, barcode: '' },
        { name: '', category_id: '{{ $categories->first()->id ?? 1 }}', unit: 'Kg', purchase_price: '', sale_price: '', stock_quantity: 10, alert_quantity: 5, barcode: '' },
        { name: '', category_id: '{{ $categories->first()->id ?? 1 }}', unit: 'Kg', purchase_price: '', sale_price: '', stock_quantity: 10, alert_quantity: 5, barcode: '' },
        { name: '', category_id: '{{ $categories->first()->id ?? 1 }}', unit: 'Kg', purchase_price: '', sale_price: '', stock_quantity: 10, alert_quantity: 5, barcode: '' }
    ],
    addRow() {
        this.rows.push({ name: '', category_id: '{{ $categories->first()->id ?? 1 }}', unit: 'Kg', purchase_price: '', sale_price: '', stock_quantity: 10, alert_quantity: 5, barcode: '' });
    },
    removeRow(index) {
        if (this.rows.length > 1) {
            this.rows.splice(index, 1);
        }
    }
}">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-table-list" style="color: #16a34a;"></i>
            <span>Add Multiple Products at Once</span>
        </h3>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" @click="addRow()" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-plus"></i> Add Row
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">Cancel</a>
        </div>
    </div>

    <form action="{{ route('products.bulk_store') }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th style="min-width: 220px;">Product Name *</th>
                        <th style="min-width: 160px;">Category *</th>
                        <th style="min-width: 100px;">Unit *</th>
                        <th style="min-width: 120px;">Cost (Rs)</th>
                        <th style="min-width: 120px;">Sale Price (Rs) *</th>
                        <th style="min-width: 110px;">Stock</th>
                        <th style="min-width: 140px;">Barcode (Optional)</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in rows" :key="index">
                        <tr>
                            <td x-text="index + 1" style="font-weight: 700; color: var(--gray-400);"></td>
                            <td>
                                <input type="text" :name="'products[' + index + '][name]'" x-model="row.name" class="form-control" placeholder="e.g. Desi Daal Mash" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;">
                            </td>
                            <td>
                                <select :name="'products[' + index + '][category_id]'" x-model="row.category_id" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select :name="'products[' + index + '][unit]'" x-model="row.unit" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;">
                                    <option value="Kg">Kg</option>
                                    <option value="Gram">Gram</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Bottle">Bottle</option>
                                    <option value="Can">Can</option>
                                    <option value="Jar">Jar</option>
                                    <option value="Piece">Piece</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="any" min="0" :name="'products[' + index + '][purchase_price]'" x-model="row.purchase_price" placeholder="0" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;">
                            </td>
                            <td>
                                <input type="number" step="any" min="0" :name="'products[' + index + '][sale_price]'" x-model="row.sale_price" placeholder="0" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.85rem; font-weight: 700;">
                            </td>
                            <td>
                                <input type="number" step="any" min="0" :name="'products[' + index + '][stock_quantity]'" x-model="row.stock_quantity" placeholder="10" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;">
                            </td>
                            <td>
                                <input type="text" :name="'products[' + index + '][barcode]'" x-model="row.barcode" placeholder="Auto" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.85rem; font-family: monospace;">
                            </td>
                            <td>
                                <button type="button" @click="removeRow(index)" class="btn btn-outline btn-sm" style="color: #ef4444; border:none; padding: 4px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="card-footer" style="padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; background: #fafafa; border-top: 1px solid var(--gray-200);">
            <button type="button" @click="addRow()" class="btn btn-outline">
                <i class="fa-solid fa-plus"></i> Add More Rows
            </button>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-floppy-disk"></i> Save All Products
            </button>
        </div>
    </form>
</div>
@endsection

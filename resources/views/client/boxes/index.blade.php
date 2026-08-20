@extends('layouts.app')

@section('title', 'Boxes Management')
@section('header', 'Boxes/Batch Management')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">My Boxes/Batches</h3>
        <a href="{{ route('boxes.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
            <i class="fas fa-plus mr-2"></i> Register New Batch
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shipper</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Boxes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documents</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($boxes as $box)
                <tr>
                    <td class="px-6 py-4 font-mono text-sm">{{ $box->batch_number }}</td>
                    <td class="px-6 py-4">{{ $box->invoice_number }}</td>
                    <td class="px-6 py-4">{{ $box->shipper_name }}</td>
                    <td class="px-6 py-4">Box {{ $box->box_number }}/{{ $box->total_boxes }}</td>
                    <td class="px-6 py-4">{{ $box->warehouse->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $box->status }}">
                            {{ ucfirst(str_replace('_', ' ', $box->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($box->invoice_document || $box->packing_list_document || $box->insurance_document)
                        <button onclick="viewDocuments({{ $box->id }})" class="text-purple-500 hover:text-purple-700">
                            <i class="fas fa-file-alt mr-1"></i> View Docs
                        </button>
                        @else
                        <span class="text-gray-400 text-sm">No documents</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="viewQR({{ $box->id }})" class="text-blue-500 hover:text-blue-700 mr-2" title="View QR Code">
                            <i class="fas fa-qrcode"></i>
                        </button>
                        <button onclick="printLabel({{ $box->id }})" class="text-green-500 hover:text-green-700 mr-2" title="Print Label">
                            <i class="fas fa-print"></i>
                        </button>
                        <a href="{{ route('boxes.track', $box->id) }}" class="text-orange-500 hover:text-orange-700" title="Track Box">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No boxes registered yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $boxes->links() }}
    </div>
</div>

<!-- QR Modal -->
<div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Box QR Code</h3>
            <button onclick="closeQRModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div id="qrContent" class="text-center">
            <div id="qrCodeImage"></div>
            <div id="qrData" class="mt-4 text-left text-sm"></div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="printQR()" class="bg-orange-500 text-white px-4 py-2 rounded-lg">Print</button>
        </div>
    </div>
</div>

<!-- Document Modal -->
<div id="docModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Shipment Documents</h3>
            <button onclick="closeDocModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div id="docContent" class="space-y-3"></div>
    </div>
</div>

<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-in_transit { background: #dbeafe; color: #2563eb; }
    .status-delivered { background: #d1fae5; color: #059669; }
    .status-received { background: #d1fae5; color: #059669; }
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    let currentBox = null;
    
    // QR Code Functions
    function viewQR(boxId) {
        fetch(`/boxes/${boxId}/qr`)
            .then(response => response.json())
            .then(data => {
                currentBox = data;
                document.getElementById('qrCodeImage').innerHTML = '';
                new QRCode(document.getElementById('qrCodeImage'), {
                    text: data.qr_data,
                    width: 200,
                    height: 200
                });
                document.getElementById('qrData').innerHTML = `
                    <p><strong>Batch:</strong> ${data.batch_number}</p>
                    <p><strong>Box:</strong> ${data.box_number}/${data.total_boxes}</p>
                    <p><strong>Invoice:</strong> ${data.invoice_number}</p>
                    <p><strong>Shipper:</strong> ${data.shipper_name}</p>
                    <p><strong>Warehouse:</strong> ${data.warehouse_name}</p>
                    <p><strong>Entry Date:</strong> ${data.entry_date}</p>
                    <p><strong>Status:</strong> ${data.status}</p>
                `;
                document.getElementById('qrModal').classList.remove('hidden');
                document.getElementById('qrModal').classList.add('flex');
            });
    }
    
    function closeQRModal() {
        document.getElementById('qrModal').classList.add('hidden');
        document.getElementById('qrModal').classList.remove('flex');
    }
    
    function printQR() {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Box QR Code - ${currentBox.batch_number}</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                    .qr-container { margin: 20px auto; }
                    .info { text-align: left; margin: 20px auto; width: 300px; }
                </style>
            </head>
            <body>
                <div class="qr-container">${document.getElementById('qrCodeImage').innerHTML}</div>
                <div class="info">${document.getElementById('qrData').innerHTML}</div>
                <script>window.print();<\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
    
    function printLabel(boxId) {
        window.open(`/boxes/${boxId}/print`, '_blank');
    }
    
    // Document Functions
    function viewDocuments(boxId) {
        fetch(`/boxes/${boxId}/documents`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                
                if (data.invoice_document) {
                    html += `
                        <div class="border rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <i class="fas fa-file-invoice text-orange-500 mr-2"></i>
                                <span class="font-medium">Invoice Document</span>
                            </div>
                            <a href="${data.invoice_document}" target="_blank" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-download mr-1"></i> View
                            </a>
                        </div>
                    `;
                }
                
                if (data.packing_list_document) {
                    html += `
                        <div class="border rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <i class="fas fa-boxes text-orange-500 mr-2"></i>
                                <span class="font-medium">Packing List</span>
                            </div>
                            <a href="${data.packing_list_document}" target="_blank" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-download mr-1"></i> View
                            </a>
                        </div>
                    `;
                }
                
                if (data.insurance_document) {
                    html += `
                        <div class="border rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <i class="fas fa-shield-alt text-orange-500 mr-2"></i>
                                <span class="font-medium">Insurance Document</span>
                            </div>
                            <a href="${data.insurance_document}" target="_blank" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-download mr-1"></i> View
                            </a>
                        </div>
                    `;
                }
                
                if (Array.isArray(data.other_documents)) {
                    data.other_documents.forEach(doc => {
                        html += `
                            <div class="border rounded-lg p-3 flex items-center justify-between">
                                <div>
                                    <i class="fas fa-file-alt text-orange-500 mr-2"></i>
                                    <span class="font-medium">Other Document</span>
                                </div>
                                <a href="${doc}" target="_blank" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-download mr-1"></i> View
                                </a>
                            </div>
                        `;
                    });
                }
                
                if (html === '') {
                    html = '<p class="text-gray-500 text-center py-4">No documents available for this shipment</p>';
                }
                
                document.getElementById('docContent').innerHTML = html;
                document.getElementById('docModal').classList.remove('hidden');
                document.getElementById('docModal').classList.add('flex');
            });
    }
    
    function closeDocModal() {
        document.getElementById('docModal').classList.add('hidden');
        document.getElementById('docModal').classList.remove('flex');
    }
</script>
@endsection

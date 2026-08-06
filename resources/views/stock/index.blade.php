@extends('layouts.app')

@section('title', 'Manage Stocks')

@section('header', 'Manage Stocks')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">My Stock Batches</h3>
            <p class="text-sm text-gray-500 mt-1">Manage all your stock batches with QR codes</p>
        </div>
        <a href="{{ route('stock.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Stock
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Stock Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Batches</p>
                    <p class="text-2xl font-bold">{{ $stocks->total() }}</p>
                </div>
                <i class="fas fa-boxes text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">In Stock</p>
                    <p class="text-2xl font-bold">{{ $stocks->where('status', 'in_stock')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Partial/Dispatched</p>
                    <p class="text-2xl font-bold">{{ $stocks->whereIn('status', ['partial', 'dispatched'])->count() }}</p>
                </div>
                <i class="fas fa-truck text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Quantity</p>
                    <p class="text-2xl font-bold">{{ $stocks->sum('remaining_quantity') }}</p>
                </div>
                <i class="fas fa-cubes text-3xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-4 flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="searchInput" placeholder="Search by product, batch ID, or SKU..." 
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
        </div>
        <select id="statusFilter" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
            <option value="">All Status</option>
            <option value="in_stock">In Stock</option>
            <option value="partial">Partial</option>
            <option value="dispatched">Dispatched</option>
            <option value="expired">Expired</option>
        </select>
        <button onclick="clearFilters()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
            <i class="fas fa-eraser mr-1"></i> Clear
        </button>
    </div>

    <!-- Stock Table -->
    @if($stocks->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No stock batches found</p>
            <p class="text-gray-400 text-sm mt-2">Click "Add New Stock" to create your first batch</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full" id="stockTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Info</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Boxes</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($stocks as $stock)
                    <tr class="hover:bg-gray-50 transition stock-row" 
                        data-product="{{ strtolower($stock->product_name) }}"
                        data-batch="{{ strtolower($stock->batch_id) }}"
                        data-sku="{{ strtolower($stock->sku) }}"
                        data-status="{{ $stock->status }}">
                        <td class="px-4 py-3">
                            <div class="text-sm font-mono font-medium text-gray-900">{{ $stock->batch_id }}</div>
                            <div class="text-xs text-gray-500">{{ $stock->sku }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $stock->product_name }}</div>
                            <div class="text-xs text-gray-500">{{ $stock->unit }}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">
                            {{ $stock->number_of_boxes }} × {{ $stock->quantity_per_box }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">
                            {{ number_format($stock->total_quantity) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-semibold {{ $stock->remaining_quantity < $stock->total_quantity * 0.2 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($stock->remaining_quantity) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($stock->status == 'in_stock')
                                <span class="status-badge status-approved">In Stock</span>
                            @elseif($stock->status == 'partial')
                                <span class="status-badge status-on_the_way">Partial</span>
                            @elseif($stock->status == 'dispatched')
                                <span class="status-badge status-assigned">Dispatched</span>
                            @else
                                <span class="status-badge status-rejected">Expired</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="showQRModal('{{ $stock->id }}', '{{ $stock->batch_id }}')" 
                                    class="text-orange-500 hover:text-orange-600 transition" title="View QR Code">
                                <i class="fas fa-qrcode text-xl"></i>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('stock.show', $stock->id) }}" 
                                   class="text-blue-500 hover:text-blue-700" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('stock.download-qr', $stock->id) }}" 
                                   class="text-green-500 hover:text-green-700" title="Download QR Code">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button onclick="copyBatchInfo('{{ $stock->batch_id }}', '{{ $stock->product_name }}', '{{ $stock->remaining_quantity }}')" 
                                        class="text-gray-500 hover:text-gray-700" title="Copy Batch Info">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $stocks->links() }}
        </div>
    @endif
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">QR Code</h3>
            <button onclick="closeQRModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 text-center">
            <div id="qrCodeImage" class="mb-4 flex justify-center">
                <!-- QR code will load here -->
            </div>
            <p id="qrBatchId" class="text-sm font-mono text-gray-600 mb-2"></p>
            <div class="flex justify-center space-x-3 mt-4">
                <a id="downloadQrBtn" href="#" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                    <i class="fas fa-download mr-2"></i> Download QR
                </a>
                <button onclick="closeQRModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Search and Filter functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const rows = document.querySelectorAll('.stock-row');
        
        rows.forEach(row => {
            const product = row.dataset.product || '';
            const batch = row.dataset.batch || '';
            const sku = row.dataset.sku || '';
            const status = row.dataset.status || '';
            
            const matchesSearch = searchTerm === '' || 
                                 product.includes(searchTerm) || 
                                 batch.includes(searchTerm) || 
                                 sku.includes(searchTerm);
            const matchesStatus = statusValue === '' || status === statusValue;
            
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    
    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        filterTable();
    }
    
    // QR Code Modal
    function showQRModal(stockId, batchId) {
        const modal = document.getElementById('qrModal');
        const qrImage = document.getElementById('qrCodeImage');
        const qrBatchId = document.getElementById('qrBatchId');
        const downloadBtn = document.getElementById('downloadQrBtn');
        
        // Set QR code URL
        const qrUrl = `/stock/${stockId}/qr-code`;
        qrImage.innerHTML = `<img src="${qrUrl}" alt="QR Code" class="w-48 h-48 mx-auto border p-2 rounded">`;
        qrBatchId.textContent = batchId;
        downloadBtn.href = `/stock/${stockId}/download-qr`;
        
        modal.classList.remove('hidden');
    }
    
    function closeQRModal() {
        document.getElementById('qrModal').classList.add('hidden');
    }
    
    // Copy batch info to clipboard
    function copyBatchInfo(batchId, productName, quantity) {
        const text = `Batch ID: ${batchId}\nProduct: ${productName}\nRemaining Quantity: ${quantity}`;
        navigator.clipboard.writeText(text);
        
        // Show temporary notification
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.innerHTML = '<i class="fas fa-check mr-2"></i> Batch info copied!';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeQRModal();
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('qrModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQRModal();
        }
    });
</script>

<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-approved { background: #d1fae5; color: #059669; }
    .status-on_the_way { background: #fed7aa; color: #ea580c; }
    .status-assigned { background: #dbeafe; color: #2563eb; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
</style>
@endsection
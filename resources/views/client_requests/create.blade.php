<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Request Warehouse Space</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('my-requests.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Required Space -->
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Required space (cubic meters)</label>
                        <input type="number" step="0.01" name="required_space" id="required_space" value="{{ old('required_space') }}" class="w-full border-gray-300 rounded" required>
                        @error('required_space') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Duration -->
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Duration (months)</label>
                        <input type="number" name="duration_months" value="{{ old('duration_months') }}" class="w-full border-gray-300 rounded" required>
                        @error('duration_months') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Vehicle & Phone -->
                    <div class="mb-4">
                        <label>Vehicle number (for tracking)</label>
                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}" class="w-full border-gray-300 rounded">
                    </div>
                    <div class="mb-4">
                        <label>Phone number (for GPS tracking)</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full border-gray-300 rounded">
                    </div>

                    <!-- Preferred Warehouse Dropdown -->
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Preferred Warehouse (Optional)</label>
                        <select name="preferred_warehouse_id" id="preferred_warehouse" class="w-full border-gray-300 rounded">
                            <option value="">-- Admin will assign the best option --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" data-price="{{ $wh->price_per_unit }}" data-unit="{{ $wh->price_unit_type }}">
                                    {{ $wh->name }} (Available: {{ number_format($wh->available_space, 2) }} m³)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estimated Monthly Rent -->
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Estimated Monthly Rent (NPR)</label>
                        <input type="text" id="estimated_rent" class="w-full border-gray-300 rounded bg-gray-100" readonly>
                    </div>

                    <!-- Map (OpenStreetMap) -->
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Warehouse Locations (Click on map to select)</label>
                        <div id="warehouseMap" style="height: 300px; margin-top: 10px;"></div>
                    </div>

                    <!-- Equipment Needed -->
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="needs_equipment" value="1" id="needs_equipment">
                            <span class="ml-2">I need heavy equipment (crane, forklift, etc.) for loading/unloading</span>
                        </label>
                    </div>
                    <div id="equipment_notes_div" style="display:none;" class="mb-4">
                        <label class="block font-medium">Describe equipment needed (type, capacity, special requirements)</label>
                        <textarea name="equipment_notes" rows="3" class="w-full border-gray-300 rounded"></textarea>
                    </div>

                    <!-- File Uploads -->
                    <div class="mb-4">
                        <label>Invoice (PDF/Image)</label>
                        <input type="file" name="invoice">
                        @error('invoice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label>Packing list (PDF/Image)</label>
                        <input type="file" name="packing_list">
                    </div>
                    <div class="mb-4">
                        <label>Insurance document (PDF/Image)</label>
                        <input type="file" name="insurance">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Leaflet Map Script -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Estimated rent calculation
        const requiredSpace = document.getElementById('required_space');
        const warehouseSelect = document.getElementById('preferred_warehouse');
        const estimatedRent = document.getElementById('estimated_rent');

        function updateEstimatedRent() {
            const selectedOption = warehouseSelect.options[warehouseSelect.selectedIndex];
            const pricePerUnit = selectedOption ? parseFloat(selectedOption.getAttribute('data-price')) : null;
            const space = parseFloat(requiredSpace.value);
            if (pricePerUnit && !isNaN(space) && space > 0) {
                let rent = pricePerUnit * space;
                estimatedRent.value = 'Rs. ' + rent.toFixed(2);
            } else {
                estimatedRent.value = '';
            }
        }

        requiredSpace.addEventListener('input', updateEstimatedRent);
        warehouseSelect.addEventListener('change', updateEstimatedRent);

        // Equipment notes toggle
        document.getElementById('needs_equipment').addEventListener('change', function() {
            document.getElementById('equipment_notes_div').style.display = this.checked ? 'block' : 'none';
        });

        // Map: show warehouses as markers (if any)
        var warehouses = @json($warehouses);
        var map = L.map('warehouseMap').setView([27.7172, 85.3240], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        warehouses.forEach(function(wh) {
            if (wh.latitude && wh.longitude) {
                var marker = L.marker([wh.latitude, wh.longitude]).addTo(map);
                marker.bindPopup("<b>" + wh.name + "</b><br>Available: " + wh.available_space + " m³");
                marker.on('click', function() {
                    document.getElementById('preferred_warehouse').value = wh.id;
                    updateEstimatedRent();
                });
            }
        });
    </script>
</x-app-layout>
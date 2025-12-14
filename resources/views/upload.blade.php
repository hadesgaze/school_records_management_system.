<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">

<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded sha dow">
        <h2 class="text-xl font-bold mb-4">Upload to Archive</h2>
        
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
        @endif

        <form action="{{ route('archive.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block font-bold mb-1">Select Category</label>
                <select id="categorySelect" name="category_id" class="w-full border p-2 rounded">
                    <option value="">-- Choose Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="dynamicFields" class="mb-4 p-4 bg-blue-50 rounded hidden">
                <h3 class="text-sm font-bold text-blue-800 mb-2 uppercase">Category Details</h3>
                </div>

            <div class="mb-4">
                <label class="block font-bold mb-1">Attach File</label>
                <input type="file" name="document" class="w-full border p-2 bg-gray-50">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload Archive</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded shadow h-fit">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Create New Category</h2>
        <form action="{{ route('category.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-bold">Category Name</label>
                <input type="text" name="name" required class="w-full border p-2 rounded">
            </div>
            
            <div class="mb-3">
                <label class="block text-sm font-bold">Define Fields</label>
                <p class="text-xs text-gray-500 mb-2">Add the fields this category requires (e.g., Author, Year).</p>
                
                <div id="fieldsWrapper">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="fields[0][name]" placeholder="Field Name (e.g. Author)" class="border p-1 w-full" required>
                        <select name="fields[0][type]" class="border p-1">
                            <option value="text">Text</option>
                            <option value="date">Date</option>
                            <option value="number">Number</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="addField()" class="text-sm text-blue-600 hover:underline">+ Add Another Field</button>
            </div>

            <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded mt-4">Save New Category</button>
        </form>
    </div>

</div>

<script>
    // Pass PHP data to JS
    const categories = @json($categories);

    const catSelect = document.getElementById('categorySelect');
    const dynamicContainer = document.getElementById('dynamicFields');

    // 1. Handle Dropdown Change
    catSelect.addEventListener('change', function() {
        const catId = this.value;
        dynamicContainer.innerHTML = ''; // Clear previous fields
        dynamicContainer.classList.add('hidden');

        if (!catId) return;

        // Find the selected category object from the JSON
        const selectedCat = categories.find(c => c.id == catId);

        if (selectedCat && selectedCat.fields.length > 0) {
            dynamicContainer.classList.remove('hidden');
            const header = document.createElement('div');
            header.innerHTML = `<p class="mb-3 text-sm">Please fill in details for <strong>${selectedCat.name}</strong>:</p>`;
            dynamicContainer.appendChild(header);

            selectedCat.fields.forEach(field => {
                const wrapper = document.createElement('div');
                wrapper.className = 'mb-3';

                const label = document.createElement('label');
                label.className = 'block text-sm font-semibold mb-1';
                label.innerText = field.field_name + (field.is_required ? ' *' : '');

                const input = document.createElement('input');
                input.type = field.field_type; // text, date, number
                input.name = `data[${field.field_slug}]`; // Stores in data array
                input.className = 'w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none';
                if(field.is_required) input.required = true;

                wrapper.appendChild(label);
                wrapper.appendChild(input);
                dynamicContainer.appendChild(wrapper);
            });
        }
    });

    // 2. Logic to add more rows in "Create Category"
    let fieldCount = 1;
    function addField() {
        const wrapper = document.getElementById('fieldsWrapper');
        const div = document.createElement('div');
        div.className = 'flex gap-2 mb-2';
        div.innerHTML = `
            <input type="text" name="fields[${fieldCount}][name]" placeholder="Field Name" class="border p-1 w-full" required>
            <select name="fields[${fieldCount}][type]" class="border p-1">
                <option value="text">Text</option>
                <option value="date">Date</option>
                <option value="number">Number</option>
            </select>
        `;
        wrapper.appendChild(div);
        fieldCount++;
    }
</script>

</body>
</html>
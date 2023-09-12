@php $editing = isset($structure) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $structure->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            >{{ old('description', ($editing ? $structure->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.number
            name="latitude"
            label="Latitude"
            :value="old('latitude', ($editing ? $structure->latitude : ''))"
            max="255"
            step="0.01"
            placeholder="Latitude"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.number
            name="longitude"
            label="Longitude"
            :value="old('longitude', ($editing ? $structure->longitude : ''))"
            max="255"
            step="0.01"
            placeholder="Longitude"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="phone"
            label="Phone"
            :value="old('phone', ($editing ? $structure->phone : ''))"
            maxlength="255"
            placeholder="Phone"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select
            name="type_structure_id"
            label="Type Structure"
            required
        >
            @php $selected = old('type_structure_id', ($editing ? $structure->type_structure_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Type Structure</option>
            @foreach($typeStructures as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="status"
            label="Status"
            :checked="old('status', ($editing ? $structure->status : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="ville_id" label="Ville" required>
            @php $selected = old('ville_id', ($editing ? $structure->ville_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Ville</option>
            @foreach($villes as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="adresse"
            label="Adresse"
            :value="old('adresse', ($editing ? $structure->adresse : ''))"
            maxlength="255"
            placeholder="Adresse"
            required
        ></x-inputs.text>
    </x-inputs.group>
</div>

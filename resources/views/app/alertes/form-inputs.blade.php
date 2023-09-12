@php $editing = isset($alerte) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="ref"
            label="Ref"
            :value="old('ref', ($editing ? $alerte->ref : ''))"
            maxlength="255"
            placeholder="Ref"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            required
            >{{ old('description', ($editing ? $alerte->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.number
            name="latitude"
            label="Latitude"
            :value="old('latitude', ($editing ? $alerte->latitude : ''))"
            max="255"
            step="0.01"
            placeholder="Latitude"
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.number
            name="longitude"
            label="Longitude"
            :value="old('longitude', ($editing ? $alerte->longitude : ''))"
            max="255"
            step="0.01"
            placeholder="Longitude"
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="type_alerte_id" label="Type Alerte" required>
            @php $selected = old('type_alerte_id', ($editing ? $alerte->type_alerte_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Type Alerte</option>
            @foreach($typeAlertes as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="etat"
            label="Etat"
            :value="old('etat', ($editing ? $alerte->etat : ''))"
            maxlength="255"
            placeholder="Etat"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="ville_id" label="Ville" required>
            @php $selected = old('ville_id', ($editing ? $alerte->ville_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Ville</option>
            @foreach($villes as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>

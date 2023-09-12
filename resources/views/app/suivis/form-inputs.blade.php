@php $editing = isset($suivi) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $suivi->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.textarea
            name="observation"
            label="Observation"
            maxlength="255"
            required
            >{{ old('observation', ($editing ? $suivi->observation : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="alerte_id" label="Alerte" required>
            @php $selected = old('alerte_id', ($editing ? $suivi->alerte_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Alerte</option>
            @foreach($alertes as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>

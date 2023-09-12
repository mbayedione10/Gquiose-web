@php $editing = isset($typeStructure) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $typeStructure->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="icon"
            label="Icon"
            :value="old('icon', ($editing ? $typeStructure->icon : ''))"
            maxlength="255"
            placeholder="Icon"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="status"
            label="Status"
            :checked="old('status', ($editing ? $typeStructure->status : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>
</div>

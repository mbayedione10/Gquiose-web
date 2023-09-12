@php $editing = isset($question) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $question->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="reponse"
            label="Reponse"
            :value="old('reponse', ($editing ? $question->reponse : ''))"
            maxlength="255"
            placeholder="Reponse"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="option1"
            label="Option1"
            :value="old('option1', ($editing ? $question->option1 : ''))"
            maxlength="255"
            placeholder="Option1"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="status"
            label="Status"
            :checked="old('status', ($editing ? $question->status : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="thematique_id" label="Thematique" required>
            @php $selected = old('thematique_id', ($editing ? $question->thematique_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Thematique</option>
            @foreach($thematiques as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>

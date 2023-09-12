@php $editing = isset($response) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="question_id" label="Question" required>
            @php $selected = old('question_id', ($editing ? $response->question_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Question</option>
            @foreach($questions as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="reponse"
            label="Reponse"
            :value="old('reponse', ($editing ? $response->reponse : ''))"
            maxlength="255"
            placeholder="Reponse"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="isValid"
            label="Is Valid"
            :checked="old('isValid', ($editing ? $response->isValid : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="utilisateur_id" label="Utilisateur" required>
            @php $selected = old('utilisateur_id', ($editing ? $response->utilisateur_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Utilisateur</option>
            @foreach($utilisateurs as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>

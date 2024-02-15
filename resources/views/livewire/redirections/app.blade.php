<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div @class([
   "lg:col-span-3"=>($action=='index'),
    "lg:col-span-2 opacity-20"=>($action=='edit'),
])>
        <x-hailo::table :table="$redirections_table"/>
        {{--<livewire:redirections-table :wire:key="'redirections-table'" />--}}
    </div>
    <div @class([
   ""=>($action=='index'),
    "lg:col-span-2"=>($action=='edit'),
])>
        <x-hailo::form :data="$formData[$redirection_form->getName()]" :form="$redirection_form" :validation="$validation_errors[$redirection_form->getName()]??null" />
    </div>
</div>

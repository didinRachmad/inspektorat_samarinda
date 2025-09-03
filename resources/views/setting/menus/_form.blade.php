@php
    $menu = $menu ?? null;
@endphp

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            {{-- Field Title --}}
            <x-form.input label="Title" name="title" :value="$menu?->title ?? ''" required />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            {{-- Field Parent Menu --}}
            <x-form.select class="col-md-4" label="Parent Menu" name="parent_id" :options="$parentMenus->pluck('title', 'id')->toArray()" :value="$menu->parent_id ?? ''" />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            {{-- Field Route --}}
            <x-form.input label="Route" name="route" :value="$menu?->route ?? ''" />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            {{-- Field Icon --}}
            <x-form.input label="Icon" name="icon" :value="$menu?->icon ?? ''" />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            {{-- Field Order --}}
            <x-form.input label="Order" name="order" :value="$menu?->order ?? ''" required />
        </div>
    </div>
</div>

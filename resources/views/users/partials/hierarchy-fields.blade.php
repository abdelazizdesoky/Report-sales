@php
    $currentLevel = old('hierarchy_level', $hierarchyUser->hierarchy_level ?? '');
    $currentValue = old('hierarchy_value', $hierarchyUser->hierarchy_value ?? '');
    $selectClasses = 'mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/30 transition-all duration-200 shadow-sm';
@endphp

<div class="space-y-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800"
     x-data="{ level: {{ Js::from($currentLevel) }} }">

    <div>
        <x-input-label for="hierarchy_level" value="الموقع في هيكل المبيعات" />
        <select id="hierarchy_level" name="hierarchy_level" x-model="level" class="{{ $selectClasses }}">
            <option value="">-- بدون ربط --</option>
            @foreach($hierarchyLabels as $key => $label)
                <option value="{{ $key }}" @selected($currentLevel === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">
            يحدد البيانات التي يراها المستخدم. المشرف يرى مناديبه، ومدير المبيعات يرى مشرفيه ومناديبهم.
            الأدمن والمدير العام والمنسق يرون كل البيانات بغض النظر عن هذا الحقل.
        </p>
        <x-input-error :messages="$errors->get('hierarchy_level')" class="mt-2" />
    </div>

    @foreach($hierarchyLabels as $key => $label)
        @php $options = $hierarchyOptions[$key] ?? []; @endphp
        <div x-show="level === '{{ $key }}'" x-cloak>
            <x-input-label :for="'hierarchy_value_'.$key" :value="'اختر '.$label" />

            @if(empty($options))
                <input type="text"
                       id="hierarchy_value_{{ $key }}"
                       name="hierarchy_value"
                       x-bind:disabled="level !== '{{ $key }}'"
                       value="{{ $currentLevel === $key ? $currentValue : '' }}"
                       class="{{ $selectClasses }}">
                <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">
                    تعذر جلب القائمة من SQL Server. أدخل القيمة يدوياً كما هي في {{ \App\Services\SalesHierarchyService::SOURCE_TABLE }}.
                </p>
            @else
                <select id="hierarchy_value_{{ $key }}"
                        name="hierarchy_value"
                        x-bind:disabled="level !== '{{ $key }}'"
                        class="{{ $selectClasses }}">
                    <option value="">-- اختر --</option>
                    @foreach($options as $value => $display)
                        <option value="{{ $value }}" @selected($currentLevel === $key && (string) $currentValue === (string) $value)>
                            {{ $display }}
                        </option>
                    @endforeach
                </select>
            @endif

            <x-input-error :messages="$errors->get('hierarchy_value')" class="mt-2" />
        </div>
    @endforeach
</div>

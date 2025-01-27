<div class="form-group">
    <label class="form-control-label">Неполный осмотр:</label>
    <article>
        <a class="text-small" href="{{ route('forms.index', ['type' => $type_anketa, 'is_dop' => !$is_dop]) }}">
            <input onchange="this.parentNode.click()" type="checkbox" @if($is_dop) checked @endif @if($id ?? null) disabled @endif>
            {{ isset($is_dop) ? ($is_dop ? 'Выкл' : 'Вкл') : 'Вкл' }}
        </a>
        <input type="hidden" name="is_dop" value="{{ $is_dop ?? 0 }}">
    </article>
</div>

<div class="form-group">
    <label>Дата осмотра до</label>
    <input type="date"
           value="{{ request()->get('TO_date', now()->subMonth()->endOfMonth()->format('Y-m-d')) }}"
           name="TO_date"
           class="form-control"/>
</div>

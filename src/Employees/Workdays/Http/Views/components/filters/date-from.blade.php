<div class="form-group">
    <label>Дата смены от</label>
    <input type="date"
           value="{{ request()->get('date', now()->subMonth()->startOfMonth()->format('Y-m-d')) }}"
           name="date"
           class="form-control"/>
</div>

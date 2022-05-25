<div class="form-group row">
    <label class="col-md-3 form-control-label">Режим ввода ПЛ:</label>
    <article class="col-md-9">
        <a class="text-small" href="{{ route('forms', isset($_GET['is_dop']) ? ['type' => $type_anketa, 'is_dop' => $_GET['is_dop'] === "1" ? 0 : 1] : ['type' => $type_anketa, 'is_dop' => 1]) }}">{{ isset($_GET['is_dop']) ? ($_GET['is_dop'] === "0" ? "+ Включить" : '- Выключить') : '+ Включить' }}</a>
        <input type="hidden" name="is_dop" value="{{ isset($_GET['is_dop']) ? ($_GET['is_dop'] === "1") : 0 }}">
    </article>
</div>

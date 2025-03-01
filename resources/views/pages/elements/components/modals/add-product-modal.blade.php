<div data-field="name" class="form-group">
    <label><b class="text-danger text-bold">* </b>Название</label>
    <input value="" type="text" required="required" name="name"
           data-label="Название" placeholder="Название"
           data-field="Product_name" class="form-control ">
</div>
<div data-field="type_product" class="form-group">
    <label><b class="text-danger text-bold">* </b>Тип</label>
    <select name="type_product"
            required="required"
            data-label="Тип"
            data-field="Product_type_product"
            class="js-chosen"
    >
        @foreach($fields['type_product']['values'] as $nameOfTypeProduct)
            <option value="{{ $nameOfTypeProduct }}">
                {{ $nameOfTypeProduct }}
            </option>
        @endforeach
    </select>
</div>
<div data-field="essence" class="form-group">
    <label>Сущности</label>
    <select name="essence"
            data-label="Сущности"
            data-field="Product_essence"
            class="filled-select2 filled-select"
    >
        <option value="">Не установлено</option>
        @foreach(\App\Product::$essence as $essenceKey => $essenceName)
            <option value="{{ $essenceKey }}">
                {{ $essenceName }}
            </option>
        @endforeach
    </select>
</div>
<div data-field="unit" class="form-group">
    <label><b class="text-danger text-bold">* </b>
        Ед.изм.</label>
    <input value="" type="text" required="required" name="unit"
           data-label="Ед.изм." placeholder="Ед.изм." data-field="Product_unit"
           class="form-control ">
</div>
<div data-field="price_unit" class="form-group">
    <label><b class="text-danger text-bold">* </b>
        Стоимость за единицу</label>
    <input value="" type="number" required="required"
           name="price_unit" data-label="Стоимость за единицу"
           placeholder="Стоимость за единицу"
           data-field="Product_price_unit" class="form-control ">
</div>
<div data-field="type_anketa" class="form-group">
    <label><b class="text-danger text-bold">* </b> Реестр</label>
    <select name="type_anketa" required="required" data-label="Реестр"
            data-field="Product_type_anketa" class="filled-select2 filled-select">
        <option value="">Не установлено</option>
        <option value="bdd">
            БДД
        </option>
        <option value="medic">
            Медицинский
        </option>
        <option value="tech">
            Технический
        </option>
        <option value="pechat_pl">
            Печать ПЛ
        </option>
        <option value="report_cart">
            Отчеты с карт
        </option>
    </select>
</div>
<div data-field="type_view" class="form-group">
    <label><b class="text-danger text-bold">* </b>Тип осмотра</label>
    <select multiple="multiple" name="type_view[]" required="required"
            data-label="Тип осмотра" data-field="Product_type_view"
            class="filled-select2 filled-select">
        <option value="">Не установлено</option>
        <option value="Предрейсовый/Предсменный">
            Предрейсовый/Предсменный
        </option>
        <option value="Послерейсовый/Послесменный">
            Послерейсовый/Послесменный
        </option>
        <option value="БДД">
            БДД
        </option>
        <option value="Отчёты с карт">
            Отчёты с карт
        </option>
        <option value="Учет ПЛ">
            Учет ПЛ
        </option>
        <option value="Печать ПЛ">
            Печать ПЛ
        </option>
    </select>
</div>

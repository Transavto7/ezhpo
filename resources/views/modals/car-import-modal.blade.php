<div id="car-import-modal" tabindex="-1" role="dialog" aria-labelledby="car-import-modal"
     class="modal fade text-left" style="display: none;" aria-modal="true">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Импортирование автомобилей</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <import-modal
                type="{{ \App\Enums\ElementType::CAR }}"
                import-url="{{ route('importElement') }}"
            >
                <p>Выберите файл соответствующий <a href="{{ Storage::disk('examples')->url('cars_example.xlsx') }}">шаблону</a> и удовлетворяющий правилам:</p>
                <ul>
                    <li>A: Название компании (Пример)</li>
                    <li>B: Инн компании - обязательное поле (7727563778)</li>
                    <li>С: Гос номер - обязательное поле (А999ТЕ77)</li>
                    <li>D: Марка и модель (ГАЗ 330214)</li>
                    <li>E: Категория ТС (С - грузовые т\с от 3.5 тн)</li>
                    <li>F: Прицеп (если есть) (Нет)</li>
                    <li>G: Дата ТО (12.12.2023)</li>
                    <li>H: Дата ОСАГО (12.12.2023)</li>
                    <li>I: Срок действия СКЗИ (12.12.2024)</li>
                </ul>
            </import-modal>
        </div>
    </div>
</div>

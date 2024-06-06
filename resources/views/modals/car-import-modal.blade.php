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
                <p>Изучите <a target="_blank" href="{{ Storage::disk('examples')->url('import_instruction.pdf') }}">инструкцию</a> по импорту данных.</p>
                <p>Выберите файл соответствующий <a href="{{ Storage::disk('examples')->url('cars_example.xlsx') }}">шаблону</a> и удовлетворяющий правилам:</p>
                <ul>
                    <li>A: Название компании (Пример)</li>
                    <li>B: Инн компании - обязательное поле (7727563778)</li>
                    <li>С: Гос номер - обязательное поле (А999ТЕ77)</li>
                    <li>D: Марка и модель (ГАЗ 330214)</li>
                    <li>
                        E: Категория ТС <br>
                        <small>
                            Допустимые значения:
                            <ul>
                                @foreach(config('elements.Car.fields.type_auto.values') as $value)
                                    <li>{{ $value }}</li>
                                @endforeach
                            </ul>
                        </small>
                    </li>
                    <li>
                        F: Прицеп (если есть) <br>
                        <small>
                            Допустимые значения:
                            <ul>
                                @foreach(config('elements.Car.fields.trailer.values') as $value)
                                    <li>{{ $value }}</li>
                                @endforeach
                            </ul>
                        </small>
                    </li>
                    <li>G: Дата ТО (12.12.2023)</li>
                    <li>H: Дата ОСАГО (12.12.2023)</li>
                    <li>I: Срок действия СКЗИ (12.12.2024)</li>
                </ul>
            </import-modal>
        </div>
    </div>
</div>

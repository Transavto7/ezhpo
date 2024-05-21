<div id="driver-import-modal" tabindex="-1" role="dialog" aria-labelledby="driver-import-modal"
     class="modal fade text-left" style="display: none;" aria-modal="true">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Импортирование водителей</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <import-modal
                type="{{ \App\Enums\ElementType::DRIVER }}"
                import-url="{{ route('importElement') }}"
            >
                <p>Выберите файл соответствующий <a href="{{ Storage::disk('examples')->url('drivers_example.xlsx') }}">шаблону</a> и удовлетворяющий правилам:</p>
                <ul>
                    <li>A: Инн компании - обязательное поле (7727563778)</li>
                    <li>B: ФИО - обязательное поле (Иванов Иван Иванович)</li>
                    <li>С: Дата рождения - обязательное поле (12.12.1990)</li>
                    <li>D: Название компании (Пример)</li>
                    <li>
                        E: Пол<br>
                        <small>
                            Допустимые значения:
                            <ul>
                                @foreach(config('elements.Driver.fields.gender.values') as $value)
                                    <li>{{ $value }}</li>
                                @endforeach
                            </ul>
                        </small>
                    </li>
                    <li>F: Телефон (89999999999)</li>
                    <li>G: СНИЛС (901-821-913 81)</li>
                    <li>H: Серия/номер ВУ (99 99 999999)</li>
                    <li>I: Срок действия ВУ (12.12.2024)</li>
                </ul>
            </import-modal>
        </div>
    </div>
</div>

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
                <p>Заполните форму внимательно и нажмите кнопку "Добавить1"</p>
            </import-modal>
        </div>
    </div>
</div>

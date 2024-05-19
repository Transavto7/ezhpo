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
            <form action="{{ route('importElement') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Заполните форму внимательно и нажмите кнопку "Добавить"</p>
                    <input type="text" name="type" value="{{ \App\Enums\ElementType::CAR }}" hidden>
                    <div class="form-group">
                        <label><b class="text-danger text-bold">* </b> Файл</label>
                        <input type="file" name="file" class="form-control" accept=".xls,.xlsx" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">Импортировать</button>
                    <button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary">Закрыть</button>
                </div>
            </form>
        </div>
    </div>
</div>

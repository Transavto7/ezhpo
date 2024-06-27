<div id="export-modal" tabindex="-1" role="dialog" aria-labelledby="export-modal"
     class="modal fade text-left" style="display: none;" aria-modal="true">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Импортирование водителей</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <export-modal export-url="{{ route('exportElement', $model) }}">
                <div class="form-group">
                    <label for="export_company_select">Выберите компанию для экспорта</label>
                    <select id="export_company_select" class="form-control">
                    </select>
                </div>
            </export-modal>
        </div>
    </div>
</div>

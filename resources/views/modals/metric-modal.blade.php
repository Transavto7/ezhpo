<div class="modal fade" id="elementsModalGenerateMetric" tabindex="-1" aria-labelledby="elementsModalGenerateMetricLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="elementsModalGenerateMetricLabel">Метрика ЛКК</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="start-date">Дата начала</label>
                        <input type="date" title="Дата начала" class="form-control start-date">
                    </div>
                    <div class="col-md-6">
                        <label for="end-date">Дата окончания</label>
                        <input type="date" title="Дата окончания" class="form-control end-date">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success spinner-btn" style="display: none">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    Загрузка...
                </button>
                <button type="button" class="btn btn-success generate-metric">Сформировать</button>
            </div>
        </div>
    </div>
</div>

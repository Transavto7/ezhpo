<div class="modal fade" id="cameraModal" tabindex="-1" role="dialog" aria-labelledby="cameraModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalTitle">Сканирование QR-кода</h5>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="width: 100%; height: 350px" id="reader"></div>
                <div class="error-div" style="display: none">
                    <div class="d-flex flex-column align-items-center">
                        <div class="qr-error alert alert-danger" ></div>
                        <a class="btn btn-outline-success restart-btn">Повторить</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

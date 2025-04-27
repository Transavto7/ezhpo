<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="type_anketa" value="{{ $type_anketa }}"/>

        @include('profile.ankets.components.pvs')

        <div class="form-group">
            <label class="form-control-label">ID водителя:</label>
            <article>
                <input value="{{ $driver_id ?? '' }}"
                       type="number"
                       oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())"
                       required
                       min="6"
                       name="driver_id"
                       class="MASK_ID_ELEM form-control">
                <div class="app-checker-prop"></div>
            </article>
        </div>

        <div class="cloning" id="cloning-first">
            <div class="form-group">
                <label class="form-control-label">Дата снятия отчета:</label>
                <article>
                    <input min="1900-02-20T20:20"
                           max="2999-02-20T20:20"
                           type="datetime-local"
                           required
                           value="{{ $default_current_date ?? '' }}"
                           name="anketa[0][date]"
                           class="form-control">
                </article>
            </div>

            @if ($attachment ?? null)
                <div class="d-flex flex-column">
                    <label class="form-control-label" for="attachment">Загруженный отчет:</label>
                    <a class="filename" href="{{ Storage::disk('report_cart')->url($attachment['path']) }}"
                       download="{{ $attachment['filename'] }}" title="{{ $attachment['filename'] }}">{{ $attachment['filename'] }}</a>
                </div>
            @endif

            <div class="form-group">
                <label class="form-control-label" for="attachment">Прикрепить отчет:</label>
                <div class="input-group mb-3">
                    <div class="custom-file report-cart-file" style="cursor: pointer">
                        <input type="file" class="custom-file-input attachment" id="attachment" name="anketa[0][attachment]" accept=".DDD">
                        <label class="custom-file-label" id="attachment-label" for="attachment">Выберите файл...</label>
                    </div>
                </div>
            </div>

            <div class="anketa-delete"></div>
        </div>
    </div>
</div>

@push('setup-styles')
    <style>
        .filename {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .custom-file-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            padding-right: 80px;
        }
    </style>
@endpush

@push('setup-scripts')
    <script>
        $('#ANKETA_FORM_ROOT').on('change', '.attachment', function (e) {
            const file = $(this).prop('files')[0]
            const $label = $(this).parent().find('#attachment-label')
            const extension = file.name.slice(-4)

            if (extension !== '.DDD' && extension !== '.ddd') {
                swal.fire({
                    title: 'Ошибка',
                    text: 'Загруженный файл должен иметь расширение .DDD',
                    icon: 'error'
                })

                return;
            }

            if (file) {
                const filename = file.name
                $label.html(filename)
            } else {
                $label.html('Выберите файл...')
            }
        })
    </script>
@endpush

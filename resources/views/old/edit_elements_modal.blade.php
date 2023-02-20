<div id="elements-modal-{{ $el->id }}-add" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Редактирование {{ $popupTitle }}</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>

            <form action="{{ route('updateElement', ['type' => $model, 'id' => $el->id ]) }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="modal-body">
                    @foreach ($fields as $k => $v)
                        @php $is_required = isset($v['noRequired']) ? '' : 'required' @endphp

                        @if($k !== 'id' && !isset($v['hidden']))
                            <div class="form-group" data-field="{{ $k }}">
                                <label>
                                    @if($is_required) <b class="text-danger text-bold">* </b> @endif
                                    {{ $v['label'] }}</label>

                                @include('templates.elements_field', [
                                    'v' => $v,
                                    'k' => $k,
                                    'default_value' => $el[$k],
                                    'element_id' => $el['id']
                                ])

                                {{--Синхронизация полей--}}
                                @if(isset($v['syncData']) && $model !== 'Company')
                                    @foreach($v['syncData'] as $syncData)
                                        <a href="{{ route('syncDataElement', [
                                            'model' => $syncData['model'],
                                            'fieldFind' => $syncData['fieldFind'],
                                            'fieldFindId' => $el['id'],
                                            'fieldSync' => $k,
                                            'fieldSyncValue' => $el[$k]
                                        ]) }}" target="_blank" class="text-info btn-link">
                                            <i class="fa fa-spinner"></i>
                                            Синхронизация с: {{ $syncData['text'] }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Сохранить</button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Закрыть</button>
                </div>
            </form>

        </div>
    </div>
</div>

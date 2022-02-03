<div>
    <label>
        <input type="radio" @if(request()->get('is_pak') === '1') checked @endif name="is_pak" value="1"> Да
    </label>
    <label>
        <input type="radio" @if(request()->get('is_pak') === '0') checked @endif name="is_pak" value="0"> Нет
    </label>
</div>

<form>
    <span>{{__('app.Show in one page')}}</span>
    <select name="countOnPageSelect" title="{{__('app.Show in one page')}}">
        <option {{ $selectedCountOnePage && $selectedCountOnePage==30?' selected="selected"':''}} value="30">30</option>
        <option {{ $selectedCountOnePage && $selectedCountOnePage==60?' selected="selected"':''}} value="60">60</option>
        <option {{ $selectedCountOnePage && $selectedCountOnePage==90?' selected="selected"':''}} value="90">90</option>
        <option {{ $selectedCountOnePage && $selectedCountOnePage==120?' selected="selected"':''}} value="120">120</option>        
    </select>
    <br>
</form>
@push('js')
<script>
    $('[name=countOnPageSelect]').on('change',function(){
        $('[name=countOnPage]').val($(this).find(':selected').val());
        $('.form-filter').submit();
    })
</script>
@endpush

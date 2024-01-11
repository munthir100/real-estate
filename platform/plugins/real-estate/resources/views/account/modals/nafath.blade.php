<div class="modal fade" id="nafathModal" role="dialog" aria-labelledby="nafathModal-label" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="#">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="nafathModal-label"><i class="til_img"></i><strong>{{ trans('plugins/real-estate::dashboard.sidebar_nafath') }}</strong>
                    </h4>
                    <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userId" class="form-label">{{__('Enter User Id')}}</label>
                        <input type="text" required class="form-control" id="userId" placeholder="{{__('Enter User Id')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ trans('plugins/real-estate::dashboard.close') }}</button>
                    <button class="btn btn-primary avatar-save" type="submit">{{ __('Verify') }}</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->
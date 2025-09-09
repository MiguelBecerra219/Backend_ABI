
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('content_framework_id') }}</label>
    <div>
        {{ Form::text('content_framework_id', $contentFrameworkProject->content_framework_id, ['class' => 'form-control' .
        ($errors->has('content_framework_id') ? ' is-invalid' : ''), 'placeholder' => 'Content Framework Id']) }}
        {!! $errors->first('content_framework_id', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">contentFrameworkProject <b>content_framework_id</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('project_id') }}</label>
    <div>
        {{ Form::text('project_id', $contentFrameworkProject->project_id, ['class' => 'form-control' .
        ($errors->has('project_id') ? ' is-invalid' : ''), 'placeholder' => 'Project Id']) }}
        {!! $errors->first('project_id', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">contentFrameworkProject <b>project_id</b> instruction.</small>
    </div>
</div>

    <div class="form-footer">
        <div class="text-end">
            <div class="d-flex">
                <a href="#" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary ms-auto ajax-submit">Submit</button>
            </div>
        </div>
    </div>

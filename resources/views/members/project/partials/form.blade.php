<div class="form-group">
  <label for="projectName" class="form-label">Name</label>
  <input id="projectName" class="form-control" type="text" name="projectName" placeholder="Name of Project" value="{{ old('projectName', $project->getProjectName()) }}" required autofocus>
  @if ($errors->has('projectName'))
  <p class="help-text">
    <strong>{{ $errors->first('projectName') }}</strong>
  </p>
  @endif
</div>

<div class="form-group">
  <label for="description" class="form-label">Description</label>
  <textarea id="description" name="description" class="form-control" placeholder="Description Here" rows="10" required>{{ old('description', $project->getDescription()) }}</textarea>
  @if ($errors->has('description'))
  <p class="help-text">
    <strong>{{ $errors->first('description') }}</strong>
  </p>
  @endif
</div>

@if (! old('id', $project->getId()))
<div class="form-check form-group">
  <input id="agreeToRules" class="form-check-input{{  $errors->has('agreeToRules') ? ' is-invalid' : '' }}" type="checkbox" name="agreeToRules" value="1" required>
  <label for="agreeToRules" class="form-check-label">I understand that leaving items in the space is at my own risk, that abandoned items may be disposed of, and have read and agree to the <a href="{{ Meta::get('rules_html') }}">{{ config('branding.space_name') }} rules</a>.</label>
  @if ($errors->has('agreeToRules'))
    <span class="invalid-feedback">
      <strong>{{ $errors->first('agreeToRules') }}</strong>
    </span>
  @endif
</div>
@endif

<button type="submit" class="btn btn-primary btn-block">{{ $submitButtonText }}</button>

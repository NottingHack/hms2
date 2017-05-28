<div class="row">
  <label for="templateName" class="form-label">Name</label>
  <div class="form-control">
    <input id="templateName" type="text" name="templateName" value="{{ old('templateName', $templateName) }}" required autofocus>
    @if ($errors->has('templateName'))
    <p class="help-text">
      <strong>{{ $errors->first('templateName') }}</strong>
    </p>
    @endif
  </div>
</div>

<div class="row">
  <label for="template" class="form-label">Template</label>
  <div class="form-control">
    <textarea id="template" name="template" rows="10" required >{{ old('template', $template) }}</textarea>
    @if ($errors->has('template'))
    <p class="help-text">
      <strong>{{ $errors->first('template') }}</strong>
    </p>
    @endif
  </div>
</div>

<div class="row">
  <div class="form-buttons">
    <button type="submit" class="button">
      {{ $submitButtonText }}
    </button>
  </div>
</div>

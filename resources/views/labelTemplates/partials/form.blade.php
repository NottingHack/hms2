<div class="container">
  <div class="form-group">
    <label for="templateName" class="form-label">Name</label>
    <input class="form-control" id="templateName" type="text" name="templateName" value="{{ old('templateName', $templateName) }}" required autofocus>
    @if ($errors->has('templateName'))
    <p class="help-text">
      <strong>{{ $errors->first('templateName') }}</strong>
    </p>
    @endif
  </div>

  <div class="form-group">
    <label for="template" class="form-label">Template</label>
    <textarea class="form-control" id="template" name="template" rows="10" required v-pre>{{ old('template', $template) }}</textarea>
    @if ($errors->has('template'))
    <p class="help-text">
      <strong>{{ $errors->first('template') }}</strong>
    </p>
    @endif
  </div>

  <div class="form-group">
    <div class="card">
      <button type="submit" class="btn btn-success">
        {{ $submitButtonText }}
      </button>
    </div>
  </div>
</div>

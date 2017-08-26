<div class="row">
  <label for="projectName" class="form-label">Name</label>
  <div class="form-control">
    <input id="projectName" type="text" name="projectName" value="{{ old('projectName', $project->getProjectName()) }}" required autofocus>
    @if ($errors->has('projectName'))
    <p class="help-text">
      <strong>{{ $errors->first('projectName') }}</strong>
    </p>
    @endif
  </div>
</div>

<div class="row">
  <label for="description" class="form-label">Description</label>
  <div class="form-control">
    <textarea id="description" name="description" rows="10" required>{{ old('description', $project->getDescription()) }}
    </textarea>
    @if ($errors->has('description'))
    <p class="help-text">
      <strong>{{ $errors->first('description') }}</strong>
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

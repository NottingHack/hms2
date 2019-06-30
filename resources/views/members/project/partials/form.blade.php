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

<button type="submit" class="btn btn-primary btn-block">{{ $submitButtonText }}</button>

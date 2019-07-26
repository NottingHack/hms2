<div class="form-group">
  <label for="name" class="form-label">Name</label>
  <input class="form-control" id="name" type="text" name="name" value="{{ old('name', $name) }}" required autofocus>
  @if ($errors->has('name'))
  <p class="help-text">
    <strong>{{ $errors->first('name') }}</strong>
  </p>
  @endif
</div>

<div class="form-group">
  <label for="link" class="form-label">Link</label>
  <input class="form-control" id="link" type="text" name="link" value="{{ old('link', $link) }}" required >
  @if ($errors->has('link'))
  <p class="help-text">
    <strong>{{ $errors->first('link') }}</strong>
  </p>
  @endif
</div>

<div class="form-group">
  <label for="description" class="form-label">Description</label>
  <input class="form-control" id="description" type="text" name="description" value="{{ old('description', $description) }}" >
  @if ($errors->has('description'))
  <p class="help-text">
    <strong>{{ $errors->first('description') }}</strong>
  </p>
  @endif
</div>

<hr>

<div class="form-group">
  <div class="form-buttons">
    <button type="submit" class="btn btn-success">
      {{ $submitButtonText }}
    </button>
  </div>
</div>

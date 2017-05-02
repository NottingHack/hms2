<div class="row">
  <label for="name" class="form-label">Name</label>
  <div class="form-control">
    <input id="name" type="text" name="name" value="{{ old('name', $name) }}" required autofocus>
    @if ($errors->has('name'))
    <p class="help-text">
      <strong>{{ $errors->first('name') }}</strong>
    </p>
    @endif
  </div>
</div>

<div class="row">
  <label for="link" class="form-label">Link</label>
  <div class="form-control">
    <input id="link" type="text" name="link" value="{{ old('link', $link) }}" required >
    @if ($errors->has('link'))
    <p class="help-text">
      <strong>{{ $errors->first('link') }}</strong>
    </p>
    @endif
  </div>
</div>

<div class="row">
  <label for="description" class="form-label">Description</label>
  <div class="form-control">
    <input id="description" type="text" name="description" value="{{ old('description', $description) }}" >
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

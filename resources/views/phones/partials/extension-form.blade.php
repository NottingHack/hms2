<form class="form-group" role="form" method="POST" action="@if ($extension->getExtension()) {{ route('phones.extensions.update', $extension->getExtension()) }} @else {{ route('phones.extensions.doRegister') }} @endif">
  @csrf

  <div class="form-group">
    <label for="extension" class="form-label">Extension</label>
    <input id="extension" class="form-control @error('extension') is-invalid @enderror" type="text" name="extension" placeholder="Your extension (e.g. 1234)" required autofocus maxlength="5" value="{{ old('extension', $extension->getExtension()) }}" @if ($extension->getExtension()) disabled @endif>
      <small id="extensionHelpBlock" class="form-text text-muted">
    This is the 3 or 4 digit number which other members can dial to reach you.
      </small>
      @error('extension')
      <p class="help-text">
        <strong>{{ $errors->first('extension') }}</strong>
      </p>
      @enderror
  </div>

  <div class="form-group">
    <label for="phoneword" class="form-label">Phoneword</label>
    <input id="phoneword" class="form-control @error('phoneword') is-invalid @enderror" type="text" name="phoneword" placeholder="(optional) phoneword for your number" autofocus maxlength="5" value="{{ old('phoneword', $extension->getPhoneword()) }}">
      <small id="phonewordHelpBlock" class="form-text text-muted">
    Phonewords are short words which can be typed using the letters on your phone's keypad are sometimes used to help remember phone numbers. For example, 9327 could be remembered as YEAR.
      </small>
      @error('phoneword')
      <p class="help-text">
        <strong>{{ $errors->first('phoneword') }}</strong>
      </p>
      @enderror
  </div>

  <div class="form-group">
    <label for="type" class="form-label">Connection Type</label>
    <select id="type" name="type" class="form-control" @if ($extension->getType()) disabled @endif>
      @foreach ($types as $type => $description)
        <option value="{{ $type }}" @if (old('type', $extension->getType()) === $type) selected @endif>{{ $description }}</option>
      @endforeach
    </select>
    <small id="typeHelpBlock" class="form-text text-muted">
      You can connect to the phone network in several different ways.
      <ul>
        <li><strong>SIP</strong> is a commonly used Voice over IP (VoIP) protocol and is supported by IP desk phones, as well as software phones (softphones) on your current mobile device or computer</li>
        <li><strong>DECT</strong> phones are wireless handsets, similar to what you may be using at home.</li>
        <li><strong>POTS</strong> is short for Plain Old Telephone Service, and is a copper pair to which an analogue phone can be connected. This can be a rotary dial or tone (DTFM) dial phone.</li>
        <li><strong>Custom</strong> allows you to reserve a number for a custom configuration on the Asterisk server.</li>
      </ul>
    </small>
    @error('type')
    <p class="help-text">
      <strong>{{ $errors->first('type') }}</strong>
    </p>
    @enderror
  </div>

  <div class="form-group">
    <label for="category" class="form-label">Extension Category</label>
    <select id="category" name="category" class="form-control">
      @foreach ($categories as $category => $description)
        <option value="{{ $category }}" @if (old('category', $extension->getCategory()) === $category) selected @endif>{{ $description }}</option>
      @endforeach
    </select>
    <small id="categoryHelpBlock" class="form-text text-muted">
      To support filtering of the extensions in our phone directory, you should specify what kind of extension this is.
      <ul>
        <li><strong>Member</strong> extensions are things like DECT phones, or something fun which you are creating, like a phone game.</li>
        <li><strong>Location</strong> extensions are for shared phones around the hackspace.</li>
        <li><strong>Service</strong> extensions are services which run on the phone system, such as voicemail or bridges.</li>
      </ul>
    </small>
    @error('category')
    <p class="help-text">
      <strong>{{ $errors->first('category') }}</strong>
    </p>
    @enderror
  </div>

  <div class="form-group">
    <label for="description" class="form-label">Description</label>
    <input id="description" class="form-control @error('description') is-invalid @enderror" type="text" name="description" placeholder="Description (e.g. your name and the service type)" required autofocus maxlength="200" value="{{ old('description', $extension->getDescription()) }}">
      <small id="descriptionHelpBlock" class="form-text text-muted">
    This is the description which will be displayed next to your phone number in the directory. Your real name (from your HMS profile) is not published, so you may want to put that here, or your username, or something else people will recognise.
      </small>
      @error('description')
      <p class="help-text">
        <strong>{{ $errors->first('description') }}</strong>
      </p>
      @enderror
  </div>

  <div class="form-group">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="hidden" name="hidden" value="true" @if (old('hidden', $extension->getHidden() ? 'true' : null) === 'true') checked @endif>
      <label class="form-check-label" for="hidden">Hide this extension to other members in the directory.</label>
    </div>
  </div>

  <button type="submit" class="btn btn-primary btn-block">{{ $submitButtonText }}</button>
</form>

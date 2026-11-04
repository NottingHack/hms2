<form method="POST" action="{{ $form->getName() ? route('forms.update', $form->getId()) : route('forms.createForm') }}">
  @csrf
  <div class="form-group">
    <label for="formName">Name</label>
    <input type="text" class="form-control" id="formName" name="name" value="{{ old('name', $form->getName()) }}" />
    <p class="help-text">
      <strong>{{ $errors->first('formName') }}</strong>
    </p>
  </div>

  <div class="form-group">
    <label for="jsonDefinition">Survey.js JSON Definition</label>
    <textarea class="form-control text-monospace"
      id="jsonDefinition" name="jsonDefinition"
      rows="6">{{ old('jsonDefinition', json_encode($form->getJsonDefinition(), JSON_PRETTY_PRINT)) }}</textarea>
    <p class="help-text">
      <strong>{{ $errors->first('jsonDefinition') }}</strong>
    </p>
    <small class="form-text text-muted">
      You can design your forms using the <a href="https://surveyjs.io/create-free-survey" target="_blank">Survey.js Survey Creator</a>. The JSON Editor tab contains the form definition which can be pasted here.
    </small>
    <small class="form-text text-muted">
      HMS provides a number of helpers to autofill options in dropdowns and radio groups. <strong>hms:teams</strong> will be replaced with teams available in HMS, and <strong>hms:tools</strong> will be replaced with tools.
    </small>
    <small class="form-text text-muted">
      For example,
      <pre>{
  "type": "dropdown",
  "name": "Team",
  "choices": "hms:teams",
  "showOtherItem": true
}</pre>
    </small>
  </div>

  <div class="form-group">
    <label for="maxResponses">Maximum Responses per Member:</label>
    <input type="number" class="form-control" id="maxResponses" name="maxResponses" value="{{ old('maxResponses', $form->getMaxResponses()) }}" />
    <p class="help-text">
      <strong>{{ $errors->first('maxResponses') }}</strong>
    </p>
    <small class="form-text text-muted">
      Specify 0 for unlimited.
    </small>
  </div>

  <div class="form-group">
    <label for="notificationKey">Notification Key:</label>
    <input type="text" class="form-control" id="notificationKey" name="notificationKey" value="{{ old('notificationKey', $form->getNotificationKey()) }}" />
    <p class="help-text">
      <strong>{{ $errors->first('notificationKey') }}</strong>
    </p>
    <small class="form-text text-muted">
      If a team should notified upon submission of this form, set the name of the field from the form.
    </small>
  </div>

  <div class="form-group">
    <label for="permissionName">Permission Name:</label>
    <input type="text" class="form-control" id="permissionName" name="permissionName" value="{{ old('permissionName', $form->getPermissionName()) }}" @if ($form->getPermissionName()) disabled @endif />
    <p class="help-text">
      <strong>{{ $errors->first('permissionName') }}</strong>
    </p>
    <small class="form-text text-muted">
      This should be the name of the form, without any special characters. This cannot be changed later.
    </small>
  </div>

  <button type="submit" class="btn btn-primary mt-3">
    @if ($form->getId())
    Update
    @else
    Create
    @endif
  </button>
</form>

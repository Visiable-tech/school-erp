@extends('layouts.admin')
@section('content')
<div class="container-fluid py-3">
<h4 class="mb-3">{{ $employee->exists ? 'Edit Employee' : 'Add Employee' }}</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ $employee->exists ? route('employee-info.update',$employee->id) : route('employee-info.store') }}" class="card shadow-sm">
@csrf
@if($employee->exists) @method('PUT') @endif
<div class="card-body">
<h6 class="border-bottom border-danger pb-2 mt-4 mb-3">Personal Information</h6>
<div class="row g-3">
<div class="col-12 col-md-6">
<label class="form-label" for="f_image_path">Employee Photo</label>
<input type="file" class="form-control" id="f_image_path" name="image_path" accept="image/png,image/jpeg,image/webp">@if($employee->image_path)<small class="text-muted">Existing file saved securely</small>@endif
@error('image_path')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_salutation">Salutation</label>
<select class="form-select" id="f_salutation" name="salutation"><option value="">-- Select --</option>
<option value="Mr" @selected(old('salutation',$employee->salutation) == 'Mr')>Mr</option>
<option value="Mrs" @selected(old('salutation',$employee->salutation) == 'Mrs')>Mrs</option>
<option value="Ms" @selected(old('salutation',$employee->salutation) == 'Ms')>Ms</option>
<option value="Dr" @selected(old('salutation',$employee->salutation) == 'Dr')>Dr</option>
</select>
@error('salutation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_first_name">First Name</label>
<input type="text" class="form-control" id="f_first_name" name="first_name" value="{{ old('first_name',$employee->first_name) }}">
@error('first_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_middle_name">Middle Name</label>
<input type="text" class="form-control" id="f_middle_name" name="middle_name" value="{{ old('middle_name',$employee->middle_name) }}">
@error('middle_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_last_name">Last Name</label>
<input type="text" class="form-control" id="f_last_name" name="last_name" value="{{ old('last_name',$employee->last_name) }}">
@error('last_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_short_name">Short Name</label>
<input type="text" class="form-control" id="f_short_name" name="short_name" value="{{ old('short_name',$employee->short_name) }}">
@error('short_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_gender">Gender</label>
<select class="form-select" id="f_gender" name="gender"><option value="">-- Select --</option>
<option value="Male" @selected(old('gender',$employee->gender) == 'Male')>Male</option>
<option value="Female" @selected(old('gender',$employee->gender) == 'Female')>Female</option>
<option value="Other" @selected(old('gender',$employee->gender) == 'Other')>Other</option>
</select>
@error('gender')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_dob">Date of Birth</label>
<input type="date" class="form-control" id="f_dob" name="dob" value="{{ old('dob', optional($employee->dob)->format('Y-m-d')) }}">
@error('dob')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_blood_group">Blood Group</label>
<select class="form-select" id="f_blood_group" name="blood_group"><option value="">-- Select --</option>
<option value="A+" @selected(old('blood_group',$employee->blood_group) == 'A+')>A+</option>
<option value="A-" @selected(old('blood_group',$employee->blood_group) == 'A-')>A-</option>
<option value="B+" @selected(old('blood_group',$employee->blood_group) == 'B+')>B+</option>
<option value="B-" @selected(old('blood_group',$employee->blood_group) == 'B-')>B-</option>
<option value="AB+" @selected(old('blood_group',$employee->blood_group) == 'AB+')>AB+</option>
<option value="AB-" @selected(old('blood_group',$employee->blood_group) == 'AB-')>AB-</option>
<option value="O+" @selected(old('blood_group',$employee->blood_group) == 'O+')>O+</option>
<option value="O-" @selected(old('blood_group',$employee->blood_group) == 'O-')>O-</option>
</select>
@error('blood_group')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_marital_status">Marital Status</label>
<select class="form-select" id="f_marital_status" name="marital_status"><option value="">-- Select --</option>
<option value="Single" @selected(old('marital_status',$employee->marital_status) == 'Single')>Single</option>
<option value="Married" @selected(old('marital_status',$employee->marital_status) == 'Married')>Married</option>
<option value="Divorced" @selected(old('marital_status',$employee->marital_status) == 'Divorced')>Divorced</option>
<option value="Widowed" @selected(old('marital_status',$employee->marital_status) == 'Widowed')>Widowed</option>
</select>
@error('marital_status')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_place_of_birth">Place of Birth</label>
<input type="text" class="form-control" id="f_place_of_birth" name="place_of_birth" value="{{ old('place_of_birth',$employee->place_of_birth) }}">
@error('place_of_birth')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_category">Category</label>
<input type="text" class="form-control" id="f_category" name="category" value="{{ old('category',$employee->category) }}">
@error('category')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_religion">Religion</label>
<input type="text" class="form-control" id="f_religion" name="religion" value="{{ old('religion',$employee->religion) }}">
@error('religion')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_nationality">Nationality</label>
<input type="text" class="form-control" id="f_nationality" name="nationality" value="{{ old('nationality',$employee->nationality) }}">
@error('nationality')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_passport_expiry_date">Passport Expiry Date</label>
<input type="date" class="form-control" id="f_passport_expiry_date" name="passport_expiry_date" value="{{ old('passport_expiry_date', optional($employee->passport_expiry_date)->format('Y-m-d')) }}">
@error('passport_expiry_date')<div class="text-danger small">{{ $message }}</div>@enderror</div>
</div>
<h6 class="border-bottom border-danger pb-2 mt-4 mb-3">Official Information</h6>
<div class="row g-3">
<div class="col-12 col-md-6">
<label class="form-label" for="f_employee_code">Employee Code</label>
<input type="text" class="form-control" id="f_employee_code" name="employee_code" value="{{ $employee->exists ? $employee->employee_code : ($previewEmployeeCode ?? '') }}" readonly required>
@error('employee_code')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_official_email">Official Email / Login ID</label>
<input type="email" class="form-control" id="f_official_email" name="official_email" value="{{ old('official_email',$employee->official_email) }}">
@error('official_email')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_user_type">User Type / ERP Role <span class="text-danger">*</span></label>
<select class="form-select" id="f_user_type" name="user_type" @disabled(!$canManageLogins)>
<option value="">-- Select Role --</option>
@if($employee->user_type && !($loginRoles ?? collect())->contains('name', $employee->user_type))
<option value="{{ $employee->user_type }}" selected>{{ $employee->user_type }} (existing)</option>
@endif
@foreach($loginRoles ?? [] as $role)
<option value="{{ $role->name }}" @selected(old('user_type', $employee->user_type) === $role->name)>{{ $role->name }}</option>
@endforeach
</select>
<small class="text-muted">ERP role controls access only when a login is created. Only authorized administrators can change it.</small>
@error('user_type')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_designation">HR Designation <span class="text-danger">*</span></label>
<select class="form-select" id="f_designation" name="designation" required>
<option value="">-- Select Designation --</option>
@foreach($designations ?? [] as $designation)
<option value="{{ $designation->name }}" @selected(old('designation', $employee->designation) === $designation->name)>{{ $designation->name }}</option>
@endforeach
</select>
<small class="text-muted">Designations are maintained by HR.</small>
@error('designation')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_department">Department</label>
<input type="text" class="form-control" id="f_department" name="department" value="{{ old('department',$employee->department) }}">
@error('department')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_employment_type">Employment Type</label>
<select class="form-select" id="f_employment_type" name="employment_type"><option value="">-- Select --</option>
<option value="Permanent" @selected(old('employment_type',$employee->employment_type) == 'Permanent')>Permanent</option>
<option value="Contract" @selected(old('employment_type',$employee->employment_type) == 'Contract')>Contract</option>
<option value="Part-Time" @selected(old('employment_type',$employee->employment_type) == 'Part-Time')>Part-Time</option>
<option value="Temporary" @selected(old('employment_type',$employee->employment_type) == 'Temporary')>Temporary</option>
</select>
@error('employment_type')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_joining_date">Joining Date</label>
<input type="date" class="form-control" id="f_joining_date" name="joining_date" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}" required>
@error('joining_date')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="rejoin" value="1" id="f_rejoin" @checked(old('rejoin',$employee->rejoin) == 1)><label class="form-check-label" for="f_rejoin">Rejoin</label></div>
@error('rejoin')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_staff_category">Staff Category</label>
<input type="text" class="form-control" id="f_staff_category" name="staff_category" value="{{ old('staff_category',$employee->staff_category) }}">
@error('staff_category')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_probation_ends_on">Probation Ends On</label>
<input type="date" class="form-control" id="f_probation_ends_on" name="probation_ends_on" value="{{ old('probation_ends_on', optional($employee->probation_ends_on)->format('Y-m-d')) }}">
@error('probation_ends_on')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_reporting_authority">Reporting Authority</label>
<input type="text" class="form-control" id="f_reporting_authority" name="reporting_authority" value="{{ old('reporting_authority',$employee->reporting_authority) }}">
@error('reporting_authority')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_confirmation_date">Confirmation Date</label>
<input type="date" class="form-control" id="f_confirmation_date" name="confirmation_date" value="{{ old('confirmation_date', optional($employee->confirmation_date)->format('Y-m-d')) }}">
@error('confirmation_date')<div class="text-danger small">{{ $message }}</div>@enderror</div>
@if($canManageLogins)
<div class="col-12">
 <div class="card border-primary mt-2" id="erp-login-panel">
  <div class="card-header fw-semibold">ERP Login Account</div>
  <div class="card-body">
   <label class="form-label" for="login_action">Account Action</label>
   @php $loginChoice=old('login_action', $employee->exists && $employee->user_id ? 'existing' : 'none'); @endphp
   <select class="form-select" name="login_action" id="login_action">
    <option value="none" @selected($loginChoice==='none')>No Login / Unlink Account</option>
    <option value="existing" @selected($loginChoice==='existing')>Link Existing User</option>
    <option value="new" @selected($loginChoice==='new')>Create New User</option>
   </select>
   <div class="mt-3" id="existing-login-fields">
    <label class="form-label" for="linked_user_id">Existing ERP User</label>
    <select class="form-select" name="linked_user_id" id="linked_user_id">
     <option value="">-- Select User --</option>
     @foreach($availableUsers as $existingUser)
      <option value="{{ $existingUser->id }}" @selected((string)old('linked_user_id',$employee->user_id)===(string)$existingUser->id)>{{ $existingUser->name }} ({{ $existingUser->email ?? $existingUser->username }})</option>
     @endforeach
    </select>
    <small class="text-muted">Linking will not change this user's password or roles.</small>
   </div>
   <div class="row g-3 mt-1" id="new-login-fields">
    <div class="col-12"><div class="alert alert-info mb-0">Login ID will be the <strong>Official Email</strong> entered above. It must be unique across ERP users.</div></div>
    <div class="col-md-6"><label class="form-label" for="login_password">Password</label><input type="password" class="form-control" id="login_password" name="login_password" autocomplete="new-password"></div>
    <div class="col-md-6"><label class="form-label" for="login_password_confirmation">Confirm Password</label><input type="password" class="form-control" id="login_password_confirmation" name="login_password_confirmation" autocomplete="new-password"></div>
    <div class="col-12"><div class="alert alert-info mb-0">The new login will receive the ERP role selected under <strong>User Type / ERP Role</strong> above. Permissions are configured by Super Admin.</div></div>
   </div>
   @if($employee->exists && $employee->user_id)<p class="text-muted small mt-3 mb-0">Current linked account: {{ $employee->user?->email ?? $employee->user?->username }}. Unlinking does not delete or disable it.</p>@endif
  </div>
 </div>
</div>
@else
 @if($employee->exists && $employee->user_id)<div class="col-12"><div class="alert alert-secondary">ERP login is linked. Contact an administrator to change it.</div></div>@endif
@endif
<div class="col-12 col-md-6">
<label class="form-label" for="f_cost_center">Cost Center</label>
<input type="text" class="form-control" id="f_cost_center" name="cost_center" value="{{ old('cost_center',$employee->cost_center) }}">
@error('cost_center')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_pay_scale">Pay Scale</label>
<input type="text" class="form-control" id="f_pay_scale" name="pay_scale" value="{{ old('pay_scale',$employee->pay_scale) }}">
@error('pay_scale')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_principal" value="1" id="f_is_principal" @checked(old('is_principal',$employee->is_principal) == 1)><label class="form-check-label" for="f_is_principal">Is Principal</label></div>
@error('is_principal')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_coordinating_sections">Coordinating Sections</label>
<input type="text" class="form-control" id="f_coordinating_sections" name="coordinating_sections" value="{{ old('coordinating_sections',$employee->coordinating_sections) }}">
@error('coordinating_sections')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_coordinating_classes">Coordinating Classes</label>
<input type="text" class="form-control" id="f_coordinating_classes" name="coordinating_classes" value="{{ old('coordinating_classes',$employee->coordinating_classes) }}">
@error('coordinating_classes')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_salary_mode">Salary Mode</label>
<select class="form-select" id="f_salary_mode" name="salary_mode"><option value="">-- Select --</option>
<option value="Bank" @selected(old('salary_mode',$employee->salary_mode) == 'Bank')>Bank</option>
<option value="Cash" @selected(old('salary_mode',$employee->salary_mode) == 'Cash')>Cash</option>
<option value="Cheque" @selected(old('salary_mode',$employee->salary_mode) == 'Cheque')>Cheque</option>
</select>
@error('salary_mode')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_qualifications">Qualifications</label>
<input type="text" class="form-control" id="f_qualifications" name="qualifications" value="{{ old('qualifications',$employee->qualifications) }}">
@error('qualifications')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_specialization">Specialization</label>
<input type="text" class="form-control" id="f_specialization" name="specialization" value="{{ old('specialization',$employee->specialization) }}">
@error('specialization')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_specialization_subjects">Specialization Subjects</label>
<input type="text" class="form-control" id="f_specialization_subjects" name="specialization_subjects" value="{{ old('specialization_subjects',$employee->specialization_subjects) }}">
@error('specialization_subjects')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_experience_teaching_years">Experience (Teaching Years)</label>
<input type="number" class="form-control" id="f_experience_teaching_years" name="experience_teaching_years" value="{{ old('experience_teaching_years',$employee->experience_teaching_years) }}">
@error('experience_teaching_years')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_experience_admin_years">Experience (Admin Years)</label>
<input type="number" class="form-control" id="f_experience_admin_years" name="experience_admin_years" value="{{ old('experience_admin_years',$employee->experience_admin_years) }}">
@error('experience_admin_years')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_default_attendance_status">Default Attendance Status</label>
<select class="form-select" id="f_default_attendance_status" name="default_attendance_status"><option value="">-- Select --</option>
<option value="Present" @selected(old('default_attendance_status',$employee->default_attendance_status) == 'Present')>Present</option>
<option value="Absent" @selected(old('default_attendance_status',$employee->default_attendance_status) == 'Absent')>Absent</option>
<option value="Leave" @selected(old('default_attendance_status',$employee->default_attendance_status) == 'Leave')>Leave</option>
</select>
@error('default_attendance_status')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_previous_employee_code">Previous Employee Code</label>
<input type="text" class="form-control" id="f_previous_employee_code" name="previous_employee_code" value="{{ old('previous_employee_code',$employee->previous_employee_code) }}">
@error('previous_employee_code')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_increment_month">Increment Month</label>
<select class="form-select" id="f_increment_month" name="increment_month"><option value="">-- Select --</option>
<option value="January" @selected(old('increment_month',$employee->increment_month) == 'January')>January</option>
<option value="February" @selected(old('increment_month',$employee->increment_month) == 'February')>February</option>
<option value="March" @selected(old('increment_month',$employee->increment_month) == 'March')>March</option>
<option value="April" @selected(old('increment_month',$employee->increment_month) == 'April')>April</option>
<option value="May" @selected(old('increment_month',$employee->increment_month) == 'May')>May</option>
<option value="June" @selected(old('increment_month',$employee->increment_month) == 'June')>June</option>
<option value="July" @selected(old('increment_month',$employee->increment_month) == 'July')>July</option>
<option value="August" @selected(old('increment_month',$employee->increment_month) == 'August')>August</option>
<option value="September" @selected(old('increment_month',$employee->increment_month) == 'September')>September</option>
<option value="October" @selected(old('increment_month',$employee->increment_month) == 'October')>October</option>
<option value="November" @selected(old('increment_month',$employee->increment_month) == 'November')>November</option>
<option value="December" @selected(old('increment_month',$employee->increment_month) == 'December')>December</option>
</select>
@error('increment_month')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_date_of_separation">Date of Separation</label>
<input type="date" class="form-control" id="f_date_of_separation" name="date_of_separation" value="{{ old('date_of_separation', optional($employee->date_of_separation)->format('Y-m-d')) }}">
@error('date_of_separation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_status">Status</label>
<select class="form-select" id="f_status" name="status"><option value="">-- Select --</option>
<option value="active" @selected(old('status',$employee->status) == 'active')>active</option>
<option value="suspended" @selected(old('status',$employee->status) == 'suspended')>suspended</option>
<option value="relieved" @selected(old('status',$employee->status) == 'relieved')>relieved</option>
<option value="resigned" @selected(old('status',$employee->status) == 'resigned')>resigned</option>
</select>
@error('status')<div class="text-danger small">{{ $message }}</div>@enderror</div>
</div>
<h6 class="border-bottom border-danger pb-2 mt-4 mb-3">Contact Information</h6>
<div class="row g-3">
<div class="col-12 col-md-6">
<label class="form-label" for="f_phone">Mobile No.</label>
<input type="tel" class="form-control" id="f_phone" name="phone" value="{{ old('phone',$employee->phone) }}">
@error('phone')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_alternate_phone">Phone No.</label>
<input type="tel" class="form-control" id="f_alternate_phone" name="alternate_phone" value="{{ old('alternate_phone',$employee->alternate_phone) }}">
@error('alternate_phone')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_email">Personal Email</label>
<input type="email" class="form-control" id="f_email" name="email" value="{{ old('email',$employee->email) }}">
@error('email')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_address_from_date">Address From Date</label>
<input type="date" class="form-control" id="f_address_from_date" name="address_from_date" value="{{ old('address_from_date', optional($employee->address_from_date)->format('Y-m-d')) }}">
@error('address_from_date')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_address">Correspondence Address</label>
<textarea class="form-control" id="f_address" name="address" rows="2">{{ old('address',$employee->address) }}</textarea>
@error('address')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_correspondence_country">Country</label>
<input type="text" class="form-control" id="f_correspondence_country" name="correspondence_country" value="{{ old('correspondence_country',$employee->correspondence_country) }}">
@error('correspondence_country')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_correspondence_state">State</label>
<input type="text" class="form-control" id="f_correspondence_state" name="correspondence_state" value="{{ old('correspondence_state',$employee->correspondence_state) }}">
@error('correspondence_state')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_correspondence_city">City</label>
<input type="text" class="form-control" id="f_correspondence_city" name="correspondence_city" value="{{ old('correspondence_city',$employee->correspondence_city) }}">
@error('correspondence_city')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_correspondence_pincode">Pincode</label>
<input type="text" class="form-control" id="f_correspondence_pincode" name="correspondence_pincode" value="{{ old('correspondence_pincode',$employee->correspondence_pincode) }}">
@error('correspondence_pincode')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_permanent_address">Permanent Address</label>
<textarea class="form-control" id="f_permanent_address" name="permanent_address" rows="2">{{ old('permanent_address',$employee->permanent_address) }}</textarea>
@error('permanent_address')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="same_as_correspondence" value="1" id="f_same_as_correspondence" @checked(old('same_as_correspondence',$employee->same_as_correspondence) == 1)><label class="form-check-label" for="f_same_as_correspondence">Same as Correspondence</label></div>
@error('same_as_correspondence')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_permanent_country">Permanent Country</label>
<input type="text" class="form-control" id="f_permanent_country" name="permanent_country" value="{{ old('permanent_country',$employee->permanent_country) }}">
@error('permanent_country')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_permanent_state">Permanent State</label>
<input type="text" class="form-control" id="f_permanent_state" name="permanent_state" value="{{ old('permanent_state',$employee->permanent_state) }}">
@error('permanent_state')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_permanent_city">Permanent City</label>
<input type="text" class="form-control" id="f_permanent_city" name="permanent_city" value="{{ old('permanent_city',$employee->permanent_city) }}">
@error('permanent_city')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_permanent_pincode">Permanent Pincode</label>
<input type="text" class="form-control" id="f_permanent_pincode" name="permanent_pincode" value="{{ old('permanent_pincode',$employee->permanent_pincode) }}">
@error('permanent_pincode')<div class="text-danger small">{{ $message }}</div>@enderror</div>
</div>
<h6 class="border-bottom border-danger pb-2 mt-4 mb-3">Family Information</h6>
<div class="row g-3">
<div class="col-12 col-md-6">
<label class="form-label" for="f_father_salutation">Father’s Salutation</label>
<select class="form-select" id="f_father_salutation" name="father_salutation"><option value="">-- Select --</option>
<option value="Mr" @selected(old('father_salutation',$employee->father_salutation) == 'Mr')>Mr</option>
<option value="Dr" @selected(old('father_salutation',$employee->father_salutation) == 'Dr')>Dr</option>
</select>
@error('father_salutation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_father_name">Father’s Name</label>
<input type="text" class="form-control" id="f_father_name" name="father_name" value="{{ old('father_name',$employee->father_name) }}">
@error('father_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_father_contact">Father’s Contact No.</label>
<input type="tel" class="form-control" id="f_father_contact" name="father_contact" value="{{ old('father_contact',$employee->father_contact) }}">
@error('father_contact')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_father_occupation">Father’s Occupation</label>
<input type="text" class="form-control" id="f_father_occupation" name="father_occupation" value="{{ old('father_occupation',$employee->father_occupation) }}">
@error('father_occupation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_mother_salutation">Mother’s Salutation</label>
<select class="form-select" id="f_mother_salutation" name="mother_salutation"><option value="">-- Select --</option>
<option value="Mrs" @selected(old('mother_salutation',$employee->mother_salutation) == 'Mrs')>Mrs</option>
<option value="Ms" @selected(old('mother_salutation',$employee->mother_salutation) == 'Ms')>Ms</option>
<option value="Dr" @selected(old('mother_salutation',$employee->mother_salutation) == 'Dr')>Dr</option>
</select>
@error('mother_salutation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_mother_name">Mother’s Name</label>
<input type="text" class="form-control" id="f_mother_name" name="mother_name" value="{{ old('mother_name',$employee->mother_name) }}">
@error('mother_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_mother_contact">Mother’s Contact No.</label>
<input type="tel" class="form-control" id="f_mother_contact" name="mother_contact" value="{{ old('mother_contact',$employee->mother_contact) }}">
@error('mother_contact')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_mother_occupation">Mother’s Occupation</label>
<input type="text" class="form-control" id="f_mother_occupation" name="mother_occupation" value="{{ old('mother_occupation',$employee->mother_occupation) }}">
@error('mother_occupation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
</div>
<h6 class="border-bottom border-danger pb-2 mt-4 mb-3">Bank / Payroll Information</h6>
<div class="row g-3">
<div class="col-12 col-md-6">
<label class="form-label" for="f_bank_from_date">Bank From Date</label>
<input type="date" class="form-control" id="f_bank_from_date" name="bank_from_date" value="{{ old('bank_from_date', optional($employee->bank_from_date)->format('Y-m-d')) }}">
@error('bank_from_date')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_pf_no">PF No.</label>
<input type="text" class="form-control" id="f_pf_no" name="pf_no" value="{{ old('pf_no',$employee->pf_no) }}">
@error('pf_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_esi_no">ESI No.</label>
<input type="text" class="form-control" id="f_esi_no" name="esi_no" value="{{ old('esi_no',$employee->esi_no) }}">
@error('esi_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_bank_name">Bank</label>
<input type="text" class="form-control" id="f_bank_name" name="bank_name" value="{{ old('bank_name',$employee->bank_name) }}">
@error('bank_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_account_no">Account No.</label>
<input type="text" class="form-control" id="f_account_no" name="account_no" value="{{ old('account_no',$employee->account_no) }}">
@error('account_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="bank_other_org_accounts" value="1" id="f_bank_other_org_accounts" @checked(old('bank_other_org_accounts',$employee->bank_other_org_accounts) == 1)><label class="form-check-label" for="f_bank_other_org_accounts">Bank Other Than Org. Accounts</label></div>
@error('bank_other_org_accounts')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_ifsc_code">IFSC Code</label>
<input type="text" class="form-control" id="f_ifsc_code" name="ifsc_code" value="{{ old('ifsc_code',$employee->ifsc_code) }}">
@error('ifsc_code')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_pf_activation_date">PF Activation Date</label>
<input type="date" class="form-control" id="f_pf_activation_date" name="pf_activation_date" value="{{ old('pf_activation_date', optional($employee->pf_activation_date)->format('Y-m-d')) }}">
@error('pf_activation_date')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_pf_appear_date">PF Appear Date</label>
<input type="date" class="form-control" id="f_pf_appear_date" name="pf_appear_date" value="{{ old('pf_appear_date', optional($employee->pf_appear_date)->format('Y-m-d')) }}">
@error('pf_appear_date')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_uan_no">UAN No.</label>
<input type="text" class="form-control" id="f_uan_no" name="uan_no" value="{{ old('uan_no',$employee->uan_no) }}">
@error('uan_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="exclude_ptax" value="1" id="f_exclude_ptax" @checked(old('exclude_ptax',$employee->exclude_ptax) == 1)><label class="form-check-label" for="f_exclude_ptax">Exclude PTax</label></div>
@error('exclude_ptax')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_eps_no">EPS No.</label>
<input type="text" class="form-control" id="f_eps_no" name="eps_no" value="{{ old('eps_no',$employee->eps_no) }}">
@error('eps_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
</div>
<h6 class="border-bottom border-danger pb-2 mt-4 mb-3">Other Information</h6>
<div class="row g-3">
<div class="col-12 col-md-6">
<label class="form-label" for="f_pan_no">PAN Card No.</label>
<input type="text" class="form-control" id="f_pan_no" name="pan_no" value="{{ old('pan_no',$employee->pan_no) }}">
@error('pan_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_aadhaar_no">Aadhaar Card No.</label>
<input type="text" class="form-control" id="f_aadhaar_no" name="aadhaar_no" value="{{ old('aadhaar_no',$employee->aadhaar_no) }}">
@error('aadhaar_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_voter_id_no">Voter ID Card No.</label>
<input type="text" class="form-control" id="f_voter_id_no" name="voter_id_no" value="{{ old('voter_id_no',$employee->voter_id_no) }}">
@error('voter_id_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_punch_card_no">Punch Card No.</label>
<input type="text" class="form-control" id="f_punch_card_no" name="punch_card_no" value="{{ old('punch_card_no',$employee->punch_card_no) }}">
@error('punch_card_no')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="skip_salary_generation" value="1" id="f_skip_salary_generation" @checked(old('skip_salary_generation',$employee->skip_salary_generation) == 1)><label class="form-check-label" for="f_skip_salary_generation">Skip Salary Generation</label></div>
@error('skip_salary_generation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_salary_slip_format">Salary Slip Format</label>
<input type="text" class="form-control" id="f_salary_slip_format" name="salary_slip_format" value="{{ old('salary_slip_format',$employee->salary_slip_format) }}">
@error('salary_slip_format')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_skip_salary_months">Skip Salary Months</label>
<input type="text" class="form-control" id="f_skip_salary_months" name="skip_salary_months" value="{{ old('skip_salary_months',$employee->skip_salary_months) }}">
@error('skip_salary_months')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_signature_path">Signature</label>
<input type="file" class="form-control" id="f_signature_path" name="signature_path" accept="image/png,image/jpeg,image/webp">@if($employee->signature_path)<small class="text-muted">Existing file saved securely</small>@endif
@error('signature_path')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="exclude_from_substitution" value="1" id="f_exclude_from_substitution" @checked(old('exclude_from_substitution',$employee->exclude_from_substitution) == 1)><label class="form-check-label" for="f_exclude_from_substitution">Exclude From Substitution</label></div>
@error('exclude_from_substitution')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<label class="form-label" for="f_personal_meeting_link">Personal Meeting Link</label>
<input type="url" class="form-control" id="f_personal_meeting_link" name="personal_meeting_link" value="{{ old('personal_meeting_link',$employee->personal_meeting_link) }}">
@error('personal_meeting_link')<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-12 col-md-6">
<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="allow_marking_locked_attendance" value="1" id="f_allow_marking_locked_attendance" @checked(old('allow_marking_locked_attendance',$employee->allow_marking_locked_attendance) == 1)><label class="form-check-label" for="f_allow_marking_locked_attendance">Allow Marking Locked Attendance</label></div>
@error('allow_marking_locked_attendance')<div class="text-danger small">{{ $message }}</div>@enderror</div>
</div>
<h6 class="border-bottom border-danger pb-2 mt-4 mb-3">Additional Information</h6>
<div class="row g-3">
<div class="col-12 col-md-6">
<label class="form-label" for="f_emergency_contact">Emergency Contact No.</label>
<input type="tel" class="form-control" id="f_emergency_contact" name="emergency_contact" value="{{ old('emergency_contact',$employee->emergency_contact) }}">
@error('emergency_contact')<div class="text-danger small">{{ $message }}</div>@enderror</div>
</div>
</div><div class="card-footer d-flex gap-2"><button type="submit" class="btn btn-primary">Save Employee</button><a href="{{ route('employee-info.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
</form></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
 const select=document.getElementById('login_action');
 if(!select) return;
 function toggle(){
  const existing=document.getElementById('existing-login-fields');
  const fresh=document.getElementById('new-login-fields');
  existing.hidden=select.value!=='existing';
  fresh.hidden=select.value!=='new';
  existing.querySelectorAll('input,select').forEach(el=>el.disabled=existing.hidden);
  fresh.querySelectorAll('input').forEach(el=>el.disabled=fresh.hidden);
 }
 select.addEventListener('change',toggle);toggle();
});
</script>
@endsection
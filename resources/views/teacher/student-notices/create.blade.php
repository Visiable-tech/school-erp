@extends('layouts.admin')
@section('title','Create Notice')
@section('content')
<div class="container-fluid"><h4>Create Student Notice</h4>
@if($errors->any())<div class="alert alert-danger">{{ implode(' | ',$errors->all()) }}</div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ route('teacher.student-notices.store') }}">@csrf
<div class="row g-3"><div class="col-lg-8"><div class="card card-body">
<label>Title</label><input class="form-control mb-3" name="title" required maxlength="255" value="{{ old('title') }}">
<label>Message / links</label><textarea class="form-control" id="notice-body" name="body" rows="12">{{ old('body') }}</textarea>
<label class="mt-3">Attachments (images, documents, PDF, video)</label><input class="form-control" type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.webm"><small>Up to 8 files, 50 MB each, subject to server limits. Use a link for large videos.</small>
</div></div><div class="col-lg-4"><div class="card card-body gap-2">
<label>Academic year</label><select class="form-select" name="academic_year_id" id="year" required>@foreach($years as $y)<option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>@endforeach</select>
<label>Send to</label><select class="form-select" name="recipient_type" id="target"><option value="section">Class & Section</option><option value="class">Entire Class (all enrolled sections must be assigned)</option><option value="student">Individual Student</option>@can('teacher-notice.school-wide')<option value="school">Full School</option>@endcan</select>
<div id="class-wrap"><label>Class</label><select class="form-select" name="school_class_id" id="class"><option value="">Select</option>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
<div id="section-wrap"><label>Section</label><select class="form-select" name="section_id" id="section"></select></div>
<div id="student-wrap"><label>Student</label><select class="form-select" name="student_id" id="student"></select></div>
<div id="recipient-error" class="text-danger small"></div>
<label>Priority</label><select class="form-select" name="priority"><option>normal</option><option>important</option><option>urgent</option></select>
<label>Publish at</label><input class="form-control" type="datetime-local" name="publish_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
<label>Expires at (optional)</label><input class="form-control" type="datetime-local" name="expires_at">
<button class="btn btn-outline-primary" name="action" value="draft">Save Draft</button><button class="btn btn-primary" name="action" value="publish">Publish Notice</button>
</div></div></div></form></div>
@endsection
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
if(window.ClassicEditor) ClassicEditor.create(document.querySelector('#notice-body'),{toolbar:['heading','bold','italic','link','bulletedList','numberedList','blockQuote','insertTable','undo','redo']}).catch(console.error);
const el=id=>document.getElementById(id), target=el('target'), cls=el('class'), sec=el('section'), student=el('student'), year=el('year');
function visibility(){for(const [id,show] of [['class',target.value!=='school'],['section',['section','student'].includes(target.value)],['student',target.value==='student']]){el(id+'-wrap').hidden=!show;el(id).disabled=!show;el(id).required=show;}}
function fill(node,rows,label){node.replaceChildren(new Option('Select',''));rows.forEach(r=>node.add(new Option(label(r),r.id)));}
const classMap=@json($classMap), allClasses=@json($classes->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values());
function filterClasses(){const allowed=(classMap[year.value]||[]).map(String);fill(cls,allClasses.filter(c=>allowed.includes(String(c.id))),r=>r.name);}
let requestNumber=0;async function load(keep=false){visibility();const seq=++requestNumber, selected=keep?sec.value:'';if(!keep)fill(sec,[],r=>r.name);fill(student,[],r=>r.name);el('recipient-error').textContent='';if(!cls.value||target.value==='school')return;try{const url=new URL(@json(route('teacher.student-notices.recipients')),location.href);url.search=new URLSearchParams({academic_year_id:year.value,school_class_id:cls.value,section_id:selected});const response=await fetch(url,{headers:{Accept:'application/json'}});if(!response.ok)throw Error('Unable to load recipients');const data=await response.json();if(seq!==requestNumber)return;fill(sec,data.sections,r=>r.name);sec.value=selected;fill(student,data.students,r=>r.student_name+' ('+r.admission_no+')');}catch(e){if(seq===requestNumber)el('recipient-error').textContent=e.message;}}
[target,cls].forEach(x=>x.addEventListener('change',()=>load()));year.addEventListener('change',()=>{filterClasses();load();});filterClasses();sec.addEventListener('change',()=>load(true));visibility();
</script>
@endpush

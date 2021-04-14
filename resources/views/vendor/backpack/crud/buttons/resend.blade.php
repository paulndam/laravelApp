@if ($crud->hasAccess('update'))
<a href="{{ url($crud->route.'/'.$entry->getKey().'/resend') }} " class="btn btn-xs btn-success"><i class="fa fa-check"></i> Resend</a>
@endif
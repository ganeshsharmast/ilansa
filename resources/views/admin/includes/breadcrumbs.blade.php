<div class="page-header">
  <nav aria-label="breadcrumb" name="{{$breadcrumbs['sidebar']}}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('admin/'.$breadcrumbs['url'])}}">{{$breadcrumbs['header']}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{$breadcrumbs['sub_header']}}</li>
    </ol>
  </nav>
</div>

<div class="custom-breadcrumb">
    @if(isset($breadcrumbs['sidebar']))
        @if($breadcrumbs['sidebar']=='company_list')    
        <button class="badge-info" onclick="location.href = '{{url('admin/company/create')}}'">Create Company</button>
        @elseif($breadcrumbs['sidebar']=='service_list')    
        <button class="badge-info" onclick="location.href = '{{url('admin/service/create')}}'">Create Service</button>
        @elseif($breadcrumbs['sidebar']=='service_edit')    
        <button class="badge-info" onclick="location.href = '{{url('admin/service/view')}}'">Service View</button>
        @elseif($breadcrumbs['sidebar']=='sub_service_list')    
        <button class="badge-info" onclick="location.href = '{{url('admin/sub-service/create')}}'">Create Sub Service</button>
        @elseif($breadcrumbs['sidebar']=='product_list')    
        <button class="badge-info" onclick="location.href = '{{url('admin/sub_service/create')}}'">Create Product</button>
        @elseif($breadcrumbs['sidebar']=='coupon_list')    
        <button class="badge-info" onclick="location.href = '{{url('admin/coupon/create')}}'">Create Coupon</button>
        @endif
        
    @endif
</div>

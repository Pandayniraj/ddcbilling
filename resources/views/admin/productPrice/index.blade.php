@extends('layouts.master')
@section('content')

    <link href="{{ asset("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.css") }}"
          rel="stylesheet" type="text/css"/>

    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {!! $page_title !!}
            <small>{!! $page_description ?? "Page description" !!}</small>
        </h1>
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Pricing</h3>
                    <a class="btn btn-default btn-sm" href="{!! route('admin.product-pricing.create', $product->id) !!}"
                       title="{{ trans('admin/courses/general.button.create') }}">
                        <i class="fa fa-plus-square"></i>
                    </a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="courses-table">
                            <thead>
                            <tr class="bg-purple">
                                <th></th>
                                <th> Project</th>
                                <th> Distributor Price</th>
                                <th> Retailer Price</th>
                                <th> Customer Price</th>
                                <th>{{ trans('admin/courses/general.columns.actions') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($productPrices as $productPrice)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{ $productPrice->project_name??'' }}</td>
                                    @if($productPrice->product->is_vat == 1) @php $vatCal=0.13; @endphp @else @php $vatCal=0; @endphp @endif
                                    <td>{{ ($productPrice->distributor_price+$vatCal*$productPrice->distributor_price)??'' }}</td>
                                    <td>{{ ($productPrice->retailer_price+$vatCal*$productPrice->retailer_price)??'' }}</td>
                                    <td>{{ ($productPrice->customer_price+$vatCal*$productPrice->customer_price)??'' }}</td>
                                    <td>
                                        @if ( $productPrice->isEditable() || $productPrice->canChangePermissions() )
                                            @if(\Auth::user()->hasRole('admins'))
                                                <a href="{!! route('admin.product-pricing.edit', $productPrice->id) !!}"
                                                   title="{{ trans('general.button.edit') }}"><i
                                                        class="fa fa-edit"></i></a>
                                            @endif
                                        @else
                                            <i class="fa fa-edit text-muted"
                                               title="{{ trans('admin/courses/general.error.cant-edit-this-course') }}"></i>
                                        @endif

                                        {{-- @if ( $productPrice->isDeletable() )
                                        <a href="{!! route('admin.products.confirm-delete', $productPrice->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash deletable"></i></a>
                                        @else
                                        <i class="fa fa-trash text-muted" title="{{ trans('admin/courses/general.error.cant-delete-this-course') }}"></i>
                                        @endif --}}
                                        <a href="{{route('admin.product-pricing.confirm-delete',$productPrice->id)}}"><i
                                                class="fa fa-trash" title="International Purchase"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $productPrices->appends(Request::except('page')) !!}
                    </div> <!-- table-responsive -->

                </div>
            </div>
        </div>
    </div>
@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
    <!-- DataTables -->
    <script src="{{ asset ("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js") }}"></script>

    <script language="JavaScript">
        function toggleCheckbox() {
            checkboxes = document.getElementsByName('chkCourse[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = !checkboxes[i].checked;
            }
        }

    </script>
    @include('confirm-multiple-delete')
    <script type="text/javascript">
        $('.multi-delete-button').multipleDeleteIndex({

            title: "Are you sure !!",
            body: 'Delete All Selected Products',
            route: '{{ route('admin.products.multipledelete') }}',
            formid: 'frmCourseList',
        });
    </script>

@endsection

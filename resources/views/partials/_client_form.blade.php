<?php $readonly = ($client->isEditable()) ? '' : 'readonly'; ?>
<link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet"
      type="text/css"/>

<script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet"/>
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>
<div class="content" style="padding-left: 0;">
    <div class="col-md-6" style="padding-left: 0;">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label" style="text-transform: capitalize;">
                {{ $_GET['relation_type']}} Name
            </label>
            <div class="col-sm-8">
                {!! Form::text('name', null, ['class' => 'form-control', $readonly]) !!}
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">
                {!! Form::label('phone', trans('admin/clients/general.columns.phone')) !!}
            </label>
            <div class="col-sm-8">
                {!! Form::text('phone', null, ['class' => 'form-control', $readonly]) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">
                Physical Address
            </label>
            <div class="col-md-8">
                {!! Form::textarea('physical_address', null, ['class'=>'form-control', 'rows'=>'2']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">
                {!! trans('admin/clients/general.columns.email') !!}
            </label>
            <div class="col-sm-8">
                {!! Form::text('email', null, ['class' => 'form-control', $readonly]) !!}
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">VAT/TAX/PAN ID</label>
            <div class="col-sm-8">
                {!! Form::text('vat', null, ['class' => 'form-control', $readonly]) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Outlet</label>
            <div class="col-sm-8">
                <select name="outlet_id" class="form-control searchable" id="outlet_id" requried>
                    <option value="">Select</option>
                    @foreach($outlets as $key => $outlet)
                        <option value={{ $key}} @if(isset($client) && (old('outlet_id')??$client->outlet_id == $key)) selected @endif>{{ $outlet }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Customer Group</label>
            <div class="col-sm-8">
                <select name="customer_group" class="form-control searchable" id="customer_group" requried>
                    <option value="">Select</option>
                    @foreach($groups as $key => $group)
                        <option value={{ $key}} @if(isset($client) && (old('customer_group')??$client->customer_group == $key)) selected @endif>{{ $group }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Relation Type</label>
            <div class="col-sm-8">
                <select name="relation_type" class="form-control searchable" id="relation_type" requried>
                    <option value="distributor" @if (isset($client) && (old('relation_type')??$client->relation_type == 'distributor')) selected @endif>Distributors
                    </option>
                    <option value="staff" @if (isset($client) && (old('relation_type')??$client->relation_type == 'staff')) selected @endif>Staff</option>
                    <option value="retailer" @if (isset($client) && (old('relation_type')??$client->relation_type == 'retailer')) selected @endif>Retailer</option>
                    <option value="direct_customer" @if (isset($client) && (old('relation_type')??$client->relation_type == 'direct_customer')) selected @endif>Direct
                        Customers</option>
                    <option value="staff-milk" @if (isset($client) && (old('relation_type')??$client->relation_type == 'staff-milk')) selected @endif>Staff Milk</option>
                </select>
            </div>
        </div>
        @if(\Request::segment(3) != 'create')
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">
                    Ledger ID
                </label>
                <div class="col-sm-6">
                    {!! Form::select('ledger_id',$ledger_list,$ledger_id,['class'=>'form-control searchable select2',$readonly]) !!}
                </div>
            </div>
        @endif

        @if((\Request::get('relation_type') == "staff") || (\Request::get('relation_type') =="staff-milk"))
            @php $dontShow = 1; @endphp
        @else
            @php $dontShow = 0; @endphp
        @endif
        @if($dontShow != 1)
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">
                    Bank Name
                </label>
                <div class="col-sm-8">
                    {!! Form::text('bank_name', null, ['class' => 'form-control', $readonly]) !!}
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">
                    Branch
                </label>
                <div class="col-sm-8">
                    {!! Form::text('bank_branch', null, ['class' => 'form-control', $readonly]) !!}
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">
                    Account Number
                </label>
                <div class="col-sm-8">
                    {!! Form::text('bank_account', null, ['class' => 'form-control', $readonly]) !!}
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-6">
        @if($dontShow != 1)
        @if(\Request::get('relation_type') !="distributor")
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label" style="text-transform: capitalize;">
                    {!! Form::label('parent_distributor', 'Distributor') !!}
                </label>
                <div class="col-sm-8">
                    {!! Form::select('parent_distributor', $distributors, null, ['class' => 'form-control searchable', $readonly,'placeholder'=>'Select Distributor']) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label" style="text-transform: capitalize;">
                    {!! Form::label('route_id', 'Route') !!}
                </label>
                <div class="col-sm-8">
                    {!! Form::select('route_id', $deliveryroutes, null, ['class' => 'form-control searchable', $readonly,'placeholder'=>'Select Routes']) !!}
                </div>
            </div>
        @endif

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Accounting Type</label>
            <div class="col-sm-8">
                <select class='form-control searchable select2 ' name="types" required>
                    @if($_GET['relation_type']=='supplier')
                        <optgroup label="Supplier">
                                <?php
                                //Sunny_deptors
                                $groups = \App\Models\COAgroups::orderBy('code', 'asc')->where('parent_id', \FinanceHelper::get_ledger_id('SUPPLIER_LEDGER_GROUP'))->where('org_id', \Auth::user()->org_id)->get();
                                foreach ($groups as $grp) {
                                    echo '<option value="' . $grp->id . '"' .
                                        (($grp->name == $client->type) ? 'selected="selected"' : "") .
                                        '>'
                                        . $grp->name . '</option>';
                                }
                                ?>
                        </optgroup>
                    @else

                        <optgroup label="Customers">
                                <?php
                                //Sunny_creditors
                                $groups = \App\Models\COAgroups::orderBy('code', 'asc')->where('parent_id', \FinanceHelper::get_ledger_id('CUSTOMER_LEDGER_GROUP'))->where('org_id', \Auth::user()->org_id)->get();
                                foreach ($groups as $grp) {
                                    echo '<option value="' . $grp->id . '"' .
                                        (($grp->name == $client->type) ? 'selected="selected"' : "") .
                                        '>'
                                        . $grp->name . '</option>';
                                }
                                ?>
                        </optgroup>
                    @endif
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">
                Threshold amount <small style="font-size:9px;font-weight:light;font-style: italic;">Leave it 0 for no threshold</small>
            </label>
            <div class="col-md-8">
                {!! Form::number('threshold_amount', null, ['class'=>'form-control','steps'=>'0.1']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">
                Threshold time (days)
            </label>
            <div class="col-md-8">
                {!! Form::number('threshold_time', null, ['class'=>'form-control','steps'=>'0.1']) !!}
            </div>
        </div>

        @if($client->name==null)
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">
                    Deposit Amount
                </label>
                <div class="col-md-8">
                    {!! Form::number('deposit_amount', null, ['class'=>'form-control','steps'=>'0.1']) !!}
                </div>
            </div>
        @endif

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">
                Reminder <i style='color: navy' class="fa fa-lightbulb-o"></i>
            </label>
            <div class="col-md-8">
                {!! Form::textarea('reminder', null, ['class'=>'form-control', 'rows'=>'2', 'placeholder'=> 'Will appear each time in thier related actions']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Image</label>
            <div class="col-md-8">
                {!! Form::file('image', null, ['class'=>'form-control', 'rows'=>'2']) !!}
            </div>
            <img src="{{ $client->image }}">
        </div>
        @endif

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Notes</label>
            <div class="col-md-8">
                {!! Form::textarea('notes', null, ['class'=>'form-control', 'rows'=>'2']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">
            {!! Form::checkbox('enabled', '1', $client->enabled) !!} {!! trans('general.status.enabled') !!}
        </label>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.searchable').select2();
    });
    $("#locations").autocomplete({
        source: "/admin/getCities"
        , minLength: 2
        , select: function (event, ui) {
            $('#locationvalue').val(ui.item.id);
        }
    });
</script>

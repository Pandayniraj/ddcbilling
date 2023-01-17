<div class="row">
    <div class="col-md-6">
      <div class="form-group">
          <label class="col-sm-4 control-label">Label<i class="imp">*</i></label>
           <div class="col-sm-8">
              <input type="text" class="form-control ticket" name="label" placeholder="Label" value="@if(isset($entrytype->label)){{ $entrytype->label }}@endif" required="required">
          </div>
      </div>
    </div>


  <div class="col-md-6">
    <div class="form-group">
      <label class="col-sm-4 control-label">Name<i class="imp">*</i></label>
         <div class="col-sm-8">
          <input type="text" class="form-control ticket" name="name" placeholder="Name" value="@if(isset($entrytype->name)){{ $entrytype->name }}@endif" >
        </div>
    </div>
   </div>
</div>

<div class="row">
    <div class="col-md-6">
      <div class="form-group">
          <label class="col-sm-4 control-label">Prefix</label>
           <div class="col-sm-8">
              <input type="text" class="form-control ticket" name="prefix" placeholder="Prefix" value="@if(isset($entrytype->prefix)){{ $entrytype->prefix }}@endif">
          </div>
      </div>
    </div>


  <div class="col-md-6">
    <div class="form-group">
      <label class="col-sm-4 control-label">Suffix</label>
         <div class="col-sm-8">
          <input type="text" class="form-control ticket" name="suffix" placeholder="Suffix" value="@if(isset($entrytype->suffix)){{ $entrytype->suffix }}@endif" >
        </div>
    </div>
   </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label">Restriction Bank Cash</label>
            <div class="col-sm-8">
                <select class="form-control" name="restriction_bankcash" id="">
                    <option selected disabled > Select</option>
                    <option @if($entrytype->restriction_bankcash==1) selected="selected " @endif value="1" >Unrestricted</option>
                    <option @if($entrytype->restriction_bankcash==2) selected="selected " @endif value="2" > Atleast one Bank or Cash account must be present on Debit side</option>
                    <option @if($entrytype->restriction_bankcash==3) selected="selected " @endif value="3" > Atleast one Bank or Cash account must be present on Credit side</option>
                    <option @if($entrytype->restriction_bankcash==4) selected="selected " @endif value="4" > Only Bank or Cash account can be present on both Debit and Credit side</option>
                    <option @if($entrytype->restriction_bankcash==5) selected="selected " @endif value="5" > Only NON Bank or Cash account can be present on both Debit and</option>
                </select>
            </div>
        </div>
    </div>


    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label">Code</label>
            <div class="col-sm-8">
                <input type="text" class="form-control ticket" name="code" placeholder="Code" value="@if(isset($entrytype->code)){{ $entrytype->code }}@endif" >
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label">Base Type</label>
            <div class="col-sm-8">
                <input type="text" class="form-control ticket" name="base_type" placeholder="Base" value="@if(isset($entrytype->base_type)){{ $entrytype->base_type }}@endif">
            </div>
        </div>
    </div>


    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label">Numbering</label>
            <div class="col-sm-8">
                <input type="text" class="form-control ticket" name="numbering" placeholder="Suffix" value="@if(isset($entrytype->numbering)){{ $entrytype->numbering }}@endif" >
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label class="col-sm-4 control-label">Zero Padding</label>
         <div class="col-sm-8">
          <input placeholder="Zero Padding" value="@if(isset($entrytype->zero_padding)){{ $entrytype->zero_padding }}@endif" name="zero_padding" class="form-control"/>
    </div>
    </div>
   </div><div class="col-md-6">
    <div class="form-group">
      <label class="col-sm-4 control-label">Description</label>
         <div class="col-sm-8">
          <textarea placeholder="Description" name="description" class="form-control">@if(isset($entrytype->description)){{ $entrytype->description }}@endif</textarea>
    </div>
    </div>
   </div>
</div>


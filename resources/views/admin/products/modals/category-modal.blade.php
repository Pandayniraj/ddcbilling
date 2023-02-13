<style>
    .modal-dialog {
        height: 90%; /* = 90% of the .modal-backdrop block = %90 of the screen */
        width: 45%;
    }

    .select2-container {
        text-align: left !important;
        width: 100% !important;
    }
</style>
<div class="modal dialog fade" role="dialog" id="modal_dialog_category" style="min-height: 500px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: flex;justify-content: space-between;">
                <h3>Create Product Category</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label> Category Name</label>
                        {!! Form::text('name', null, ['class' => 'form-control','id'=>'catname', $readonly]) !!}
                    </div>
                    {{-- <div class="col-md-6 form-group">--}}
                    {{--     <label class="control-label">Menu</label>--}}
                    {{--     <select name="menu_id" class="form-control searchable" id="menu_id" required>--}}
                    {{--         <option value="">Select Menu</option>--}}
                    {{--         @foreach($menus as $o)--}}
                    {{--             <option value="{{$o->id}}">{{ucfirst($o->menu_name)}}</option>--}}
                    {{--         @endforeach--}}
                    {{--     </select>--}}
                    {{-- </div>--}}
                </div>
                <div class="modal-footer" id='form_edit_part'>
                    <button class="btn btn-success btn-sm" data-dismiss="modal" id="submitbtncat"><i
                            class="fa  fa-check"></i> Create
                    </button>
                    <button class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-time"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#submitbtncat').on('click', function () {
        var name = $('#catname').val()
        var menu_id = $('#menu_id').val()
        if (!name) {
            alert('Name is required')
            return false
        }
        $.ajax({
            type: "POST"
            // , contentType: "application/json; charset=utf-8"
            // , url: "/admin/productcats?name=" + name + "&menu_id=" + menu_id + "&enabled=1"
            , url: "{{route('admin.productcats.store')}}"
            , data: {
                name: name,
                enabled: 1,
                "_token": "{{ csrf_token() }}"
            },
            success: function (result) {
                if (result.data) {
                    var category_el = $('#product-category');
                    category_el.prepend($('<option>', {
                        value: result.data.id,
                        text: result.data.name
                    }));
                    category_el.val(result.data.id).change()
                }
                $('#catname').val('')
                $('#menu_id').val('')
            }
        });
    })
</script>

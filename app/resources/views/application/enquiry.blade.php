<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Enquiry Form::</title>
    <meta name="description" content="" />
	<meta name="keywords" content="" />
	<link href="{{ asset ("/img/favicon.png") }}" rel="icon">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Application CSS-->
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head> 

  <!-- Body -->
  <body>
  	
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="tbc-form">
                    <header class="row">
                        <div class="col-md-12">

                           <div class="col-md-6"> <a href="#">
                            <img src="{!!env('APP_URL') !!}{!! '/org/'. $organization->logo !!}" alt="" class="img-responsive">
                        </a> </div>
                           <div class="col-md-6">  <h3 class=""> Online Enquiry Form </h3> </div>
                        </div>
                        
                    </header> <!-- /header -->
                    <div class="clearfix"></div>
                    
                    <p class="lead">Please fill in this enquiry form completely as this will help speed up your enquiry.</p>
                    
                    
                    @foreach($errors->all() as $error)
                        <p class="alert alert-danger">{{ $error }}</p> 
                    @endforeach
        
                    @if(Session::has('msg'))
                    <br/>
                    <p id="ajax-success-msg" class="alert alert-success">{{Session::get('msg')}}</p>
                    @endif
            
                    {!! Form::open( ['route' => 'post_enquiry', 'id' => 'form_edit_lead'] ) !!} 
                    <div class="top">
                        @honeypot
                        
                        
                                 <p>Please select the product or service: Skip this option and write in description if you are not sure</p>
                                 <div class="checkbox">
                                 	<select name="course_id" id="course_id" class="form-control" style="width:200px;">
                                        <option value="">Select options</option>
                                    	@foreach($courses as $cr)
                                        <option value="{{ $cr->id }}">{{ $cr->name }}</option>
                                        @endforeach
                                    </select>                                 	
                                </div>	
                           
                    </div> <!-- /top -->
                    <div class="personal">
                        <label class="title bg-title">2. Personal or Company Details</label>
                        
                           
                           <div class="row">

                            <div class="col-md-6">
                                    <label class="" for="name">
                                            <span class="">Full Name *</span>
                                    </label>

                                    	{!! Form::text('name', null, ['class' => 'form-control', 'id'=>'name', 'required'=>'required']) !!}
                                        
                                       
                            </div> <div class="col-md-6">      
                               
                                     <label class="">
                                            <span class="">Mobile Phone *</span>
                                        </label>
                                    	{!! Form::text('mob_phone', null, ['class' => 'form-control', 'id'=>'input-1','required'=>'required']) !!}

                                </div>
                                       
                            </div>     
                                   <br/>
                                <div class="row">
                                    <div class="col-md-6">
                                    <label class="" for="input-1">
                                            <span class="">Email *</span>
                                        </label>
                                    	{!! Form::email('email', null, ['class' => 'form-control', 'id'=>'input-1','required'=>'required']) !!}
                                        
                                        
                                    </div><div class="col-md-6">
                               
                                     <label class="" for="input-1">
                                            <span class="">Residental No.</span>
                                        </label>
                                    	{!! Form::text('home_phone', null, ['class' => 'form-control', 'id'=>'input-1']) !!}
                                    </div>
                                       
                                 <div class="row">         
                                   
                             
                           
                       
                    </div> <!-- /Personal -->
                 
                    <hr style="margin:10px 0; border:none;">
                    <div class="experience">                        
                        <div class="table-responsive">
                            <div class="form-group">
                              <label for="comment">Please write enquiry or questions:</label>
                              <textarea class="form-control" rows="2" name="description" id="comment"></textarea>
                            </div>                       
                        </div>
                    </div> <!-- /Experience -->
                    <!-- <input type="checkbox" id="accept" name="accept"> Send me New Offers and Newsletters.<br><br> -->
                    <input type="hidden" id="communication_id" name="communication_id" value="{{ $communication_id }}"> <br><br>
                    <input type="hidden" id="org_id" name="org_id" value="1">
                    <input type="submit" class="btn btn-primary btn-lg" name="submit" id="submit" value="Submit Enquiry">
                    </form>
                </div>
            </div>
        </div>
    </div>
  
    <!-- jQuery library -->
    <script src="{{ asset ("/appjs/jquery.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/appjs/bootstrap.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/appjs/classie.js") }}" type="text/javascript"></script>
    
    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>
    <script>
		
		$(document).ready(function() {
						
			$('#course_id').change(function() {
				$('#auto-course').html($("#course_id option:selected").text());
			});
			
			$("input[type=email]").blur(function(){
				$.ajax({
						url: "/getLeadDetail",
						type: 'POST',
						data: { email: this.value, _token : $("input[name=_token]").val()},
						success: function(result){
							if(result['result'] == 1)	// It means lead with given email is already exits and we can auto populate the data
							{
								// .focus() is set only for style friendly
								$("select[name=intake_id] option[value='"+result['intake_id']+"']").attr("selected", true);
								$("select[name=course_id] option[value='"+result['course_id']+"']").attr("selected", true);
								$("select[name=title] option[value='"+result['title']+"']").attr("selected", true);
								$("input[name=name]").val(result['name']).focus();
								$("input[name=dob]").val(result['dob']).focus();
								$("input[name=mob_phone]").val(result['mob_phone']).focus();
								$("input[name=home_phone]").val(result['home_phone']).focus();
								$("input[name=guardian_phone]").val(result['guardian_phone']).focus();
								$("input[name=address_line_1]").val(result['address_line_1']).focus();
								
								$("select[name=gender] option[value='"+result['gender']+"']").attr("selected", true);
								$("select[name=nationality] option[value='"+result['nationality']+"']").attr("selected", true);
								$("select[name=marital_status] option[value='"+result['marital_status']+"']").attr("selected", true);
								$("input[name=disability]").val(result['disability']).attr("checked", true);
								
								$("input[name=disability_detail]").val(result['disability_detail']).focus();
								$("input[name=company_id]").val(result['company_id']);
								$("input[name=qualification]").val(result['qualification']);
								$("input[name=peroid_attended]").val(result['peroid_attended']);
								$("input[name=grade]").val(result['grade']);
								$("input[name=organization]").val(result['organization']);
								$("input[name=job_title]").val(result['job_title']);
								$("input[name=job_start]").val(result['job_start']);
								$("input[name=job_end]").val(result['job_end']);								
								$("input[name=job_desc]").val(result['job_desc']);
								$('#auto-name').html(result['name']);
								$('#auto-course').html(result['course_name']);
								
							}
						}
				});
			});
			
			/*$('#accept').change(function(){
				if(this.checked)
					$('#submit').removeAttr('disabled');
				else
					$('#submit').attr('disabled', 'disabled');
			});*/
		});
	</script>
    <script>
        (function() {
            // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
            if (!String.prototype.trim) {
                (function() {
                    // Make sure we trim BOM and NBSP
                    var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                    String.prototype.trim = function() {
                        return this.replace(rtrim, '');
                    };
                })();
            }
    
            [].slice.call( document.querySelectorAll( 'input.input__field' ) ).forEach( function( inputEl ) {
                // in case the input is already filled..
                if( inputEl.value.trim() !== '' ) {
                    classie.add( inputEl.parentNode, 'input--filled' );
                }
    
                // events:
                inputEl.addEventListener( 'focus', onInputFocus );
                inputEl.addEventListener( 'blur', onInputBlur );
            } );
    
            function onInputFocus( ev ) {
                classie.add( ev.target.parentNode, 'input--filled' );
            }
    
            function onInputBlur( ev ) {
                if( ev.target.value.trim() === '' ) {
                    classie.remove( ev.target.parentNode, 'input--filled' );
                }
            }
	
        })();
    </script>
  
  </body>
</html>

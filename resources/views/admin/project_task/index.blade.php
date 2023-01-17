@extends('layouts.master')
@section('content')

<style>
	.imgSect { display:none;
	}
	.loadMore {
		color:green;
		cursor:pointer;
	}
	.loadMore:hover {
		color:black;
	}
	.showLess {
		color:red;
		cursor:pointer;
	}
	.showLess:hover {
		color:black;
	}


	#leads-table td:first-child {
		text-align: center !important;
	}

	#leads-table td:nth-child(2) {
		font-weight: normal; !important;
	}

	#leads-table td:last-child a {
		margin: 0 2px;
	}

	tr {
		text-align: center;
	}

	#nameInput,
	#productInput,
	#statusInput,
	#ratingInput {
		background-image: url('/images/searchicon.png');
		/* Add a search icon to input */
		background-position: 10px 12px;
		/* Position the search icon */
		background-repeat: no-repeat;
		/* Do not repeat the icon image */
		font-size: 16px;
		/* Increase font-size */
		padding: 12px 12px 12px 40px;
		/* Add some padding */
		border: 1px solid #ddd;
		/* Add a grey border */
		margin-bottom: 12px;
		/* Add some space below the input */
		margin-right: 5px;
	}
	.box-body {
		padding:0px !important;
	}
	tr {
		text-align: left !important;
	}

	.openlink:hover {
		color: #3470aa;
		cursor: pointer;
	}

	.blink_me {
		animation: blinker 1s linear infinite;
	}

	@keyframes blinker {
		50% {
			opacity: 0;
		}
	}
	.thumbnail {
		max-width: 150px !important;
		height: auto;
		float: left;
		padding: 0px !important;
		margin-bottom: 6px !important;
		width: 130px;
		margin-left: 10px;
	}
	.thumbnail .caption {
		padding: 2px;
		color: #333;
		font-size: 14px;
		text-align: center;
	}
	i.fa.fa-refresh.fa-spin {
		color: red;
	}
	i.fa.fa-user-plus {
		color: #3470aa !important;
	}
	i.fa.fa-commenting {
		color: #3470aa !important;
	}
	.blink_me {
		animation: blinker 1s linear infinite;
		color: red;
		margin-left: 5px;
	}
	.openlink {
		text-transform: none;
		font-size: 18px;
		font-weight: 600;
		text-transform: capitalize;
	}
	.user-block {
		margin-top: 8px;
		margin-bottom: 4px;
		font-size: 15px;
		color: #000;
	}
	.box-body2 {
		padding-right: 0px !important;
	}

	.box {
		border-top:none !important;
		margin-bottom:0px !important;
		box-shadow:none;
	}
	.panel-body {
		padding: 0px;
	}

	section.content-header {
		margin-top: -15px;
		margin-bottom: 2px;
	}
	img.img-thum {
		height: 80px !important;
		max-width: 100px;
		max-width: 100% !important;
		width: auto;
	}
	.mailbox-attachment-icon {
		text-align: center;
		font-size: 49px;
		color: #666;
		padding: 0px 0px !important;
	}
	.deadline {
		font-size: 12px;
	}
	span.text-muted.pull-right {
		background-color: #fcf8e3;
	}
	span.text-muted {
		margin-top: 4px;
	}
	.panel-default>.panel-heading {
		color: #000 !important;
		background-color:#fff !important;
		font-size: 16px;
		font-weight: bold;

	}
	.box-footer {
		padding: 0px !important;
	}
	.box-comment {
		padding: 20px 14px 5px 18px !important;
		border-bottom: solid 1px #ddd;
		
	}
	.box-footer {
		margin-top: 20px;
		border-bottom-right-radius: 12px;
		border-bottom-left-radius: 12px;
	}
	.panel-default {
		border-color: #fff;
		box-shadow: rgb(100 100 111 / 20%) 0px 7px 29px 0px !important;
		border-radius: 12px;
		background-color: #ffff !important;
	}
	.content-header>h1 {
		font-size: 24px;
	}
	.panel{
		border: none !important;
	}
	@media (min-width: 769px) and (max-width: 1200px) {
		.box-header {
			padding: 0px;
		}
		span.btn.btn-default.btn-sm {
			margin-top: 6px;
			margin-left: 5px;
		}
		span.pull-right {
			display: inline-block;
			margin-top: 9px;
		}
		.input-group.input-group-sm.hidden-xs {
			width: 220px !important;
		}

	}
	@media (min-width: 481px) and (max-width: 768px) {
		input#start_date_project {
			width: 120px;
			display: inline-block;
			margin-left: 7px;
		}
		select#filter-user-project {
			margin-top: 7px;
		}
		a.btn.btn-default.btn-sm {
			width: 110px;
		}
		input#end_date_project {
			width: 114px !important;
		}
		.openlink {
			text-transform: uppercase;
			font-size: 15px;
		}
	}
	@media (min-width: 320px) and (max-width: 480px) {

		.filter.form-inline {
			margin: 0px !important;
		}
		input#end_date_project {
			width: 43% !important;
			display: inline-block;
			margin-top: 8px;
		}
		input#start_date_project {
			width: 55% !important;
			display: inline-block;
			margin-left: 5px;
		}
		select#filter-status-project {
			margin-top: 6px;
			margin-left: 0px;
		}
		a.btn.btn-default.btn-sm {
			width: 40% !important;
		}
		select#filter-user-project {
			width: 50% !important;
		}
		.box-body2 {
			padding-right: 0px !important;
			padding-left: 0px !important;
		}
	}


	.more {
		display: none;
	}
	.imagemore {
		display: none;
	}
	.imagemyBtn {
		color: #3c8dbc;
		cursor: pointer;
		font-size: 13px;
		position: relative;
		text-transform: capitalize;
	}
	p.myBtn {
		color: #3c8dbc;
		cursor: pointer;
		font-size: 13px;
		position: relative;
		text-transform: capitalize;
	}
	.panel-heading.headcont {
		padding: 0px;
	}


	.mar-left {
		margin-left: -14px;
	}

	.sSubNav {
		background: #3470aa;
		/*background-image: url('/images/nav-bg2.jpg');*/
		background-position: 10% 20%;
		background-repeat: no-repeat;
		background-size: cover;
		color: white;
	}
	.task-section {
		margin-top: 16px;
	}
	.form-control {
		border-radius: 4px !important;
		box-shadow: none;
		border-color: #3470aa6b;
		height: 30px;
	}
	.comment-head span {
		color: #5e5873 !important;
		font-weight: 700;
		position: relative;
		top: 9px;
		left: 19px;
		bottom: 16px !important;
	}
	.panel-head {
		margin-left: 9px;
		font-weight: 700;
		color: #3470aa;
		margin-bottom: 6px;
		margin-top: -14px;
	}
	.comment-head {
		margin-top: 11px;
	}

	span.activity-head {
		color: #5e5873 !important;
		font-weight: 700;
		position: relative;
		top: 9px;
		left: 5px;
		bottom: 16px !important;
		font-size: 14px !important;
	}
	.comment-text {
		color: #5e5873 !important;
	}
</style>


<div class="col-md-12">
	<div class="row">
		<div class="panel panel-default">
			<div class="panel-heading headcont sSubNav">
				<section class="content-header hidden-xs" >
					<h1>
						Just In Tasks!
						<small>Tasks that are just created</small>
					</h1>

					{{ TaskHelper::topSubMenu('topsubmenu.projects')}}
					{!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
				</section>
			</div>
			<div class="panel-body panel1">
				<div class='col-md-12 box'>
					<!-- Box -->
					{!! Form::open( array('route' => 'admin.leads.enable-selected', 'id' => 'frmLeadList') ) !!}
					<div class="box">
						<div class="box-header">
							<div class="wrap" style="margin-top:5px;">
								<div class="filter form-inline" style="margin:0 30px 0 0;">
									<a class="btn btn-default btn-sm" href="#" onclick="openwindow()" title="admin/projects/general.button.create">
										<i class="fa fa-edit"></i> Quick Task
									</a>
									{!! Form::text('start_date', \Request::get('start_date'), ['style' => 'width:120px;', 'class' => 'form-control', 'id'=>'start_date_project', 'placeholder'=>'Start Date']) !!}&nbsp;&nbsp;
									<!-- <label for="end_date" style="float:left; padding-top:7px;">End Date: </label> -->
									{!! Form::text('end_date', \Request::get('end_date'), ['style' => 'width:120px; display:inline-block;', 'class' => 'form-control', 'id'=>'end_date_project', 'placeholder'=>'End Date']) !!}&nbsp;&nbsp;

									{!! Form::select('user_id', ['' => 'Select user'] + $users, \Request::get('user_id'), ['id'=>'filter-user-project', 'class'=>'form-control', 'style'=>'width:110px; display:inline-block;']) !!}
									&nbsp;&nbsp;

									{!! Form::select('status_id', ['' => 'select','new' => 'New','ongoing' => 'ongoing','completed'=>'completed'], \Request::get('status_id'), ['id'=>'filter-status-project', 'class'=>'form-control', 'style'=>'width:100px; display:inline-block;']) !!}&nbsp;&nbsp;

									<span class="btn btn-primary btn-sm" id="btn-submit-filter-project-task">
										<i class="fa fa-list"></i> {{ trans('admin/projects/general.button.filter') }}
									</span>

									<span class="btn btn-default btn-sm" id="">
										<i class="fa fa-list"></i>

										<a href="/admin/project_tasks">{{ trans('admin/projects/general.button.reset') }}</a>
									</span>
									<span class="pull-right">
										<form method="GET" action="/admin/projects/search/tasks/">
											<div class="input-group input-group-sm hidden-xs" style="width: 172px;">
												<input type="text" name="table_search" class="form-control pull-right" placeholder="Search Tasks">

												<div class="input-group-btn">
													<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>

									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12">
	<div class="row">
		<div class="box-body box-1 col-md-8">
			<div class="panel-task">
				<div class="panel-head"> 
					<thead>
						<tr class="bg-info">
							<th></th>
							<th>{{ trans('admin/projects/general.columns.project_task') }}</th>
						</tr>
					</thead>
				</div>
				<div class="body-task">
					<div class="project-task-section" id="leads-table">
						@if(isset($projects_tasks) && !empty($projects_tasks))
						@foreach($projects_tasks as $lk => $lead)
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="col-md-12">
									<div class="task-section" title="{{ $lead->desciption}}">
										<span style="font-size: 15px !important; color: #5e5873 !important;">
											<span class="openlink" id="{{$lead->id}}">
												@if($lead->status == 'completed')
												<span style="color: gray; font-size: 25px !important;">
													{!! $lead->subject !!}</span>
													@else
													{!! $lead->subject !!}
													@endif

												</span>
												<?php
												if(!empty($lead->category_id)){
													echo '<mark>'. $lead->category->name.'</mark>' ;
												}
												?>
											</span>
											<div class="user-block">

												<img src="/images/profiles/{{$lead->user->image ? $lead->user->image:'logo.png'}}" class="img-circle img-bordered-sm" style="width: 33px; height: 33px;" alt="">
												<span class="username">
													<a href="/admin/profile/show/{{ $lead->user->id}}">{{ substr($lead->user->first_name,0,8)." ".substr($lead->user->last_name,0,15) }}</a>
													<small>{{ $lead->user->designation->designations }}</small>

												</span>
												
													@if($lead->status == 'new')
													<span class="description blink_me"> Created {{Carbon\Carbon::parse($lead->created_at)->diffForHumans()}}</span>
													@else
													<span class="description">  {{Carbon\Carbon::parse($lead->created_at)->diffForHumans()}}</span>
													@endif

													<?php
													if($lead->status == 'completed'){
														echo '<i style="color:green" class="fa fa-check-circle"></i> completed';
													}elseif($lead->status == 'ongoing'){
														echo '<i class="fa fa-refresh fa-spin"></i> ongoing';

													}else{
														echo 'new';
													}

													?>

													in {{ $lead->project->name}}
												
											</div>

											<i style="color:lightgray;" class="fa fa-user-plus"></i> assigned to 
											@foreach($lead->taskuser as $k => $v)
											<a target="_blank" href="/admin/profile/show/{{ $v->user_id }}"><img class="img-circle img-sm" height="30px" width="30px" alt="" src="{{ TaskHelper::getProfileImage($v->user_id) }}" title="{{ $v->user_id->username }}"> </a>
											@endforeach

											&nbsp; &nbsp;
											<span class="text-muted">
												<i style="color:" class="fa fa-commenting"> </i> {{ \TaskHelper::comment_count($lead->id)}} </span>

												<p class="deadline" style=""> {!! nl2br($lead->description) !!} &nbsp; Deadline: 
													<?php
													echo Carbon\Carbon::parse($lead->end_date)->diffForHumans();
													?>
												</p>


												<div class="col-md-12" style="padding-top:8px;">
													<div class="card">
														<div class="card-body">

															<div class="row">

																<?php 
																$task_attachments = \App\Models\ProjectTaskAttachment::where('task_id', $lead->id)->get();

																?>


																@foreach($task_attachments as $key => $ta)

																@if(is_array(getimagesize(public_path().'/task_attachments/'.$ta->attachment)))
																<div class="imgSect img-Sect{{$lk}}">
																	<div class="thumbnail img-responsive img-enlargable">
																		<a href="/task_attachments/{{$ta->attachment}}" download="{{$ta->attachment}}">

																			<img src="/task_attachments/{{$ta->attachment}}" alt="task_img" class="img-thum">
																			<div class="caption" style=" border-top: 1px solid #ddd;">
																				<p style="padding: 0; margin: 0;">
																					{{ substr($ta->attachment,-10) }}..
																				</p>
																			</div>
																		</a>
																	</div>
																</div>

																@else
																<div class="imgSect img-Sect{{$lk}}">
																	<div class="thumbnail img-responsive img-enlargable">
																		<a href="/task_attachments/{{$ta->attachment}}" download="{{$ta->attachment}}">

																			<span class="mailbox-attachment-icon" style="height: 82px;"><i class="fa fa-file-pdf-o"></i></span>

																			<div class="caption" style="padding: 0 !important;margin-top: -13px; border-top: 1px solid black;">
																				<p style="padding: 0; margin: 0;">
																					{{ substr($ta->attachment,-10) }}..
																				</p>
																			</div>
																		</a>
																	</div>
																</div>

																@endif
																@endforeach


															</div>
														</div>
													</div>

												</div>
									<!-- <div class="col-md-12 mar-left">
										<a href="javascript:void(0);" data-id="{{$lk}}" class="loadMore">Load Image</a>
									</div> -->
								</div>
							</div>
						</div>
					</div>


					@endforeach
					@endif

				</div>
			</div>
		</div>
	</div>
	<div class="box-body2 col-md-4">
		<div class="panel panel-default">
			<div class="comment-head">
				<span style="padding-bottom: 15px"> Comments</span>
			</div>
			<div class="panel-body">
				<div class="box-widget">
					<div class="box-footer box-comments">


						@php
						$index=0;
						@endphp
						@foreach($comments as $k => $v)
						<!-- /.box-comment -->
						<div class="box-comment">
							<!-- User image -->
							<img style="float: left; margin-right: 15px; width:4rem;height:4rem;" class="img-circle img-sm" src="/images/profiles/{{$v->user->image ? $v->user->image : 'logo.png'}}" alt="">

							<div class="comment-text">
								<span class="username">

									<a href="/admin/profile/show/{{ $v->user->id}}">
										{{ substr($v->user->first_name,0,8)." ".substr($v->user->last_name,0,15) }}</a>

										in <span class="text-muted">{{ $v->task->subject}}</span><br/>

									</span><!-- /.username -->
									@php
									$firstshow=mb_substr($v->comment_text,0,100);
									$secondshow=mb_substr($v->comment_text,100,null);
									@endphp

									<p>{{$firstshow}}<span class="dots dots{{$index}}">...</span><span class="more more{{$index}}">{{$secondshow}}</span></p>


									@if($secondshow && $secondshow != null)
									<p onclick="myFunction({{$index}})" class="myBtn myBtn{{$index}}">Read more</p>
									@endif
									
								</div>
								<br/>
								<!-- /.comment-text -->
							</div>
							@php
							$index+=1;
							@endphp
							@endforeach
						</div>
					</div>


					
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
					<span class="activity-head" style="font-weight: bold;font-size: 16px; padding-left:23px;"> Activities</span>
					<div class="box-widget">
						<div class="box-footer box-comments">
							@foreach($activityx as $k => $v)
							<div style="padding-bottom: 12px; padding-top: 12px; " class="box-comment">
								<img style="float: left; margin-right: 7px" width="25px" height="25px" class="img-circle img-sm" src="/images/profiles/{{$v->user->image ? $v->user->image : 'logo.png'}}" alt="">

								<div class="comment-text">
									<span class="username">

										<a href="/admin/profile/show/{{ $v->user->id}}">
											{{ substr($v->user->first_name,0,8)." ".substr($v->user->last_name,0,15) }}</a>
										</span>
										{!! mb_substr($v->activity,0,100) !!}
										
											<span class="text-muted">{{ $v->task->subject}}</span>
											<span class="pull-right">{{Carbon\Carbon::parse($v->created_at)->diffForHumans()}}</span>
										
										</div>
										<br/>
									</div>

									@endforeach


								</div>
							</div>

						</div>
					</div>


				</div>
			</div>
		</div>
		<div class='row'>

			<div class="">




			</div> <!-- table-responsive -->



		</div><!-- /.box-body -->

		<div class="box-body2 col-md-3">

		</div>


		<div style="text-align: center;"> {!! $projects_tasks->render() !!} </div>


	</div><!-- /.box -->

	<div role="dialog" class="modal fade" id="sendSMS" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="width:500px;">
			<!-- Modal content-->
			<div class="modal-content">
				<div style="background:green; color:#fff; text-align:center; font-weight:bold;" class="modal-header">
					<button data-dismiss="modal" class="close" type="button">×</button>
					<h4 class="modal-title">Send SMS</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<!-- <span>Note: Maximum 138 character limit</span><br/> -->
						<!-- <textarea rows="3" name="message" class="form-control" id="compose-textarea" onBlur="countChar(this)" placeholder="Type your message." maxlength="138"></textarea> -->
						<textarea rows="3" name="message" class="form-control" id="compose-textarea" placeholder="Type your message."></textarea>
						<!-- <span class="char-cnt"></span> -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="document.forms['frmLeadList'].action = '{!! route('admin.leads.send-sms') !!}';  document.forms['frmLeadList'].submit(); return false;" title="{{ trans('general.button.disable') }}" class="btn btn-primary">{{ trans('general.button.send') }}</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('general.button.cancel') }}</button>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="lead_type" id="lead_type" value="{{\Request::get('type')}}">
	{!! Form::close() !!}

</div><!-- /.col -->

</div><!-- /.row -->
@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')

<script language="JavaScript">
	function toggleCheckbox() {
		checkboxes = document.getElementsByName('chkLead[]');
		for (var i = 0, n = checkboxes.length; i < n; i++) {
			checkboxes[i].checked = !checkboxes[i].checked;
		}
	}

</script>

<script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
<link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet" type="text/css" />
<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>
<script>
	// $(document).ready(function () {
	// 	size = $(".imgSect").size();
	// 	x=5;
	// 	$('.imgSect:lt('+x+')').show();
	// 	$('.loadMore').click(function () {
	// 		x= (x+5 <=size) ? x+5 : size;
	// 		$('.imgSect :lt('+x+')').show();
	// 	});
	// 	$('.showLess').click(function () {
	// 		x=(x-5<0) ? 3 : x-5;
	// 		$('.imgSect').not(':lt('+x+')').hide();
	// 	});
	// });
	$(document).ready(function(){
		$(".imgSect").slice(0, 5).show();
		$(".loadMore").on("click", function(e){
			var id = $(this).attr('data-id');
			e.preventDefault();
			var section_class = '.img-Sect'+id;
			// alert(section_class);
			$(section_class+":hidden").slice(0, 5).slideDown();
			if($(section_class+":hidden").length == 0) {
				// $(this).text("No Content").addClass("noContent");
			}
		});

	})
</script>


<script>
	function myFunction(index) {
		var moreText = $('.more'+index).html();
		var btnText = $('.myBtn'+index).html();
		var dots = $('.dots'+index).html();
		  // console.log(btnText);
		  if(btnText == 'Read more')
		  {
		  	$('.dots'+index).css('display','none');
		  	$('.more'+index).css('display','inline');
		  	$('.myBtn'+index).html('Read less');
		  }
		  else
		  {
		  	$('.dots'+index).css('display','inline');
		  	$('.more'+index).css('display','none');
		  	$('.myBtn'+index).html('Read more');
		  }

		  // if (dots.style.display === "none") {
		  //     dots.style.display = "inline";
		  //     btnText.innerHTML = "Read more"; 
		  //     moreText.style.display = "none";
		  // } else {
		  //     dots.style.display = "none";
		  //     btnText.innerHTML = "Read less"; 
		  //     moreText.style.display = "inline";
		  // }
		}
		function myimageFunction(imageindex) {
			var moreText = document.getElementsByClassName("imagemore")[imageindex];
			var btnText = document.getElementsByClassName("imagemyBtn")[imageindex];

			if (dots.style.display === "none") {
				btnText.innerHTML = "more image"; 
				moreText.style.display = "none";
			} else {
				btnText.innerHTML = "less image"; 
				moreText.style.display = "inline";
			}
		}
	</script>
	<script>
		function openwindow() {
			var win = window.open(`/admin/project_tasks/create/task-global-modal`, '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=500,left=500,width=400, height=560');
		}

		function HandlePopupResult(result) {
			if (result) {
				location.reload();
				let tasks = result.task;
				var newtaskid = result.id;
				setTimeout(function() {
					$('#new').prepend(tasks);
					$("#ajax_status").after("<span style='color:green;' id='status_update'>Task sucessfully created</span>");
					$('#status_update').delay(3000).fadeOut('slow');
					jQueryTaskStuff(`.new${newtaskid}`);
				}, 500);
			} else {
				$("#ajax_status").after("<span style='color:red;' id='status_update'>failed to create Task</span>");
				$('#status_update').delay(3000).fadeOut('slow');
			}
		}

		$(function() {
			$('#start_date_project').datetimepicker({
				//inline: true,
				format: 'YYYY-MM-DD HH:mm'
				, sideBySide: true
			});
			$('#end_date_project').datetimepicker({
				//inline: true,
				format: 'YYYY-MM-DD HH:mm'
				, sideBySide: true
			});
		});

	</script>
	<script>
		$("#btn-submit-filter-project-task").on("click", function() {

			user_id = $("#filter-user-project").val();
			start_date = $("#start_date_project").val();
			end_date = $("#end_date_project").val();
			status_id = $("#filter-status-project").val();


			window.location.href = "{!! url('/') !!}/admin/project_tasks?user_id=" + user_id + "&start_date=" + start_date + "&end_date=" + end_date + "&status_id=" + status_id;
		});

	</script>

	<script>
	 function HandlePeopleChanges(prams, task_ids, isChanged) { // this function is called from another window
	 	if (prams) {
	 		console.log(prams);
	 		$.post("/admin/ajaxTaskPeopleUpdate", {
	 			id: task_ids
	 			, peoples: prams
	 			, _token: $('meta[name="csrf-token"]').attr('content')
	 		}
	 		, function(data) {
	 			console.log(data);
						  //alert("Data: " + data + "\nStatus: " + status);
						});
	 	}
	 	if (isChanged) {
	 		location.reload();
	 	}

	 }

	 function UpdateChanges(isChanged) {
	 	if (isChanged) {
	 		location.reload();
	 	}
	 }


	 $('.openlink').click(function() {
	 	let id = this.id;
		  //window.open('/admin/project_task/'+id);
		  window.open('/admin/project_task/' + id, '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=0,left=100,width=1005, height=670');
		});



	 function searchName() {
		  // Declare variables
		  var input, filter, table, tr, td, i;
		  input = document.getElementById("nameInput");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("leads-table");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		  	td = tr[i].getElementsByTagName("td")[2];
		  	if (td) {
		  		if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
		  			tr[i].style.display = "";
		  		} else {
		  			tr[i].style.display = "none";
		  		}
		  	}
		  }
		}

		function searchProduct() {
		  // Declare variables
		  var input, filter, table, tr, td, i;
		  input = document.getElementById("productInput");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("leads-table");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		  	td = tr[i].getElementsByTagName("td")[3];
		  	if (td) {
		  		if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
		  			tr[i].style.display = "";
		  		} else {
		  			tr[i].style.display = "none";
		  		}
		  	}
		  }
		}

		function searchStatus() {
		  // Declare variables
		  var input, filter, table, tr, td, i;
		  input = document.getElementById("statusInput");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("leads-table");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		  	td = tr[i].getElementsByTagName("td")[6];
		  	if (td) {
		  		if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
		  			tr[i].style.display = "";
		  		} else {
		  			tr[i].style.display = "none";
		  		}
		  	}
		  }
		}

		function searchRating() {
		  // Declare variables
		  var input, filter, table, tr, td, i;
		  input = document.getElementById("ratingInput");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("leads-table");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		  	td = tr[i].getElementsByTagName("td")[7];
		  	if (td) {
		  		if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
		  			tr[i].style.display = "";
		  		} else {
		  			tr[i].style.display = "none";
		  		}
		  	}
		  }
		}

	</script>

	@endsection

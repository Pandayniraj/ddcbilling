@extends('layouts.master')
@section('content')
<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
               Performance Indicator Template
                <small>Indicator Form</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>
        <form method="post" action="/admin/performance/performance-indicator">   
 <div class="panel panel-custom">
      <div class="panel-heading">


<div class="row">
	<div class="col-sm-6">
                                        <div class="panel panel-custom">
                                            <h2 class="panel-title"> Functional Skills </h2><br/>

                                            <div class="bg-info box-header">

                                                <span class="panel-title pull-left">Quality of Work </span>
                                                <span class="panel-title pull-right">
                                                <span class="badge bg-yellow">15</span>
                                                 Marks </span>
                                                
                                            </div>


                                            <div class="box-body">
                                                <br/>

												<div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Accuracy, neatness and timeliness of work</span>
                                                    <div class="col-sm-4">
                                                        <select name="customer_experiece_management"
                                                                class="form-control">
                                                            @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                    
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Adherence to duties and procedures in Job Description and Work Instructions</span>
                                                    <div class="col-sm-4">
                                                        <select name="marketing" class="form-control">
                                                                  @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                           
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Synchronization with organizations/functional goals</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            
                                           
                                           
                                        </div>
                                    </div>




                            <div class="panel panel-custom">
                                            <div class="bg-info box-header">
                                                <span class="panel-title pull-left">Work Habits</span>
                                                <span class="panel-title pull-right">
                                                <span class="badge bg-yellow">20</span>
                                                 Marks </span>
                                                
                                            </div>


                                            <div class="box-body">
                                                <br/>

                                                <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Punctuality to workplace</span>
                                                    <div class="col-sm-4">
                                                        <select name="customer_experiece_management"
                                                                class="form-control">
                                                            @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                    
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Attendance</span>
                                                    <div class="col-sm-4">
                                                        <select name="marketing" class="form-control">
                                                                  @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                           
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Does the employee stay busy, look for things to do, takes initiatives at workplace</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>

                                             <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Submits reports on time and meets deadlines</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                        </div>
                                    </div>



                                     <div class="panel panel-custom">
                                            <div class="bg-info box-header">
                                                <span class="panel-title pull-left">Job Knowledge</span>
                                                <span class="panel-title pull-right">
                                                <span class="badge bg-yellow">15</span>
                                                 Marks </span>
                                                
                                            </div>


                                            <div class="box-body">
                                            <div class="row">
                                                    
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Skill and ability to perform job satisfactorily</span>
                                                    <div class="col-sm-4">
                                                        <select name="marketing" class="form-control">
                                                                  @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                           
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Shown interest in learning and improving</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>

                                             <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Problem solving ability</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                        </div>
                                    </div>



					           </div>






			<div class="col-sm-6">


                    <div class="panel panel-custom">
                         <h2 class="panel-title"> Interpersonal Skills </h2><br/>

                                            <div class="bg-success box-header">
                                                <span class="panel-title pull-left">Interpersonal relations/ behaviour</span>
                                                <span class="panel-title pull-right">
                                                    <span class="badge bg-yellow">25</span>
                                                 Marks </span>
                                                
                                            </div>


                                            <div class="box-body">
                                            <div class="row">
                                                    
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Responds and contributes to team efforts</span>
                                                    <div class="col-sm-4">
                                                        <select name="marketing" class="form-control">
                                                                  @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                           
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Responds positively to suggestions and instructions and criticism</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>

                                             <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Keeps supervisor informed of all details</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            

                                            <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Adapts well to changing circumstances</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Seeks feedback to improve</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>


                                        </div>
                                    </div>





                    <div class="panel panel-custom">
                         <h2 class="panel-title"> Leadership Skills </h2><br/>

                                            <div class="bg-warning box-header">
                                                <span class="panel-title pull-left">Interpersonal relations/ behaviour</span>
                                                <span class="panel-title pull-right">
                                                <span class="badge bg-yellow">25</span>
                                            Marks </span>
                                                
                                            </div>


                                            <div class="box-body">
                                            <div class="row">
                                                    
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Aspirant to climb up the ladder, accepts challenges, new responsibilities and roles. (out of 10)</span>
                                                    <div class="col-sm-4">
                                                        <select name="marketing" class="form-control">
                                                                  @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                           
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Innovative thinking - contribution to organizations and functions and personal growth. (out of 10)</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>

                                             <div class="row">
                                                <div class="form-group" id="border-none">
                                                    <span
                                                        class="col-sm-8">Work motivation. (out of 5)</span>
                                                    <div class="col-sm-4">
                                                        <select name="management" class="form-control">
                                                                @foreach($technical_competency as $key=>$tm)
                                                            <option value="{{$key}}">{{$tm}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><br>


                                        </div>
                                    </div>






        </div>
<div class="col-md-12">
        <div class="form-group">
            <button class="btn btn-primary" id="btn-submit-edit" type="submit">Save</button>
            <a class="btn btn-default" href="/admin/performance/indicator">Cancel</a>
        </div>
    </div>
</div>

</form>
</div>
</div>
@endsection
@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')
@endsection

@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
            Get Emails in Meronetwork ERP
                <small>{!! $page_descriptions !!}</small>
            </h1>
            <p>You may need to enable 2FA and use app passwords to connect to your IMAP account <a target="_blank" href="https://support.google.com/mail/answer/185833">Gmail Guidelines</a>  | <a target="_blank" href="https://help.zoho.com/portal/en/kb/bigin/channels/email/articles/generate-an-app-specific-password">Zoho Guidelines</a>

            </p>
           
        </section>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::model( $imap, ['route' => ['admin.myprofile.updateimap'], 'method' => 'POST', 'id' => 'form_edit_imap'] ) !!}

                <div class="tab-pane active" id="tab_details">
                    <div class="form-group">
                       <label> IMAP Email Address</label>
                        {!! Form::text('imap_email', null, ['class' => 'form-control']) !!}
                    </div>
                
                    <div class="form-group">
                         <label> IMAP Email Password</label>
                        {!! Form::text('imap_password', null, ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::submit( trans('general.button.update'), ['class' => 'btn btn-primary', 'id' => 'btn-submit-edit'] ) !!}
                    <a href="{!! route('admin.myprofile.showimap') !!}" title="{{ trans('general.button.cancel') }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
                </div>

                {!! Form::close() !!}

            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection

@section('body_bottom')

@endsection

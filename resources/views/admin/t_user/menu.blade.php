<?php $hal = "group";
use App\TUserModel;?>
@extends('layouts.admin.master')
@section('title', 'Manajemen User - Menu Group')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/select2/dist/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">

<style>
.example-modal .modal {
  position: relative;
  top: auto;
  bottom: auto;
  right: auto;
  left: auto;
  display: block;
  z-index: 1;
}

.example-modal .modal {
  background: transparent !important;
}

hr.style3 {
	border-top: 1px dashed #8c8b8b;
}
</style>

@endsection


@section('content')
<!-- Content Header (Page header) -->



<section class="content-header">
  <h1>
    Menu Group
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">

    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">{{$nama_group}}</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body" style="">
        <?php if (session('error') == 1) {
          ?>
          <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            Success : <strong class="d-block d-sm-inline-block-force">Data berhasil dirubah !</strong>
          </div><!-- alert -->
          <?php
        } ?>


        <div class="row">
          <div class="col-md-12">


            <!-- form start -->
            <form class="form-horizontal" action="{{ route('t_user.store') }}" method="post">
              {{csrf_field()}}
              <input type="hidden" name="group_id" value="{{$id}}">
              <div class="box-header">
                <a href="{{route('group.index')}}" class="btn btn-warning pull-left"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary btn-save pull-right"><i class="fa fa-floppy-o"></i> Simpan </button>
              </div>
              <div class="box-body">
                <hr class="style3" style="margin:-3px">
                <!-- <?php  ?> -->
                <?php $count = 1; ?>
                @foreach($menu2 as $data)

                <?php $no=1; ?>

                <div class="form-group" >
                  <?php
                    if ($data->menu_id_parent!=0) {
                      $style = "margin-left: 15px;";
                    }else {
                      $style = "margin-left: 5px;";
                    }
                   ?>
                  <label for="inputPassword3" class="col-sm-6 control-label" style="text-align:left;"><div style="{{$style}}">
                    {{$count}}.&nbsp {{$data->menu_nama}}
                  </div></label>
                  <div class="col-sm-6">
                    <div class="checkbox">
                      <label>


                        <?php
                        $t_user_id = TUserModel::where([
                          ['menu_id', '=', $data->menu_id],
                          ['group_id', '=', $id],
                          ])->value('t_user_id');
                          // echo "string = ".$t_user_id;
                          $menu_id = TUserModel::where('t_user_id','=',$t_user_id)->value('menu_id');

                          ?>
                          <input type="checkbox" name='menu_id[]' value="{{$data->menu_id}}" <?php if(($data->menu_id==$menu_id)) { ?> checked="checked" <?php } ?> >



                        </label>
                      </div>
                    </div>
                  </div>
                  <hr class="style3" style="margin:-3px">

                @foreach(App\MenuModel::where('menu_id_parent', '=', $data->menu_id)->get() as $menuItem)

                <div class="form-group" >
                  <?php
                    if ($menuItem->menu_id_parent!=0) {
                      $style = "margin-left: 20px;";
                    }else {
                      $style = "margin-left: 5px;";
                    }
                   ?>
                  <label for="inputPassword3" class="col-sm-6 control-label" style="text-align:left;"><div style="{{$style}}">
                  {{$count}}.{{$no}}.&nbsp {{$menuItem->menu_nama}}
                  </div></label>
                  <div class="col-sm-6">
                    <div class="checkbox">
                      <label>


                        <?php
                        $t_user_id = TUserModel::where([
                          ['menu_id', '=', $menuItem->menu_id],
                          ['group_id', '=', $id],
                          ])->value('t_user_id');
                          // echo "string = ".$t_user_id;
                          $menu_id = TUserModel::where('t_user_id','=',$t_user_id)->value('menu_id');

                          ?>
                          <input type="checkbox" name='menu_id[]' value="{{$menuItem->menu_id}}" <?php if(($menuItem->menu_id==$menu_id)) { ?> checked="checked" <?php } ?> >



                        </label>
                      </div>
                    </div>
                  </div>
                  <hr class="style3" style="margin:-3px">
                  <?php $no++ ?>
                  @endforeach
                  <?php $count ++ ?>
                  @endforeach
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  <a href="{{route('group.index')}}" class="btn btn-warning pull-left"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                  <button type="submit" class="btn btn-primary btn-save pull-right"><i class="fa fa-floppy-o"></i> Simpan </button>
                </div>
                <!-- /.box-footer -->
              </form>

              <!-- /.form-group -->
              <div class="form-group">
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->

          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>

  </section>
  <!-- /.content -->
  @endsection


  @section('js')
  <!-- DataTables -->
  <script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>


  <script>
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>




<script>
$(document).ready(function() {
  $('.js-example-basic-single').select2({
  });
});


$(".alert").delay(4000).slideUp(200, function() {
  $(this).alert('close');
});

</script>

@endsection

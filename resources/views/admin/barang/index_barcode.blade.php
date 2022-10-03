<?php $hal = "barangprint"; ?>
@extends('layouts.admin.master')
@section('title', 'Barang Barcode')

@section('css')
<!-- DataTables -->
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
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: #000;
}

/*.select2-container .select-all {
    position: absolute;
    top: 6px;
    right: 4px;
    width: 20px;
    height: 20px;
    margin: auto;
    display: block;
    background: url('data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNDc0LjggNDc0LjgwMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDc0LjggNDc0LjgwMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0zOTYuMjgzLDI1Ny4wOTdjLTEuMTQtMC41NzUtMi4yODItMC44NjItMy40MzMtMC44NjJjLTIuNDc4LDAtNC42NjEsMC45NTEtNi41NjMsMi44NTdsLTE4LjI3NCwxOC4yNzEgICAgYy0xLjcwOCwxLjcxNS0yLjU2NiwzLjgwNi0yLjU2Niw2LjI4M3Y3Mi41MTNjMCwxMi41NjUtNC40NjMsMjMuMzE0LTEzLjQxNSwzMi4yNjRjLTguOTQ1LDguOTQ1LTE5LjcwMSwxMy40MTgtMzIuMjY0LDEzLjQxOCAgICBIODIuMjI2Yy0xMi41NjQsMC0yMy4zMTktNC40NzMtMzIuMjY0LTEzLjQxOGMtOC45NDctOC45NDktMTMuNDE4LTE5LjY5OC0xMy40MTgtMzIuMjY0VjExOC42MjIgICAgYzAtMTIuNTYyLDQuNDcxLTIzLjMxNiwxMy40MTgtMzIuMjY0YzguOTQ1LTguOTQ2LDE5LjctMTMuNDE4LDMyLjI2NC0xMy40MThIMzE5Ljc3YzQuMTg4LDAsOC40NywwLjU3MSwxMi44NDcsMS43MTQgICAgYzEuMTQzLDAuMzc4LDEuOTk5LDAuNTcxLDIuNTYzLDAuNTcxYzIuNDc4LDAsNC42NjgtMC45NDksNi41Ny0yLjg1MmwxMy45OS0xMy45OWMyLjI4Mi0yLjI4MSwzLjE0Mi01LjA0MywyLjU2Ni04LjI3NiAgICBjLTAuNTcxLTMuMDQ2LTIuMjg2LTUuMjM2LTUuMTQxLTYuNTY3Yy0xMC4yNzItNC43NTItMjEuNDEyLTcuMTM5LTMzLjQwMy03LjEzOUg4Mi4yMjZjLTIyLjY1LDAtNDIuMDE4LDguMDQyLTU4LjEwMiwyNC4xMjYgICAgQzguMDQyLDc2LjYxMywwLDk1Ljk3OCwwLDExOC42Mjl2MjM3LjU0M2MwLDIyLjY0Nyw4LjA0Miw0Mi4wMTQsMjQuMTI1LDU4LjA5OGMxNi4wODQsMTYuMDg4LDM1LjQ1MiwyNC4xMyw1OC4xMDIsMjQuMTNoMjM3LjU0MSAgICBjMjIuNjQ3LDAsNDIuMDE3LTguMDQyLDU4LjEwMS0yNC4xM2MxNi4wODUtMTYuMDg0LDI0LjEzNC0zNS40NSwyNC4xMzQtNTguMDk4di05MC43OTcgICAgQzQwMi4wMDEsMjYxLjM4MSw0MDAuMDg4LDI1OC42MjMsMzk2LjI4MywyNTcuMDk3eiIgZmlsbD0iIzAwMDAwMCIvPgoJCTxwYXRoIGQ9Ik00NjcuOTUsOTMuMjE2bC0zMS40MDktMzEuNDA5Yy00LjU2OC00LjU2Ny05Ljk5Ni02Ljg1MS0xNi4yNzktNi44NTFjLTYuMjc1LDAtMTEuNzA3LDIuMjg0LTE2LjI3MSw2Ljg1MSAgICBMMjE5LjI2NSwyNDYuNTMybC03NS4wODQtNzUuMDg5Yy00LjU2OS00LjU3LTkuOTk1LTYuODUxLTE2LjI3NC02Ljg1MWMtNi4yOCwwLTExLjcwNCwyLjI4MS0xNi4yNzQsNi44NTFsLTMxLjQwNSwzMS40MDUgICAgYy00LjU2OCw0LjU2OC02Ljg1NCw5Ljk5NC02Ljg1NCwxNi4yNzdjMCw2LjI4LDIuMjg2LDExLjcwNCw2Ljg1NCwxNi4yNzRsMTIyLjc2NywxMjIuNzY3YzQuNTY5LDQuNTcxLDkuOTk1LDYuODUxLDE2LjI3NCw2Ljg1MSAgICBjNi4yNzksMCwxMS43MDQtMi4yNzksMTYuMjc0LTYuODUxbDIzMi40MDQtMjMyLjQwM2M0LjU2NS00LjU2Nyw2Ljg1NC05Ljk5NCw2Ljg1NC0xNi4yNzRTNDcyLjUxOCw5Ny43ODMsNDY3Ljk1LDkzLjIxNnoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K') no-repeat center;
    background-size: contain;
    cursor: pointer;
    z-index: 999999;
  }*/
  </style>

@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Barang Barcode
    <!-- <small>it all starts here</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Cetak Barcode Barang</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <form class="form form_filter" method="get" target="blank" action="{{url('barangprint_cetak')}}" autocomplete="off" enctype="">
        <div class="col-md-12 div_filter">
          <div class="col-md-3">
            <div class="form-group">
            <label>Barang</label>
              <select name="id_barang[]" id="my-select2" class="select2_multiple" multiple="" style="width:100%;" required="">
                <option value="barang_all" id="barang_all" selected="">Semua Barang</option>
                @foreach ($data['barang'] as $item)
                <option value="<?php echo $item->barang_id ?>"> <?php echo $item->barang_nama?></option>
               @endforeach
              </select>
              <!-- <button type="button" id="bt">Unselect</button> -->
            </div>
          </div> 
          <div class="col-md-3">
            <div class="form-group">
            <label>Satuan</label>
              <select name="id_satuan" id="" class="form-control select2" style="width:100%;">
                <option value="0" >--Pilih Satuan--</option>
                @foreach ($data['satuan'] as $item)
                <option value="<?php echo $item->satuan_id ?>" {{($item->satuan_id == 7) ? "selected":""}} > {{ $item->satuan_nama }} ({{$item->satuan_satuan}})</option>
               @endforeach
              </select>
            </div>
          </div> 
           <div class="col-md-3">
             <div class="form-group">
             <!-- <button type="button" style="margin-top: 25px;" class="btn btn-primary"><b><i class="fa fa-file-pdf-o"></i></b> Cetak</button> -->
             <button type="submit" style="margin-top: 25px;" class="btn btn-success"><b><i class="fa fa-print"></i></b> Cetak</button>
           </div>                    
           </div>                    
         </div>
      </form>
    
    </div>
    <!-- /.box-body -->
    
    <!-- /.box-footer-->
  </div>

</section>


@endsection

@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript">
$(document).ready(function() {
  $('input[name="range"]').daterangepicker({
   locale: {
      format: 'DD/MM/YYYY'
    }
  });
  $(".select2_multiple").select2({
    multiple: true,
  });

  /*$('.select2_multiple[multiple]').siblings('.select2-container').append('<span class="select-all"></span>');

  $(document).on('click', '.select-all', function (e) {
    selectAllSelect2($(this).siblings('.selection').find('.select2-search__field'));
  });

  $(document).on("keyup", ".select2-search__field", function (e) {
    var eventObj = window.event ? event : e;
    if (eventObj.keyCode === 65 && eventObj.ctrlKey)
       selectAllSelect2($(this));
  });
        
        
  function selectAllSelect2(that){

    var selectAll = true;
    var existUnselected = false;
    var id = that.parents("span[class*='select2-container']").siblings('select[multiple]').attr('id');
    var item = $("#" + id);

    item.find("option").each(function (k, v) {
        if (!$(v).prop('selected')) {
            existUnselected = true;
            return false;
        }
    });

    selectAll = existUnselected ? selectAll : !selectAll;

    item.find("option").prop('selected', selectAll).trigger('change');
  }*/

});

 $('.btn_batal_ganti').on('click',function(e){
    location.reload();
  });
  /*$('#bt').click(function () {
      $('#my-select2').val('').trigger("change");
  });*/
</script>
@endsection

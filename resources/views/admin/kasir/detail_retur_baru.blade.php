<?php $hal = 'kasirretur'; ?>
@extends('layouts.admin.master')
@section('title', 'Retur Penjualan')

@section('css')
    <link rel="stylesheet"
        href="{{ asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/selectize/css/selectize.css') }}">
    <link rel="stylesheet" href="{{ asset('public/selectize/css/selectize.bootstrap3.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/plugins/iCheck/all.css') }}">
@endsection


@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-refresh"></i> &nbsp;Retur Penjualan</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form action="{{ url('simpan_kasir_retur') }}" method="POST" target="blank">
                            @csrf
                            <input type="hidden" name="PARAM1[id_retur]" value="{{ $data['data']['id_retur'] }}">
                            <input type="hidden" name="PARAM1[id_gudang]" value="{{ $data['data']['id_gudang'] }}">
                            <input type="hidden" name="PARAM1[id_pelanggan]" value="{{ $data['data']['id_pelanggan'] }}">
                            <div class="box-body">
                                <div class="form-group col-md-12 " style="border:1px solid black;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal Input</label>
                                                {{-- name="PARAM1[tanggal]" --}}
                                                <input class="form-control" type="text"
                                                    placeholder="Tanggal"
                                                    value="{{ $data['data']['tanggal'] == '' ? date('d-m-Y') : $data['data']['tanggal'] }}"
                                                    readonly />
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Jatuh Tempo</label>
                                                {{-- name="PARAM1[tanggal_tempo]" --}}
                                                <input class="form-control" type="text"
                                                    placeholder="Tanggal Tempo"
                                                    value="{{ $data['data']['tanggal_tempo'] == '' ? date('d-m-Y') : $data['data']['tanggal_tempo'] }}"
                                                    readonly />
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Pelanggan</label>
                                                <input class="form-control" style="width: 100%;"
                                                    value="{{ $data['data']['nama_pelanggan'] }}" disabled />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal Faktur</label>
                                                {{-- name="PARAM1[tanggal_faktur]" --}}
                                                <input class="form-control" type="text"
                                                    placeholder="Tanggal Faktur"
                                                    value="{{ $data['data']['tanggal_faktur'] == '' ? date('d-m-Y') : $data['data']['tanggal_faktur'] }}"
                                                    readonly />
                                            </div>
                                            <div class="form-group">
                                                <label>Nomor Faktur</label>
                                                <input type="text" name="PARAM1[no_faktur]" class="form-control"
                                                    value="{{ $data['data']['nomor'] == '' ? $data['no_auto'] : $data['data']['nomor'] }}"
                                                    readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Gudang</label>
                                                <input class="form-control" style="width: 100%;"
                                                    value="{{ $data['data']['nama_gudang'] }}" disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12" style="border:1px solid black;">
                                    <h3>Detail Retur</h3>
                                    <hr />
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped table-barang"
                                            id="table_barang">
                                            <thead>
                                                <tr>
                                                    <th width="3%">No.</th>
                                                    <th width="35%">Nama Barang / Produk</th>
                                                    <th width="15%">Jumlah Retur</th>
                                                    <th width="15%">Satuan</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class=" col-md-6 col-sm-12 m-l-5">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-form">
                                            <tbody>
                                                <tr class="active">
                                                    <td colspan="2">Form Retur</td>
                                                </tr>
                                                <tr>
                                                    <td>Kode Retur</td>
                                                    <td>
                                                        <input type="text" name="PARAM1[kode_retur]" class="form-control"
                                                            required disabled value="{{ $data['data']['kode_retur'] }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal Retur</td>
                                                    <td>
                                                        <input class="form-control datepicker" type="text"
                                                            name="PARAM1[tanggal]" placeholder="Tanggal Retur" value="{{ date('d-m-Y') }}"
                                                            required disabled />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Alasan</td>
                                                    <td>
                                                        <textarea class="form-control" rows="2" name="PARAM1[keterangan]" required disabled>{{ $data['data']['keterangan'] }}</textarea>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ url('kasirreturbaru') }}" class="btn btn-md btn-warning"><span
                                        class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
@endsection


@section('js')
    <script src="{{ asset('public/admin/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript">
        var table_barang;
        var id_retur = '{{ $data['data']['id_retur'] }}';
        $(document).ready(function() {
            table_barang = $("#table_barang").DataTable({
                "paging": false
            });
            table_barang.on('order.dt search.dt', function() {
                table_barang.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            get_edit(id_retur);
        })

        function get_edit(id) {
            $.ajax({
                url: "{{ url('kasirreturbaru_get_edit') }} ",
                type: 'post',
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(respon) {
                    if (respon.produk.length > 0) {
                        for (i in respon.produk) {
                            table_barang.row.add([
                              `<div class="text-center"></div>`,
                              `<div>${respon.produk[i].nama_produk}</div>`,
                              `<div class="text-right">${format_angka(respon.produk[i].jumlah)}</div>`,
                              `<div>${respon.produk[i].nama_satuan}</div>`,
                            ]).draw(false);
                        }
                    }

                    if (respon.barang.length > 0) {
                        for (i in respon.barang) {
                            var nama = respon.barang[i].nama_barang;
                            var kode = respon.barang[i].kode_barang;
                            var alias = respon.barang[i].alias_barang;
                            if (alias === null || alias === "" || alias === 0) {
                                var nama_barang = kode + " || " + nama;
                            } else {
                                var nama_barang = kode + " || " + nama + " || " + alias;
                            }
                            table_barang.row.add([
                              `<div class="text-center"></div>`,
                              `<div>${nama_barang}</div>`,
                              `<div class="text-right">${format_angka(respon.barang[i].jumlah)}</div>`,
                              `<div>${respon.barang[i].nama_satuan}</div>`,
                            ]).draw(false);
                        }
                    }
                },
                complete: () => {
                    bindCheckbox();
                }
            })
        }

        function CheckAll(check) {
            $(".checkbox_detail").prop('checked', true).change();
            $("#btn_checkall").hide();
            $("#btn_uncheckall").show();
        }

        function UnCheckAll(check) {
            $(".checkbox_detail").prop('checked', false).change();
            $("#btn_checkall").show();
            $("#btn_uncheckall").hide();
        }

        function bindCheckbox() {
            $(`input[id^=checkbox]`).change(function() {
                var jumlah = $(this).attr('jumlah');
                var id = $(this).attr('tabel_id');
                var checked = !this.checked;
                $(`#jumlah_retur${id}`).prop('disabled', checked);
                $(`.hidden_param${id}`).prop('disabled', checked);
                $(`#jumlah_retur${id}`).val((!checked) ? jumlah : 0);
            })
        }
    </script>
@endsection

<?php $hal = 'kasir'; ?>
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
                        @if(session()->has('message'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-check"></i> Retur Sukses!</h4>
                                {{ session()->get('message') }}
                            </div>
                        @endif
                        <form id="form_retur" action="{{ url('simpan_kasir_retur') }}" method="POST">
                            @csrf
                            <input type="hidden" name="PARAM1[id_kasir]" value="{{ $data['data']['id_kasir'] }}">
                            <input type="hidden" name="PARAM1[id_gudang]" value="{{ $data['data']['id_gudang'] }}">
                            <input type="hidden" name="PARAM1[id_pelanggan]" value="{{ $data['data']['id_pelanggan'] }}">
                            <input type="hidden" name="PARAM1[tanggal]" value="{{ date('Y-m-d', strtotime($data['data']['tanggal'])) }}">
                            <input type="hidden" name="PARAM1[tanggal_faktur]"
                                value="{{ date('Y-m-d', strtotime($data['data']['tanggal_faktur'])) }}">
                            <input type="hidden" name="PARAM1[tanggal_tempo]"
                                value="{{ date('Y-m-d', strtotime($data['data']['tanggal_tempo'])) }}">
                            <div class="box-body">
                                <div class="form-group col-md-12 " style="border:1px solid black;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal Input</label>
                                                {{-- name="PARAM1[tanggal]" --}}
                                                <input class="form-control" type="text" placeholder="Tanggal"
                                                    value="{{ $data['data']['tanggal'] }}" readonly />
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Jatuh Tempo</label>
                                                {{-- name="PARAM1[tanggal_tempo]" --}}
                                                <input class="form-control" type="text" placeholder="Tanggal Tempo"
                                                    value="{{ $data['data']['tanggal_tempo'] }}" readonly />
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
                                                <input class="form-control" type="text" placeholder="Tanggal Faktur"
                                                    value="{{ $data['data']['tanggal_faktur'] }}" readonly />
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
                                    <button class="btn btn-success" type="button" onclick="CheckAll(true)"
                                        id="btn_checkall"><i class="fa fa-check"></i> &nbsp;Retur
                                        Semua Barang / Produk</button>
                                    <button style="display: none;" class="btn btn-default" type="button"
                                        onclick="UnCheckAll(true)" id="btn_uncheckall"><i class="fa fa-check"></i>
                                        &nbsp;Batal Retur
                                        Semua Barang / Produk</button>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped table-barang"
                                            id="table_barang">
                                            <thead>
                                                <tr>
                                                    <th width="3%"></th>
                                                    <th width="3%">No.</th>
                                                    <th width="35%">Nama Barang / Produk</th>
                                                    <th width="15%">Jumlah</th>
                                                    <th width="20%">Retur</th>
                                                    <th width="20%">Keterangan</th>
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
                                                        <input type="text" name="PARAM1[kode_retur]"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal Retur</td>
                                                    <td>
                                                        <input class="form-control datepicker" type="text"
                                                            name="PARAM1[tanggal_retur]" placeholder="Tanggal Retur"
                                                            value="{{ date('d-m-Y') }}" required />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Alasan</td>
                                                    <td>
                                                        <textarea class="form-control" rows="2" name="PARAM1[keterangan]" required></textarea>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ url('kasir') }}" class="btn btn-md btn-warning"><span
                                        class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
                                <button type="submit" class="btn bg-blue btn-md pull-right btn_simpan"><span
                                        class="glyphicon glyphicon-floppy-disk"></span> Simpan</button>
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
        var id_kasir = '{{ $data['data']['id_kasir'] }}';
        $(document).ready(function() {
            table_barang = $("#table_barang").DataTable({
                "paging": false
            });
            table_barang.on('order.dt search.dt', function() {
                table_barang.column(1, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            $("#form_retur").on('submit', function(e) {
                e.preventDefault();
                var that_ = this;
                swal({
                    title: "Retur Penjualan?",
                    text: "Anda akan meretur data ini",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: 'Ya',
                    cancelButtonText: "Tidak",
                }, function() {
                    that_.submit();
                })
            });

            get_edit(id_kasir);
        })

        function get_edit(id) {
            $.ajax({
                url: "{{ url('kasir_get_edit_retur') }} ",
                type: 'post',
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(respon) {
                    var checkJumlah = 0;
                    var checkJumlahRetur = 0;
                    if (respon.produk.length > 0) {
                        for (i in respon.produk) {
                            var isDisabled = (respon.produk[i].jumlah == 0) ? 'disabled' : '';
                            var isClassDisabled = (respon.produk[i].jumlah == 0) ? 'checkbox_detail_disabled' :
                                'checkbox_detail';
                            table_barang.row.add([
                                `<div class="text-center"><input type="checkbox" class="${isClassDisabled}" tabel_id="${respon.produk[i].id}" jumlah="${respon.produk[i].jumlah}" id="checkbox${respon.produk[i].id}" ${isDisabled}><div>`,
                                `<div class="text-center"></div>`,
                                `<div>${respon.produk[i].nama_produk}</div>`,
                                `<div class="text-right">${format_angka(respon.produk[i].jumlah)} ${respon.produk[i].nama_satuan}</div>`,
                                `<div class="text-center"><input id="jumlah_retur${respon.produk[i].id}" max="${respon.produk[i].jumlah}" min="1" onkeyup="maksimal($(this));" name="PARAM2[${respon.produk[i].id}][jumlah_retur]" class="form-control number-only" value="0" disabled/></div>`,
                                `<div>${respon.produk[i].keterangan_retur}</div>
                                <input type="hidden" class="hidden_param${respon.produk[i].id}" name="PARAM2[${respon.produk[i].id}][id_kasir_detail_produk]" value="${respon.produk[i].id}" disabled>
                                <input type="hidden" class="hidden_param${respon.produk[i].id}" name="PARAM2[${respon.produk[i].id}][id_produk]" value="${respon.produk[i].id_produk}" disabled>
                                <input type="hidden" class="hidden_param${respon.produk[i].id}" name="PARAM2[${respon.produk[i].id}][id_satuan]" value="${respon.produk[i].id_satuan}" disabled>
                                <input type="hidden" class="hidden_param${respon.produk[i].id}" name="PARAM2[${respon.produk[i].id}][status]" value="${respon.produk[i].status}" disabled>`
                            ]).draw(false);
                            checkJumlah = parseFloat(respon.produk[i].jumlah) + checkJumlah;
                            checkJumlahRetur = parseFloat(respon.produk[i].jumlah_retur) + checkJumlahRetur;
                        }
                    }
                    if (respon.barang.length > 0) {
                        for (i in respon.barang) {
                            var nama = respon.barang[i].nama_barang;
                            var kode = respon.barang[i].kode_barang;
                            var alias = respon.barang[i].alias_barang;
                            var isDisabled = (respon.barang[i].jumlah == 0) ? 'disabled' : '';
                            var isDisabledClass = (respon.barang[i].jumlah == 0) ? 'checkbox_detail_disabled' :
                                'checkbox_detail';
                            if (alias === null || alias === "" || alias === 0) {
                                var nama_barang = kode + " || " + nama;
                            } else {
                                var nama_barang = kode + " || " + nama + " || " + alias;
                            }
                            table_barang.row.add([
                                `<div class="text-center"><input type="checkbox" class="${isDisabledClass}" tabel_id="${respon.barang[i].id}" jumlah="${respon.barang[i].jumlah}" id="checkbox${respon.barang[i].id}" ${isDisabled}><div>`,
                                `<div class="text-center"></div>`,
                                `<div>${nama_barang}</div>`,
                                `<div class="text-right">${format_angka(respon.barang[i].jumlah)} ${respon.barang[i].nama_satuan}</div>`,
                                `<div class="text-center"><input id="jumlah_retur${respon.barang[i].id}" max="${respon.barang[i].jumlah}" min="1" onkeyup="maksimal($(this));" name="PARAM2[${respon.barang[i].id}][jumlah_retur]" class="form-control number-only" value="0" disabled/></div>`,
                                `<div>${respon.barang[i].keterangan_retur}</div>
                                <input type="hidden" class="hidden_param${respon.barang[i].id}" name="PARAM2[${respon.barang[i].id}][id_kasir_detail]" value="${respon.barang[i].id}" disabled>
                                <input type="hidden" class="hidden_param${respon.barang[i].id}" name="PARAM2[${respon.barang[i].id}][id_barang]" value="${respon.barang[i].id_barang}" disabled>
                                <input type="hidden" class="hidden_param${respon.barang[i].id}" name="PARAM2[${respon.barang[i].id}][id_satuan]" value="${respon.barang[i].id_satuan}" disabled>
                                <input type="hidden" class="hidden_param${respon.barang[i].id}" name="PARAM2[${respon.barang[i].id}][status]" value="${respon.barang[i].status}" disabled>`
                            ]).draw(false);
                            checkJumlah = parseFloat(respon.barang[i].jumlah) + checkJumlah;
                            checkJumlahRetur = parseFloat(respon.barang[i].jumlah_retur) + checkJumlahRetur;
                        }
                    }
                    var checkTotal = checkJumlah - checkJumlahRetur;
                    if (checkTotal == 0) $('.btn_simpan').replaceWith('');
                },
                complete: () => {
                    bindCheckbox();
                    $('.number-only').keypress(function(e) {
                        var txt = String.fromCharCode(e.which);
                        if (!txt.match(/[0-9.,]/)) {
                            return false;
                        }
                    });
                }
            })
        }

        function CheckAll(check) {
            const classCheckbox = $(".checkbox_detail")
            if (classCheckbox.length > 0) {
                classCheckbox.prop('checked', true).change();
                $("#btn_checkall").hide();
                $("#btn_uncheckall").show();
            }
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

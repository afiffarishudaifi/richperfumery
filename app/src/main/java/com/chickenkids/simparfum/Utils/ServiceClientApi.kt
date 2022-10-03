package com.chickenkids.simparfum.Utils

import com.chickenkids.simparfum.modelData.ResponseClosing
import com.chickenkids.simparfum.modelData.ResponseKasir
import com.chickenkids.simparfum.modelData.ResponseRedeem
import com.chickenkids.simparfum.modelStokopname.PersediaanResponse
import com.chickenkids.simparfum.modelStokopname.ResponseStokOpname
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST
import rx.Single

interface ServiceClientApi {

    @POST("api/get_struk.php")
    @FormUrlEncoded
    fun getDataKasir(
        @Field("id_kasir") id_kasir: String
    ): Single<ResponseKasir>

    @POST("api/get_strukredeem.php")
    @FormUrlEncoded
    fun getDataRedeem(
        @Field("id_kasir") id_kasir: String
    ): Single<ResponseRedeem>

    @POST("api/get_closingstruk.php")
    @FormUrlEncoded
    fun getDataClosing(
        @Field("id_gudang") id_gudang: String,
        @Field("tanggal") tanggal: String
    ): Single<ResponseClosing>

    @POST("api/get_detail_persediaan.php")
    @FormUrlEncoded
    fun getDataPersediaan(
        @Field("id_barang") id_barang: String,
        @Field("id_satuan") id_satuan: String,
        @Field("id_gudang") id_gudang: String
    ): Single<PersediaanResponse>


    @POST("api/insert_stok_opname.php")
    @FormUrlEncoded
    fun simpanStokOpname(
        @Field("id_stokopname") id_stokopname: String,
        @Field("tanggal") tanggal: String,
        @Field("id_barang") id_barang: String,
        @Field("id_gudang") id_gudang: String,
        @Field("stok") stok: String,
        @Field("fisik") fisik: String,
        @Field("selisih") selisih: String,
        @Field("id_satuan") id_satuan: String,
        @Field("keterangan") keterangan: String,
        @Field("id_log_stok") id_log_stok: String
    ): Single<ResponseStokOpname>
}
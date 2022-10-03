package com.chickenkids.simparfum.activityStokopname

import android.util.Log
import com.chickenkids.simparfum.Utils.ServiceClientApi
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers


class StokOpnamePresenter(private val view: StokOpnameView, private val serviceApi: ServiceClientApi) {
    fun getDataPersediaan(id_barang:String, id_satuan:String, id_gudang: String) {
        view.showLoading()
        serviceApi.getDataPersediaan(id_barang,id_satuan,id_gudang)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(
                { responseData ->
                    view.hideLoading()
                    if(responseData.error == 0){
                        view.errorDataPersediaan(responseData.message.toString())
                    }else{
                        view.showDataPersediaan(responseData)
                    }
                },
                { error ->
                    view.hideLoading()
                    Log.e("Error", error.message)
                }
            )
    }
    fun simpanStokOpname(
        id_stokopname:String,
        tanggal:String,
        id_barang:String,
        id_gudang:String,
        stok:String,
        fisik:String,
        selisih:String,
        id_satuan:String,
        keterangan:String,
        id_log_stok:String
    ) {

        view.showLoading()
        serviceApi.simpanStokOpname(
            id_stokopname,
            tanggal,
            id_barang,
            id_gudang,
            stok,
            fisik,
            selisih,
            id_satuan,
            keterangan,
            id_log_stok
        )
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(
                { responseData ->
                    view.hideLoading()
                    if(responseData.error!!){
                        //error
                        view.responseSaveStokOpname("0",responseData.message.toString())
                    }else{
                        //success
                        view.responseSaveStokOpname("1",responseData.message.toString())
                    }
                },
                { error ->
                    view.hideLoading()
                    Log.e("Error", error.message)
                }
            )
    }

    fun getDataPersediaan(id_barang: String) {

    }
}
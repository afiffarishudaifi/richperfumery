package com.chickenkids.simparfum

import android.util.Log
import com.chickenkids.simparfum.Utils.ServiceClientApi
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ClosingPrintPresenter(private val view: ClosingPrintView, private val serviceApi: ServiceClientApi) {

    fun getDataClosing(id_gudang:String,tanggal:String) {
        view.showLoading()
        serviceApi.getDataClosing(id_gudang,tanggal)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(
                { responseData ->
                    view.hideLoading()
                    if(responseData.error!! == 1 ){
                        view.showDataList(responseData)
                    }else{
                        view.errorDataList(responseData.message.toString())
                    }
                },
                { error ->
                    view.hideLoading()
                    Log.e("Error", error.message)
                }
            )
    }
}

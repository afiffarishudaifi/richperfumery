package com.chickenkids.simparfum.activityPrint

import android.util.Log
import com.chickenkids.simparfum.Utils.ServiceClientApi
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers


class PrintPresenter(private val view: PrintView, private val serviceApi: ServiceClientApi) {
    fun getDataKasir(id_kasir:String) {
        view.showLoading()
        serviceApi.getDataKasir(id_kasir)
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

class PrintRedeemPresenter(private val view: PrintRedeemView, private val serviceApi: ServiceClientApi) {
    fun getDataRedeem(id_kasir:String) {
        view.showLoading()
        serviceApi.getDataRedeem(id_kasir)
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
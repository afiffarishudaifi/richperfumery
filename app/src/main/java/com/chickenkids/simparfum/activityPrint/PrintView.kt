package com.chickenkids.simparfum.activityPrint

import com.chickenkids.simparfum.modelData.ResponseKasir


interface PrintView {
    fun showDataList(data: ResponseKasir)
    fun errorDataList(message:String)
    fun setToolbar()
    fun showLoading()
    fun hideLoading()
}
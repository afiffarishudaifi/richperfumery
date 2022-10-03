package com.chickenkids.simparfum

import com.chickenkids.simparfum.modelData.ResponseClosing

interface ClosingPrintView {
    fun showDataList(data: ResponseClosing)
    fun errorDataList(message:String)
    fun setToolbar()
    fun showLoading()
    fun hideLoading()
}

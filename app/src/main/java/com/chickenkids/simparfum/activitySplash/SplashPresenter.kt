package com.chickenkids.simparfum.activitySplash

class SplashPresenter(private val view: SplashView) {
    fun getLoading(time:Long) {
        view.runLoading(time)
    }
}
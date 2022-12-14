package com.chickenkids.simparfum.activityPrint

import android.app.Activity
import android.bluetooth.BluetoothAdapter
import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.View
import android.view.Window
import android.widget.AdapterView.OnItemClickListener
import android.widget.ArrayAdapter
import android.widget.ListView
import android.widget.TextView
import com.chickenkids.simparfum.R


class DeviceListActivity : Activity() {
    private var mBluetoothAdapter: BluetoothAdapter? = null
    private var mPairedDevicesArrayAdapter: ArrayAdapter<String>? = null

    private val mDeviceClickListener = OnItemClickListener { mAdapterView, mView, mPosition, mLong ->
        try {
            mBluetoothAdapter!!.cancelDiscovery()
            val mDeviceInfo = (mView as TextView).text.toString()
            val mDeviceAddress = mDeviceInfo.substring(mDeviceInfo.length - 17)
            Log.v(TAG, "Device_Address $mDeviceAddress")

            val mBundle = Bundle()
            mBundle.putString("DeviceAddress", mDeviceAddress)
            val mBackIntent = Intent()
            mBackIntent.putExtras(mBundle)
            setResult(RESULT_OK, mBackIntent)
            finish()
        } catch (ex: Exception) {

        }
    }

    override fun onCreate(mSavedInstanceState: Bundle?) {
        super.onCreate(mSavedInstanceState)
        requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS)
        setContentView(R.layout.device_list)

        setResult(RESULT_CANCELED)
        mPairedDevicesArrayAdapter = ArrayAdapter(this, R.layout.device_name)

        val mPairedListView = findViewById<ListView>(R.id.paired_devices)
        mPairedListView.adapter = mPairedDevicesArrayAdapter
        mPairedListView.onItemClickListener = mDeviceClickListener

        mBluetoothAdapter = BluetoothAdapter.getDefaultAdapter()
        val mPairedDevices = mBluetoothAdapter!!.bondedDevices

        val title_paired= findViewById<TextView>(R.id.title_paired_devices)

        if (mPairedDevices.size > 0) {
            title_paired.visibility = View.VISIBLE
            for (mDevice in mPairedDevices) {
                mPairedDevicesArrayAdapter!!.add(mDevice.name + "\n" + mDevice.address)
            }
        } else {
            val mNoDevices = "None Paired"//getResources().getText(R.string.none_paired).toString();
            mPairedDevicesArrayAdapter!!.add(mNoDevices)
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        if (mBluetoothAdapter != null) {
            mBluetoothAdapter!!.cancelDiscovery()
        }
    }

    companion object {
        protected val TAG = "TAG"
    }

}

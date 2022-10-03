package com.chickenkids.simparfum.activityPrint

import android.annotation.SuppressLint
import android.app.Activity
import android.app.ProgressDialog
import android.bluetooth.BluetoothAdapter
import android.bluetooth.BluetoothDevice
import android.bluetooth.BluetoothSocket
import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.os.Message
import android.util.Log
import android.widget.Button
import androidx.appcompat.app.AppCompatActivity
import com.chickenkids.simparfum.R
import com.chickenkids.simparfum.Utils.ServiceClientApi
import com.chickenkids.simparfum.Utils.StringAlignUtils
import com.chickenkids.simparfum.Utils.UtilNet
import com.chickenkids.simparfum.activityMain.MainActivity
import com.chickenkids.simparfum.modelData.ResponseRedeem
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast
import java.io.IOException
import java.nio.ByteBuffer
import java.util.*

class CetakRedeemActivity : AppCompatActivity(),
    PrintRedeemView,Runnable {

    /** SETTING PRINTER BLUETOOTH*/
    private lateinit var mScan: Button
    private lateinit var mPrint: Button
    private lateinit var mDisc: Button
    private var mBluetoothAdapter: BluetoothAdapter? = null
    private val applicationUUID = UUID.fromString("00001101-0000-1000-8000-00805F9B34FB")
    private var mBluetoothConnectProgressDialog: ProgressDialog? = null
    private var mBluetoothSocket: BluetoothSocket? = null
    private lateinit var mBluetoothDevice: BluetoothDevice

    private val mHandler = @SuppressLint("HandlerLeak")
    object : Handler() {
        override fun handleMessage(msg: Message) {
            mBluetoothConnectProgressDialog!!.dismiss()
            toast("DeviceConnected")
            prosesPrint()
        }
    }
    /** SETTING PRINTER BLUETOOTH*/
    var BILL = ""
    private  var listData : MutableList<ResponseRedeem> = mutableListOf()
    private lateinit var api: ServiceClientApi
    private var utilNet = UtilNet()
//    private lateinit var presenter: PrintPresenter
    private lateinit var presenter: PrintRedeemPresenter
    private var id_kasir :String ? = null
    private var nama_outlet :String ? = null
    private var alamat_outlet :String ? = null
    private var telp_outlet :String ? = null
    private var thanksText :String ? = null
    private var noteText :String ? = null
    private var note2Text :String ? = null
    private var nama_kasir :String ? = null

    var dialog: ProgressDialog? = null
    override fun showLoading() {
        dialog = ProgressDialog.show(this, "","Please Wait...", true)
    }
    override fun hideLoading() {
        dialog!!.dismiss()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_redeem_cetak)
        val data = intent
        id_kasir = data.getStringExtra("id_kasir")
        nama_outlet = data.getStringExtra("nama_outlet")
        alamat_outlet = data.getStringExtra("alamat_outlet")
        telp_outlet = data.getStringExtra("telp_outlet")
        thanksText = "Terima Kasih Atas Kunjungan Anda"
        noteText = "Barang yang sudah dibeli tidak"
        note2Text = "dapat ditukar/dikembalikan"
        nama_kasir = data.getStringExtra("nama_kasir")

        api = utilNet.getService().create(ServiceClientApi::class.java)
        presenter =
            PrintRedeemPresenter(this, api)
        presenter.getDataRedeem("$id_kasir")
        /** PRINT BLUETOOTH*/
        mScan = findViewById(R.id.Scan)
        mScan.setOnClickListener {
            mBluetoothAdapter = BluetoothAdapter.getDefaultAdapter()
            if (mBluetoothAdapter == null) {
                toast("Message1")
            } else {
                if (!mBluetoothAdapter!!.isEnabled) {
                    val enableBtIntent = Intent(BluetoothAdapter.ACTION_REQUEST_ENABLE)
                    startActivityForResult(enableBtIntent,
                        REQUEST_ENABLE_BT
                    )
                } else {
                    ListPairedDevices()
                    val connectIntent = Intent(this@CetakRedeemActivity,
                        DeviceListActivity::class.java)
                    startActivityForResult(connectIntent,
                        REQUEST_CONNECT_DEVICE
                    )
                }
            }
        }

        mPrint = findViewById(R.id.mPrint)
        mPrint.setOnClickListener {
            prosesPrint()
        }

        mDisc = findViewById(R.id.dis)
        mDisc.setOnClickListener {
            //            if (mBluetoothAdapter != null){
//                mBluetoothAdapter!!.disable()
//            }
            toast("$listData")
            Log.d("Hasilnya","$listData")
        }

        /** PRINT BLUETOOTH*/
    }

    override fun showDataList(data: ResponseRedeem) {
        val utilCenter = StringAlignUtils(
            31,
            StringAlignUtils.Alignment.CENTER
        )
        val textNamaOutlet= utilCenter.format(nama_outlet)
        val textAlamatOutlet = utilCenter.format(alamat_outlet)
        val textTelpOutlet = utilCenter.format(telp_outlet)

        BILL += "$textNamaOutlet"
        BILL += "$textAlamatOutlet"
        BILL += "$textTelpOutlet"
        BILL += "\n================================\n"
        BILL += "Kasir      : ${nama_kasir}\n"
        BILL += "Tanggal    : ${data.tanggalFaktur}\n"
        BILL += "No. Trx    : ${data.noFaktur}\n"
        BILL += "Pelanggan  : ${data.namaPelanggan}\n"
        BILL += "No. Member : ${data.noMember}\n"
        BILL += "--------------------------------\n"
        System.out.println(data.detailProduk)
        if (data.detailProduk != null) {
//            toast("Barangnya ${data.detailProduk}")
            for (i in data.detailProduk!!.indices) {
                BILL += String.format(
                    "%1$1s %2$5s %3$10s %4$10s",
                    "${data.detailProduk[i].nama}" + "\n",
                    "${data.detailProduk[i].jumlah}x",
                    "@" + "${data.detailProduk[i].poin}",
                    "${data.detailProduk[i].total_poin}\n"
                )
            }
        }else{
                toast("Produk kosong");
        }


        BILL += "--------------------------------\n"
        val utilRight = StringAlignUtils(
            17,
            StringAlignUtils.Alignment.RIGHT
        )
        val utilRight2 = StringAlignUtils(
            12,
            StringAlignUtils.Alignment.RIGHT
        )
        val textQTY= utilRight.format(data.totalQty)
        val textTagihan= utilRight.format(0)
        val textPoin = utilRight.format(data.totalPoin)
//        val textSubtotal= utilRight.format(data.totalSubtotal)

        BILL += "Total QTY      $textQTY"
        BILL += "Total Poin     $textPoin"
        BILL += "Total Tagihan  $textTagihan"
        BILL += "--------------------------------\n"
        BILL += "MetodeBayar : ${data.namaMetodebayar}\n"
        BILL += "================================\n"
        BILL += "$thanksText\n"
        BILL += "\n\n "
        BILL += "\n\n"
        BILL += "$noteText\n"
        BILL += "$note2Text\n"
        BILL += "\n\n "
        BILL += "\n\n "

        System.out.println(BILL)
    }

    fun prosesPrint(){
        val t = object : Thread() {
            override fun run() {
                try {
                    val os = mBluetoothSocket!!.outputStream

                    os.write(BILL.toByteArray())
                    //This is printer specific code you can comment ==== > Start

                    // Setting height
                    val gs = 29
                    os.write(
                        intToByteArray(
                            gs
                        ).toInt())
                    val h = 104
                    os.write(
                        intToByteArray(
                            h
                        ).toInt())
                    val n = 162
                    os.write(
                        intToByteArray(
                            n
                        ).toInt())

                    // Setting Width
                    val gs_width = 29
                    os.write(
                        intToByteArray(
                            gs_width
                        ).toInt())
                    val w = 119
                    os.write(
                        intToByteArray(
                            w
                        ).toInt())
                    val n_width = 2
                    os.write(
                        intToByteArray(
                            n_width
                        ).toInt())

                } catch (e: Exception) {
                    Log.e("CetakRedeemActivity", "Exe ", e)
                }
                startActivity<MainActivity>(
                    "codeIntent" to "4"
                )
            }
        }
        t.start()
    }


    override fun errorDataList(message: String) {
        toast("$message")
        startActivity<MainActivity>(
            "codeIntent" to "4"
        )
    }

    override fun setToolbar() {

    }


    override fun onDestroy() {
        // TODO Auto-generated method stub
        super.onDestroy()
        try {
            if (mBluetoothSocket != null)
                mBluetoothSocket!!.close()
        } catch (e: Exception) {
            Log.e("Tag", "Exe ", e)
        }

    }



    override fun onActivityResult(mRequestCode: Int, mResultCode: Int, mDataIntent: Intent?) {
        super.onActivityResult(mRequestCode, mResultCode, mDataIntent)

        when (mRequestCode) {
            REQUEST_CONNECT_DEVICE -> if (mResultCode == Activity.RESULT_OK) {
                val mExtra = mDataIntent!!.extras
                val mDeviceAddress = mExtra!!.getString("DeviceAddress")
                Log.v(TAG, "Coming incoming address " + mDeviceAddress!!)
                mBluetoothDevice = mBluetoothAdapter!!.getRemoteDevice(mDeviceAddress)
                mBluetoothConnectProgressDialog = ProgressDialog.show(this,"Connecting...", mBluetoothDevice.name + " : "+ mBluetoothDevice.address, true, false)
                val mBluetoothConnectThread = Thread(this)
                mBluetoothConnectThread.start()
                // pairToDevice(mBluetoothDevice); This method is replaced by
                // progress dialog with thread
            }

            REQUEST_ENABLE_BT -> if (mResultCode == Activity.RESULT_OK) {
                ListPairedDevices()
                val connectIntent = Intent(this@CetakRedeemActivity,
                    DeviceListActivity::class.java)
                startActivityForResult(connectIntent,
                    REQUEST_CONNECT_DEVICE
                )
            } else {
                toast("Message")
            }
        }
    }

    private fun ListPairedDevices() {
        val mPairedDevices = mBluetoothAdapter!!.bondedDevices

        if (mPairedDevices.size > 0) {
            for (mDevice in mPairedDevices) {
                Log.v(TAG, "PairedDevices: " + mDevice.name + "  "+ mDevice.address)
            }
        }
    }

    override fun run() {
        try {
            mBluetoothSocket = mBluetoothDevice.createRfcommSocketToServiceRecord(applicationUUID)
            mBluetoothAdapter!!.cancelDiscovery()
            mBluetoothSocket!!.connect()
            mHandler.sendEmptyMessage(0)
        } catch (eConnectException: IOException) {
            Log.d(TAG, "CouldNotConnectToSocket", eConnectException)
            toast("CouldNotConnectToSocket")
            closeSocket(this.mBluetoothSocket!!)
            return
        }
    }

    private fun closeSocket(nOpenSocket: BluetoothSocket) {
        try {
            nOpenSocket.close()
            Log.d(TAG, "SocketClosed")
            toast("SocketClosed")
            mBluetoothConnectProgressDialog!!.dismiss()
        } catch (ex: IOException) {
            Log.d(TAG, "CouldNotCloseSocket")
            toast("CouldNotCloseSocket")
            mBluetoothConnectProgressDialog!!.dismiss()
        }

    }

    companion object {
        private const val TAG = "TAG"
        private const val REQUEST_CONNECT_DEVICE = 1
        private const val REQUEST_ENABLE_BT = 2

        fun intToByteArray(value: Int): Byte {
            val b = ByteBuffer.allocate(4).putInt(value).array()

            for (k in b.indices) {
                println("Selva  [" + k + "] = " + "0x" + UnicodeFormatter.byteToHex(
                    b[k]
                )
                )
            }

            return b[3]
        }
    }


    override fun onBackPressed() {
        startActivity<MainActivity>(
            "codeIntent" to "4"
        )
        super.onBackPressed()
    }
}
